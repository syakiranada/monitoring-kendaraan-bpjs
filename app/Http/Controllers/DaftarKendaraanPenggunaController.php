<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public function daftarKendaraan(Request $request)
    {
        // Query dasar untuk kendaraan dengan aset 'guna'
        $search = $request->search;
        $query = Kendaraan::where('aset', 'guna');

        // Jika terdapat pencarian, tambahkan kondisi filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('merk', 'like', "%$search%")
                ->orWhere('tipe', 'like', "%$search%")
                ->orWhere('plat_nomor', 'like', "%$search%")
                ->orWhere('kapasitas', 'like', "%$search%")
                ->orWhere('status_ketersediaan', 'like', "%$search%");
            });
        }

        // Dapatkan hasil query dengan pagination
        $kendaraan = $query->paginate(5);

        return view('pengguna.daftarKendaraan', compact('kendaraan', 'search'));
}

    // public function daftarKendaraan(Request $request)
    // {
    //     // Ambil semua kendaraan dengan aset 'guna' sebagai basis query untuk pagination
    //     $query = Kendaraan::where('aset', 'guna')->orderBy('created_at', 'desc');

    //     // Jika ada input pencarian, filter berdasarkan merk, tipe, atau plat_nomor
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('merk', 'like', "%$search%")
    //             ->orWhere('tipe', 'like', "%$search%")
    //             ->orWhere('plat_nomor', 'like', "%$search%")
    //             ->orWhere('kapasitas', 'like', "%$search%")
    //             ->orWhere('status_ketersediaan', 'like', "%$search%");
    //         });
    //     }

    //     // Hasil query dengan pagination
    //     $kendaraan = $query->paginate(5);

    //     // Untuk keperluan dropdown filter, ambil semua kendaraan dengan aset 'guna'
    //     $kendaraan = Kendaraan::where('aset', 'guna')->get();

    //     return view('pengguna.daftarKendaraan', compact('kendaraan', 'kendaraans'));
    // }

    
    public function detail($id)
    {
        $kendaraan = Kendaraan::with([
            'asuransi', 
            'bbm'=> function($query) {
                $query->orderBy('tgl_isi', 'desc');
            }, 
            'cekFisik' => function($query) {
                $query->orderBy('tgl_cek_fisik', 'desc');
            }, 
            'pajak', 
            'peminjaman', 
            'servisInsidental', 
            'servisRutin'
        ])->findOrFail($id);

        $cekFisikTerbaru = $kendaraan->cekFisik->first();
        $bbm = $kendaraan->bbm->first();

        // Get latest tax data
        $latestPajak = DB::table('pajak')
            ->where('id_kendaraan', $id)
            ->orderBy('tahun', 'desc')
            ->first();

        // Get latest insurance data
        $latestAsuransi = DB::table('asuransi')
            ->where('id_kendaraan', $id)
            ->orderBy('tahun', 'desc')
            ->first();
        // Get latest servis rutin
        $latestServisRutin = DB::table('servis_rutin')
            ->where('id_kendaraan', $id)
            ->orderBy('tgl_servis_real', 'desc')
            ->first();

        $statusServisRutin = $this->calculateStatusServisRutin($latestServisRutin);

        $statusPajak = $this->calculateStatusPajak($latestPajak);
        $statusAsuransi = $this->calculateStatusAsuransi($latestAsuransi);

        return view('pengguna.detailDaftarKendaraan', compact(
            'kendaraan', 
            'cekFisikTerbaru', 
            'bbm',
            'statusPajak',
            'statusAsuransi',
            'statusServisRutin'
        ));
    }
    // public function detail($id)
    // {
    //     $kendaraan = Kendaraan::with([
    //         'asuransi', 
    //         'bbm'=> function($query) {
    //             $query->orderBy('tgl_isi', 'desc');
    //         }, 
    //         'cekFisik' => function($query) {
    //             $query->orderBy('tgl_cek_fisik', 'desc');
    //         }, 
    //         'pajak', 
    //         'peminjaman', 
    //         'servisInsidental', 
    //         'servisRutin'
    //     ])->findOrFail($id);

    //     $cekFisikTerbaru = $kendaraan->cekFisik->first();
    //     $bbm = $kendaraan->bbm->first();

    //     // Get latest servis rutin
    //     $latestServisRutin = DB::table('servis_rutin')
    //         ->where('id_kendaraan', $id)
    //         ->orderBy('tgl_servis_real', 'desc')
    //         ->first();

    //     $statusServisRutin = $this->calculateStatusServisRutin($latestServisRutin);

    //     return view('pengguna.detailDaftarKendaraan', compact(
    //         'kendaraan', 
    //         'cekFisikTerbaru', 
    //         'bbm',
            
    //         'statusServisRutin'
    //     ));
    // }

    private function calculateStatusPajak($pajak)
    {
        if (!$pajak) {
            return 'BELUM ADA DATA PAJAK';
        }

        $today = Carbon::now();
        $jatuhTempo = Carbon::parse($pajak->tgl_jatuh_tempo)->addYear(); // Tambah 1 tahun dari tanggal jatuh tempo terakhir
        $oneMonthBefore = $jatuhTempo->copy()->subMonth();

        if ($today->greaterThan($jatuhTempo)) {
            return 'JATUH TEMPO';
        }

        if ($today->between($oneMonthBefore, $jatuhTempo)) {
            return 'MENDEKATI JATUH TEMPO';
        }

        return 'SUDAH DIBAYAR';
    }

    private function calculateStatusAsuransi($asuransi)
    {
        if (!$asuransi) {
            return 'BELUM ADA DATA ASURANSI';
        }

        $today = Carbon::now();
        $jatuhTempo = Carbon::parse($asuransi->tgl_perlindungan_akhir);
        $oneMonthBefore = $jatuhTempo->copy()->subMonth();

        if ($today->greaterThan($jatuhTempo)) {
            return 'JATUH TEMPO';
        }

        if ($today->between($oneMonthBefore, $jatuhTempo)) {
            return 'MENDEKATI JATUH TEMPO';
        }

        return 'SUDAH DIBAYAR';
    }
    private function calculateStatusServisRutin($servisRutin)
    {
        if (!$servisRutin) {
            return 'BELUM ADA DATA SERVIS';
        }

        $today = Carbon::now();
        $tglServisSelanjutnya = Carbon::parse($servisRutin->tgl_servis_selanjutnya);
        $oneWeekBefore = $tglServisSelanjutnya->copy()->subWeek(); // 1 minggu sebelum jadwal
        
        if ($today->greaterThan($tglServisSelanjutnya)) {
            return 'WAKTUNYA SERVIS';
        }

        if ($today->between($oneWeekBefore, $tglServisSelanjutnya)) {
            return 'MENDEKATI JADWAL SERVIS';
        }

        return 'SUDAH SERVIS';
    }
}
    
