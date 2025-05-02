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

        // Tambahkan semua join di awal
        $query = Kendaraan::leftJoin('pajak', function($join) {
            $join->on('kendaraan.id_kendaraan', '=', 'pajak.id_kendaraan')
                ->whereRaw('pajak.id_pajak = (SELECT MAX(p2.id_pajak) FROM pajak p2 WHERE p2.id_kendaraan = kendaraan.id_kendaraan)');
        })
        ->leftJoin('asuransi', function($join) {
            $join->on('kendaraan.id_kendaraan', '=', 'asuransi.id_kendaraan')
                ->whereRaw('asuransi.id_asuransi = (SELECT MAX(a2.id_asuransi) FROM asuransi a2 WHERE a2.id_kendaraan = kendaraan.id_kendaraan)');
        })
        ->leftJoin('servis_rutin', function($join) {
            $join->on('kendaraan.id_kendaraan', '=', 'servis_rutin.id_kendaraan')
                ->whereRaw('servis_rutin.id_servis_rutin = (SELECT MAX(s2.id_servis_rutin) FROM servis_rutin s2 WHERE s2.id_kendaraan = kendaraan.id_kendaraan)');
        })
        ->leftJoin('cek_fisik', function($join) {
            $join->on('kendaraan.id_kendaraan', '=', 'cek_fisik.id_kendaraan')
                ->whereRaw('cek_fisik.id_cek_fisik = (SELECT MAX(c2.id_cek_fisik) FROM cek_fisik c2 WHERE c2.id_kendaraan = kendaraan.id_kendaraan)');
        })
        ->select('kendaraan.*');

        if ($search) {
            // Filter eksplisit dulu
            if (stripos($search, 'tidak tersedia') !== false) {
                $query->where('kendaraan.status_ketersediaan', '=', 'tidak tersedia');
                $search = trim(str_ireplace('tidak tersedia', '', $search));
            } elseif (stripos($search, 'tersedia') !== false) {
                $query->where('kendaraan.status_ketersediaan', '=', 'tersedia');
                $search = trim(str_ireplace('tersedia', '', $search));
            }

            if (stripos($search, 'tidak guna') !== false) {
                $query->where('kendaraan.aset', '=', 'tidak guna');
                $search = trim(str_ireplace('tidak guna', '', $search));
            } elseif (stripos($search, 'guna') !== false) {
                $query->where('kendaraan.aset', '=', 'guna');
                $search = trim(str_ireplace('guna', '', $search));
            } elseif (stripos($search, 'lelang') !== false) {
                $query->where('kendaraan.aset', '=', 'lelang');
                $search = trim(str_ireplace('lelang', '', $search));
            } elseif (stripos($search, 'jual') !== false) {
                $query->where('kendaraan.aset', '=', 'jual');
                $search = trim(str_ireplace('jual', '', $search));
            }
            $keywords = preg_split('/\s+/', $search); // Pecah pencarian berdasarkan spasi
            
            $query->where(function($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function($q2) use ($word) {
                        // Pencarian dasar
                        $q2->where('kendaraan.plat_nomor', 'like', "%{$word}%")
                        ->orWhere('kendaraan.merk', 'like', "%{$word}%")
                        ->orWhere('kendaraan.tipe', 'like', "%{$word}%")
                        ->orWhere('kendaraan.warna', 'like', "%{$word}%")
                        ->orWhere('kendaraan.jenis', 'like', "%{$word}%")
                        ->orWhere('kendaraan.aset', 'like', "%{$word}%")
                        ->orWhere('kendaraan.bahan_bakar', 'like', "%{$word}%")
                        ->orWhere('kendaraan.no_mesin', 'like', "%{$word}%")
                        ->orWhere('kendaraan.no_rangka', 'like', "%{$word}%")
                        ->orWhere('kendaraan.frekuensi_servis', 'like', "%{$word}%")
                        ->orWhere('kendaraan.status_ketersediaan', 'like', "%{$word}%");
                        
                        // Pencarian angka (kapasitas dan nilai keuangan)
                        $q2->orWhere('kendaraan.kapasitas', 'like', "%{$word}%")
                        ->orWhereRaw("REPLACE(REPLACE(CAST(kendaraan.nilai_perolehan AS CHAR), '.', ''), ',', '') LIKE ?", ["%{$word}%"])
                        ->orWhereRaw("REPLACE(REPLACE(CAST(kendaraan.nilai_buku AS CHAR), '.', ''), ',', '') LIKE ?", ["%{$word}%"]);
                        
                        // Pencarian tanggal di kendaraan
                        $q2->orWhereRaw('DATE_FORMAT(kendaraan.tgl_pembelian, "%d-%m-%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(kendaraan.tgl_pembelian, "%d/%m/%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(kendaraan.tgl_pembelian, "%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(kendaraan.tgl_pembelian, "%m") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(kendaraan.tgl_pembelian, "%d") like ?', ["%{$word}%"]);
                        
                        // Pencarian tanggal di pajak
                        $q2->orWhereRaw('DATE_FORMAT(pajak.tgl_bayar, "%d-%m-%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(pajak.tgl_bayar, "%d/%m/%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(pajak.tgl_bayar, "%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(pajak.tgl_bayar, "%m") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(pajak.tgl_bayar, "%d") like ?', ["%{$word}%"]);
                        
                        // Pencarian tanggal di asuransi
                        $q2->orWhereRaw('DATE_FORMAT(asuransi.tgl_bayar, "%d-%m-%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(asuransi.tgl_bayar, "%d/%m/%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(asuransi.tgl_bayar, "%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(asuransi.tgl_bayar, "%m") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(asuransi.tgl_bayar, "%d") like ?', ["%{$word}%"]);
                        
                        // Pencarian tanggal di servis_rutin
                        $q2->orWhereRaw('DATE_FORMAT(servis_rutin.tgl_servis_real, "%d-%m-%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(servis_rutin.tgl_servis_real, "%d/%m/%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(servis_rutin.tgl_servis_real, "%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(servis_rutin.tgl_servis_real, "%m") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(servis_rutin.tgl_servis_real, "%d") like ?', ["%{$word}%"]);
                        
                        // Pencarian tanggal di cek_fisik
                        $q2->orWhereRaw('DATE_FORMAT(cek_fisik.tgl_cek_fisik, "%d-%m-%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(cek_fisik.tgl_cek_fisik, "%d/%m/%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(cek_fisik.tgl_cek_fisik, "%Y") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(cek_fisik.tgl_cek_fisik, "%m") like ?', ["%{$word}%"])
                        ->orWhereRaw('DATE_FORMAT(cek_fisik.tgl_cek_fisik, "%d") like ?', ["%{$word}%"]);
                    });
                }
            });
        }

       // Simpan query pencarian untuk hasil tampilan utama
        $searchResults = $query->get();

        // Buat query baru yang mengambil semua kendaraan (tanpa filter pencarian)
        $allKendaraan = Kendaraan::all(); // Atau gunakan model yang sesuai
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

        // Kemudian gunakan $searchResults untuk hasil pencarian yang ditampilkan
        // dan $alerts untuk peringatan

        // Pastikan tidak ada duplikasi dengan groupBy
        $dataKendaraan = $query->groupBy('kendaraan.id_kendaraan')->paginate(10)->appends(['search' => $search]);
        
        return view('admin.kendaraan.daftar_kendaraan', compact('dataKendaraan', 'search', 'alerts'));
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
                'Merk' => 'required|string|max:255',
                'Tipe' => 'required|string|max:255',
                'Plat_Nomor' => 'required|string|max:20',
                'Warna' => 'required|string|max:50',
                'jenis_kendaraan' => 'required|string',
                'aset_guna' => 'required|string',
                'Kapasitas' => 'required|integer|min:1',
                'Tanggal_Beli' => 'required|date',
                'Nilai_Perolehan' => 'required|numeric',
                'Nilai_Buku' => 'required|numeric',
                'bahan_bakar' => 'required|string',
                'Nomor_Mesin' => 'required|string|max:100',
                'Nomor_Rangka' => 'required|string|max:100',
                'Tanggal_Bayar_Pajak' => 'required|date',
                'Tanggal_Jatuh_Tempo_Pajak' => 'required|date',
                'Tanggal_Cek_Fisik' => 'required|date',
                'Frekuensi' => 'required|integer|min:1',
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
                'merk' => $request->{'Merk'},
                'tipe' => $request->{'Tipe'},
                'plat_nomor' => $request->{'Plat_Nomor'},
                'warna' => $request->{'Warna'},
                'jenis' => $request->jenis_kendaraan,
                'aset' => $request->aset_guna,
                'kapasitas' => $request->{'Kapasitas'},
                'tgl_pembelian' => $request->{'Tanggal_Beli'},
                'nilai_perolehan' => $request->{'Nilai_Perolehan'},
                'nilai_buku' => $request->{'Nilai_Buku'},
                'bahan_bakar' => $request->bahan_bakar,
                'no_mesin' => $request->{'Nomor_Mesin'},
                'no_rangka' => $request->{'Nomor_Rangka'},
                'frekuensi_servis' => $request->{'Frekuensi'},
                'status_ketersediaan' => $statusKetersediaan, 
            ]);
    
            Pajak::create([
                'user_id' => Auth::id(),
                'id_kendaraan' => $kendaraan->id_kendaraan, 
                'tgl_bayar' => date('Y-m-d', strtotime($request->{'Tanggal_Bayar_Pajak'})),
                'tgl_jatuh_tempo' => date('Y-m-d', strtotime($request->{'Tanggal_Jatuh_Tempo_Pajak'})),
                'tahun' => date('Y', strtotime($request->{'Tanggal_Bayar_Pajak'})),
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
                'tgl_cek_fisik' => $request->{'Tanggal_Cek_Fisik'},
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
                'Merk' => 'required|string|max:255',
                'Tipe' => 'required|string|max:255',
                'Plat_Nomor' => 'required|string|max:20',
                'Warna' => 'required|string|max:50',
                'jenis_kendaraan' => 'required|string',
                'aset_guna' => 'required|string',
                'Kapasitas' => 'required|integer|min:1',
                'Tanggal_Beli' => 'required|date',
                'Nilai_Perolehan' => 'required|numeric',
                'Nilai_Buku' => 'required|numeric',
                'bahan_bakar' => 'required|string',
                'Nomor_Mesin' => 'required|string|max:100',
                'Nomor_Rangka' => 'required|string|max:100',
                'Tanggal_Bayar_Pajak' => 'required|date',
                'Tanggal_Jatuh_Tempo_Pajak' => 'required|date',
                'Tanggal_Cek_Fisik' => 'required|date',
                'Frekuensi' => 'required|integer|min:1',
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
                'merk' => $request->{'Merk'},
                'tipe' => $request->{'Tipe'},
                'plat_nomor' => $request->{'Plat_Nomor'},
                'warna' => $request->{'Warna'},
                'jenis' => $request->jenis_kendaraan,
                'aset' => $request->aset_guna,
                'kapasitas' => $request->{'Kapasitas'},
                'tgl_pembelian' => $request->{'Tanggal_Beli'},
                'nilai_perolehan' => $request->{'Nilai_Perolehan'},
                'nilai_buku' => $request->{'Nilai_Buku'},
                'bahan_bakar' => $request->bahan_bakar,
                'no_mesin' => $request->{'Nomor_Mesin'},
                'no_rangka' => $request->{'Nomor_Rangka'},
                'frekuensi_servis' => $request->{'Frekuensi'},
                'status_ketersediaan' => $statusKetersediaan,
            ]);

            Pajak::updateOrCreate(
                ['id_kendaraan' => $kendaraan->id_kendaraan],
                [
                    'user_id' => Auth::id(),
                    'tgl_bayar' => date('Y-m-d', strtotime($request->{'Tanggal_Bayar_Pajak'})),
                    'tgl_jatuh_tempo' => date('Y-m-d', strtotime($request->{'Tanggal_Jatuh_Tempo_Pajak'})),
                    'tahun' => date('Y', strtotime($request->{'Tanggal_Bayar_Pajak'})),
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
                    'tgl_cek_fisik' => $request->{'Tanggal_Cek_Fisik'},
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

    public function detail($id_kendaraan, Request $request) {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);
        $cekFisik = CekFisik::where('id_kendaraan', $id_kendaraan)->latest('tgl_cek_fisik')->first();
        $pajak = Pajak::where('id_kendaraan', $id_kendaraan)->latest('tgl_bayar')->first();
        $asuransi = Asuransi::where('id_kendaraan', $id_kendaraan)->latest('tgl_bayar')->first();
        $bbm = BBM::where('id_kendaraan', $id_kendaraan)->latest('tgl_isi')->first(); 
        $servisRutin = ServisRutin::where('id_kendaraan', $id_kendaraan)->latest('tgl_servis_real')->first();
    
        $page = $request->query('page');
        $search = $request->query('search');
        return view('admin.kendaraan.detail', compact('kendaraan', 'cekFisik', 'pajak', 'asuransi', 'bbm', 'servisRutin', 'page', 'search'));
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