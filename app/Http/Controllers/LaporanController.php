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
        ]);

        $fotoPath = $request->file('foto')->store('laporan', 'public');

        Laporan::create([
            'user_id' => auth()->id(),
            'kategori_id' => $validated['kategori_id'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'foto' => $fotoPath,
        ]);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dikirim.');
    }

    public function show(Laporan $laporan): View
    {
        abort_unless($laporan->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        $laporan->load(['kategori', 'user']);

        return view('laporan.show', compact('laporan'));
    }
}
