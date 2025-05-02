<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

class PeminjamanPenggunaController extends Controller
{
    // public function peminjamanPage(Request $request)
    // {
    //     $userId = Auth::id(); // Ambil ID user yang sedang login
    //     $daftarPeminjaman = Peminjaman::with('kendaraan') // Ambil data kendaraan juga
    //         ->where('user_id', $userId)
    //         ->orderBy('created_at', 'desc') // Urutkan dari terbaru
    //         ->paginate(2);
         
        
    //     $search = $request->search;
    //     $query = Peminjaman::with(['user', 'kendaraan'])->orderBy('tgl_mulai', 'desc');

    //     // Search functionality
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->whereHas('user', function ($qUser) use ($search) {
    //                 $qUser->where('name', 'like', "%$search%");
    //             })
    //             ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
    //                 $qKendaraan->where('merk', 'like', "%$search%")
    //                         ->orWhere('tipe', 'like', "%$search%")
    //                         ->orWhere('plat_nomor', 'like', "%$search%");
    //             })
    //             ->orWhere('tujuan', 'like', "%$search%")
    //             ->orWhere('status_pinjam', 'like', "%$search%");
    //         });
    //     }    
    //     return view('pengguna.peminjaman', compact('daftarPeminjaman'));
    // }
    // public function peminjamanPage(Request $request)
    // {
    //     $userId = Auth::id(); // Ambil ID user yang sedang login
    //     $search = $request->search;
    //     // Buat query dasar dengan filter user
    //     $query = Peminjaman::with(['user', 'kendaraan'])
    //                 ->where('user_id', $userId)
    //                 ->orderBy('created_at', 'desc');

    //     // Jika ada input pencarian, tambahkan filter pencarian
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->whereHas('user', function ($qUser) use ($search) {
    //                     $qUser->where('name', 'like', "%$search%");
    //                 })
    //                 ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
    //                     $qKendaraan->where('merk', 'like', "%$search%")
    //                             ->orWhere('tipe', 'like', "%$search%")
    //                             ->orWhere('plat_nomor', 'like', "%$search%");
    //                 })
    //                 ->orWhere('tujuan', 'like', "%$search%")
    //                 ->orWhere('status_pinjam', 'like', "%$search%");
    //         });
    //     }

    //     // Atur urutan dan paginate hasil query
    //     $daftarPeminjaman = $query->paginate(10);

    //     return view('pengguna.peminjaman', compact('daftarPeminjaman', 'search'));
    // }
//     public function peminjamanPage(Request $request)
// {
//     $userId = Auth::id(); // Ambil ID user yang sedang login
//     $search = $request->search;

//     // Coba konversi format tanggal DD-MM-YY ke YYYY-MM-DD
//     $searchDate = null;
//     if (preg_match('/^(\d{2})-(\d{2})-(\d{2,4})$/', $search, $matches)) {
//         $day = $matches[1];
//         $month = $matches[2];
//         $year = strlen($matches[3]) == 2 ? '20' . $matches[3] : $matches[3]; // Ubah YY ke YYYY
//         $searchDate = "$year-$month-$day"; // Format jadi YYYY-MM-DD
//     }

//     // Buat query dasar dengan filter user
//     $query = Peminjaman::with(['user', 'kendaraan'])
//                 ->where('user_id', $userId)
//                 ->orderBy('created_at', 'desc');

//     // Jika ada input pencarian, tambahkan filter pencarian
//     if ($request->filled('search')) {
//         $query->where(function ($q) use ($search, $searchDate) {
//             $q->whereHas('user', function ($qUser) use ($search) {
//                     $qUser->where('name', 'like', "%$search%");
//                 })
//                 ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
//                     $qKendaraan->where('merk', 'like', "%$search%")
//                             ->orWhere('tipe', 'like', "%$search%")
//                             ->orWhere('plat_nomor', 'like', "%$search%");
//                 })
//                 ->orWhere('tujuan', 'like', "%$search%")
//                 ->orWhere('status_pinjam', 'like', "%$search%");

//             // Search berdasarkan tanggal jika input cocok format DD-MM-YY
//             if ($searchDate) {
//                 $q->orWhereDate('tgl_mulai', $searchDate)
//                   ->orWhereDate('tgl_selesai', $searchDate)
//                   ->orWhereDate('tgl_kembali_real', $searchDate);
//             }

//             // Search berdasarkan jam
//             $q->orWhereTime('jam_mulai', $search)
//               ->orWhereTime('jam_selesai', $search)
//               ->orWhereTime('jam_kembali_real', $search);
//         });
//     }

//     // Atur urutan dan paginate hasil query
//     $daftarPeminjaman = $query->paginate(10);

//     return view('pengguna.peminjaman', compact('daftarPeminjaman', 'search'));
// }
// public function peminjamanPage(Request $request)
// {
//     $userId = Auth::id(); // Ambil ID user yang sedang login
//     $search = $request->search;

//     // Inisialisasi variabel pencarian tanggal
//     $searchDate = null;
//     $searchMonthYear = null; // Format YYYY-MM
//     $searchDayMonth = null;  // Format MM-DD (tanpa tahun)

//     // Coba konversi input jika cocok format DD-MM-YYYY
//     if (preg_match('/^(\d{2})-(\d{2})-(\d{2,4})$/', $search, $matches)) {
//         $day = $matches[1];
//         $month = $matches[2];
//         $year = strlen($matches[3]) == 2 ? '20' . $matches[3] : $matches[3]; // Ubah YY ke YYYY
//         $searchDate = "$year-$month-$day"; // Format YYYY-MM-DD
//     }
//     // Cek format MM-YYYY atau YYYY-MM
//     elseif (preg_match('/^(\d{2})-(\d{4})$/', $search, $matches) || preg_match('/^(\d{4})-(\d{2})$/', $search, $matches)) {
//         $year = strlen($matches[1]) == 4 ? $matches[1] : $matches[2]; // Ambil bagian yang 4 digit sebagai tahun
//         $month = strlen($matches[1]) == 2 ? $matches[1] : $matches[2]; // Ambil bagian yang 2 digit sebagai bulan
//         $searchMonthYear = "$year-$month"; // Format YYYY-MM
//     }
//     // Cek format DD-MM (tanpa tahun)
//     elseif (preg_match('/^(\d{2})-(\d{2})$/', $search, $matches)) {
//         $day = $matches[1];
//         $month = $matches[2];
//         $searchDayMonth = "$month-$day"; // Format MM-DD
//     }

//     // Buat query dasar dengan filter user
//     $query = Peminjaman::with(['user', 'kendaraan'])
//                 ->where('user_id', $userId)
//                 ->orderBy('created_at', 'desc');

//     // Jika ada input pencarian, tambahkan filter pencarian
//     if ($request->filled('search')) {
//         $query->where(function ($q) use ($search, $searchDate, $searchMonthYear, $searchDayMonth) {
//             $q->whereHas('user', function ($qUser) use ($search) {
//                     $qUser->where('name', 'like', "%$search%");
//                 })
//                 ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
//                     $qKendaraan->where('merk', 'like', "%$search%")
//                             ->orWhere('tipe', 'like', "%$search%")
//                             ->orWhere('plat_nomor', 'like', "%$search%");
//                 })
//                 ->orWhere('tujuan', 'like', "%$search%")
//                 ->orWhere('status_pinjam', 'like', "%$search%");

//             // Search berdasarkan tanggal lengkap (YYYY-MM-DD)
//             if ($searchDate) {
//                 $q->orWhereDate('tgl_mulai', $searchDate)
//                   ->orWhereDate('tgl_selesai', $searchDate)
//                   ->orWhereDate('tgl_kembali_real', $searchDate);
//             }

//             // Search berdasarkan bulan dan tahun (YYYY-MM)
//             if ($searchMonthYear) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%Y-%m') = ?", [$searchMonthYear])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%Y-%m') = ?", [$searchMonthYear])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%Y-%m') = ?", [$searchMonthYear]);
//             }

//             // Search berdasarkan hari dan bulan (MM-DD) tanpa melihat tahun
//             if ($searchDayMonth) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%m-%d') = ?", [$searchDayMonth])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%m-%d') = ?", [$searchDayMonth])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%m-%d') = ?", [$searchDayMonth]);
//             }

//             // Search berdasarkan jam
//             $q->orWhereTime('jam_mulai', $search)
//               ->orWhereTime('jam_selesai', $search)
//               ->orWhereTime('jam_kembali_real', $search);
//         });
//     }

//     // Atur urutan dan paginate hasil query
//     $daftarPeminjaman = $query->paginate(10);

//     return view('pengguna.peminjaman', compact('daftarPeminjaman', 'search'));
// }
// public function peminjamanPage(Request $request)
// {
//     $userId = Auth::id(); // Ambil ID user yang sedang login
//     $search = $request->search;

//     // Inisialisasi variabel pencarian tanggal
//     $searchDate = null;         // Format YYYY-MM-DD (tanggal lengkap)
//     $searchMonthYear = null;    // Format YYYY-MM (bulan dan tahun)
//     $searchDayMonth = null;     // Format MM-DD (hari & bulan, tanpa tahun)
//     $searchDayOnly = null;      // Format DD (hari saja)
//     $searchMonthOnly = null;    // Format MM (bulan saja)
//     $searchYearOnly = null;     // Format YYYY (tahun saja)
    


//     // Parsing input berdasarkan format:
//     // Cek format DD-MM-YYYY
//     if (preg_match('/^(\d{2})-(\d{2})-(\d{2,4})$/', $search, $matches)) {
//         $day = $matches[1];
//         $month = $matches[2];
//         $year = (strlen($matches[3]) == 2) ? '20' . $matches[3] : $matches[3];
//         $searchDate = "$year-$month-$day"; // Format YYYY-MM-DD
//     }
//     // Cek format MM-YYYY atau YYYY-MM
//     elseif (preg_match('/^(\d{2})-(\d{4})$/', $search, $matches) || preg_match('/^(\d{4})-(\d{2})$/', $search, $matches)) {
//         if (strlen($matches[1]) == 4) {
//             $year = $matches[1];
//             $month = $matches[2];
//         } else {
//             $year = $matches[2];
//             $month = $matches[1];
//         }
//         $searchMonthYear = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT); // Format YYYY-MM
//     }
//     // Cek format DD-MM (tanpa tahun)
//     elseif (preg_match('/^(\d{2})-(\d{2})$/', $search, $matches)) {
//         $day = $matches[1];
//         $month = $matches[2];
//         $searchDayMonth = str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT); // Format MM-DD
//     }
//     // Cek jika input hanya 4 digit (tahun saja)
//     elseif (preg_match('/^\d{4}$/', $search, $matches)) {
//         $searchYearOnly = $search; // Format YYYY
//     }
//     // Cek jika input hanya 1-2 digit (hari saja atau bulan saja)
//     elseif (preg_match('/^\d{1,2}$/', $search, $matches)) {
//         $num = str_pad($matches[0], 2, '0', STR_PAD_LEFT);
//         $searchDayOnly = $num;    // misalnya "05" sebagai hari
//         $searchMonthOnly = $num;  // misalnya "05" sebagai bulan
//     }

//     // Buat query dasar dengan filter user
//     $query = Peminjaman::with(['user', 'kendaraan'])
//                 ->where('user_id', $userId)
//                 ->orderBy('created_at', 'desc');

//     // Jika ada input pencarian, tambahkan filter pencarian
//     if ($request->filled('search')) {
//         $query->where(function ($q) use ($search, $searchDate, $searchMonthYear, $searchDayMonth, $searchDayOnly, $searchMonthOnly, $searchYearOnly) {
//             // Pencarian berdasarkan relasi user dan kendaraan
//             $q->whereHas('user', function ($qUser) use ($search) {
//                 $qUser->where('name', 'like', "%$search%");
//             })
//                 ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
//                     $qKendaraan->where('merk', 'like', "%$search%")
//                               ->orWhere('tipe', 'like', "%$search%")
//                               ->orWhere('plat_nomor', 'like', "%$search%");
//                 })
//                 ->orWhere('tujuan', 'like', "%$search%")
//                 ->orWhere('status_pinjam', 'like', "%$search%")
//                 ->orWhere('kondisi_kendaraan', 'like', "%$search%")
//                 ->orWhere('detail_insiden', 'like', "%$search%");
              
//                 $q->orWhereRaw("CONCAT_WS(' ', tujuan, status_pinjam, kondisi_kendaraan, detail_insiden, 
//                 (SELECT name FROM users WHERE users.id = peminjaman.user_id), 
//                 (SELECT merk FROM kendaraan WHERE kendaraan.id_kendaraan = peminjaman.id_kendaraan), 
//                 (SELECT tipe FROM kendaraan WHERE kendaraan.id_kendaraan = peminjaman.id_kendaraan), 
//                 (SELECT plat_nomor FROM kendaraan WHERE kendaraan.id_kendaraan = peminjaman.id_kendaraan), 
//                 DATE_FORMAT(tgl_mulai, '%Y-%m-%d'), DATE_FORMAT(tgl_selesai, '%Y-%m-%d'), 
//                 DATE_FORMAT(tgl_kembali_real, '%Y-%m-%d') ) LIKE ?", ["%$search%"]);


//             // Pencarian berdasarkan tanggal lengkap (YYYY-MM-DD)
//             if ($searchDate) {
//                 $q->orWhereDate('tgl_mulai', $searchDate)
//                   ->orWhereDate('tgl_selesai', $searchDate)
//                   ->orWhereDate('tgl_kembali_real', $searchDate);
//             }
//             // Pencarian berdasarkan bulan dan tahun (YYYY-MM)
//             if ($searchMonthYear) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%Y-%m') = ?", [$searchMonthYear])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%Y-%m') = ?", [$searchMonthYear])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%Y-%m') = ?", [$searchMonthYear]);
//             }
//             // Pencarian berdasarkan hari dan bulan (MM-DD) tanpa tahun
//             if ($searchDayMonth) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%m-%d') = ?", [$searchDayMonth])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%m-%d') = ?", [$searchDayMonth])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%m-%d') = ?", [$searchDayMonth]);
//             }
//             // Pencarian berdasarkan hari saja (DD)
//             if ($searchDayOnly) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%d') = ?", [$searchDayOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%d') = ?", [$searchDayOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%d') = ?", [$searchDayOnly]);
//             }
//             // Pencarian berdasarkan bulan saja (MM)
//             if ($searchMonthOnly) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%m') = ?", [$searchMonthOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%m') = ?", [$searchMonthOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%m') = ?", [$searchMonthOnly]);
//             }
//             // Pencarian berdasarkan tahun saja (YYYY)
//             if ($searchYearOnly) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%Y') = ?", [$searchYearOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%Y') = ?", [$searchYearOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%Y') = ?", [$searchYearOnly]);
//             }
            
//             // Pencarian berdasarkan waktu (jam)
//             $searchTime = str_replace('.', ':', $search);
//             $q->orWhereTime('jam_mulai', $searchTime)
//             ->orWhereTime('jam_selesai', $searchTime)
//             ->orWhereTime('jam_kembali_real', $searchTime);
          
            
//         });
//     }

//     // Atur urutan dan paginate hasil query
//     $daftarPeminjaman = $query->paginate(10);

//     return view('pengguna.peminjaman', compact('daftarPeminjaman', 'search'));
// }

##currently
// public function peminjamanPage(Request $request)
// {
//     $userId = Auth::id(); // Get ID of currently logged in user
//     $search = $request->search;

//     // Initialize date search variables
//     $searchDate = null;         // Format YYYY-MM-DD (complete date)
//     $searchMonthYear = null;    // Format YYYY-MM (month and year)
//     $searchDayMonth = null;     // Format MM-DD (day & month, without year)
//     $searchDayOnly = null;      // Format DD (day only)
//     $searchMonthOnly = null;    // Format MM (month only)
//     $searchYearOnly = null;     // Format YYYY (year only)
    
//     // Parse input based on format:
//     // Check DD-MM-YYYY format
//     if (preg_match('/^(\d{2})-(\d{2})-(\d{2,4})$/', $search, $matches)) {
//         $day = $matches[1];
//         $month = $matches[2];
//         $year = (strlen($matches[3]) == 2) ? '20' . $matches[3] : $matches[3];
//         $searchDate = "$year-$month-$day"; // Format YYYY-MM-DD
//     }
//     // Check MM-YYYY or YYYY-MM format
//     elseif (preg_match('/^(\d{2})-(\d{4})$/', $search, $matches) || preg_match('/^(\d{4})-(\d{2})$/', $search, $matches)) {
//         if (strlen($matches[1]) == 4) {
//             $year = $matches[1];
//             $month = $matches[2];
//         } else {
//             $year = $matches[2];
//             $month = $matches[1];
//         }
//         $searchMonthYear = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT); // Format YYYY-MM
//     }
//     // Check DD-MM format (without year)
//     elseif (preg_match('/^(\d{2})-(\d{2})$/', $search, $matches)) {
//         $day = $matches[1];
//         $month = $matches[2];
//         $searchDayMonth = str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT); // Format MM-DD
//     }
//     // Check if input is only 4 digits (year only)
//     elseif (preg_match('/^\d{4}$/', $search, $matches)) {
//         $searchYearOnly = $search; // Format YYYY
//     }
//     // Check if input is only 1-2 digits (day only or month only)
//     elseif (preg_match('/^\d{1,2}$/', $search, $matches)) {
//         $num = str_pad($matches[0], 2, '0', STR_PAD_LEFT);
//         $searchDayOnly = $num;    // e.g., "05" as day
//         $searchMonthOnly = $num;  // e.g., "05" as month
//     }

//     // Create base query with user filter
//     $query = Peminjaman::with(['user', 'kendaraan'])
//                 ->where('user_id', $userId)
//                 ->orderBy('created_at', 'desc');

//     // If there's a search input, add search filter
//     if ($request->filled('search')) {
//         $query->where(function ($q) use ($search, $searchDate, $searchMonthYear, $searchDayMonth, $searchDayOnly, $searchMonthOnly, $searchYearOnly) {
//             // Search based on user and vehicle relations
//             $q->whereHas('user', function ($qUser) use ($search) {
//                 $qUser->where('name', 'like', "%$search%");
//             })
//             ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
//                 $qKendaraan->where('merk', 'like', "%$search%")
//                           ->orWhere('tipe', 'like', "%$search%")
//                           ->orWhere('plat_nomor', 'like', "%$search%");
//             })
//             ->orWhere('tujuan', 'like', "%$search%")
//             ->orWhere('status_pinjam', 'like', "%$search%")
//             ->orWhere('kondisi_kendaraan', 'like', "%$search%")
//             ->orWhere('detail_insiden', 'like', "%$search%");

//             // Handle combination searches with multiple terms
//             // First, split search terms if they contain spaces
//             $searchTerms = explode(' ', $search);
//             if (count($searchTerms) > 1) {
//                 foreach ($searchTerms as $key => $term) {
//                     if (empty($term)) continue;
                    
//                     // Skip the first term for first iteration
//                     if ($key > 0) {
//                         $previousTerm = $searchTerms[$key-1];
                        
//                         // Handle combination searches (previous_term current_term)
//                         $q->orWhereHas('kendaraan', function ($qk) use ($previousTerm, $term) {
//                             // merk + anything combinations
//                             $qk->where(function($qmerk) use ($previousTerm, $term) {
//                                 $qmerk->where('merk', 'like', "%$previousTerm%")
//                                     ->where(function($qsub) use ($term) {
//                                         $qsub->where('tipe', 'like', "%$term%")
//                                             ->orWhere('plat_nomor', 'like', "%$term%");
//                                     });
//                             });
                            
//                             // tipe + anything combinations
//                             $qk->orWhere(function($qtipe) use ($previousTerm, $term) {
//                                 $qtipe->where('tipe', 'like', "%$previousTerm%")
//                                     ->where(function($qsub) use ($term) {
//                                         $qsub->where('merk', 'like', "%$term%")
//                                             ->orWhere('plat_nomor', 'like', "%$term%");
//                                     });
//                             });
                            
//                             // plat + anything combinations
//                             $qk->orWhere(function($qplat) use ($previousTerm, $term) {
//                                 $qplat->where('plat_nomor', 'like', "%$previousTerm%")
//                                     ->where(function($qsub) use ($term) {
//                                         $qsub->where('merk', 'like', "%$term%")
//                                             ->orWhere('tipe', 'like', "%$term%");
//                                     });
//                             });
//                         });
                        
//                         // Handle dates and times combinations
//                         $dateFields = ['tgl_mulai', 'tgl_selesai', 'tgl_kembali_real'];
//                         $timeFields = ['jam_mulai', 'jam_selesai', 'jam_kembali_real'];
//                         $otherFields = ['tujuan', 'status_pinjam', 'kondisi_kendaraan', 'detail_insiden'];
                        
//                         // Vehicle attributes + date fields
//                         $q->orWhere(function($qvd) use ($previousTerm, $term, $dateFields) {
//                             // Check if first term is vehicle attribute and second is a date/time
//                             $qvd->whereHas('kendaraan', function($qk) use ($previousTerm) {
//                                 $qk->where('merk', 'like', "%$previousTerm%")
//                                   ->orWhere('tipe', 'like', "%$previousTerm%")
//                                   ->orWhere('plat_nomor', 'like', "%$previousTerm%");
//                             })->where(function($qdf) use ($term, $dateFields) {
//                                 foreach ($dateFields as $field) {
//                                     $qdf->orWhereRaw("DATE_FORMAT($field, '%d-%m-%Y') LIKE ?", ["%$term%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%Y-%m-%d') LIKE ?", ["%$term%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%d-%m') LIKE ?", ["%$term%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%m-%Y') LIKE ?", ["%$term%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%Y-%m') LIKE ?", ["%$term%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%d') LIKE ?", ["%$term%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%m') LIKE ?", ["%$term%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%Y') LIKE ?", ["%$term%"]);
//                                 }
//                             });
                            
//                             // Check if first term is date and second is vehicle attribute
//                             $qvd->orWhere(function($qd) use ($previousTerm, $term, $dateFields) {
//                                 foreach ($dateFields as $field) {
//                                     $qd->orWhereRaw("DATE_FORMAT($field, '%d-%m-%Y') LIKE ?", ["%$previousTerm%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%Y-%m-%d') LIKE ?", ["%$previousTerm%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%d-%m') LIKE ?", ["%$previousTerm%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%m-%Y') LIKE ?", ["%$previousTerm%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%Y-%m') LIKE ?", ["%$previousTerm%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%d') LIKE ?", ["%$previousTerm%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%m') LIKE ?", ["%$previousTerm%"])
//                                         ->orWhereRaw("DATE_FORMAT($field, '%Y') LIKE ?", ["%$previousTerm%"]);
//                                 }
//                             })->whereHas('kendaraan', function($qk) use ($term) {
//                                 $qk->where('merk', 'like', "%$term%")
//                                   ->orWhere('tipe', 'like', "%$term%")
//                                   ->orWhere('plat_nomor', 'like', "%$term%");
//                             });
//                         });
                        
//                         // Vehicle attributes + time fields
//                         $q->orWhere(function($qvt) use ($previousTerm, $term, $timeFields) {
//                             // Check if first term is vehicle attribute and second is time
//                             $qvt->whereHas('kendaraan', function($qk) use ($previousTerm) {
//                                 $qk->where('merk', 'like', "%$previousTerm%")
//                                   ->orWhere('tipe', 'like', "%$previousTerm%")
//                                   ->orWhere('plat_nomor', 'like', "%$previousTerm%");
//                             })->where(function($qtf) use ($term, $timeFields) {
//                                 $termTime = str_replace('.', ':', $term);
//                                 foreach ($timeFields as $field) {
//                                     $qtf->orWhereRaw("TIME_FORMAT($field, '%H:%i') LIKE ?", ["%$termTime%"]);
//                                 }
//                             });
                            
//                             // Check if first term is time and second is vehicle attribute
//                             $qvt->orWhere(function($qt) use ($previousTerm, $term, $timeFields) {
//                                 $previousTermTime = str_replace('.', ':', $previousTerm);
//                                 foreach ($timeFields as $field) {
//                                     $qt->orWhereRaw("TIME_FORMAT($field, '%H:%i') LIKE ?", ["%$previousTermTime%"]);
//                                 }
//                             })->whereHas('kendaraan', function($qk) use ($term) {
//                                 $qk->where('merk', 'like', "%$term%")
//                                   ->orWhere('tipe', 'like', "%$term%")
//                                   ->orWhere('plat_nomor', 'like', "%$term%");
//                             });
//                         });
                        
//                         // Vehicle attributes + other fields
//                         $q->orWhere(function($qvo) use ($previousTerm, $term, $otherFields) {
//                             // Check if first term is vehicle attribute and second is other field
//                             $qvo->whereHas('kendaraan', function($qk) use ($previousTerm) {
//                                 $qk->where('merk', 'like', "%$previousTerm%")
//                                   ->orWhere('tipe', 'like', "%$previousTerm%")
//                                   ->orWhere('plat_nomor', 'like', "%$previousTerm%");
//                             })->where(function($qof) use ($term, $otherFields) {
//                                 foreach ($otherFields as $field) {
//                                     $qof->orWhere($field, 'like', "%$term%");
//                                 }
//                             });
                            
//                             // Check if first term is other field and second is vehicle attribute
//                             $qvo->orWhere(function($qo) use ($previousTerm, $term, $otherFields) {
//                                 foreach ($otherFields as $field) {
//                                     $qo->orWhere($field, 'like', "%$previousTerm%");
//                                 }
//                             })->whereHas('kendaraan', function($qk) use ($term) {
//                                 $qk->where('merk', 'like', "%$term%")
//                                   ->orWhere('tipe', 'like', "%$term%")
//                                   ->orWhere('plat_nomor', 'like', "%$term%");
//                             });
//                         });
                        
//                         // Date + time combinations
//                         $q->orWhere(function($qdt) use ($previousTerm, $term, $dateFields, $timeFields) {
//                             // First term date, second term time
//                             foreach ($dateFields as $dateField) {
//                                 foreach ($timeFields as $timeField) {
//                                     $qdt->orWhere(function($qsub) use ($previousTerm, $term, $dateField, $timeField) {
//                                         $termTime = str_replace('.', ':', $term);
//                                         $qsub->whereRaw("DATE_FORMAT($dateField, '%d-%m-%Y') LIKE ?", ["%$previousTerm%"])
//                                              ->whereRaw("TIME_FORMAT($timeField, '%H:%i') LIKE ?", ["%$termTime%"]);
//                                     });
//                                 }
//                             }
                            
//                             // First term time, second term date
//                             foreach ($timeFields as $timeField) {
//                                 foreach ($dateFields as $dateField) {
//                                     $qdt->orWhere(function($qsub) use ($previousTerm, $term, $dateField, $timeField) {
//                                         $previousTermTime = str_replace('.', ':', $previousTerm);
//                                         $qsub->whereRaw("TIME_FORMAT($timeField, '%H:%i') LIKE ?", ["%$previousTermTime%"])
//                                              ->whereRaw("DATE_FORMAT($dateField, '%d-%m-%Y') LIKE ?", ["%$term%"]);
//                                     });
//                                 }
//                             }
//                         });
                        
//                         // Date + other field combinations
//                         $q->orWhere(function($qdo) use ($previousTerm, $term, $dateFields, $otherFields) {
//                             // First term date, second term other field
//                             foreach ($dateFields as $dateField) {
//                                 foreach ($otherFields as $otherField) {
//                                     $qdo->orWhere(function($qsub) use ($previousTerm, $term, $dateField, $otherField) {
//                                         $qsub->whereRaw("DATE_FORMAT($dateField, '%d-%m-%Y') LIKE ?", ["%$previousTerm%"])
//                                              ->where($otherField, 'like', "%$term%");
//                                     });
//                                 }
//                             }
                            
//                             // First term other field, second term date
//                             foreach ($otherFields as $otherField) {
//                                 foreach ($dateFields as $dateField) {
//                                     $qdo->orWhere(function($qsub) use ($previousTerm, $term, $dateField, $otherField) {
//                                         $qsub->where($otherField, 'like', "%$previousTerm%")
//                                              ->whereRaw("DATE_FORMAT($dateField, '%d-%m-%Y') LIKE ?", ["%$term%"]);
//                                     });
//                                 }
//                             }
//                         });
                        
//                         // Time + other field combinations
//                         $q->orWhere(function($qto) use ($previousTerm, $term, $timeFields, $otherFields) {
//                             // First term time, second term other field
//                             foreach ($timeFields as $timeField) {
//                                 foreach ($otherFields as $otherField) {
//                                     $qto->orWhere(function($qsub) use ($previousTerm, $term, $timeField, $otherField) {
//                                         $previousTermTime = str_replace('.', ':', $previousTerm);
//                                         $qsub->whereRaw("TIME_FORMAT($timeField, '%H:%i') LIKE ?", ["%$previousTermTime%"])
//                                              ->where($otherField, 'like', "%$term%");
//                                     });
//                                 }
//                             }
                            
//                             // First term other field, second term time
//                             foreach ($otherFields as $otherField) {
//                                 foreach ($timeFields as $timeField) {
//                                     $qto->orWhere(function($qsub) use ($previousTerm, $term, $timeField, $otherField) {
//                                         $termTime = str_replace('.', ':', $term);
//                                         $qsub->where($otherField, 'like', "%$previousTerm%")
//                                              ->whereRaw("TIME_FORMAT($timeField, '%H:%i') LIKE ?", ["%$termTime%"]);
//                                     });
//                                 }
//                             }
//                         });
                        
//                         // Other field combinations with each other
//                         $q->orWhere(function($qoo) use ($previousTerm, $term, $otherFields) {
//                             foreach ($otherFields as $field1) {
//                                 foreach ($otherFields as $field2) {
//                                     if ($field1 != $field2) {
//                                         $qoo->orWhere(function($qsub) use ($previousTerm, $term, $field1, $field2) {
//                                             $qsub->where($field1, 'like', "%$previousTerm%")
//                                                  ->where($field2, 'like', "%$term%");
//                                         });
//                                     }
//                                 }
//                             }
//                         });
//                     }
//                 }
//             }
            
//             // Search for complete concatenated text (fallback for simpler cases)
//             $q->orWhereRaw("CONCAT_WS(' ', 
//                 tujuan, status_pinjam, kondisi_kendaraan, detail_insiden, 
//                 (SELECT name FROM users WHERE users.id = peminjaman.user_id), 
//                 (SELECT merk FROM kendaraan WHERE kendaraan.id_kendaraan = peminjaman.id_kendaraan), 
//                 (SELECT tipe FROM kendaraan WHERE kendaraan.id_kendaraan = peminjaman.id_kendaraan), 
//                 (SELECT plat_nomor FROM kendaraan WHERE kendaraan.id_kendaraan = peminjaman.id_kendaraan), 
//                 DATE_FORMAT(tgl_mulai, '%Y-%m-%d'), DATE_FORMAT(tgl_selesai, '%Y-%m-%d'), 
//                 DATE_FORMAT(tgl_kembali_real, '%Y-%m-%d'),
//                 TIME_FORMAT(jam_mulai, '%H:%i'), TIME_FORMAT(jam_selesai, '%H:%i'),
//                 TIME_FORMAT(jam_kembali_real, '%H:%i')
//             ) LIKE ?", ["%$search%"]);

//             // Search based on complete date (YYYY-MM-DD)
//             if ($searchDate) {
//                 $q->orWhereDate('tgl_mulai', $searchDate)
//                   ->orWhereDate('tgl_selesai', $searchDate)
//                   ->orWhereDate('tgl_kembali_real', $searchDate);
//             }
//             // Search based on month and year (YYYY-MM)
//             if ($searchMonthYear) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%Y-%m') = ?", [$searchMonthYear])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%Y-%m') = ?", [$searchMonthYear])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%Y-%m') = ?", [$searchMonthYear]);
//             }
//             // Search based on day and month (MM-DD) without year
//             if ($searchDayMonth) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%m-%d') = ?", [$searchDayMonth])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%m-%d') = ?", [$searchDayMonth])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%m-%d') = ?", [$searchDayMonth]);
//             }
//             // Search based on day only (DD)
//             if ($searchDayOnly) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%d') = ?", [$searchDayOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%d') = ?", [$searchDayOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%d') = ?", [$searchDayOnly]);
//             }
//             // Search based on month only (MM)
//             if ($searchMonthOnly) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%m') = ?", [$searchMonthOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%m') = ?", [$searchMonthOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%m') = ?", [$searchMonthOnly]);
//             }
//             // Search based on year only (YYYY)
//             if ($searchYearOnly) {
//                 $q->orWhereRaw("DATE_FORMAT(tgl_mulai, '%Y') = ?", [$searchYearOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_selesai, '%Y') = ?", [$searchYearOnly])
//                   ->orWhereRaw("DATE_FORMAT(tgl_kembali_real, '%Y') = ?", [$searchYearOnly]);
//             }
            
//             // Search based on time (hours)
//             $searchTime = str_replace('.', ':', $search);
//             $q->orWhereRaw("TIME_FORMAT(jam_mulai, '%H:%i') LIKE ?", ["%$searchTime%"])
//               ->orWhereRaw("TIME_FORMAT(jam_selesai, '%H:%i') LIKE ?", ["%$searchTime%"])
//               ->orWhereRaw("TIME_FORMAT(jam_kembali_real, '%H:%i') LIKE ?", ["%$searchTime%"]);
//         });
//     }

//     // Set order and paginate query results
//     $daftarPeminjaman = $query->paginate(10);

//     return view('pengguna.peminjaman', compact('daftarPeminjaman', 'search'));
// }
private function buildDateSearch($query, $column, $search)
{
    // Check if search might be a date in various formats
    // Format: d (day only - 1 to 31)
    if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])$/', $search)) {
        $day = (int) $search;
        $query->orWhereRaw("DAY($column) = ?", [$day]);
    }

    // Format: m (month only - 1 to 12)
    if (preg_match('/^(0?[1-9]|1[0-2])$/', $search)) {
        $month = (int) $search;
        $query->orWhereRaw("MONTH($column) = ?", [$month]);
    }

    // Format: Y (year only - 4 digits)
    if (preg_match('/^(20\d{2})$/', $search)) {
        $year = (int) $search;
        $query->orWhereRaw("YEAR($column) = ?", [$year]);
    }

    // Format: d-m (day-month)
    if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])[\-\/](0?[1-9]|1[0-2])$/', $search)) {
        $parts = preg_split('/[\-\/]/', $search);
        $day = (int) $parts[0];
        $month = (int) $parts[1];
        $query->orWhereRaw("DAY($column) = ? AND MONTH($column) = ?", [$day, $month]);
    }

    // Format: m-Y (month-year)
    if (preg_match('/^(0?[1-9]|1[0-2])[\-\/](20\d{2})$/', $search)) {
        $parts = preg_split('/[\-\/]/', $search);
        $month = (int) $parts[0];
        $year = (int) $parts[1];
        $query->orWhereRaw("MONTH($column) = ? AND YEAR($column) = ?", [$month, $year]);
    }

    // Format: d-m-Y (day-month-year)
    if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])[\-\/](0?[1-9]|1[0-2])[\-\/](20\d{2})$/', $search)) {
        $parts = preg_split('/[\-\/]/', $search);
        $day = (int) $parts[0];
        $month = (int) $parts[1];
        $year = (int) $parts[2];
        $query->orWhereRaw("DAY($column) = ? AND MONTH($column) = ? AND YEAR($column) = ?", [$day, $month, $year]);
    }

    // Also try the default LIKE search for backward compatibility
    $query->orWhere($column, 'like', "%$search%");
}

public function peminjamanPage(Request $request)
{
    $userId = Auth::id();
    $search = $request->input('search');
    $daftarPeminjaman = Peminjaman::with(['user', 'kendaraan'])
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc');

    // If search is filled, apply search filter
    if ($request->filled('search')) {
        $searchWords = explode(' ', $request->search); // split into array of words

        $daftarPeminjaman->where(function ($q) use ($searchWords) {
            foreach ($searchWords as $word) {
                $q->where(function ($q2) use ($word) {
                    // Search based on user and vehicle relations
                    $q2->whereHas('user', function ($qUser) use ($word) {
                            $qUser->where('name', 'like', "%$word%");
                        })
                        ->orWhereHas('kendaraan', function ($qKendaraan) use ($word) {
                            $qKendaraan->where('merk', 'like', "%$word%")
                                       ->orWhere('tipe', 'like', "%$word%")
                                       ->orWhere('plat_nomor', 'like', "%$word%");
                        })
                        ->orWhere('tujuan', 'like', "%$word%")
                        ->orWhere('status_pinjam', 'like', "%$word%")
                        ->orWhere('kondisi_kendaraan', 'like', "%$word%")
                        ->orWhere('detail_insiden', 'like', "%$word%");

                    // Apply date search for fields like 'tgl_mulai', 'tgl_selesai', 'tgl_kembali_real'
                    $this->buildDateSearch($q2, 'tgl_mulai', $word);
                    $this->buildDateSearch($q2, 'tgl_selesai', $word);
                    $this->buildDateSearch($q2, 'tgl_kembali_real', $word);
                });
            }
        });
    }

    // pagination
    $daftarPeminjaman = $daftarPeminjaman->paginate(10);

    return view('pengguna.peminjaman', compact('daftarPeminjaman','search'));
}



    public function showForm()
    {
        return view('pengguna.formPeminjaman');
    }
    public function getAvailableKendaraan(Request $request)
    {
        // Gabungkan tanggal & waktu dari request 
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_mulai} {$request->jam_mulai}");
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_selesai} {$request->jam_selesai}");

        // Query kendaraan yang TIDAK sedang dipinjam dalam rentang waktu yang dipilih GET YANG DI TABEL PEMINJAMAN STATUSNYA "DISETUJUI"
        // Query kendaraan yang TIDAK sedang dipinjam dalam rentang waktu yang dipilih DAN memiliki status "Tersedia"
        // $availableKendaraan = Kendaraan::where('status_ketersediaan', 'Tersedia')
        //     // Hanya kendaraan dengan status "Tersedia"
        //     ->where('aset', 'Guna')
        //     ->whereNotIn('id_kendaraan', function ($query) use ($startDateTime, $endDateTime) {
        //         $query->select('id_kendaraan')
        //             ->from('peminjaman')
        //             ->where('status_pinjam', 'Disetujui') // Hanya peminjaman yang disetujui
        //             ->where(function ($q) use ($startDateTime, $endDateTime) {
        //                 $q->whereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND CONCAT(tgl_selesai, " ", jam_selesai)', [$startDateTime])
        //                     ->orWhereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND CONCAT(tgl_selesai, " ", jam_selesai)', [$endDateTime])
        //                     ->orWhereRaw('CONCAT(tgl_mulai, " ", jam_mulai) <= ? AND CONCAT(tgl_selesai, " ", jam_selesai) >= ?', [$startDateTime, $endDateTime]);
        //             });
        //     })->get();
        $availableKendaraan = Kendaraan::where('status_ketersediaan', 'Tersedia')
            ->where('aset', 'Guna')
            ->whereNotIn('id_kendaraan', function ($query) use ($startDateTime, $endDateTime) {
                $query->select('id_kendaraan')
                    ->from('peminjaman')
                    ->where('status_pinjam', 'Disetujui') // Hanya peminjaman yang disetujui
                    ->where(function ($q) use ($startDateTime, $endDateTime) {
                        $q->where(function ($subQuery) use ($startDateTime, $endDateTime) {
                            $subQuery->whereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), CONCAT(tgl_selesai, " ", jam_selesai))', [$startDateTime])
                                ->orWhereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), CONCAT(tgl_selesai, " ", jam_selesai))', [$endDateTime])
                                ->orWhereRaw('CONCAT(tgl_mulai, " ", jam_mulai) <= ? AND IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), CONCAT(tgl_selesai, " ", jam_selesai)) >= ?', [$startDateTime, $endDateTime]);
                        })
                        // Jika kendaraan sudah dikembalikan, kendaraan bisa dipinjam lagi setelah pengembalian
                        ->whereRaw('IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), CONCAT(tgl_selesai, " ", jam_selesai)) > ?', [$startDateTime]);
                    });
            })->get();


        return response()->json($availableKendaraan);
    }
    public function simpan(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'tgl_mulai' => 'required|date',
                'jam_mulai' => 'required',
                'tgl_selesai' => 'required|date',
                'jam_selesai' => 'required',
                'kendaraan' => 'required|exists:kendaraan,id_kendaraan',
                'tujuan' => 'required|string|max:255',
            ]);

            // Gabungkan tanggal dan jam untuk validasi
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_mulai} {$request->jam_mulai}");
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_selesai} {$request->jam_selesai}");

            // Validasi waktu
            if ($endDateTime <= $startDateTime) {
                return response()->json([
                    'message' => 'Waktu selesai harus setelah waktu mulai'
                ], 422);
            }

            // Cek ketersediaan kendaraan
            $isKendaraanBooked = Peminjaman::where('id_kendaraan', $request->kendaraan)
                ->where('status_pinjam', 'Disetujui')
                ->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->whereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND CONCAT(tgl_selesai, " ", jam_selesai)', [$startDateTime])
                        ->orWhereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND CONCAT(tgl_selesai, " ", jam_selesai)', [$endDateTime])
                        ->orWhereRaw('CONCAT(tgl_mulai, " ", jam_mulai) <= ? AND CONCAT(tgl_selesai, " ", jam_selesai) >= ?', [$startDateTime, $endDateTime]);
                })->exists();

            if ($isKendaraanBooked) {
                return response()->json([
                    'message' => 'Kendaraan tidak tersedia untuk waktu yang dipilih'
                ], 422);
            }

            // Simpan peminjaman
            $peminjaman = new Peminjaman();
            $peminjaman->user_id = Auth::id();
            $peminjaman->id_kendaraan = $request->kendaraan;
            $peminjaman->tgl_mulai = $request->tgl_mulai;
            $peminjaman->jam_mulai = $request->jam_mulai;
            $peminjaman->tgl_selesai = $request->tgl_selesai;
            $peminjaman->jam_selesai = $request->jam_selesai;
            $peminjaman->tujuan = $request->tujuan;
            $peminjaman->status_pinjam = 'Menunggu Persetujuan';
            $peminjaman->save();

            return response()->json([
                'message' => 'Peminjaman berhasil disimpan',
                'data' => $peminjaman
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan peminjaman'
            ], 500);
        }
    }
    
    public function detail($id)
    {
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])
            ->findOrFail($id);

        return view('pengguna.detailPeminjaman', compact('peminjaman'));
    }
    
    public function batal($id)
{
    DB::beginTransaction();
    try {
        // Cari data peminjaman berdasarkan ID
        $peminjaman = Peminjaman::findOrFail($id);

        // Update status menjadi "Dibatalkan"
        $peminjaman->status_pinjam = 'Dibatalkan';
        $peminjaman->save();

        // Jika peminjaman merupakan perpanjangan, kembalikan status peminjaman awal menjadi "Disetujui"
        if ($peminjaman->perpanjangan_dari) { 
            $peminjamanAwal = Peminjaman::find($peminjaman->perpanjangan_dari);
            if ($peminjamanAwal) {
                $peminjamanAwal->status_pinjam = 'Disetujui';
                $peminjamanAwal->save();
            }
        }

        DB::commit();

        // Redirect ke halaman detail peminjaman dengan pesan sukses
        return redirect()->route('pengguna.detailPeminjaman', ['id' => $id])
            ->with('success', 'Peminjaman berhasil dibatalkan.');

    } catch (\Exception $e) {
        DB::rollBack();
        // Jika terjadi error, redirect dengan pesan error
        return redirect()->back()->with('error', 'Terjadi kesalahan saat membatalkan peminjaman.');
    }
}

    public function showFormPengembalian($id)
    {
        // Ambil peminjaman yang sedang berlangsung berdasarkan user yang login
        $peminjaman = Peminjaman::where('id_peminjaman', $id)
            ->where('user_id', Auth::id())
            ->with('kendaraan')
            ->firstOrFail();
        
            return view('pengguna.formPengembalian',  compact('peminjaman'));
    }
    public function simpanPengembalian(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'tgl' => 'required|date',
                'jam' => 'required',
                'kondisi_kendaraan' => 'required|in:Baik,Terjadi Insiden',
                'detail' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            // Ambil data peminjaman
            $peminjaman = Peminjaman::where('id_peminjaman', $id)
                ->where('user_id', Auth::id())
                ->where('status_pinjam', 'Disetujui')
                ->with('kendaraan')
                ->firstOrFail();

            // Update peminjaman
            $peminjaman->update([
                'tgl_kembali_real' => $request->tgl,
                'jam_kembali_real' => $request->jam,
                'kondisi_kendaraan' => $request->kondisi_kendaraan,
                'detail_insiden' => $request->detail,
                'status_pinjam' => 'Telah Dikembalikan',
                'updated_at' => now(),
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengembalian kendaraan berhasil disimpan.'
                ]);
            }

            return redirect()
                ->route('peminjaman')
                ->with('success', 'Pengembalian kendaraan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    public function showFormPerpanjangan($id)
    {
        // Ambil peminjaman yang sedang berlangsung berdasarkan user yang login
        $peminjaman = Peminjaman::where('id_peminjaman', $id)
            ->where('user_id', Auth::id())
            ->with('kendaraan')
            ->firstOrFail();
        
            return view('pengguna.formPerpanjangan',  compact('peminjaman'));
    }

    public function perpanjangan(Request $request)
    {
        $peminjaman = Peminjaman::find($request->id_peminjaman);

        if (!$peminjaman) {
            return response()->json(['message' => 'Peminjaman tidak ditemukan'], 404);
        }
        try {
            // Validate request
            $validated = $request->validate([
                'tgl_selesai' => 'required|date',
                'jam_selesai' => 'required',
                'tujuan' => 'required|string|max:255',
            ]);

            DB::beginTransaction();

            // Get existing peminjaman
            $oldPeminjaman = Peminjaman::find($request->id_peminjaman);
            if (!$oldPeminjaman) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peminjaman tidak ditemukan',
                    'type' => 'error'
                ], 404);
            }

            // Create Carbon instances for time comparison
            $startDateTime = Carbon::parse($oldPeminjaman->tgl_selesai . ' ' . $oldPeminjaman->jam_selesai);
            $endDateTime = Carbon::parse($request->tgl_selesai . ' ' . $request->jam_selesai);

            // Validate end time is after start time
            if ($endDateTime <= $startDateTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu selesai harus setelah waktu mulai peminjaman',
                    'type' => 'validation'
                ], 422);
            }

            // Check if vehicle is available (include time check)
            // $conflictingBookings = Peminjaman::where('id_kendaraan', $oldPeminjaman->id_kendaraan)
            // ->where('id_peminjaman', '!=', $oldPeminjaman->id_peminjaman)
            // ->where(function ($query) use ($startDateTime, $endDateTime) {
            //     $query->where(function ($q) use ($startDateTime, $endDateTime) {
            //         $q->whereBetween('tgl_mulai', [$startDateTime->format('Y-m-d'), $endDateTime->format('Y-m-d')])
            //             ->orWhereBetween('tgl_selesai', [$startDateTime->format('Y-m-d'), $endDateTime->format('Y-m-d')]);
            //     })->where(function ($q) use ($startDateTime, $endDateTime) {
            //         $q->where(function ($q2) use ($startDateTime, $endDateTime) {
            //             $q2->whereBetween('jam_mulai', [$startDateTime->format('H:i:s'), $endDateTime->format('H:i:s')])
            //                 ->orWhereBetween('jam_selesai', [$startDateTime->format('H:i:s'), $endDateTime->format('H:i:s')]);
            //         });
            //     })->whereIn('status_pinjam', ['Disetujui', 'Menunggu Persetujuan']);
            // })->get();

            // Check if vehicle is available (include time check)
            $conflictingBookings = Peminjaman::where('id_kendaraan', $oldPeminjaman->id_kendaraan)
            ->where('id_peminjaman', '!=', $oldPeminjaman->id_peminjaman)
            ->whereIn('status_pinjam', ['Disetujui', 'Menunggu Persetujuan'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->whereRaw('? BETWEEN tgl_mulai AND IFNULL(tgl_kembali_real, tgl_selesai)', [$startDateTime])
                        ->orWhereRaw('? BETWEEN tgl_mulai AND IFNULL(tgl_kembali_real, tgl_selesai)', [$endDateTime])
                        ->orWhereRaw('tgl_mulai <= ? AND IFNULL(tgl_kembali_real, tgl_selesai) >= ?', [$startDateTime, $endDateTime]);
                })
                ->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->whereRaw('? BETWEEN jam_mulai AND IFNULL(jam_kembali_real, jam_selesai)', [$startDateTime->format('H:i:s')])
                        ->orWhereRaw('? BETWEEN jam_mulai AND IFNULL(jam_kembali_real, jam_selesai)', [$endDateTime->format('H:i:s')])
                        ->orWhereRaw('jam_mulai <= ? AND IFNULL(jam_kembali_real, jam_selesai) >= ?', [$startDateTime->format('H:i:s'), $endDateTime->format('H:i:s')]);
                })
                ->whereRaw('IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), CONCAT(tgl_selesai, " ", jam_selesai)) > ?', [$startDateTime]);
            })->get();


            if ($conflictingBookings->count() > 0) {
            // Buat array detail konflik untuk ditampilkan di pop-up
            $conflicts = [];
            foreach ($conflictingBookings as $conflict) {
                $conflicts[] = [
                    'tanggal' => $conflict->tgl_mulai . ' s/d ' . $conflict->tgl_selesai,
                    'waktu'   => $conflict->jam_mulai . ' s/d ' . $conflict->jam_selesai,
                    'status'  => $conflict->status_pinjam,
                    // Jika relasi user ada, misalnya $conflict->user->name
                    'peminjam'=> $conflict->user->name ?? 'N/A'
                ];
            }

            DB::rollback(); // Explicit rollback
            return response()->json([
                'success'   => false,
                'message'   => 'Terdapat bentrok jadwal peminjaman',
                'type'      => 'conflict',
                'conflicts' => $conflicts
            ], 422);
            }


            // Create new peminjaman record
            $newPeminjaman = new Peminjaman();
            $newPeminjaman->fill([
                'tgl_mulai' => $oldPeminjaman->tgl_selesai,
                'jam_mulai' => $oldPeminjaman->jam_selesai,
                'tgl_selesai' => $request->tgl_selesai,
                'jam_selesai' => $request->jam_selesai,
                'id_kendaraan' => $oldPeminjaman->id_kendaraan,
                'user_id' => auth()->id(),
                'tujuan' => $request->tujuan,
                'status_pinjam' => 'Menunggu Persetujuan',
                'perpanjangan_dari' => $oldPeminjaman->id_peminjaman
            ]);

            // Update status of old peminjaman
            $oldPeminjaman->status_pinjam = 'Diperpanjang';
            $oldPeminjaman->save();
            $newPeminjaman->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perpanjangan peminjaman berhasil diajukan',
                'redirect' => route('peminjaman')
            ]);

        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'type' => 'validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
    
//     public function perpanjangan(Request $request)
// {
//     $peminjaman = Peminjaman::find($request->id_peminjaman);

//     if (!$peminjaman) {
//         return response()->json(['message' => 'Peminjaman tidak ditemukan'], 404);
//     }
//     try {
//         // Validate request
//         $validated = $request->validate([
//             'tgl_selesai' => 'required|date',
//             'jam_selesai' => 'required',
//             'tujuan' => 'required|string|max:255',
//         ]);

//         DB::beginTransaction();

//         // Get existing peminjaman
//         $oldPeminjaman = Peminjaman::find($request->id_peminjaman);
//         if (!$oldPeminjaman) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Peminjaman tidak ditemukan',
//                 'type' => 'error'
//             ], 404);
//         }

//         // Create Carbon instances for time comparison
//         $startDateTime = Carbon::parse($oldPeminjaman->tgl_selesai . ' ' . $oldPeminjaman->jam_selesai);
//         $endDateTime = Carbon::parse($request->tgl_selesai . ' ' . $request->jam_selesai);
//         $currentDateTime = Carbon::now();

//         // Check if current time is before the end time of old peminjaman
//         if ($currentDateTime > $startDateTime) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Tidak dapat mengajukan perpanjangan setelah waktu selesai peminjaman',
//                 'type' => 'validation'
//             ], 422);
//         }

//         // Validate end time is after start time
//         if ($endDateTime <= $startDateTime) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Waktu selesai harus setelah waktu mulai peminjaman',
//                 'type' => 'validation'
//             ], 422);
//         }

//         // Check if vehicle is available (include time check)
//         $conflictingBookings = Peminjaman::where('id_kendaraan', $oldPeminjaman->id_kendaraan)
//             ->where('id_peminjaman', '!=', $oldPeminjaman->id_peminjaman)
//             ->where(function ($query) use ($startDateTime, $endDateTime) {
//                 $query->where(function ($q) use ($startDateTime, $endDateTime) {
//                     $q->whereBetween('tgl_mulai', [$startDateTime->format('Y-m-d'), $endDateTime->format('Y-m-d')])
//                         ->orWhereBetween('tgl_selesai', [$startDateTime->format('Y-m-d'), $endDateTime->format('Y-m-d')]);
//                 })->where(function ($q) use ($startDateTime, $endDateTime) {
//                     $q->where(function ($q2) use ($startDateTime, $endDateTime) {
//                         $q2->whereBetween('jam_mulai', [$startDateTime->format('H:i:s'), $endDateTime->format('H:i:s')])
//                         ->orWhereBetween('jam_selesai', [$startDateTime->format('H:i:s'), $endDateTime->format('H:i:s')]);
//                     });
//                 })->whereIn('status_pinjam', ['Disetujui', 'Menunggu Persetujuan']);
//             })->get();

//         if ($conflictingBookings->count() > 0) {
//             DB::rollback();
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Terdapat bentrok jadwal peminjaman',
//                 'type' => 'conflict'
//             ], 422);
//         }

//         // Create new peminjaman record
//         $newPeminjaman = new Peminjaman();
//         $newPeminjaman->fill([
//             'tgl_mulai' => $oldPeminjaman->tgl_selesai,
//             'jam_mulai' => $oldPeminjaman->jam_selesai,
//             'tgl_selesai' => $request->tgl_selesai,
//             'jam_selesai' => $request->jam_selesai,
//             'id_kendaraan' => $oldPeminjaman->id_kendaraan,
//             'user_id' => auth()->id(),
//             'tujuan' => $request->tujuan,
//             'status_pinjam' => 'Menunggu Persetujuan',
//             'perpanjangan_dari' => $oldPeminjaman->id_peminjaman
//         ]);

//         // Update status of old peminjaman to 'Diperpanjang'
//         $oldPeminjaman->status_pinjam = 'Diperpanjang';
//         $oldPeminjaman->save();
//         $newPeminjaman->save();

//         DB::commit();

//         return response()->json([
//             'success' => true,
//             'message' => 'Perpanjangan peminjaman berhasil diajukan',
//             'redirect' => route('peminjaman')
//         ]);

//     } catch (ValidationException $e) {
//         DB::rollback();
//         return response()->json([
//             'success' => false,
//             'message' => 'Validasi gagal',
//             'type' => 'validation',
//             'errors' => $e->errors()
//         ], 422);
//     } catch (\Exception $e) {
//         DB::rollback();
//         return response()->json([
//             'success' => false,
//             'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
//             'type' => 'error'
//         ], 500);
//     }
// }


// Updated rejection handler
// public function tolakPerpanjangan(Request $request)
// {
//     try {
//         DB::beginTransaction();

//         // Cari peminjaman perpanjangan yang akan ditolak
//         $perpanjangan = Peminjaman::findOrFail($request->id_peminjaman);
        
//         // Cek apakah ini memang perpanjangan (memiliki id peminjaman sebelumnya)
//         if ($perpanjangan->perpanjangan_dari) {
//             // Cari peminjaman sebelumnya
//             $peminjamanSebelumnya = Peminjaman::findOrFail($perpanjangan->perpanjangan_dari);
            
//             // Ubah status peminjaman sebelumnya kembali menjadi Disetujui
//             $peminjamanSebelumnya->status_pinjam = 'Disetujui';
//             $peminjamanSebelumnya->save();
//         }

//         // Ubah status perpanjangan menjadi Ditolak
//         $perpanjangan->status_pinjam = 'Ditolak';
//         $perpanjangan->save();

//         DB::commit();

//         return response()->json([
//             'success' => true,
//             'message' => 'Perpanjangan ditolak dan status peminjaman sebelumnya dikembalikan',
//             'redirect' => route('peminjaman')
//         ]);

//     } catch (\Exception $e) {
//         DB::rollback();
//         return response()->json([
//             'success' => false,
//             'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
//             'type' => 'error'
//         ], 500);
//     }
// }

}
