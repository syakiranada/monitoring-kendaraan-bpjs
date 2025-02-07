<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Asuransi;
use App\Models\BBM;
use App\Models\CekFisik;
use App\Models\Pajak;
use App\Models\Peminjaman;
use App\Models\ServisInsidental;
use App\Models\ServisRutin;

class DaftarKendaraanPenggunaController extends Controller
{
    public function daftarKendaraan()
    {
        $kendaraan = Kendaraan::where('aset', 'guna')->get();
        return view('pengguna.daftarKendaraan', compact('kendaraan'));
    }
    public function detail($id)
    {
        $kendaraan = Kendaraan::with([
            'asuransi', 
            'bbm'=> function($query) {
                $query->orderBy('tgl_isi', 'desc'); // Ambil data terbaru
            }, 
            'cekFisik' => function($query) {
                $query->orderBy('tgl_cek_fisik', 'desc'); // Ambil data cek fisik terbaru
            }, 
            'pajak', 
            'peminjaman', 
            'servisInsidental', 
            'servisRutin'
        ])->findOrFail($id);

        $cekFisikTerbaru = $kendaraan->cekFisik->first(); // Ambil data terbaru dari koleksi
        $bbm =$kendaraan->bbm->first();

        return view('pengguna.detailDaftarKendaraan', compact('kendaraan', 'cekFisikTerbaru', 'bbm'));
    }


}
