<?php

namespace App\Http\Controllers;

use App\Models\CekFisik;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CekFisikController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Kendaraan::leftJoin('cek_fisik as cf', 'kendaraan.id_kendaraan', '=', 'cf.id_kendaraan')
            ->select(
                'kendaraan.id_kendaraan',
                'kendaraan.merk',
                'kendaraan.tipe',
                'kendaraan.plat_nomor',
                DB::raw('(SELECT tgl_cek_fisik FROM cek_fisik WHERE cek_fisik.id_kendaraan = kendaraan.id_kendaraan ORDER BY tgl_cek_fisik DESC LIMIT 1) as tgl_cek_fisik_terakhir')
            )
            ->where('kendaraan.aset', '!=', 'Lelang')
            ->groupBy('kendaraan.id_kendaraan', 'kendaraan.merk', 'kendaraan.tipe', 'kendaraan.plat_nomor');

        if ($search) {
            $keywords = preg_split('/\s+/', $search); // pecah berdasarkan spasi

            $query->where(function($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function($q2) use ($word) {
                        $q2->where('kendaraan.merk', 'like', "%{$word}%")
                           ->orWhere('kendaraan.tipe', 'like', "%{$word}%")
                           ->orWhere('kendaraan.plat_nomor', 'like', "%{$word}%")
                           ->orWhereRaw('DATE_FORMAT(cf.tgl_cek_fisik, "%d-%m-%Y") like ?', ["%{$word}%"])
                           ->orWhereRaw('DATE_FORMAT(cf.tgl_cek_fisik, "%d/%m/%Y") like ?', ["%{$word}%"])
                           ->orWhereRaw('DATE_FORMAT(cf.tgl_cek_fisik, "%Y") like ?', ["%{$word}%"])
                           ->orWhereRaw('DATE_FORMAT(cf.tgl_cek_fisik, "%m") like ?', ["%{$word}%"]);
                    });
                }
            });
        }

        $kendaraan = $query->paginate(10)->appends(['search' => $search]);
        return view('admin.cek-fisik.index', compact('kendaraan'));
    }
    
    // // SEARCH 1 KOLOM:
    // public function index(Request $request)
    // {
    //     $search = $request->input('search');

    //     $dateSearch = false;
    //     $dateConditions = [];
    
    //     // Check if search is date-related and prepare conditions
    //     if ($search) {
    //         // Day only (1-31)
    //         if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])$/', $search)) {
    //             $dateSearch = true;
    //             $day = (int) $search;
    //             $dateConditions[] = ["DAY(cf.tgl_cek_fisik) = ?", [$day]];
    //         }
            
    //         // Month only (1-12)
    //         if (preg_match('/^(0?[1-9]|1[0-2])$/', $search)) {
    //             $dateSearch = true;
    //             $month = (int) $search;
    //             $dateConditions[] = ["MONTH(cf.tgl_cek_fisik) = ?", [$month]];
    //         }
            
    //         // Year only (4 digits)
    //         if (preg_match('/^(20\d{2})$/', $search)) {
    //             $dateSearch = true;
    //             $year = (int) $search;
    //             $dateConditions[] = ["YEAR(cf.tgl_cek_fisik) = ?", [$year]];
    //         }
            
    //         // Day-Month (d-m)
    //         if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])[\-\/](0?[1-9]|1[0-2])$/', $search)) {
    //             $dateSearch = true;
    //             $parts = preg_split('/[\-\/]/', $search);
    //             $day = (int) $parts[0];
    //             $month = (int) $parts[1];
    //             $dateConditions[] = ["DAY(cf.tgl_cek_fisik) = ? AND MONTH(cf.tgl_cek_fisik) = ?", [$day, $month]];
    //         }
            
    //         // Month-Year (m-Y)
    //         if (preg_match('/^(0?[1-9]|1[0-2])[\-\/](20\d{2})$/', $search)) {
    //             $dateSearch = true;
    //             $parts = preg_split('/[\-\/]/', $search);
    //             $month = (int) $parts[0];
    //             $year = (int) $parts[1];
    //             $dateConditions[] = ["MONTH(cf.tgl_cek_fisik) = ? AND YEAR(cf.tgl_cek_fisik) = ?", [$month, $year]];
    //         }
            
    //         // Full date (d-m-Y)
    //         if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])[\-\/](0?[1-9]|1[0-2])[\-\/](20\d{2})$/', $search)) {
    //             $dateSearch = true;
    //             $parts = preg_split('/[\-\/]/', $search);
    //             $day = (int) $parts[0];
    //             $month = (int) $parts[1];
    //             $year = (int) $parts[2];
    //             $dateConditions[] = ["DAY(cf.tgl_cek_fisik) = ? AND MONTH(cf.tgl_cek_fisik) = ? AND YEAR(cf.tgl_cek_fisik) = ?", [$day, $month, $year]];
    //         }
    //     }

    //     $query = Kendaraan::leftJoin('cek_fisik', 'kendaraan.id_kendaraan', '=', 'cek_fisik.id_kendaraan')
    //         ->select(
    //             'kendaraan.id_kendaraan',
    //             'kendaraan.merk',
    //             'kendaraan.tipe',
    //             'kendaraan.plat_nomor',
    //             'kendaraan.aset',
    //             DB::raw('(SELECT tgl_cek_fisik FROM cek_fisik WHERE cek_fisik.id_kendaraan = kendaraan.id_kendaraan ORDER BY tgl_cek_fisik DESC LIMIT 1) as tgl_cek_fisik_terakhir')
    //         )
    //         ->where('kendaraan.aset', '!=', 'Lelang');;
        
    //     if ($search && !$dateSearch) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('kendaraan.merk', 'LIKE', "%{$search}%")
    //             ->orWhere('kendaraan.tipe', 'LIKE', "%{$search}%")
    //             ->orWhere('kendaraan.plat_nomor', 'LIKE', "%{$search}%");
    //         });
    //     }

    //     // Handle date search with a JOIN and WHERE clause approach
    //     if ($dateSearch && !empty($dateConditions)) {
    //         // Using a different approach with joins to handle the date filtering
    //         $query = Kendaraan::select(
    //             'kendaraan.id_kendaraan',
    //             'kendaraan.merk',
    //             'kendaraan.tipe',
    //             'kendaraan.plat_nomor',
    //             DB::raw('MAX(cf.tgl_cek_fisik) as tgl_cek_fisik_terakhir')
    //         )
    //         ->join(DB::raw('(SELECT id_kendaraan, MAX(tgl_cek_fisik) as max_date FROM cek_fisik GROUP BY id_kendaraan) as latest_dates'), 
    //             'kendaraan.id_kendaraan', '=', 'latest_dates.id_kendaraan')
    //         ->join(DB::raw('cek_fisik as cf'), function($join) {
    //             $join->on('kendaraan.id_kendaraan', '=', 'cf.id_kendaraan')
    //                 ->on('cf.tgl_cek_fisik', '=', 'latest_dates.max_date');
    //         });

    //         // Apply date conditions
    //         foreach ($dateConditions as $condition) {
    //             $query->whereRaw($condition[0], $condition[1]);
    //         }

    //         $query->groupBy('kendaraan.id_kendaraan', 'kendaraan.merk', 'kendaraan.tipe', 'kendaraan.plat_nomor');
    //     } else {
    //         // If not searching by date, use the original groupBy
    //         $query->groupBy('kendaraan.id_kendaraan', 'kendaraan.merk', 'kendaraan.tipe', 'kendaraan.plat_nomor');
    //     }

    //     $kendaraan = $query->paginate(10)->appends(['search' => $search]);
    //     return view('admin.cek-fisik.index', compact('kendaraan'));
    // }


    public function create($id_kendaraan)
    {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);
        return view('admin.cek-fisik.create', compact('kendaraan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'tgl_cek_fisik' => 'required|date',
            'mesin' => 'required',
            'accu' => 'required',
            'air_radiator' => 'required',
            'air_wiper' => 'required',
            'body' => 'required',
            'ban' => 'required',
            'pengharum' => 'required',
            'kondisi_keseluruhan' => 'required',
            'catatan' => 'nullable|string'
        ]);

        CekFisik::create(array_merge($request->all(), ['user_id' => Auth::id()]));

        return redirect()->route('admin.cek-fisik.index', [
            'page' => $request->input('page', 1),
            'search' => $request->input('search')
        ])->with('success', 'Cek fisik berhasil dicatat.');
    }

    public function detail($id_kendaraan)
    {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);
        
        // Ambil cek fisik terakhir untuk kendaraan
        $cekFisik = CekFisik::where('id_kendaraan', $id_kendaraan)
            ->orderBy('tgl_cek_fisik', 'desc')
            ->first();

        return view('admin.cek-fisik.detail', compact('kendaraan', 'cekFisik'));
    }

    public function edit($id_kendaraan)
    {
        $search = request()->query('search');
        $page = request()->query('page', 1);

        // Ambil cek fisik terakhir untuk kendaraan
        $cekFisik = CekFisik::where('id_kendaraan', $id_kendaraan)
            ->orderBy('tgl_cek_fisik', 'desc')
            ->first();

        if (!$cekFisik) {
            return redirect()->route('admin.cek-fisik.index', [
                'page' => $page,
                'search' => $search
            ])->with('error', 'Cek fisik tidak ditemukan.');
        }

        $kendaraan = Kendaraan::find($id_kendaraan);

        if (!$kendaraan) {
            return redirect()->route('admin.cek-fisik.index', [
                'page' => $page,
                'search' => $search
            ])->with('error', 'Kendaraan tidak ditemukan.');
        }
    
        return view('admin.cek-fisik.edit', compact('cekFisik', 'kendaraan', 'search', 'page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_cek_fisik' => 'required|date',
            'mesin' => 'required',
            'accu' => 'required',
            'air_radiator' => 'required',
            'air_wiper' => 'required',
            'body' => 'required',
            'ban' => 'required',
            'pengharum' => 'required',
            'kondisi_keseluruhan' => 'required',
            'catatan' => 'nullable|string'
        ]);

        $cekFisik = CekFisik::findOrFail($id);
        $cekFisik->update($request->all());

        $page = $request->input('page', 1);
        $search = $request->input('search');

        return redirect()
            ->route('admin.cek-fisik.index', [
                'page' => $page,
                'search' => $search
            ])
            ->with('success', 'Data cek fisik berhasil diperbarui.');
    }

    public function destroy($id_kendaraan)
    {
        // Ambil cek fisik terakhir untuk kendaraan
        $cekFisikTerakhir = CekFisik::where('id_kendaraan', $id_kendaraan)
            ->orderBy('tgl_cek_fisik', 'desc')
            ->first();

        if ($cekFisikTerakhir) {
            $cekFisikTerakhir->delete();
        }

        $page = request()->query('page', 1);
        $search = request()->query('search');

        return redirect()->route('admin.cek-fisik.index', [
            'page' => $page,
            'search' => $search
        ])->with('success', 'Cek fisik terakhir berhasil dihapus.');
    }
}
