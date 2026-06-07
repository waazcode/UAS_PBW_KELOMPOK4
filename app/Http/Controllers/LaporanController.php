<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
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
        $laporans = Laporan::with(['kategori', 'user'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get()
            ->map(fn (Laporan $laporan) => [
                'id' => $laporan->id,
                'judul' => $laporan->judul,
                'deskripsi' => $laporan->deskripsi,
                'status' => $laporan->status,
                'status_label' => $laporan->status_label,
                'kategori_id' => $laporan->kategori_id,
                'kategori_nama' => $laporan->kategori->nama,
                'latitude' => $laporan->latitude,
                'longitude' => $laporan->longitude,
                'pelapor' => $laporan->user->name,
                'created_at' => $laporan->created_at->format('d M Y'),
            ]);

        $kategoris = Kategori::orderBy('nama')->get();

        return view('laporan.peta', [
            'laporans' => $laporans,
            'kategoris' => $kategoris,
            'statuses' => Laporan::STATUS,
        ]);
    }

    public function create(): View
    {
        $kategoris = Kategori::orderBy('nama')->get();

        return view('laporan.create', compact('kategoris'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'foto' => ['required', 'image', 'max:2048'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $fotoPath = $request->file('foto')->store('laporan', 'public');

        Laporan::create([
            'user_id' => auth()->id(),
            'kategori_id' => $validated['kategori_id'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'foto' => $fotoPath,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return redirect()->route('laporan.peta')
            ->with('success', 'Laporan berhasil dikirim! Terima kasih atas partisipasi Anda.');
    }

    public function show(Laporan $laporan): View
    {
        abort_unless($laporan->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        $laporan->load(['kategori', 'user']);

        return view('laporan.show', compact('laporan'));
    }
}
