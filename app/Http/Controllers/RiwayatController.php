<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        return view('admin.riwayat.index');
    }

    public function peminjaman(Request $request)
    {
        $query = Peminjaman::with(['user', 'kendaraan'])->orderBy('tgl_mulai', 'desc');

        // Filter berdasarkan kendaraan (merk, tipe, plat nomor)
        if ($request->filled('kendaraan')) {
            $query->whereHas('kendaraan', function($q) use ($request) {
                $q->where('plat_nomor', $request->kendaraan);
            });
        }

        // Filter berdasarkan peminjam
        if ($request->filled('peminjam')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->peminjam . '%');
            });
        }

        $riwayatPeminjaman = $query->get();
        $kendaraan = Kendaraan::all(); // Ambil semua kendaraan untuk dropdown filter

        return view('admin.riwayat.peminjaman', compact('riwayatPeminjaman', 'kendaraan'));
    }

    public function detailPeminjaman($id)
    {
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])->findOrFail($id);

        return view('admin.riwayat.detail-peminjaman', compact('peminjaman'));
    }



}
