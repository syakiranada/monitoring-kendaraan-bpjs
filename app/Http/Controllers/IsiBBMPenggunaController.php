<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\Kendaraan;

class IsiBBMPenggunaController extends Controller
{
    public function index()
    {
        // Ambil kendaraan yang dipinjam oleh user saat ini dengan status 'Disetujui' atau 'Diperpanjang'
        $kendaraanDipinjam = Peminjaman::with('kendaraan') // Pastikan relasi 'kendaraan' ada di model Peminjaman
            ->where('user_id', Auth::id())
            ->whereIn('status_pinjam', ['Disetujui', 'Diperpanjang'])
            ->get()
            ->pluck('kendaraan'); // Ambil kendaraan terkait

        // Ambil riwayat pengisian BBM user (jika diperlukan, uncomment baris ini)
        // $riwayatBBM = PengisianBBM::with(['kendaraan'])
        //     ->where('user_id', Auth::id())
        //     ->orderBy('tanggal_pengisian', 'desc')
        //     ->paginate(10);

        // Return ke view dengan data yang diambil
        return view('pengguna.pengisianBBM', compact('kendaraanDipinjam'));
    }
}
