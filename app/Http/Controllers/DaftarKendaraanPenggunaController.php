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
//     public function daftarKendaraan(Request $request)
// {
//     $search = $request->input('search');
//     // Subquery: Data pajak terbaru per kendaraan
//     // $latestPajak = DB::table('pajak')
//     //     ->select(
//     //         'id_kendaraan',
//     //         DB::raw('MAX(tgl_bayar) as max_bayar'),
//     //         DB::raw('MAX(tgl_jatuh_tempo) as max_jatuh_tempo')
//     //     )
//     //     ->groupBy('id_kendaraan');
    
//     // // Query utama dengan joinSub agar alias latest_pajak tersedia
//     // $pajakQuery = Kendaraan::select(
//     //         'kendaraan.*',
//     //         'pajak.id_pajak',
//     //         'pajak.user_id',
//     //         'pajak.tahun',
//     //         'pajak.tgl_bayar',
//     //         'pajak.tgl_jatuh_tempo',
//     //         'pajak.bukti_bayar_pajak',
//     //         'pajak.nominal',
//     //         'pajak.biaya_pajak_lain',
//     //         DB::raw('DATE_ADD(latest_pajak.max_jatuh_tempo, INTERVAL 1 YEAR) as tgl_jatuh_tempo_seharusnya')
//     //     )
//     //     ->joinSub($latestPajak, 'latest_pajak', function ($join) {
//     //         $join->on('kendaraan.id_kendaraan', '=', 'latest_pajak.id_kendaraan');
//     //     })
//     //     ->leftJoin('pajak', function ($join) {
//     //         $join->on('kendaraan.id_kendaraan', '=', 'pajak.id_kendaraan')
//     //              ->on('pajak.tgl_bayar', '=', 'latest_pajak.max_bayar')
//     //              ->on('pajak.tgl_jatuh_tempo', '=', 'latest_pajak.max_jatuh_tempo');
//     //     })
//     //     ->where('kendaraan.aset', 'guna');
    
//     $kendaraanQuery = Kendaraan::with([
//         'pajak' => function ($query) {
//             $query->latest('tgl_bayar');
//         },
//         'asuransi',
//         'servisRutin'
//     ])->where('aset', 'guna');

//     // Apply filtering logic
//     $this->applyFilters($kendaraanQuery, $search);
    
//     $dataKendaraan = $kendaraanQuery->get();

//     Log::info("Initial search parameters:", [
//         'search' => $search
//     ]);
    
  
        

//     $searchDate = null;
//     if (!empty($search) && preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) {
//         try {
//             $searchDate = Carbon::createFromFormat('d-m-Y', $search);
//         } catch (\Exception $e) {
//             Log::error("Error parsing search date:", ['search' => $search, 'error' => $e->getMessage()]);
//         }
//     }
    
//     // Check for status-related search terms
//     // $statusSearch = false;
//     // if (!empty($search)) {
//     //     $lowerSearch = strtolower($search);
//     //     $originalSearch = $search;
        
//     //     // Check for availability status
//     //     if (stripos($lowerSearch, 'tidak tersedia') !== false) {
//     //         $dataKendaraanQuery->where('status_ketersediaan', '=', 'tidak tersedia');
//     //         $search = trim(str_ireplace('tidak tersedia', '', $search));
//     //         $statusSearch = true;
//     //     } elseif (stripos($lowerSearch, 'tersedia') !== false) {
//     //         $dataKendaraanQuery->where('status_ketersediaan', '=', 'tersedia');
//     //         $search = trim(str_ireplace('tersedia', '', $search));
//     //         $statusSearch = true;
//     //     }
    
//     //     // IMPORTANT: Remove the elseif structure to allow multiple conditions to be processed
//     //     // pajak
//     //     // if (stripos($lowerSearch, 'jatuh tempo pajak') !== false || 
//     //     //     stripos($lowerSearch, 'pajak jatuh tempo') !== false) {
            
//     //     //     $pajakQuery->whereNotNull('latest_pajak.max_jatuh_tempo')
//     //     //         ->whereRaw("CURDATE() >= DATE_ADD(latest_pajak.max_jatuh_tempo, INTERVAL 1 YEAR)");
            
//     //     //     $search = trim(str_ireplace(['jatuh tempo pajak', 'pajak jatuh tempo'], '', $search));
//     //     //     $statusSearch = true;
//     //     // }
//     //     if (stripos($lowerSearch, 'jatuh tempo pajak') !== false || 
//     //         stripos($lowerSearch, 'pajak jatuh tempo') !== false) {
            
//     //         // Menggunakan inline subquery untuk ambil record pajak terbaru (MAX(tgl_jatuh_tempo))
//     //         $pajakQuery->whereRaw("
//     //             CURDATE() >= DATE_ADD(
//     //                 (SELECT MAX(tgl_jatuh_tempo) FROM pajak 
//     //                  WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
//     //                 INTERVAL 1 YEAR
//     //             )
//     //         ");
            
//     //         $search = trim(str_ireplace(['jatuh tempo pajak', 'pajak jatuh tempo'], '', $search));
//     //         $statusSearch = true;
//     //     }
    
    

        
//     //     if (stripos($lowerSearch, 'mendekati jatuh tempo pajak') !== false || 
//     //         stripos($lowerSearch, 'pajak mendekati jatuh tempo') !== false) {
//     //         $dataKendaraanQuery->whereHas('pajak', function($query) {
//     //             $today = Carbon::now();
//     //             // Mendekati jatuh tempo = antara (jatuh tempo - 1 bulan) dan jatuh tempo
//     //             $query->whereRaw("DATE_ADD(tgl_jatuh_tempo, INTERVAL 1 YEAR) >= ?", [$today->toDateString()])
//     //                   ->whereRaw("DATE_ADD(tgl_jatuh_tempo, INTERVAL 11 MONTH) <= ?", [$today->toDateString()]);
//     //         });
//     //         $search = trim(str_ireplace(['mendekati jatuh tempo pajak', 'pajak mendekati jatuh tempo'], '', $search));
//     //         $statusSearch = true;
//     //     } 
        
//     //     if (stripos($lowerSearch, 'sudah dibayar pajak') !== false || 
//     //         stripos($lowerSearch, 'pajak sudah dibayar') !== false) {
//     //         $dataKendaraanQuery->whereHas('pajak', function($query) {
//     //             $today = Carbon::now();
//     //             // Sudah dibayar = hari ini lebih kecil dari (jatuh tempo - 1 bulan)
//     //             $query->whereRaw("DATE_ADD(tgl_jatuh_tempo, INTERVAL 11 MONTH) > ?", [$today->toDateString()]);
//     //         });
//     //         $search = trim(str_ireplace(['sudah dibayar pajak', 'pajak sudah dibayar'], '', $search));
//     //         $statusSearch = true;
//     //     } 
        
//     //     if (stripos($lowerSearch, 'belum ada data pajak') !== false) {
//     //         $dataKendaraanQuery->whereDoesntHave('pajak');
//     //         $search = trim(str_ireplace('belum ada data pajak', '', $search));
//     //         $statusSearch = true;
//     //     }
    
//     //     // Filter status asuransi
//     //     if (stripos($lowerSearch, 'jatuh tempo asuransi') !== false || 
//     //         stripos($lowerSearch, 'asuransi jatuh tempo') !== false) {
//     //         $dataKendaraanQuery->whereHas('asuransi', function($query) {
//     //             $today = Carbon::now();
//     //             // Jatuh tempo = tanggal perlindungan akhir lebih kecil dari hari ini
//     //             $query->whereDate('tgl_perlindungan_akhir', '<', $today);
//     //         });
//     //         $search = trim(str_ireplace(['jatuh tempo asuransi', 'asuransi jatuh tempo'], '', $search));
//     //         $statusSearch = true;
//     //     } 
        
//     //     if (stripos($lowerSearch, 'mendekati jatuh tempo asuransi') !== false || 
//     //         stripos($lowerSearch, 'asuransi mendekati jatuh tempo') !== false) {
//     //         $dataKendaraanQuery->whereHas('asuransi', function($query) {
//     //             $today = Carbon::now();
//     //             // Mendekati jatuh tempo = antara (jatuh tempo - 1 bulan) dan jatuh tempo
//     //             $query->whereDate('tgl_perlindungan_akhir', '>=', $today)
//     //                   ->whereDate('tgl_perlindungan_akhir', '<=', $today->copy()->addMonth());
//     //         });
//     //         $search = trim(str_ireplace(['mendekati jatuh tempo asuransi', 'asuransi mendekati jatuh tempo'], '', $search));
//     //         $statusSearch = true;
//     //     } 
        
//     //     if (stripos($lowerSearch, 'sudah dibayar asuransi') !== false || 
//     //         stripos($lowerSearch, 'asuransi sudah dibayar') !== false) {
//     //         $dataKendaraanQuery->whereHas('asuransi', function($query) {
//     //             $today = Carbon::now();
//     //             // Sudah dibayar = tanggal perlindungan akhir lebih dari (hari ini + 1 bulan)
//     //             $query->whereDate('tgl_perlindungan_akhir', '>', $today->copy()->addMonth());
//     //         });
//     //         $search = trim(str_ireplace(['sudah dibayar asuransi', 'asuransi sudah dibayar'], '', $search));
//     //         $statusSearch = true;
//     //     } 
        
//     //     if (stripos($lowerSearch, 'belum ada data asuransi') !== false) {
//     //         $dataKendaraanQuery->whereDoesntHave('asuransi');
//     //         $search = trim(str_ireplace('belum ada data asuransi', '', $search));
//     //         $statusSearch = true;
//     //     }
    
//     //     // General "belum ada data" - should be after specific ones
//     //     if (stripos($lowerSearch, 'belum ada data') !== false) {
//     //         $dataKendaraanQuery->whereDoesntHave('asuransi');
//     //         $search = trim(str_ireplace('belum ada data', '', $search));
//     //         $statusSearch = true;
//     //     }
    
//     //     // Filter status servis
//     //     if (stripos($lowerSearch, 'jatuh tempo servis') !== false || 
//     //         stripos($lowerSearch, 'servis jatuh tempo') !== false) {
//     //         $dataKendaraanQuery->whereHas('servisRutin', function($query) {
//     //             $today = Carbon::now();
                
//     //             // Jatuh tempo = tanggal servis selanjutnya lebih kecil dari hari ini
//     //             $query->whereDate('tgl_servis_selanjutnya', '<', $today);
//     //         });
//     //         $search = trim(str_ireplace(['jatuh tempo servis', 'servis jatuh tempo'], '', $search));
//     //         $statusSearch = true;
//     //     } 
        
//     //     if (stripos($lowerSearch, 'mendekati jadwal servis') !== false) {
//     //         $dataKendaraanQuery->whereHas('servisRutin', function($query) {
//     //             $today = Carbon::now();
//     //             // Mendekati jadwal servis = antara hari ini dan (hari ini + 1 bulan)
//     //             $query->whereDate('tgl_servis_selanjutnya', '>=', $today)
//     //                   ->whereDate('tgl_servis_selanjutnya', '<=', $today->copy()->addMonth());
//     //         });
//     //         $search = trim(str_ireplace('mendekati jadwal servis', '', $search));
//     //         $statusSearch = true;
//     //     } 
        
//     //     if (stripos($lowerSearch, 'sudah servis') !== false) {
//     //         $dataKendaraanQuery->whereHas('servisRutin', function($query) {
//     //             $today = Carbon::now();
//     //             // Sudah servis = tanggal servis selanjutnya lebih dari (hari ini + 1 bulan)
//     //             $query->whereDate('tgl_servis_selanjutnya', '>', $today->copy()->addMonth());
//     //         });
//     //         $search = trim(str_ireplace('sudah servis', '', $search));
//     //         $statusSearch = true;
//     //     } 
        
//     //     if (stripos($lowerSearch, 'belum ada data servis') !== false) {
//     //         $dataKendaraanQuery->whereDoesntHave('servisRutin');
//     //         $search = trim(str_ireplace('belum ada data servis', '', $search));
//     //         $statusSearch = true;
//     //     }
        
//     //     // If we need to revert to the original search (optional)
//     //     if ($search !== $originalSearch && !$statusSearch) {
//     //         $search = $originalSearch;
//     //     }
//     // }

//     if ($searchDate) {
//         // Handle date search logic
//         $matchingKendaraanIds = [];
//         $kendaraanIds = Kendaraan::pluck('id_kendaraan');

//         if (!empty($matchingKendaraanIds)) {
//             $dataKendaraanQuery->whereIn('id_kendaraan', $matchingKendaraanIds);
//         } else {
//             $dataKendaraanQuery->whereNull('id_kendaraan');
//         }
//     } elseif (!$statusSearch && !empty($search)) {
//         // Regular search for vehicle attributes
//         $dataKendaraanQuery->where(function ($query) use ($search) {
//             $query->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.merk)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.warna)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.plat_nomor)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.aset)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.warna)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.aset)) LIKE ?", ["%".strtolower($search)."%"]);
            
//             $query->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', CAST(kendaraan.kapasitas AS CHAR))) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', CAST(kendaraan.kapasitas AS CHAR))) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.jenis)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.jenis)) LIKE ?", ["%".strtolower($search)."%"]);
            
//             $query->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.no_mesin)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.no_rangka)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.no_rangka)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.no_mesin)) LIKE ?", ["%".strtolower($search)."%"]);

//             $query->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.bahan_bakar)) LIKE ?", ["%".strtolower($search)."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.bahan_bakar)) LIKE ?", ["%".strtolower($search)."%"]);

//             if (preg_match('/(\d{4})/', $search, $yearMatches)) {
//                 $year = $yearMatches[1];
//                 $query->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', YEAR(kendaraan.tgl_pembelian))) LIKE ?", 
//                     ["%".strtolower(str_replace($year, '', $search)).$year."%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', YEAR(kendaraan.tgl_pembelian))) LIKE ?", 
//                     ["%".strtolower(str_replace($year, '', $search)).$year."%"]);
//             }
         
//             if (preg_match('/(\d+)/', $search, $numMatches)) {
//                 $num = $numMatches[1];
//                 $query->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', CAST(kendaraan.kapasitas AS CHAR))) LIKE ?", 
//                     ["%".strtolower(str_replace($num, '', $search)).$num."%"]);
//             }
            
//             $searchTerms = explode(' ', strtolower($search));
//             $searchTerms = array_filter($searchTerms);
            
//             if (!empty($searchTerms)) {
//                 $query->orWhere(function ($andQuery) use ($searchTerms) {
//                     foreach ($searchTerms as $term) {
//                         $andQuery->where(function ($termQuery) use ($term) {
//                             $termQuery->whereRaw("LOWER(kendaraan.plat_nomor) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.merk) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.tipe) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.warna) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.jenis) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.aset) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.bahan_bakar) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.no_mesin) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.no_rangka) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("CAST(kendaraan.kapasitas AS CHAR) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("CAST(YEAR(kendaraan.tgl_pembelian) AS CHAR) LIKE ?", ["%$term%"]);
                                
                        
//                             $cleanTerm = str_replace([',', '.'], '', $term);
//                             $termQuery->orWhereRaw("REPLACE(REPLACE(CAST(kendaraan.nilai_perolehan AS CHAR), '.', ''), ',', '') LIKE ?", ["%$cleanTerm%"])
//                                 ->orWhereRaw("REPLACE(REPLACE(CAST(kendaraan.nilai_buku AS CHAR), '.', ''), ',', '') LIKE ?", ["%$cleanTerm%"]);
//                         });
//                     }
//                 });
//             }
//         });
//     }

//     Log::info("Final SQL Query:", ['sql' => $dataKendaraanQuery->toSql(), 'bindings' => $dataKendaraanQuery->getBindings()]);

//     $kendaraan = $dataKendaraanQuery->paginate(10)->appends(['search' => $request->input('search')]);
    
//     return view('pengguna.daftarKendaraan', compact('kendaraan', 'search'));
// }
// // public function daftarKendaraan(Request $request)
// //     {
// //         $search = $request->input('search');
        
// //         // Fetch kendaraan with the latest pajak using eager loading
// //         $kendaraanQuery = Kendaraan::with([
// //             'pajak' => function ($query) {
// //                 $query->latest('tgl_bayar');
// //             },
// //             'asuransi',
// //             'servisRutin'
// //         ])->where('aset', 'guna');

// //         // Apply filtering logic
// //         $this->applyFilters($kendaraanQuery, $search);
        
// //         // Execute query and get results
// //         $dataKendaraan = $kendaraanQuery->get();
        
// //         return response()->json($dataKendaraan);
// //     }

//     private function applyFilters($query, $search)
//     {
//         if (empty($search)) return;

//         $search = strtolower(trim($search));

//         // Date-based search
//         if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) {
//             try {
//                 $searchDate = Carbon::createFromFormat('d-m-Y', $search);
//                 // Example: Search by tgl_jatuh_tempo in pajak
//                 $query->whereHas('pajak', function ($q) use ($searchDate) {
//                     $q->whereDate('tgl_jatuh_tempo', $searchDate);
//                 });
//                 return;
//             } catch (\Exception $e) {
//                 Log::error("Invalid date format: " . $e->getMessage());
//             }
//         }

//         // Status-based filtering
//         $filters = [
//             'tersedia' => ['status_ketersediaan', 'tersedia'],
//             'tidak tersedia' => ['status_ketersediaan', 'tidak tersedia'],
//             'jatuh tempo pajak' => function ($q) {
//                 $q->whereHas('pajak', function ($query) {
//                     $query->whereRaw("CURDATE() >= DATE_ADD(tgl_jatuh_tempo, INTERVAL 1 YEAR)");
//                 });
//             },
//             'sudah dibayar pajak' => function ($q) {
//                 $q->whereHas('pajak', function ($query) {
//                     $query->whereRaw("DATE_ADD(tgl_jatuh_tempo, INTERVAL 11 MONTH) > CURDATE()");
//                 });
//             },
//             'belum ada data pajak' => function ($q) {
//                 $q->whereDoesntHave('pajak');
//             },
//             'jatuh tempo asuransi' => function ($q) {
//                 $q->whereHas('asuransi', function ($query) {
//                     $query->whereDate('tgl_perlindungan_akhir', '<', Carbon::now());
//                 });
//             },
//             'sudah dibayar asuransi' => function ($q) {
//                 $q->whereHas('asuransi', function ($query) {
//                     $query->whereDate('tgl_perlindungan_akhir', '>', Carbon::now()->addMonth());
//                 });
//             },
//             'belum ada data asuransi' => function ($q) {
//                 $q->whereDoesntHave('asuransi');
//             }
//         ];

//         foreach ($filters as $key => $value) {
//             if (stripos($search, $key) !== false) {
//                 if (is_array($value)) {
//                     $query->where($value[0], '=', $value[1]);
//                 } else {
//                     $query->where($value);
//                 }
//                 return;
//             }
//         }

//         // General search
//         $query->where(function ($q) use ($search) {
//             $q->orWhereRaw("LOWER(merk) LIKE ?", ["%$search%"])
//                 ->orWhereRaw("LOWER(tipe) LIKE ?", ["%$search%"])
//                 ->orWhereRaw("LOWER(plat_nomor) LIKE ?", ["%$search%"]);
//         });
//     }
    
//     public function detail($id)
//     {
//         $kendaraan = Kendaraan::with([
//             'asuransi', 
//             'bbm'=> function($query) {
//                 $query->orderBy('tgl_isi', 'desc');
//             }, 
//             'cekFisik' => function($query) {
//                 $query->orderBy('tgl_cek_fisik', 'desc');
//             }, 
//             'pajak', 
//             'peminjaman', 
//             'servisInsidental', 
//             'servisRutin'
//         ])->findOrFail($id);

//         $cekFisikTerbaru = $kendaraan->cekFisik->first();
//         $bbm = $kendaraan->bbm->first();

//         // Get latest tax data
//         $latestPajak = DB::table('pajak')
//             ->where('id_kendaraan', $id)
//             ->orderBy('tahun', 'desc')
//             ->first();

//         // Get latest insurance data
//         $latestAsuransi = DB::table('asuransi')
//             ->where('id_kendaraan', $id)
//             ->orderBy('tahun', 'desc')
//             ->first();
//         // Get latest servis rutin
//         $latestServisRutin = DB::table('servis_rutin')
//             ->where('id_kendaraan', $id)
//             ->orderBy('tgl_servis_real', 'desc')
//             ->first();

//         $statusServisRutin = $this->calculateStatusServisRutin($latestServisRutin);

//         $statusPajak = $this->calculateStatusPajak($latestPajak);
//         $statusAsuransi = $this->calculateStatusAsuransi($latestAsuransi);

//         return view('pengguna.detailDaftarKendaraan', compact(
//             'kendaraan', 
//             'cekFisikTerbaru', 
//             'bbm',
//             'statusPajak',
//             'statusAsuransi',
//             'statusServisRutin'
//         ));
//     }

## CURRENTLY
// public function daftarKendaraan(Request $request)
// {
//     $search = $request->input('search');

//     // Query dasar: kendaraan dengan aset 'guna' dan eager load relasi yang diperlukan
//     $kendaraanQuery = Kendaraan::with([
//         'pajak' => function ($query) {
//             // Ambil data pajak terbaru (berdasarkan tgl_bayar)
//             $query->latest('tgl_bayar');
//         },
//         'asuransi',
//         'servisRutin'
//     ])->where('aset', 'guna');

//     Log::info("Initial search parameters:", ['search' => $search]);

//     // Terapkan filtering (baik berdasarkan status maupun pencarian umum)
//     $this->applyFilters($kendaraanQuery, $search, $request);

//     // Ambil hasil dengan paginasi dan lampirkan parameter search
//     $kendaraan = $kendaraanQuery->paginate(10)->appends(['search' => $search]);

//     return view('pengguna.daftarKendaraan', compact('kendaraan', 'search'));
// }

// private function applyFilters($query, $search, Request $request)
// {
//     if (empty($search)) {
//         return;
//     }

//     $statusSearch = $request->input('statusSearch', false);

//     $search = strtolower(trim($search));
//     $lowerSearch = strtolower($search);
//     $searchDate = null;

//     // Cek status ketersediaan dalam pencarian
//     if (stripos($lowerSearch, 'tidak tersedia') !== false) {
//         $query->where('status_ketersediaan', '=', 'tidak tersedia');
//         $search = trim(str_ireplace('tidak tersedia', '', $search));
//         $statusSearch = true;
//     } elseif (stripos($lowerSearch, 'tersedia') !== false) {
//         $query->where('status_ketersediaan', '=', 'tersedia');
//         $search = trim(str_ireplace('tersedia', '', $search));
//         $statusSearch = true;
//     }
//     if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) {
//         try {
//             $searchDate = Carbon::createFromFormat('d-m-Y', $search);
//             $query->whereHas('pajak', function ($q) use ($searchDate) {
//                 $q->whereDate('tgl_jatuh_tempo', $searchDate);
//             });
//             return;
//         } catch (\Exception $e) {
//             Log::error("Invalid date format: " . $e->getMessage());
//         }
//     }

//     // Filter status berdasarkan kata kunci dalam search
//     $filters = [
//         // 'tersedia' => ['status_ketersediaan', 'tersedia'],
//         // 'tidak tersedia' => ['status_ketersediaan', 'tidak tersedia'],
//         'jatuh tempo pajak', 'pajak jatuh tempo', 'jatuh tempo' => function ($q) {
//             // Status "JATUH TEMPO" untuk pajak: hari ini sudah mencapai atau melebihi (tgl_jatuh_tempo + 1 tahun)
//             $q->whereRaw("
//             CURDATE() >= DATE_ADD(
//                 (SELECT MAX(tgl_jatuh_tempo) FROM pajak 
//                 WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 1 YEAR
//             )
//         ");

//         },
//         'mendekati jatuh tempo pajak', 'mendekati jatuh tempo', 'pajak mendekati jatuh tempo' => function ($q) {
//             $q->whereRaw("
//                 CURDATE() BETWEEN DATE_ADD(
//                     (SELECT MAX(tgl_jatuh_tempo) FROM pajak 
//                     WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
//                     INTERVAL 11 MONTH
//                 ) 
//                 AND DATE_ADD(
//                     (SELECT MAX(tgl_jatuh_tempo) FROM pajak 
//                     WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
//                     INTERVAL 1 YEAR
//                 ) - INTERVAL 1 DAY
//             ");
//         },

//         'sudah dibayar pajak', 'sudah dibayar', 'pajak sudah dibayar' => function ($q) {
//             // Status "SUDAH DIBAYAR" untuk pajak: belum mencapai (tgl_jatuh_tempo + 11 bulan)
//             $q->whereRaw("
//             CURDATE() < DATE_ADD(
//                 (SELECT MAX(tgl_jatuh_tempo) FROM pajak 
//                  WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 11 MONTH
//             )
//         ");
//         },
//         'belum ada data pajak', 'belum ada data', 'pajak belum ada data' => function ($q) {
//             $q->whereDoesntHave('pajak');
//         },
//         'jatuh tempo asuransi', 'jatuh tempo', 'asuransi jatuh tempo' => function ($q) {
//             $q->whereRaw("
//             CURDATE() >= DATE_ADD(
//                 (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi
//                  WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 0 DAY
//             )
//         ");
//         },
//         'mendekati jatuh tempo asuransi', 'mendekati jatuh tempo', 'asuransi mendekati jatuh tempo' => function ($q) {
//             $q->whereRaw("
//                 CURDATE() BETWEEN DATE_ADD(
//                     (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi 
//                     WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan),
//                     INTERVAL -1 MONTH
//                 ) 
//                 AND (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi 
//                     WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan)
//             ");
//         },

//         'sudah dibayar asuransi', 'asuransi sudah dibayar', 'sudah dibayar'  => function ($q) {
//             $q->whereRaw("
//             CURDATE() < DATE_ADD(
//                 (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi
//                  WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 1 MONTH
//             )
//         ");
//         },
//         'belum ada data asuransi', 'belum ada data', 'asuransi belum ada data' => function ($q) {
//             $q->whereDoesntHave('asuransi');
//         },
//         // Filter servis (tambahan)
//         'jatuh tempo servis', 'jatuh tempo', 'servis jatuh tempo' => function ($q) {
//             $q->whereRaw("
//             CURDATE() >= 
//                 (SELECT MAX(tgl_servis_selanjutnya) FROM servis_rutin
//                  WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan)
//         ");
//         },
//         'mendekati jatuh tempo servis', 'mendekati jatuh tempo', 'servis mendekati jatuh tempo' => function ($q) {
//             $q->whereRaw("
//             CURDATE() BETWEEN 
//                 DATE_SUB(
//                     (SELECT MAX(tgl_servis_selanjutnya) FROM servis_rutin 
//                      WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan),
//                     INTERVAL 1 MONTH
//                 )
//                 AND (SELECT MAX(tgl_servis_selanjutnya) FROM servis_rutin 
//                      WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan)
//         ");
//         },
//         'sudah servis' => function ($q) {
//             $q->whereRaw("
//             CURDATE() < DATE_SUB(
//                 (SELECT MAX(tgl_servis_selanjutnya) FROM servis_rutin 
//                  WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 1 MONTH
//             )
//         ");
//         },
//         'belum ada data servis', 'belum ada data' => function ($q) {
//             $q->whereDoesntHave('servisRutin');
//         }
//     ];
//     // Terapkan filter jika ditemukan dalam pencarian
//     foreach ($filters as $key => $filter) {
//         if (stripos($lowerSearch, $key) !== false) {
//             $query->where($filter);
//             $search = trim(str_ireplace($key, '', $search));
//         }
//     }
   
//     if ($searchDate) {
//         // Handle pencarian berdasarkan tanggal
//         $matchingKendaraanIds = [];
//         $kendaraanIds = Kendaraan::pluck('id_kendaraan');
    
//         if (!empty($matchingKendaraanIds)) {
//             $query->whereIn('id_kendaraan', $matchingKendaraanIds);
//         } else {
//             $query->whereNull('id_kendaraan');
//         }
//     } elseif (!$statusSearch && !empty($search)) {
//         // Pencarian umum berdasarkan atribut kendaraan
//         $query->where(function ($query) use ($search) {
//             $searchLower = strtolower($search);
            
//             $query->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.merk)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.warna)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.plat_nomor)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.aset)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.warna)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.aset)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', CAST(kendaraan.kapasitas AS CHAR))) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', CAST(kendaraan.kapasitas AS CHAR))) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.jenis)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.jenis)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.no_mesin)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.no_rangka)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.no_rangka)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.no_mesin)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.bahan_bakar)) LIKE ?", ["%$searchLower%"])
//                 ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.bahan_bakar)) LIKE ?", ["%$searchLower%"]);
    
//             // Pencarian berdasarkan tahun pembelian
//             if (preg_match('/(\d{4})/', $search, $yearMatches)) {
//                 $year = $yearMatches[1];
//                 $query->orWhereRaw("YEAR(kendaraan.tgl_pembelian) = ?", [$year]);
//             }
//             // Jika pengguna hanya mencari "Rusak" atau "Baik"
//         if (in_array($searchLower, ['rusak', 'baik'])) {
//             $query->orWhereHas('cekFisik', function ($q) use ($searchLower) {
//                 $q->whereRaw("LOWER(mesin) = ?", [$searchLower])
//                   ->orWhereRaw("LOWER(accu) = ?", [$searchLower])
//                   ->orWhereRaw("LOWER(air_radiator) = ?", [$searchLower])
//                   ->orWhereRaw("LOWER(air_wiper) = ?", [$searchLower])
//                   ->orWhereRaw("LOWER(body) = ?", [$searchLower])
//                   ->orWhereRaw("LOWER(ban) = ?", [$searchLower])
//                   ->orWhereRaw("LOWER(pengharum) = ?", [$searchLower]);
//             });
//         }
    
//             // Pencarian berdasarkan angka dalam input (misalnya kapasitas)
//             if (preg_match('/(\d+)/', $search, $numMatches)) {
//                 $num = $numMatches[1];
//                 $query->orWhereRaw("CAST(kendaraan.kapasitas AS CHAR) LIKE ?", ["%$num%"]);
//             }
    
//             // Pemecahan kata kunci dan pencarian per kata
//             $searchTerms = array_filter(explode(' ', $searchLower));
            
//             if (!empty($searchTerms)) {
//                 $query->orWhere(function ($andQuery) use ($searchTerms) {
//                     foreach ($searchTerms as $term) {
//                         $cleanTerm = str_replace([',', '.'], '', $term);
    
//                         $andQuery->where(function ($termQuery) use ($term, $cleanTerm) {
//                             $termQuery->orWhereRaw("LOWER(kendaraan.plat_nomor) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.merk) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.tipe) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.warna) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.jenis) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.aset) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.bahan_bakar) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.no_mesin) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("LOWER(kendaraan.no_rangka) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("CAST(kendaraan.kapasitas AS CHAR) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("CAST(YEAR(kendaraan.tgl_pembelian) AS CHAR) LIKE ?", ["%$term%"])
//                                 ->orWhereRaw("REPLACE(REPLACE(CAST(kendaraan.nilai_perolehan AS CHAR), '.', ''), ',', '') LIKE ?", ["%$cleanTerm%"])
//                                 ->orWhereRaw("REPLACE(REPLACE(CAST(kendaraan.nilai_buku AS CHAR), '.', ''), ',', '') LIKE ?", ["%$cleanTerm%"]);
//                         });
//                     }
//                 });
//             }
//         });
//     }
    
// }
public function daftarKendaraan(Request $request)
{
    $search = $request->input('search');

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

    Log::info("Initial search parameters:", ['search' => $search]);

    // Terapkan filtering (baik berdasarkan status maupun pencarian umum)
    $this->applyFilters($kendaraanQuery, $search, $request);

    // Ambil hasil dengan paginasi dan lampirkan parameter search
    $kendaraan = $kendaraanQuery->paginate(10)->appends(['search' => $search]);

    return view('pengguna.daftarKendaraan', compact('kendaraan', 'search'));
}

private function applyFilters($query, $search, $request)
{
    // Pastikan search diisi
    if ($request->filled('search')) {
        $searchWords = explode(' ', $search); // Pisahkan menjadi array kata
        // $query->where('aset', 'guna');
        // Query pencarian berdasarkan kata kunci (merk, tipe kendaraan, dll.)

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
        $this->applyPajakFilters($query, $search);
        $this->applyAsuransiFilters($query, $search);
        $this->applyServisFilters($query, $search);
        // Log generated query dan bindings
        Log::info('Generated Query: ' . $query->toSql());
        Log::info('Bindings: ' . json_encode($query->getBindings()));
    }
}

private function applyPajakFilters($query, $search)
{
    $query->where('kendaraan.aset', 'guna');
    if (str_contains(strtolower($search), 'jatuh tempo pajak')) {
        $query->orWhereHas('pajak', function ($q) {
            // Menggunakan subquery untuk mendapatkan MAX(tgl_jatuh_tempo) dan bandingkan dengan CURDATE()
            $q->whereRaw("
                CURDATE() >= DATE_ADD(
                    (SELECT MAX(tgl_jatuh_tempo) 
                     FROM pajak 
                     WHERE pajak.id_kendaraan = kendaraan.id_kendaraan), 
                    INTERVAL 1 YEAR
                )
            ");
        });
    }

    if (str_contains(strtolower($search), 'mendekati jatuh tempo pajak')) {
        $query->orWhereHas('pajak', function ($q) {
            $q->whereRaw("
                CURDATE() BETWEEN DATE_ADD(
                    (SELECT MAX(tgl_jatuh_tempo) FROM pajak 
                    WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
                    INTERVAL 11 MONTH
                ) 
                AND DATE_ADD(
                    (SELECT MAX(tgl_jatuh_tempo) FROM pajak 
                    WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
                    INTERVAL 1 YEAR
                ) - INTERVAL 1 DAY

            ");
        });
    }

    if (str_contains(strtolower($search), 'sudah dibayar pajak')) {
        $query->orWhereHas('pajak', function ($q) {
            $q->whereRaw("
                CURDATE() < DATE_ADD(
                    (SELECT MAX(tgl_jatuh_tempo) 
                     FROM pajak 
                     WHERE pajak.id_kendaraan = kendaraan.id_kendaraan), 
                    INTERVAL 11 MONTH
                )
            ");
        });
    }

    if (str_contains(strtolower($search), 'belum ada data pajak')) {
        $query->orWhereDoesntHave('pajak');
    }
}



private function applyAsuransiFilters($query, $word)
{
    $query->where('kendaraan.aset', 'guna');
    // Jatuh tempo asuransi: Menggunakan MAX(tgl_perlindungan_akhir) untuk mengambil data terbaru
    if (str_contains(strtolower($word), 'jatuh tempo asuransi')) {
        $query->orWhereHas('asuransi', function ($q) {
            $q->whereRaw("
                CURDATE() >= DATE_ADD(
                (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi
                 WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan),
                INTERVAL 0 DAY
            )

            ");
        });
    }

    // Mendekati jatuh tempo asuransi (1 bulan sebelum tgl_perlindungan_akhir)
    if (str_contains(strtolower($word), 'mendekati jatuh tempo asuransi')) {
        $query->orWhereHas('asuransi', function ($q) {
            $q->whereRaw("
               CURDATE() BETWEEN DATE_ADD(
                    (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi 
                    WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan),
                    INTERVAL -1 MONTH
                ) 
                AND (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi 
                    WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan)

            ");
        });
    }

    // Sudah dibayar asuransi (masih dalam 1 bulan dari tgl_perlindungan_akhir)
    if (str_contains(strtolower($word), 'sudah dibayar asuransi')) {
        $query->orWhereHas('asuransi', function ($q) {
            $q->whereRaw("
                CURDATE() < DATE_ADD(
                (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi
                 WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan),
                INTERVAL 1 MONTH
            )

            ");
        });
    }

    // Belum ada data asuransi
    if (str_contains(strtolower($word), 'belum ada data asuransi')) {
        $query->orWhereDoesntHave('asuransi');
    }
}


private function applyServisFilters($query, $search)
{
    // Pastikan filter kendaraan dengan aset 'guna' diterapkan di awal
    $query->where('kendaraan.aset', 'guna'); // Pastikan filter aset diterapkan pertama

    // Jatuh tempo servis
    if (str_contains(strtolower($search), 'jatuh tempo servis')) {
        $query->orWhereHas('servisRutin', function ($q) {
            // Ambil data servis terbaru berdasarkan tgl_servis_selanjutnya
            $q->whereRaw("
                CURDATE() >= 
                (SELECT MAX(tgl_servis_selanjutnya) 
                 FROM servis_rutin 
                 WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan
                 )
            ");
        });
    }

    // Mendekati jatuh tempo servis (1 bulan sebelum tgl_servis_selanjutnya)
    if (str_contains(strtolower($search), 'mendekati jatuh tempo servis')) {
        $query->orWhereHas('servisRutin', function ($q) {
            $q->whereRaw("
                CURDATE() BETWEEN 
                DATE_SUB(
                    (SELECT MAX(tgl_servis_selanjutnya) 
                     FROM servis_rutin 
                     WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan),
                    INTERVAL 1 MONTH
                )
                AND (SELECT MAX(tgl_servis_selanjutnya) 
                     FROM servis_rutin 
                     WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan)
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
                     WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan),
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


// private function applyPajakFilters($query, $word)
// {
//     // Jatuh tempo pajak: Jika tanggal pajak melebihi tanggal sekarang (CURDATE())
//     if (str_contains(strtolower($word), 'jatuh tempo pajak') || str_contains(strtolower($word), 'pajak jatuh tempo') || str_contains(strtolower($word), 'jatuh tempo')) {
//         $query->orWhereRaw("
//             CURDATE() >= DATE_ADD(
//                 (SELECT MAX(tgl_jatuh_tempo) FROM pajak WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 1 YEAR
//             )
//         ");
//     }

//     // Mendekati jatuh tempo pajak: Jika tanggal saat ini berada dalam interval 11 bulan sampai 1 tahun
//     if (str_contains(strtolower($word), 'mendekati jatuh tempo pajak') || str_contains(strtolower($word), 'mendekati jatuh tempo') || str_contains(strtolower($word), 'pajak mendekati jatuh tempo')) {
//         $query->orWhereRaw("
//             CURDATE() BETWEEN DATE_ADD(
//                 (SELECT MAX(tgl_jatuh_tempo) FROM pajak WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 11 MONTH
//             ) 
//             AND DATE_ADD(
//                 (SELECT MAX(tgl_jatuh_tempo) FROM pajak WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 1 YEAR
//             ) - INTERVAL 1 DAY
//         ");
//     }

//     // Sudah dibayar pajak: Jika tanggal saat ini kurang dari 11 bulan dari tanggal jatuh tempo pajak
//     if (str_contains(strtolower($word), 'sudah dibayar pajak') || str_contains(strtolower($word), 'sudah dibayar') || str_contains(strtolower($word), 'pajak sudah dibayar')) {
//         $query->orWhereRaw("
//             CURDATE() < DATE_ADD(
//                 (SELECT MAX(tgl_jatuh_tempo) FROM pajak WHERE pajak.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 11 MONTH
//             )
//         ");
//     }

//     // Belum ada data pajak: Jika tidak ada data pajak
//     if (str_contains(strtolower($word), 'belum ada data pajak') || str_contains(strtolower($word), 'belum ada data') || str_contains(strtolower($word), 'pajak belum ada data')) {
//         $query->orWhereDoesntHave('pajak');
//     }
// }

// private function applyAsuransiFilters($query, $word)
// {
//     // Jatuh tempo asuransi: Jika perlindungan asuransi sudah berakhir (tgl_perlindungan_akhir)
//     if (str_contains(strtolower($word), 'jatuh tempo asuransi') || str_contains(strtolower($word), 'asuransi jatuh tempo') || str_contains(strtolower($word), 'jatuh tempo')) {
//         $query->orWhereRaw("
//             CURDATE() >= DATE_ADD(
//                 (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 0 DAY
//             )
//         ");
//     }

//     // Mendekati jatuh tempo asuransi: Jika tanggal saat ini berada dalam interval 1 bulan sebelum tgl_perlindungan_akhir
//     if (str_contains(strtolower($word), 'mendekati jatuh tempo asuransi') || str_contains(strtolower($word), 'mendekati jatuh tempo') || str_contains(strtolower($word), 'asuransi mendekati jatuh tempo')) {
//         $query->orWhereRaw("
//             CURDATE() BETWEEN DATE_ADD(
//                 (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL -1 MONTH
//             ) 
//             AND (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan)
//         ");
//     }

//     // Sudah dibayar asuransi: Jika perlindungan asuransi belum habis dalam 1 bulan
//     if (str_contains(strtolower($word), 'sudah dibayar asuransi') || str_contains(strtolower($word), 'asuransi sudah dibayar') || str_contains(strtolower($word), 'sudah dibayar')) {
//         $query->orWhereRaw("
//             CURDATE() < DATE_ADD(
//                 (SELECT MAX(tgl_perlindungan_akhir) FROM asuransi WHERE asuransi.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 1 MONTH
//             )
//         ");
//     }

//     // Belum ada data asuransi: Jika tidak ada data asuransi
//     if (str_contains(strtolower($word), 'belum ada data asuransi') || str_contains(strtolower($word), 'belum ada data') || str_contains(strtolower($word), 'asuransi belum ada data')) {
//         $query->orWhereDoesntHave('asuransi');
//     }
// }

// private function applyServisFilters($query, $word)
// {
//     // Jatuh tempo servis: Jika servis selanjutnya sudah lewat (tgl_servis_selanjutnya)
//     if (str_contains(strtolower($word), 'jatuh tempo servis') || str_contains(strtolower($word), 'servis jatuh tempo') || str_contains(strtolower($word), 'jatuh tempo')) {
//         $query->orWhereRaw("
//             CURDATE() >= 
//                 (SELECT MAX(tgl_servis_selanjutnya) FROM servis_rutin
//                  WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan)
//         ");
//     }

//     // Mendekati jatuh tempo servis: Jika servis mendekati jatuh tempo (1 bulan sebelum tgl_servis_selanjutnya)
//     if (str_contains(strtolower($word), 'mendekati jatuh tempo servis') || str_contains(strtolower($word), 'mendekati jatuh tempo') || str_contains(strtolower($word), 'servis mendekati jatuh tempo')) {
//         $query->orWhereRaw("
//             CURDATE() BETWEEN 
//                 DATE_SUB(
//                     (SELECT MAX(tgl_servis_selanjutnya) FROM servis_rutin WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan),
//                     INTERVAL 1 MONTH
//                 )
//                 AND (SELECT MAX(tgl_servis_selanjutnya) FROM servis_rutin WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan)
//         ");
//     }

//     // Sudah servis: Jika servis dilakukan kurang dari 1 bulan yang lalu
//     if (str_contains(strtolower($word), 'sudah servis')) {
//         $query->orWhereRaw("
//             CURDATE() < DATE_SUB(
//                 (SELECT MAX(tgl_servis_selanjutnya) FROM servis_rutin 
//                  WHERE servis_rutin.id_kendaraan = kendaraan.id_kendaraan),
//                 INTERVAL 1 MONTH
//             )
//         ");
//     }

//     // Belum ada data servis: Jika tidak ada data servis
//     if (str_contains(strtolower($word), 'belum ada data servis') || str_contains(strtolower($word), 'belum ada data')) {
//         $query->orWhereDoesntHave('servisRutin');
//     }
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
    
