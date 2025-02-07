<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Kendaraan;
use App\Models\Asuransi;
use App\Models\BBM;
use App\Models\CekFisik;
use App\Models\Pajak;
use App\Models\Peminjaman;
use App\Models\ServisInsidental;
use App\Models\ServisRutin;

class PeminjamanPenggunaController extends Controller
{
    public function peminjamanPage()
    {
        $userId = Auth::id(); // Ambil ID user yang sedang login
        $daftarPeminjaman = Peminjaman::with('kendaraan') // Ambil data kendaraan juga
            ->where('user_id', $userId)
            ->orderBy('tgl_mulai', 'desc') // Urutkan dari terbaru
            ->get();
        
        return view('pengguna.peminjaman', compact('daftarPeminjaman'));
    }
    public function form()
    {
        return view('pengguna.formPeminjaman');
    }
}
