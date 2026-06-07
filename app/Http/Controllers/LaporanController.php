<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Komentar;
use App\Models\Laporan;
use App\Support\DisplayText;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class LaporanController extends Controller
{
    private const ACEH_BOUNDS = [
        'min_lat' => 2.0,
        'max_lat' => 6.3,
        'min_lng' => 95.0,
        'max_lng' => 98.3,
    ];
    public function index(): View
    {
        $laporans = Laporan::with(['kategori', 'user'])
            ->latest()
            ->get();

        return view('laporan.index', compact('laporans'));
    }

    public function peta(): View
    {
        $laporans = Laporan::with(['kategori', 'user'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get()
            ->map(fn (Laporan $laporan) => [
                'id' => $laporan->id,
                'judul' => $laporan->judul,
                'deskripsi' => $laporan->deskripsi,
                'alamat' => $laporan->alamat_tampilan,
                'status' => $laporan->status,
                'status_label' => $laporan->status_label,
                'kategori_id' => $laporan->kategori_id,
                'kategori_nama' => $laporan->kategori->nama_tampilan,
                'latitude' => $laporan->latitude,
                'longitude' => $laporan->longitude,
                'pelapor' => $laporan->user->name,
                'created_at' => $laporan->created_at->format('d M Y'),
                'detail_url' => route('laporan.show', $laporan),
            ]);

        $kategoris = Kategori::orderBy('nama')->get();

        return view('laporan.peta', [
            'laporans' => $laporans,
            'kategoris' => $kategoris,
            'statuses' => Laporan::STATUS,
            'highlightId' => request()->integer('laporan') ?: null,
        ]);
    }

    public function create(): View
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $lainnyaKategoriId = $kategoris->firstWhere('nama', 'Lainnya')?->id;

        return view('laporan.create', compact('kategoris', 'lainnyaKategoriId'));
    }

    public function suggestAlamat(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:200'],
        ]);

        $query = trim($request->q);
        $cacheKey = 'alamat_suggest:'.md5(mb_strtolower($query));

        $results = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($query) {
            $searchQuery = $query;
            if (! str_contains(strtolower($searchQuery), 'aceh')) {
                $searchQuery .= ', Aceh';
            }

            $bounds = self::ACEH_BOUNDS;

            $response = Http::withHeaders([
                'User-Agent' => config('app.name', 'SafeZone').'/1.0',
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $searchQuery,
                'format' => 'json',
                'addressdetails' => 0,
                'limit' => 6,
                'countrycodes' => 'id',
                'viewbox' => "{$bounds['min_lng']},{$bounds['max_lat']},{$bounds['max_lng']},{$bounds['min_lat']}",
                'bounded' => 1,
            ]);

            if (! $response->successful()) {
                return [];
            }

            return collect($response->json())
                ->filter(fn (array $item) => $this->isWithinAceh((float) $item['lat'], (float) $item['lon']))
                ->map(fn (array $item) => [
                    'label' => DisplayText::format($item['display_name']),
                    'lat' => (float) $item['lat'],
                    'lng' => (float) $item['lon'],
                ])
                ->take(6)
                ->values()
                ->all();
        });

        return response()->json($results);
    }

    public function reverseAlamat(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        if (! $this->isWithinAceh((float) $request->lat, (float) $request->lng)) {
            return response()->json(['label' => null], 422);
        }

        $response = Http::withHeaders([
            'User-Agent' => config('app.name', 'SafeZone').'/1.0',
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'lat' => $request->lat,
            'lon' => $request->lng,
            'format' => 'json',
        ]);

        if (! $response->successful()) {
            return response()->json(['label' => null]);
        }

        return response()->json([
            'label' => DisplayText::format($response->json('display_name')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'kategori_lain' => ['nullable', 'string', 'max:100'],
            'alamat' => ['required', 'string', 'max:500'],
            'foto' => ['required', 'image', 'max:2048'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $lainnyaId = Kategori::where('nama', 'Lainnya')->value('id');
        $kategoriId = (int) $validated['kategori_id'];

        if ($lainnyaId && $kategoriId === (int) $lainnyaId) {
            $request->validate([
                'kategori_lain' => ['required', 'string', 'max:100'],
            ]);

            $kategori = Kategori::firstOrCreate([
                'nama' => DisplayText::format($request->kategori_lain),
            ]);
            $kategoriId = $kategori->id;
        }

        if (! $this->isWithinAceh((float) $validated['latitude'], (float) $validated['longitude'])) {
            return back()
                ->withInput()
                ->withErrors(['latitude' => 'Lokasi harus berada di wilayah Aceh.']);
        }

        $fotoPath = $request->file('foto')->store('laporan', 'public');

        Laporan::create([
            'user_id' => auth()->id(),
            'kategori_id' => $kategoriId,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'alamat' => DisplayText::format($validated['alamat']),
            'foto' => $fotoPath,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil dikirim! Lihat di peta untuk melihat lokasi laporan Anda.');
    }

    public function show(Laporan $laporan): View
    {
        $laporan->load(['kategori', 'user', 'komentars.user']);

        return view('laporan.show', [
            'laporan' => $laporan,
            'backUrl' => auth()->user()->isAdmin()
                ? route('admin.laporan.index')
                : route('laporan.index'),
            'isAdminView' => auth()->user()->isAdmin(),
        ]);
    }

    public function storeKomentar(Request $request, Laporan $laporan): RedirectResponse
    {
        $validated = $request->validate([
            'isi' => ['required', 'string', 'max:1000'],
        ]);

        Komentar::create([
            'laporan_id' => $laporan->id,
            'user_id' => auth()->id(),
            'isi' => $validated['isi'],
        ]);

        $redirectRoute = auth()->user()->isAdmin()
            ? route('admin.laporan.show', $laporan)
            : route('laporan.show', $laporan);

        return redirect($redirectRoute)
            ->with('success', 'Komentar berhasil ditambahkan.');
    }

    private function isWithinAceh(float $lat, float $lng): bool
    {
        $bounds = self::ACEH_BOUNDS;

        return $lat >= $bounds['min_lat']
            && $lat <= $bounds['max_lat']
            && $lng >= $bounds['min_lng']
            && $lng <= $bounds['max_lng'];
    }
}
