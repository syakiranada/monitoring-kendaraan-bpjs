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
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class DaftarKendaraanAdminController extends Controller
{
    
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusKetersediaanFilter = $request->input('status_ketersediaan');
        $dataKendaraanQuery = Kendaraan::query();

        Log::info("Initial search parameters:", [
            'search' => $search,
            'statusKetersediaanFilter' => $statusKetersediaanFilter
        ]);

        $searchDate = null;
        if (!empty($search) && preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) {
            try {
                $searchDate = Carbon::createFromFormat('d-m-Y', $search);
            } catch (\Exception $e) {
                Log::error("Error parsing search date:", ['search' => $search, 'error' => $e->getMessage()]);
            }
        }

        if ($searchDate) {
            $matchingKendaraanIds = [];
            $kendaraanIds = Kendaraan::pluck('id_kendaraan');

            foreach ($kendaraanIds as $id_kendaraan) {
                $latestDates = [
                    CekFisik::where('id_kendaraan', $id_kendaraan)->latest('tgl_cek_fisik')->value('tgl_cek_fisik'),
                    Pajak::where('id_kendaraan', $id_kendaraan)->latest('tgl_bayar')->value('tgl_bayar'),
                    Asuransi::where('id_kendaraan', $id_kendaraan)->latest('tgl_bayar')->value('tgl_bayar'),
                    BBM::where('id_kendaraan', $id_kendaraan)->latest('tgl_isi')->value('tgl_isi'),
                    ServisRutin::where('id_kendaraan', $id_kendaraan)->latest('tgl_servis_real')->value('tgl_servis_real')
                ];

                $latestDates = array_filter($latestDates);

                foreach ($latestDates as $date) {
                    if (Carbon::parse($date)->isSameDay($searchDate)) {
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
        Log::info("Final SQL Query:", ['sql' => $dataKendaraanQuery->toSql(), 'bindings' => $dataKendaraanQuery->getBindings()]);

        $dataKendaraan = $dataKendaraanQuery->paginate(10);
        return view('admin.kendaraan.daftar_kendaraan', compact('dataKendaraan', 'search', 'statusKetersediaanFilter'));
    }


    public function tambah()
    {
        $user_id = Auth::id();
        return view('admin.kendaraan.tambah', compact('user_id'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('DEBUG: Incoming request', ['request_data' => $request->all()]);

            $request->validate([
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
                'tanggal_asuransi' => 'required|date',
                'tanggal_perlindungan_awal' => 'required|date',
                'tanggal_perlindungan_akhir' => 'required|date',
                'tanggal_bayar_pajak' => 'required|date',
                'tanggal_jatuh_tempo_pajak' => 'required|date',
                'tanggal_cek_fisik' => 'required|date',
                'frekuensi' => 'required|integer|min:1',
                'status_pinjam' => 'required|string',
                'current_page' => 'required|integer|min:1',
            ]);

            Log::info('DEBUG: Validation passed');

            $statusKetersediaan = ($request->aset_guna === 'Guna') ? 'TERSEDIA' : 'TIDAK TERSEDIA';

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

            Asuransi::create([
                'user_id' => Auth::id(),
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'tgl_bayar' => $request->tanggal_asuransi,
                'tahun' => date('Y', strtotime($request->tanggal_perlindungan_akhir)),
                'tgl_perlindungan_awal' => $request->tanggal_perlindungan_awal,
                'tgl_perlindungan_akhir' => $request->tanggal_perlindungan_akhir,
            ]);

            CekFisik::create([
                'user_id' => Auth::id(),
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'tgl_cek_fisik' => $request->tanggal_cek_fisik,
            ]);

            $totalKendaraan = Kendaraan::count();
            $perPage = 10;
            $lastPage = ceil($totalKendaraan / $perPage);

            Log::info('DEBUG: Data kendaraan dan terkait berhasil disimpan');

            return redirect()->route('kendaraan.daftar_kendaraan', ['page' => $lastPage])
                            ->with('success', 'Data kendaraan dan semua terkait berhasil disimpan!');
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

            $request->validate([
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
                'tanggal_asuransi' => 'required|date',
                'tanggal_perlindungan_awal' => 'required|date',
                'tanggal_perlindungan_akhir' => 'required|date',
                'tanggal_bayar_pajak' => 'required|date',
                'tanggal_jatuh_tempo_pajak' => 'required|date',
                'tanggal_cek_fisik' => 'required|date',
                'frekuensi' => 'required|integer|min:1',
                'status_pinjam' => 'required|string',
                'current_page' => 'required|integer|min:1',
            ]);

            Log::info('DEBUG: Validation passed');

            $kendaraan = Kendaraan::findOrFail($id);

            $statusKetersediaan = $request->aset_guna;
            
            Log::info('DEBUG: Status kendaraan yang diterima', ['status_pinjam' => $request->aset_guna]);

            if (in_array($request->aset_guna, ['Lelang', 'Jual', 'Tidak Guna'])) {
                $statusKetersediaan = 'TIDAK TERSEDIA';
            }

            Log::info('DEBUG: Status ketersediaan yang diterapkan', ['status_ketersediaan' => $statusKetersediaan]);

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

            CekFisik::updateOrCreate(
                ['id_kendaraan' => $kendaraan->id_kendaraan],
                [
                    'user_id' => Auth::id(),
                    'tgl_cek_fisik' => $request->tanggal_cek_fisik,
                ]
            );

            $currentPage = $request->input('current_page', 1);

            Log::info('DEBUG: Data kendaraan dan terkait berhasil diupdate');

            return redirect()->route('kendaraan.daftar_kendaraan', ['page' => $currentPage])
                            ->with('success', 'Data kendaraan dan semua terkait berhasil diperbarui!');

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

            $page = request()->query('page');
            return redirect()->route('kendaraan.daftar_kendaraan', ['page' => $page])
                ->with('success', 'Kendaraan dan semua data terkait berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('DEBUG_KENDARAAN_DELETE: Error saat menghapus kendaraan', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus kendaraan!']);
        }
    }
}
