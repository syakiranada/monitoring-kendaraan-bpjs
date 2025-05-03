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
    
    $search = $request->input('search', '');
    $page = $request->input('page', 1);

    // Query dasar: kendaraan dengan aset 'guna' dan eager load relasi yang diperlukan
    $kendaraanQuery = Kendaraan::with([
        'pajak' => function ($query) {
            // Ambil data pajak terbaru berdasarkan tgl_bayar
            $query->latest('tgl_bayar')->take(1); // Ambil hanya yang terbaru
        },
        'asuransi' => function ($query) {
            // Ambil data asuransi terbaru berdasarkan tgl_perlindungan_akhir
            $query->latest('tgl_perlindungan_akhir')->take(1); // Ambil hanya yang terbaru
        },
        'servisRutin' => function ($query) {
            // Ambil data servis terbaru berdasarkan tgl_servis_selanjutnya
            $query->latest('tgl_servis_selanjutnya')->take(1); // Ambil hanya yang terbaru
        }
    ])->where('aset', 'guna');

    Log::info("Initial search parameters:", ['search' => request('search')]);

    // Terapkan filtering (baik berdasarkan status maupun pencarian umum)
    $this->applyFilters($kendaraanQuery, $search, $request);

    // Ambil hasil dengan paginasi dan lampirkan parameter search
    // $kendaraan = $kendaraanQuery->paginate(10)->appends(['search' => $search, 'page' => $currentPage]);
    // $kendaraan = $kendaraanQuery->paginate(10)->appends(['search' => $search]);
    // Paginate the results with the search query parameter preserved
    $kendaraan = $kendaraanQuery->paginate(10)->appends(['search' => request('search')]);

    return view('pengguna.daftarKendaraan', compact('kendaraan', 'search', 'page'));
}

private function applyFilters($query, $search, $request)
{
    // Pastikan search diisi
    if ($request->filled('search')) {
        $searchWords = explode(' ', $search); // Pisahkan menjadi array kata
        // $query->where('aset', 'guna');
        // Query pencarian berdasarkan kata kunci (merk, tipe kendaraan, dll.)
         // Cek apakah ada kata kunci khusus seperti 'pajak', 'asuransi', atau 'servis'
         $isPajakSearch = false;
         $isAsuransiSearch = false;
         $isServisSearch = false;
         
 
         foreach ($searchWords as $word) {
             if (str_contains(strtolower($word), 'pajak')) {
                 $isPajakSearch = true;
             }
             if (str_contains(strtolower($word), 'asuransi')) {
                 $isAsuransiSearch = true;
             }
             if (str_contains(strtolower($word), 'servis')) {
                 $isServisSearch = true;
             }
         }

        $query->where(function ($q) use ($searchWords) {
            foreach ($searchWords as $word) {
                $q->where(function ($q2) use ($word) {
                    // Pencarian berdasarkan nama merk, tipe kendaraan, dan plat nomor
                    $q2->where('merk', 'like', "%$word%")
                        ->orWhere('tipe', 'like', "%$word%")
                        ->orWhere('plat_nomor', 'like', "%$word%")
                        ->orWhere('warna', 'like', "%$word%")
                        ->orWhere('jenis', 'like', "%$word%")
                        ->orWhere('aset', 'like', "%$word%")
                        ->orWhere('bahan_bakar', 'like', "%$word%")
                        ->orWhere('no_mesin', 'like', "%$word%")
                        ->orWhere('no_rangka', 'like', "%$word%")
                        ->orWhere('kapasitas', 'like', "%$word%")
                        ->orWhere('tgl_pembelian', 'like', "%$word%")
                        ->orWhere('status_ketersediaan', 'like', "%$word%");
                        
                });
            }
        });

        // Apply pajak, asuransi, dan servis hanya sekali, bukan dalam loop pencarian
        // $this->applyPajakFilters($query, $search);
        // $this->applyAsuransiFilters($query, $search);
        // $this->applyServisFilters($query, $search);
        // Jika kata kunci mengarah ke pajak, terapkan filter pajak
        if ($isPajakSearch) {
            $this->applyPajakFilters($query, $search);
        }

        // Jika kata kunci mengarah ke asuransi, terapkan filter asuransi
        if ($isAsuransiSearch) {
            $this->applyAsuransiFilters($query, $search);
        }

        // Jika kata kunci mengarah ke servis, terapkan filter servis
        if ($isServisSearch) {
            $this->applyServisFilters($query, $search);
        }

        // Log generated query dan bindings
        Log::info('Generated Query: ' . $query->toSql());
        Log::info('Bindings: ' . json_encode($query->getBindings()));
    }
}
private function applyPajakFilters($query, $search)
{
    $query->where('kendaraan.aset', 'guna');
    if (str_contains(strtolower($search), 'pajak jatuh tempo')|| str_contains(strtolower($search), 'jatuh tempo pajak')) {
        $query->orWhereHas('pajak', function ($q) {
            // Menggunakan subquery untuk mendapatkan MAX(tgl_jatuh_tempo) dan bandingkan dengan CURDATE()
            $q->whereRaw("
                CURDATE() >= DATE_ADD(
                    (SELECT MAX(tgl_jatuh_tempo) 
                     FROM pajak 
                     WHERE pajak.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna'), 
                    INTERVAL 1 YEAR
                )
            ");
        });
    }

    if (str_contains(strtolower($search), 'mendekati jatuh tempo pajak') || str_contains(strtolower($search), 'pajak mendekati jatuh tempo')) {

        $query->orWhereHas('pajak', function ($q) {
            $q->whereRaw("
                CURDATE() BETWEEN DATE_ADD(
                    (SELECT MAX(tgl_jatuh_tempo) FROM pajak 
                    WHERE pajak.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna'),
                    INTERVAL 11 MONTH
                ) 
                AND DATE_ADD(
                    (SELECT MAX(tgl_jatuh_tempo) FROM pajak 
                    WHERE pajak.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna'),
                    INTERVAL 1 YEAR
                ) - INTERVAL 1 DAY

            ");
        });
    }

    if (str_contains(strtolower($search), 'sudah dibayar pajak') || str_contains(strtolower($search), 'pajak sudah dibayar') ){
        $query->orWhereHas('pajak', function ($q) {
            $q->whereRaw("
                CURDATE() < DATE_ADD(
                    (SELECT MAX(tgl_jatuh_tempo) 
                     FROM pajak 
                     WHERE pajak.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna'), 
                    INTERVAL 11 MONTH
                )
            ");
        });
    }

    if (str_contains(strtolower($search), 'belum ada data pajak') || str_contains(strtolower($search), 'pajak belum ada data')) {
       
        $query->orWhereDoesntHave('pajak');
    }
}



private function applyAsuransiFilters($query, $search)
{
    $query->where('kendaraan.aset', 'guna');
    // Jatuh tempo asuransi: Menggunakan MAX(tgl_perlindungan_akhir) untuk mengambil data terbaru
    if (str_contains(strtolower($search), 'asuransi jatuh tempo')|| str_contains(strtolower($search), 'jatuh tempo asuransi')) {
        $query->orWhereHas('asuransi', function ($q) {
            $q->whereRaw("
                CURDATE() >= DATE_ADD(
                (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi
                 WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna'),
                INTERVAL 0 DAY
            )

            ");
        });
    }

    // Mendekati jatuh tempo asuransi (1 bulan sebelum tgl_perlindungan_akhir)
    if (str_contains(strtolower($search), 'mendekati jatuh tempo asuransi') || str_contains(strtolower($search), 'asuransi mendekati jatuh tempo')) {
        $query->orWhereHas('asuransi', function ($q) {
            $q->whereRaw("
               CURDATE() BETWEEN DATE_ADD(
                    (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi 
                    WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna'),
                    INTERVAL -1 MONTH
                ) 
                AND (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi 
                    WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna')

            ");
        });
    }

    // Sudah dibayar asuransi (masih dalam 1 bulan dari tgl_perlindungan_akhir)
    if (str_contains(strtolower($search), 'sudah dibayar asuransi') || str_contains(strtolower($search), 'asuransi sudah dibayar')) {
        $query->orWhereHas('asuransi', function ($q) {
            $q->whereRaw("
                CURDATE() < DATE_ADD(
                (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi
                 WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna'),
                INTERVAL 1 MONTH
            )

            ");
        });
    }

    // Belum ada data asuransi
    if (str_contains(strtolower($search), 'belum ada data asuransi') || str_contains(strtolower($search), 'asuransi belum ada data')) {
        $query->orWhereDoesntHave('asuransi');
    }
}


private function applyServisFilters($query, $search)
{
    // Pastikan filter kendaraan dengan aset 'guna' diterapkan di awal
    $query->where('kendaraan.aset', 'guna');  // Pastikan filter aset diterapkan pertama

    // Jatuh tempo servis
    if (str_contains(strtolower($search), 'servis jatuh tempo')|| str_contains(strtolower($search), 'jatuh tempo servis')) {
        $query->orWhereHas('servisRutin', function ($q) {
            // Ambil data servis terbaru berdasarkan tgl_servis_selanjutnya
            $q->whereRaw("
                CURDATE() >= 
                (SELECT MAX(tgl_servis_selanjutnya) 
                 FROM servis_rutin 
                 WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan
                 AND kendaraan.aset = 'guna')  
            ");
        });
    }

    // Mendekati jatuh tempo servis (1 bulan sebelum tgl_servis_selanjutnya)
    if (str_contains(strtolower($search), 'mendekati jatuh tempo servis') || str_contains(strtolower($search), 'servis mendekati jatuh tempo')) {
        $query->orWhereHas('servisRutin', function ($q) {
            $q->whereRaw("
                CURDATE() BETWEEN 
                DATE_SUB(
                    (SELECT MAX(tgl_servis_selanjutnya) 
                     FROM servis_rutin 
                     WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna'),
                    INTERVAL 1 MONTH
                )
                AND (SELECT MAX(tgl_servis_selanjutnya) 
                     FROM servis_rutin 
                     WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna')
            ");
        });
    }

    // Sudah servis (kurang dari 1 bulan dari tgl_servis_selanjutnya)
    if (str_contains(strtolower($search), 'sudah servis')) {
        $query->orWhereHas('servisRutin', function ($q) {
            $q->whereRaw("
                CURDATE() < DATE_SUB(
                    (SELECT MAX(tgl_servis_selanjutnya) 
                     FROM servis_rutin 
                     WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan AND kendaraan.aset = 'guna'),
                    INTERVAL 1 MONTH
                )
            ");
        });
    }

    // Belum ada data servis
    if (str_contains(strtolower($search), 'belum ada data servis')) {
        $query->orWhereDoesntHave('servisRutin');
    }
}


public function detail($id,Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
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
            'kendaraan', 'page',
            'cekFisikTerbaru', 
            'bbm',
            'statusPajak',
            'statusAsuransi',
            'statusServisRutin'
        ));
    }
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
    $oneMonthBefore = $tglServisSelanjutnya->copy()->subMonth(); // 1 bulan sebelum jadwal

    if ($today->greaterThan($tglServisSelanjutnya)) {
        return 'JATUH TEMPO';
    }

    if ($today->between($oneMonthBefore, $tglServisSelanjutnya)) {
        return 'MENDEKATI JATUH TEMPO';
    }

    return 'SUDAH SERVIS';
}

   

}
    
