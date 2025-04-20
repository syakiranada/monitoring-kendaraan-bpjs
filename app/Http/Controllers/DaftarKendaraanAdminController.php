<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pajak;
use App\Models\Kendaraan;
use App\Models\Asuransi;
use App\Models\CekFisik;
use App\Models\ServisRutin;
use App\Models\ServisInsidental;
use App\Models\BBM;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class DaftarKendaraanAdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusKetersediaanFilter = $request->input('status_ketersediaan');
        $dataKendaraanQuery = Kendaraan::query();
        $convertedDate = null;
        $partialDate = null;

        if ($search) {
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) {
                try {
                    $convertedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
                } catch (\Exception $e) {
                    $convertedDate = null;
                }
            }
            if (preg_match('/^\d{2}-\d{2}$/', $search)) {
                $partialDate = '%' . $search . '%';  
            } elseif (preg_match('/^\d{2}$/', $search)) {
                $partialDate = $search . '%';  
            } elseif (preg_match('/^\d{1,2}$/', $search)) {
                $partialDate = '%' . $search . '-%';  
            } elseif (preg_match('/^\d{4}$/', $search)) {
                $partialDate = $search . '%';  
            }

        $searchDate = null;
        $searchDay = null;
        $searchMonth = null;
        $searchYear = null;
        
        if (!empty($search)) {
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) {
                try {
                    $searchDate = Carbon::createFromFormat('d-m-Y', $search);
                    $searchDay = $searchDate->day;
                    $searchMonth = $searchDate->month;
                    $searchYear = $searchDate->year;
                } catch (\Exception $e) {
                    Log::error("Error parsing full date:", ['search' => $search, 'error' => $e->getMessage()]);
                }
            }
            elseif (preg_match('/^\d{2}-\d{2}$/', $search)) {
                try {
                    $temp = Carbon::createFromFormat('d-m', $search);
                    $searchDay = $temp->day;
                    $searchMonth = $temp->month;
                } catch (\Exception $e) {
                    Log::error("Error parsing day-month:", ['search' => $search, 'error' => $e->getMessage()]);
                }
            }
            elseif (preg_match('/^\d{2}-\d{4}$/', $search)) {
                try {
                    $temp = Carbon::createFromFormat('m-Y', $search);
                    $searchMonth = $temp->month;
                    $searchYear = $temp->year;
                } catch (\Exception $e) {
                    Log::error("Error parsing month-year:", ['search' => $search, 'error' => $e->getMessage()]);
                }
            }
            elseif (preg_match('/^\d{2}$/', $search) && intval($search) >= 1 && intval($search) <= 31) {
                $searchDay = intval($search);
            }
            elseif (preg_match('/^\d{2}$/', $search) && intval($search) >= 1 && intval($search) <= 12) {
                $searchMonth = intval($search);
            }
            elseif (preg_match('/^\d{4}$/', $search)) {
                $searchYear = intval($search);
            }
        }
        
        if ($searchDay !== null || $searchMonth !== null || $searchYear !== null) {
            $matchingKendaraanIds = [];
            $kendaraanIds = Kendaraan::pluck('id_kendaraan');
            
            foreach ($kendaraanIds as $id_kendaraan) {
                $latestDates = [
                    Kendaraan::where('id_kendaraan', $id_kendaraan)->value('tgl_pembelian'),
                    CekFisik::where('id_kendaraan', $id_kendaraan)->latest('tgl_cek_fisik')->value('tgl_cek_fisik'),
                    Pajak::where('id_kendaraan', $id_kendaraan)->latest('tgl_bayar')->value('tgl_bayar'),
                    Asuransi::where('id_kendaraan', $id_kendaraan)->latest('tgl_bayar')->value('tgl_bayar'),
                    BBM::where('id_kendaraan', $id_kendaraan)->latest('tgl_isi')->value('tgl_isi'),
                    ServisRutin::where('id_kendaraan', $id_kendaraan)->latest('tgl_servis_real')->value('tgl_servis_real')
                ];
                
                $latestDates = array_filter($latestDates);
                
                foreach ($latestDates as $date) {
                    $carbonDate = Carbon::parse($date);
                    $matches = true;
                    
                    if ($searchDay !== null && $carbonDate->day != $searchDay) {
                        $matches = false;
                    }
                    if ($searchMonth !== null && $carbonDate->month != $searchMonth) {
                        $matches = false;
                    }
                    if ($searchYear !== null && $carbonDate->year != $searchYear) {
                        $matches = false;
                    }
                    
                    if ($matches) {
                        $matchingKendaraanIds[] = $id_kendaraan;
                        break;
                    }
                }
            }
            
            if (!empty($matchingKendaraanIds)) {
                $dataKendaraanQuery->whereIn('id_kendaraan', $matchingKendaraanIds);
            } else {
                $dataKendaraanQuery->whereNull('id_kendaraan');
            }
        }
        } else {
            $originalSearch = $search; 

            if (!empty($search)) {
                if (stripos($search, 'tidak tersedia') !== false) {
                    $dataKendaraanQuery->where('status_ketersediaan', '=', 'tidak tersedia');
                    $search = trim(str_ireplace('tidak tersedia', '', $search));
                } elseif (stripos($search, 'tersedia') !== false) {
                    $dataKendaraanQuery->where('status_ketersediaan', '=', 'tersedia');
                    $search = trim(str_ireplace('tersedia', '', $search));
                }
            }

            if (!empty($statusKetersediaanFilter)) {
                $dataKendaraanQuery->where('status_ketersediaan', '=', strtolower($statusKetersediaanFilter));
            }

            if (!empty($search)) {
                $dataKendaraanQuery->where(function ($query) use ($search) {
                    $query->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.merk)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.warna)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.plat_nomor)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.aset)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.warna)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.aset)) LIKE ?", ["%".strtolower($search)."%"]);
                    
                    $query->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', CAST(kendaraan.kapasitas AS CHAR))) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', CAST(kendaraan.kapasitas AS CHAR))) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.jenis)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.jenis)) LIKE ?", ["%".strtolower($search)."%"]);
                    
                
                    $query->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.frekuensi_servis)) LIKE ?", ["%".strtolower($search)."%"])
                            ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.frekuensi_servis)) LIKE ?", ["%".strtolower($search)."%"]);
                
                    $query->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.no_mesin)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.no_rangka)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.no_rangka)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.no_mesin)) LIKE ?", ["%".strtolower($search)."%"]);

                    $query->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', kendaraan.bahan_bakar)) LIKE ?", ["%".strtolower($search)."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', kendaraan.bahan_bakar)) LIKE ?", ["%".strtolower($search)."%"]);

                    if (preg_match('/(\d{4})/', $search, $yearMatches)) {
                        $year = $yearMatches[1];
                        $query->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', YEAR(kendaraan.tgl_pembelian))) LIKE ?", 
                            ["%".strtolower(str_replace($year, '', $search)).$year."%"])
                        ->orWhereRaw("LOWER(CONCAT(kendaraan.tipe, ' ', YEAR(kendaraan.tgl_pembelian))) LIKE ?", 
                            ["%".strtolower(str_replace($year, '', $search)).$year."%"]);
                    }
                 
                    if (preg_match('/(\d+)/', $search, $numMatches)) {
                        $num = $numMatches[1];
                        $query->orWhereRaw("LOWER(CONCAT(kendaraan.merk, ' ', kendaraan.tipe, ' ', CAST(kendaraan.kapasitas AS CHAR))) LIKE ?", 
                            ["%".strtolower(str_replace($num, '', $search)).$num."%"]);
                    }
                    
                    $searchTerms = explode(' ', strtolower($search));
                    $searchTerms = array_filter($searchTerms);
                    
                    if (!empty($searchTerms)) {
                        $query->orWhere(function ($andQuery) use ($searchTerms) {
                            foreach ($searchTerms as $term) {
                                $andQuery->where(function ($termQuery) use ($term) {
                                    $termQuery->whereRaw("LOWER(kendaraan.plat_nomor) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("LOWER(kendaraan.merk) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("LOWER(kendaraan.tipe) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("LOWER(kendaraan.warna) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("LOWER(kendaraan.jenis) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("LOWER(kendaraan.aset) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("LOWER(kendaraan.bahan_bakar) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("LOWER(kendaraan.no_mesin) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("LOWER(kendaraan.no_rangka) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("CAST(kendaraan.kapasitas AS CHAR) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("CAST(YEAR(kendaraan.tgl_pembelian) AS CHAR) LIKE ?", ["%$term%"])
                                        ->orWhereRaw("LOWER(kendaraan.frekuensi_servis) LIKE ?", ["%$term%"]);
                                
                                    $cleanTerm = str_replace([',', '.'], '', $term);
                                    $termQuery->orWhereRaw("REPLACE(REPLACE(CAST(kendaraan.nilai_perolehan AS CHAR), '.', ''), ',', '') LIKE ?", ["%$cleanTerm%"])
                                        ->orWhereRaw("REPLACE(REPLACE(CAST(kendaraan.nilai_buku AS CHAR), '.', ''), ',', '') LIKE ?", ["%$cleanTerm%"]);
                                });
                            }
                        });
                    }
                });
            }
        }
        
        $allKendaraan = $dataKendaraanQuery->get();
        $alerts = []; 
        foreach ($allKendaraan as $k) {
            $incomplete = [];
            $pajak = $k->pajak()->latest()->first();
            if (!$pajak || !$pajak->nominal) {
                $incomplete[] = "Pajak";
            }

            $asuransi = $k->asuransi()->latest()->first();
            if (!$asuransi || !$asuransi->nominal) {
                $incomplete[] = "Asuransi";
            }

            $cekFisik = $k->cekFisik()->latest()->first();
            if (!$cekFisik || !$cekFisik->accu) {
                $incomplete[] = "Cek Fisik";
            }

            $servisRutin = $k->servisRutin()->latest()->first();
            if (!$servisRutin || !$servisRutin->tgl_servis_real) {
                $incomplete[] = "Servis Rutin";
            }

            if (!empty($incomplete)) {
                $alerts[] = [
                    'vehicle' => "{$k->merk} {$k->tipe} (Plat: {$k->plat_nomor})",
                    'incomplete' => $incomplete
                ];
            }
        }

        $dataKendaraan = $dataKendaraanQuery->paginate(10)->appends(['search' => $search]);
        
        return view('admin.kendaraan.daftar_kendaraan', compact('dataKendaraan', 'search', 'statusKetersediaanFilter', 'alerts'));
    }


    public function tambah()
    {
        $user_id = Auth::id();
        return view('admin.kendaraan.tambah', compact('user_id'));
    }

   
    public function checkPlat(Request $request)
    {
        $platNomor = $request->input('plat_nomor');
        $excludeId = $request->input('exclude_id');
        
        $exists = DB::table('kendaraan')
                    ->where('plat_nomor', $platNomor)
                    ->where('id_kendaraan', '!=', $excludeId)
                    ->exists();
        
        Log::info("Checking plate: {$platNomor}, exclude ID: {$excludeId}, exists: " . ($exists ? 'true' : 'false'));
        
        return response()->json(['exists' => $exists]);
    }

    public function store(Request $request)
    {
        try {
            Log::info('DEBUG: Incoming request', ['request_data' => $request->all()]);
    
            $validationRules = [
                'merk' => 'required|string|max:255',
                'tipe' => 'required|string|max:255',
                'plat_nomor' => 'required|string|max:20',
                'warna' => 'required|string|max:50',
                'jenis_kendaraan' => 'required|string',
                'aset_guna' => 'required|string',
                'kapasitas' => 'required|integer|min:1',
                'tanggal_beli' => 'required|date',
                'nilai_perolehan' => 'required|numeric',
                'nilai_buku' => 'required|numeric',
                'bahan_bakar' => 'required|string',
                'nomor_mesin' => 'required|string|max:100',
                'nomor_rangka' => 'required|string|max:100',
                'tanggal_bayar_pajak' => 'required|date',
                'tanggal_jatuh_tempo_pajak' => 'required|date',
                'tanggal_cek_fisik' => 'required|date',
                'frekuensi' => 'required|integer|min:1',
                'status_pinjam' => 'required|string',
                'current_page' => 'required|integer|min:1',
            ];
    
            if ($request->filled('tanggal_asuransi') || $request->filled('tanggal_perlindungan_awal') || $request->filled('tanggal_perlindungan_akhir')) {
                $validationRules['tanggal_asuransi'] = 'required|date';
                $validationRules['tanggal_perlindungan_awal'] = 'required|date';
                $validationRules['tanggal_perlindungan_akhir'] = 'required|date|after:tanggal_perlindungan_awal';
            }
    
            $request->validate($validationRules);
    
            Log::info('DEBUG: Validation passed');
    
            $statusKetersediaan = ($request->aset_guna === 'Guna') 
            ? ($request->status_pinjam ?? 'TERSEDIA') 
            : 'TIDAK TERSEDIA';        
    
            $kendaraan = Kendaraan::create([
                'merk' => $request->merk,
                'tipe' => $request->tipe,
                'plat_nomor' => $request->plat_nomor,
                'warna' => $request->warna,
                'jenis' => $request->jenis_kendaraan,
                'aset' => $request->aset_guna,
                'kapasitas' => $request->kapasitas,
                'tgl_pembelian' => $request->tanggal_beli,
                'nilai_perolehan' => $request->nilai_perolehan,
                'nilai_buku' => $request->nilai_buku,
                'bahan_bakar' => $request->bahan_bakar,
                'no_mesin' => $request->nomor_mesin,
                'no_rangka' => $request->nomor_rangka,
                'frekuensi_servis' => $request->frekuensi,
                'status_ketersediaan' => $statusKetersediaan, 
            ]);
    
            Pajak::create([
                'user_id' => Auth::id(),
                'id_kendaraan' => $kendaraan->id_kendaraan, 
                'tgl_bayar' => date('Y-m-d', strtotime($request->tanggal_bayar_pajak)),
                'tgl_jatuh_tempo' => date('Y-m-d', strtotime($request->tanggal_jatuh_tempo_pajak)),
                'tahun' => date('Y', strtotime($request->tanggal_bayar_pajak)),
            ]);
    
            if ($request->filled('tanggal_asuransi') && 
                $request->filled('tanggal_perlindungan_awal') && 
                $request->filled('tanggal_perlindungan_akhir')) {
                
                Asuransi::create([
                    'user_id' => Auth::id(),
                    'id_kendaraan' => $kendaraan->id_kendaraan,
                    'tgl_bayar' => $request->tanggal_asuransi,
                    'tahun' => date('Y', strtotime($request->tanggal_perlindungan_akhir)),
                    'tgl_perlindungan_awal' => $request->tanggal_perlindungan_awal,
                    'tgl_perlindungan_akhir' => $request->tanggal_perlindungan_akhir,
                ]);
            }
    
            CekFisik::create([
                'user_id' => Auth::id(),
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'tgl_cek_fisik' => $request->tanggal_cek_fisik,
            ]);
    
            $search = $request->query('search', request()->input('search', ''));

            $totalKendaraan = Kendaraan::when($search, function ($query, $search) {
                return $query->where('merk', 'LIKE', "%{$search}%");
            })->count();
            
            $perPage = 10; 
            $lastPage = max(1, ceil($totalKendaraan / $perPage)); 

            return redirect()->route('kendaraan.daftar_kendaraan', [
                'page' => $lastPage,
                'search' => $search ?: null
            ])->with('success', 'Data kendaraan dan semua terkait berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('DEBUG: Exception occurred', ['error' => $e->getMessage()]);
            return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit($id_kendaraan)
    {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);
        
        $pajak = Pajak::where('id_kendaraan', $id_kendaraan)->first();
        $asuransi = Asuransi::where('id_kendaraan', $id_kendaraan)->first();
        $cekFisik = CekFisik::where('id_kendaraan', $id_kendaraan)->first();
        return view('admin.kendaraan.edit', compact('kendaraan', 'pajak', 'asuransi', 'cekFisik'));
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('DEBUG: Incoming update request', ['request_data' => $request->all()]);

            $validationRules = [
                'merk' => 'required|string|max:255',
                'tipe' => 'required|string|max:255',
                'plat_nomor' => 'required|string|max:20',
                'warna' => 'required|string|max:50',
                'jenis_kendaraan' => 'required|string',
                'aset_guna' => 'required|string',
                'kapasitas' => 'required|integer|min:1',
                'tanggal_beli' => 'required|date',
                'nilai_perolehan' => 'required|numeric',
                'nilai_buku' => 'required|numeric',
                'bahan_bakar' => 'required|string',
                'nomor_mesin' => 'required|string|max:100',
                'nomor_rangka' => 'required|string|max:100',
                'tanggal_bayar_pajak' => 'required|date',
                'tanggal_jatuh_tempo_pajak' => 'required|date',
                'tanggal_cek_fisik' => 'required|date',
                'frekuensi' => 'required|integer|min:1',
                'status_pinjam' => 'required|string',
                'current_page' => 'required|integer|min:1',
            ];

            if ($request->filled('tanggal_asuransi') || $request->filled('tanggal_perlindungan_awal') || $request->filled('tanggal_perlindungan_akhir')) {
                $validationRules['tanggal_asuransi'] = 'required|date';
                $validationRules['tanggal_perlindungan_awal'] = 'required|date';
                $validationRules['tanggal_perlindungan_akhir'] = 'required|date|after:tanggal_perlindungan_awal';
            }

            $request->validate($validationRules);

            Log::info('DEBUG: Validation passed');

            $kendaraan = Kendaraan::findOrFail($id);
            $statusKetersediaan = ($request->aset_guna === 'Guna') 
            ? ($request->status_pinjam ?? 'TERSEDIA') 
            : 'TIDAK TERSEDIA';


            $kendaraan->update([
                'merk' => $request->merk,
                'tipe' => $request->tipe,
                'plat_nomor' => $request->plat_nomor,
                'warna' => $request->warna,
                'jenis' => $request->jenis_kendaraan,
                'aset' => $request->aset_guna,
                'kapasitas' => $request->kapasitas,
                'tgl_pembelian' => $request->tanggal_beli,
                'nilai_perolehan' => $request->nilai_perolehan,
                'nilai_buku' => $request->nilai_buku,
                'bahan_bakar' => $request->bahan_bakar,
                'no_mesin' => $request->nomor_mesin,
                'no_rangka' => $request->nomor_rangka,
                'frekuensi_servis' => $request->frekuensi,
                'status_ketersediaan' => $statusKetersediaan,
            ]);

            Pajak::updateOrCreate(
                ['id_kendaraan' => $kendaraan->id_kendaraan],
                [
                    'user_id' => Auth::id(),
                    'tgl_bayar' => date('Y-m-d', strtotime($request->tanggal_bayar_pajak)),
                    'tgl_jatuh_tempo' => date('Y-m-d', strtotime($request->tanggal_jatuh_tempo_pajak)),
                    'tahun' => date('Y', strtotime($request->tanggal_bayar_pajak)),
                ]
            );

            if ($request->filled('tanggal_asuransi') && 
                $request->filled('tanggal_perlindungan_awal') && 
                $request->filled('tanggal_perlindungan_akhir')) {
                
                Asuransi::updateOrCreate(
                    ['id_kendaraan' => $kendaraan->id_kendaraan],
                    [
                        'user_id' => Auth::id(),
                        'tgl_bayar' => $request->tanggal_asuransi,
                        'tahun' => date('Y', strtotime($request->tanggal_perlindungan_akhir)),
                        'tgl_perlindungan_awal' => $request->tanggal_perlindungan_awal,
                        'tgl_perlindungan_akhir' => $request->tanggal_perlindungan_akhir,
                    ]
                );
            }

            CekFisik::updateOrCreate(
                ['id_kendaraan' => $kendaraan->id_kendaraan],
                [
                    'user_id' => Auth::id(),
                    'tgl_cek_fisik' => $request->tanggal_cek_fisik,
                ]
            );

            $currentPage = $request->input('current_page', 1);

            Log::info('DEBUG: Data kendaraan dan terkait berhasil diperbarui');
                    
            $search = $request->query('search', request()->input('search', ''));

            return redirect()->route('kendaraan.daftar_kendaraan', [
                'page' => $currentPage,
                'search' => $search ?: null 
            ])->with('success', 'Data kendaraan dan semua terkait berhasil diperbarui!');
                            
        } catch (\Exception $e) {
            Log::error('DEBUG: Exception occurred', ['error' => $e->getMessage()]);
            return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function detail($id_kendaraan) {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);
        $cekFisik = CekFisik::where('id_kendaraan', $id_kendaraan)->latest('tgl_cek_fisik')->first();
        $pajak = Pajak::where('id_kendaraan', $id_kendaraan)->latest('tgl_bayar')->first();
        $asuransi = Asuransi::where('id_kendaraan', $id_kendaraan)->latest('tgl_bayar')->first();
        $bbm = BBM::where('id_kendaraan', $id_kendaraan)->latest('tgl_isi')->first(); 
        $servisRutin = ServisRutin::where('id_kendaraan', $id_kendaraan)->latest('tgl_servis_real')->first();
        return view('admin.kendaraan.detail', compact('kendaraan', 'cekFisik', 'pajak', 'asuransi', 'bbm', 'servisRutin'));
    }


    public function hapus($id_kendaraan, Request $request)
    {
        try {
            $kendaraan = Kendaraan::findOrFail($id_kendaraan);
            Pajak::where('id_kendaraan', $id_kendaraan)->delete();
            Asuransi::where('id_kendaraan', $id_kendaraan)->delete();
            CekFisik::where('id_kendaraan', $id_kendaraan)->delete();
            ServisRutin::where('id_kendaraan', $id_kendaraan)->delete();
            ServisInsidental::where('id_kendaraan', $id_kendaraan)->delete();
            BBM::where('id_kendaraan', $id_kendaraan)->delete();
            Peminjaman::where('id_kendaraan', $id_kendaraan)->delete();
            $kendaraan->delete();
            Log::info('DEBUG_KENDARAAN_DELETE: Kendaraan dan semua data terkait berhasil dihapus', ['id_kendaraan' => $id_kendaraan]);
    
            $search = $request->query('search', '');
            $search = preg_replace('/\?page=\d+/', '', $search); 

            return redirect()->route('kendaraan.daftar_kendaraan', [
                'page' => $request->query('page', 1),
                'search' => $search
            ])->with('success', 'Kendaraan dan semua data terkait berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('DEBUG_KENDARAAN_DELETE: Error saat menghapus kendaraan', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus kendaraan!']);
        }
    }
}
