<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanAdminController extends Controller
{
    /**
     * Tampilkan daftar laporan untuk admin.
     */
    public function index(): View
    {
        $laporans = Laporan::with(['user', 'kategori'])
            ->withCount('komentars')
            ->latest()
            ->get();

        return view('admin.laporan.index', compact('laporans'));
    }

    public function show(Laporan $laporan): View
    {
        $laporan->load(['kategori', 'user', 'komentars.user']);

        return view('laporan.show', [
            'laporan' => $laporan,
            'backUrl' => route('admin.laporan.index'),
            'isAdminView' => true,
        ]);
    }

    /**
     * Ubah status laporan.
     */
    public function updateStatus(Request $request, Laporan $laporan): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:menunggu,proses,selesai'],
        ]);

        $laporan->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }
}
