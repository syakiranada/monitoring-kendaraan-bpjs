<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengajuanPeminjamanController extends Controller
{
    public function index()
    {
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])
            ->where('status_pinjam', 'Menunggu Persetujuan')
            ->get();

        return view('admin.pengajuan-peminjaman.index', compact('peminjaman'));
    }

    public function detail($id)
    {
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])->findOrFail($id);
        return view('admin.pengajuan-peminjaman.detail', compact('peminjaman'));
    }

    public function setujui($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status_pinjam = 'Disetujui';
        $peminjaman->save();

        return redirect()->route('admin.pengajuan-peminjaman.index')->with('success', 'Peminjaman telah disetujui');
    }

    public function tolak($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status_pinjam = 'Ditolak';
        $peminjaman->save();
        if ($peminjaman->perpanjangan_dari) {
            $peminjamanAwal = Peminjaman::find($peminjaman->perpanjangan_dari);
            if ($peminjamanAwal) {
                $peminjamanAwal->status_pinjam = 'Disetujui';
                $peminjamanAwal->save();
            }
        }

        return redirect()->route('admin.pengajuan-peminjaman.index')->with('success', 'Peminjaman telah ditolak');
    }
   

}
