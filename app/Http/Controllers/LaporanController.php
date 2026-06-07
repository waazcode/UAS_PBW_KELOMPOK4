<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class LaporanController extends Controller
{
    private const BANDA_ACEH_LAT = 5.5483;

    private const BANDA_ACEH_LNG = 95.3238;

    private const BANDA_ACEH_BOUNDS = [
        'min_lat' => 5.48,
        'max_lat' => 5.60,
        'min_lng' => 95.26,
        'max_lng' => 95.38,
    ];
    public function index(): View
    {
        $laporans = Laporan::with(['kategori', 'user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('laporan.index', compact('laporans'));
    }

    public function peta(): View
    {
        $query = Laporan::with(['kategori', 'user'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $laporans = $query->latest()->get()
            ->map(fn (Laporan $laporan) => [
                'id' => $laporan->id,
                'judul' => $laporan->judul,
                'deskripsi' => $laporan->deskripsi,
                'alamat' => $laporan->alamat,
                'status' => $laporan->status,
                'status_label' => $laporan->status_label,
                'kategori_id' => $laporan->kategori_id,
                'kategori_nama' => $laporan->kategori->nama,
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
            'isAdmin' => auth()->user()->isAdmin(),
        ]);
    }

    public function create(): View
    {
        $kategoris = Kategori::orderBy('nama')->get();

        return view('laporan.create', compact('kategoris'));
    }

    public function suggestAlamat(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:200'],
        ]);

        $query = $request->q;
        if (! str_contains(strtolower($query), 'banda aceh')) {
            $query .= ', Banda Aceh';
        }

        $bounds = self::BANDA_ACEH_BOUNDS;

        $response = Http::withHeaders([
            'User-Agent' => config('app.name', 'SafeZone').'/1.0',
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $query,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 8,
            'countrycodes' => 'id',
            'viewbox' => "{$bounds['min_lng']},{$bounds['max_lat']},{$bounds['max_lng']},{$bounds['min_lat']}",
            'bounded' => 1,
        ]);

        if (! $response->successful()) {
            return response()->json([]);
        }

        $results = collect($response->json())
            ->filter(fn (array $item) => $this->isWithinBandaAceh((float) $item['lat'], (float) $item['lon']))
            ->map(fn (array $item) => [
                'label' => $item['display_name'],
                'lat' => (float) $item['lat'],
                'lng' => (float) $item['lon'],
            ])
            ->take(6)
            ->values();

        return response()->json($results);
    }

    public function reverseAlamat(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        if (! $this->isWithinBandaAceh((float) $request->lat, (float) $request->lng)) {
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
            'label' => $response->json('display_name'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'alamat' => ['required', 'string', 'max:500'],
            'foto' => ['required', 'image', 'max:2048'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        if (! $this->isWithinBandaAceh((float) $validated['latitude'], (float) $validated['longitude'])) {
            return back()
                ->withInput()
                ->withErrors(['latitude' => 'Lokasi harus berada di wilayah Banda Aceh.']);
        }

        $fotoPath = $request->file('foto')->store('laporan', 'public');

        Laporan::create([
            'user_id' => auth()->id(),
            'kategori_id' => $validated['kategori_id'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'alamat' => $validated['alamat'],
            'foto' => $fotoPath,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil dikirim! Lihat di peta untuk melihat lokasi laporan Anda.');
    }

    public function show(Laporan $laporan): View
    {
        abort_unless($laporan->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        $laporan->load(['kategori', 'user']);

        return view('laporan.show', compact('laporan'));
    }

    private function isWithinBandaAceh(float $lat, float $lng): bool
    {
        $bounds = self::BANDA_ACEH_BOUNDS;

        return $lat >= $bounds['min_lat']
            && $lat <= $bounds['max_lat']
            && $lng >= $bounds['min_lng']
            && $lng <= $bounds['max_lng'];
    }
}
