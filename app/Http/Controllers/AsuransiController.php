<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asuransi;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AsuransiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $dataKendaraanQuery = Kendaraan::select(
            'kendaraan.*',
            'asuransi.id_asuransi', 
            'asuransi.user_id', 
            'asuransi.tahun', 
            'asuransi.tgl_bayar', 
            'asuransi.tgl_perlindungan_awal', 
            'asuransi.tgl_perlindungan_akhir', 
            'asuransi.polis', 
            'asuransi.bukti_bayar_asuransi', 
            'asuransi.nominal', 
            'asuransi.biaya_asuransi_lain',
            DB::raw('MAX(asuransi.tgl_perlindungan_akhir) as tgl_jatuh_tempo') 
        )
        ->distinct()
        ->leftJoin(DB::raw('(SELECT id_kendaraan, MAX(tgl_bayar) as max_bayar, MAX(tgl_perlindungan_akhir) as max_perlindungan_akhir 
                        FROM asuransi GROUP BY id_kendaraan) as latest_asuransi'), function ($join) {
            $join->on('kendaraan.id_kendaraan', '=', 'latest_asuransi.id_kendaraan');
        })
        ->leftJoin('asuransi', function ($join) {
            $join->on('kendaraan.id_kendaraan', '=', 'asuransi.id_kendaraan')
                ->on('asuransi.tgl_bayar', '=', 'latest_asuransi.max_bayar')
                ->on('asuransi.tgl_perlindungan_akhir', '=', 'latest_asuransi.max_perlindungan_akhir');
        })
        ->groupBy('kendaraan.id_kendaraan', 'asuransi.id_asuransi', 'asuransi.user_id', 'asuransi.tahun', 'asuransi.tgl_bayar', 'asuransi.tgl_perlindungan_awal', 'asuransi.tgl_perlindungan_akhir', 'asuransi.polis', 'asuransi.bukti_bayar_asuransi', 'asuransi.nominal', 'asuransi.biaya_asuransi_lain'); // Pastikan semua kolom yang ada dalam select di group by


        $dataKendaraan = $dataKendaraanQuery->get();
    
        foreach ($dataKendaraan as $item) {
            $today = now(); 
            $dueDate = $item->tgl_jatuh_tempo ? \Carbon\Carbon::parse($item->tgl_jatuh_tempo) : null;
    
            if (!$dueDate) {
                $item->status = 'BELUM ADA DATA ASURANSI';
            } elseif ($today->diffInDays($dueDate, false) <= 0) { 
                $item->status = 'JATUH TEMPO';
            } elseif ($today->diffInDays($dueDate, false) <= 30) {
                $item->status = 'MENDEKATI JATUH TEMPO';
            } else {
                $item->status = 'SUDAH DIBAYAR';
            }
        }
    
        if (!empty($search)) {
            $dataKendaraan = $dataKendaraan->filter(function ($item) use ($search) {
                $search = strtolower($search);
                $formatTanggal = function ($tanggal) {
                    return $tanggal ? \Carbon\Carbon::parse($tanggal)->format('d-m-Y') : null;
                };
    
                $tgl_bayar = $formatTanggal($item->tgl_bayar);
                $tgl_perlindungan_awal = $formatTanggal($item->tgl_perlindungan_awal);
                $tgl_perlindungan_akhir = $formatTanggal($item->tgl_perlindungan_akhir);
    
                $normalizeNumber = function ($number) {
                    return preg_replace('/[.,]/', '', (string) $number);
                };
    
                $nominal = $normalizeNumber($item->nominal);
                $biayaAsuransiLain = $normalizeNumber($item->biaya_asuransi_lain);
                $searchNumeric = $normalizeNumber($search);
    
                $kombinasiPencarian = [
                    strtolower($item->merk . ' ' . $item->tipe . ' ' . $item->plat_nomor),
                    strtolower($item->tipe . ' ' . $item->plat_nomor),
                    strtolower($item->merk . ' ' . $item->tipe . ' ' . $item->tahun),
                    strtolower($item->merk . ' ' . $item->tipe . ' ' . $item->status),
                    strtolower($item->merk . ' ' . $item->plat_nomor),
                    strtolower($item->tipe . ' ' . $item->tahun),
                    strtolower($item->plat_nomor . ' ' . $item->tahun),
                    strtolower($item->merk . ' ' . $item->tipe . ' ' . $tgl_perlindungan_awal),
                    strtolower($item->merk . ' ' . $item->tipe . ' ' . $tgl_perlindungan_akhir),
                    strtolower($item->merk . ' ' . $item->tipe . ' ' . $nominal),
                    strtolower($item->merk . ' ' . $item->tipe . ' ' . $biayaAsuransiLain),
                    strtolower($item->merk . ' ' . $item->tipe . ' ' . $tgl_bayar),
                    strtolower($item->merk . ' ' . $item->tipe . ' ' . $item->polis),
                ];
    
                foreach ($kombinasiPencarian as $kombinasi) {
                    if (stripos($kombinasi, $search) !== false) {
                        return true;
                    }
                }
    
                return stripos($item->status, $search) !== false ||
                    stripos((string) $item->tahun, $search) !== false ||
                    stripos((string) $tgl_bayar, $search) !== false ||
                    stripos((string) $tgl_perlindungan_awal, $search) !== false ||
                    stripos((string) $tgl_perlindungan_akhir, $search) !== false ||
                    stripos((string) $item->polis, $search) !== false ||
                    stripos($nominal, $searchNumeric) !== false ||
                    stripos($biayaAsuransiLain, $searchNumeric) !== false;
            });
        }
    
        if (!empty($statusFilter)) {
            $dataKendaraan = $dataKendaraan->filter(function ($item) use ($statusFilter) {
                return strtolower($item->status) == strtolower($statusFilter);
            });
        }
    
        $dataKendaraan = new \Illuminate\Pagination\LengthAwarePaginator(
            $dataKendaraan->forPage($request->page, 10), 
            $dataKendaraan->count(), 
            10, 
            $request->page, 
            ['path' => $request->url(), 'query' => $request->query()] 
        );
    
        return view('admin.asuransi.daftar_kendaraan_asuransi', compact('dataKendaraan', 'search', 'statusFilter'));
    }
    
    public function kelola($id_kendaraan)
    {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);
        return view('admin.asuransi.kelola', compact('kendaraan'));
    }

    public function detail($id_asuransi) 
    {
        $asuransi = Asuransi::with('kendaraan')
            ->where('id_asuransi', $id_asuransi)
            ->firstOrFail();
        
        $previousAsuransi = Asuransi::where('id_kendaraan', $asuransi->id_kendaraan)
            ->where('id_asuransi', '<', $id_asuransi) 
            ->orderBy('id_asuransi', 'desc') 
            ->first();
        
        if ($previousAsuransi) {
            $asuransi->tgl_jatuh_tempo = $previousAsuransi->tgl_perlindungan_akhir;
        } else {
            $asuransi->tgl_jatuh_tempo = null;
        }
    
        return view('admin.asuransi.detail', compact('asuransi'));
    }
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
                'tanggal_bayar' => 'required|date_format:Y-m-d',
                'tgl_perlindungan_awal' => 'required|date',
                'tgl_perlindungan_akhir' => 'required|date|after:tgl_perlindungan_awal',
                'nominal_tagihan' => 'required|numeric',
                'biaya_lain' => 'nullable|numeric',
                'foto_polis' => 'required|mimes:jpeg,jpg,png,pdf|max:5120',
                'bukti_bayar_asuransi' => 'required|mimes:jpeg,jpg,png,pdf|max:5120',
            ]);

            $page = $request->input('current_page', 1);
            $kendaraan = Kendaraan::findOrFail($request->id_kendaraan);
            $polisPath = $request->file('foto_polis')->store('polis', 'public');
            $buktiPath = $request->file('bukti_bayar_asuransi')->store('bukti_bayar_asuransi', 'public');

            $asuransi = Asuransi::create([
                'user_id' => Auth::id(),
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'tahun' => date('Y', strtotime($request->tanggal_bayar)),
                'tgl_bayar' => $request->tanggal_bayar,
                'polis' => $polisPath,
                'bukti_bayar_asuransi' => $buktiPath,
                'tgl_perlindungan_awal' => $request->tgl_perlindungan_awal,
                'tgl_perlindungan_akhir' => $request->tgl_perlindungan_akhir,
                'biaya_asuransi_lain' => $request->biaya_lain ?? 0,
                'nominal' => $request->nominal_tagihan,
            ]);

            return redirect()
            ->route('asuransi.daftar_kendaraan_asuransi', ['page' => $page])
            ->with('success', 'Data asuransi berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data asuransi!');
        }
    }


    public function edit($id_asuransi)
    {
        $asuransi = Asuransi::with('kendaraan')->where('id_asuransi', $id_asuransi)->firstOrFail();
        return view('admin.asuransi.edit', compact('asuransi'));
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'tanggal_bayar' => 'required|date',
                'tgl_perlindungan_awal' => 'required|date',
                'tgl_perlindungan_akhir' => 'required|date|after:tgl_perlindungan_awal',
                'nominal_tagihan' => 'required|numeric',
                'biaya_lain' => 'nullable|numeric',
                'foto_polis' => 'nullable|mimes:jpeg,jpg,png,pdf|max:5120',
                'bukti_bayar_asuransi' => 'nullable|mimes:jpeg,jpg,png,pdf|max:5120',
            ]);

            $asuransi = Asuransi::findOrFail($id);

            if ($request->has('delete_polis') && $request->delete_polis == 1) {
                if ($asuransi->polis && Storage::disk('public')->exists($asuransi->polis)) {
                    Storage::disk('public')->delete($asuransi->polis);
                }
                $asuransi->polis = null;
            }

            if ($request->has('delete_bukti_bayar_asuransi') && $request->delete_bukti_bayar_asuransi == 1) {
                if ($asuransi->bukti_bayar_asuransi && Storage::disk('public')->exists($asuransi->bukti_bayar_asuransi)) {
                    Storage::disk('public')->delete($asuransi->bukti_bayar_asuransi);
                }
                $asuransi->bukti_bayar_asuransi = null;
            }

            if ($request->hasFile('foto_polis')) {
                if ($asuransi->polis && Storage::disk('public')->exists($asuransi->polis)) {
                    Storage::disk('public')->delete($asuransi->polis);
                }
                $polisPath = $request->file('foto_polis')->store('polis', 'public');
                $asuransi->polis = $polisPath;
            }

            if ($request->hasFile('bukti_bayar_asuransi')) {
                if ($asuransi->bukti_bayar_asuransi && Storage::disk('public')->exists($asuransi->bukti_bayar_asuransi)) {
                    Storage::disk('public')->delete($asuransi->bukti_bayar_asuransi);
                }
                $buktiPath = $request->file('bukti_bayar_asuransi')->store('bukti_bayar_asuransi', 'public');
                $asuransi->bukti_bayar_asuransi = $buktiPath;
            }

            $asuransi->tgl_bayar = $request->tanggal_bayar;
            $asuransi->tgl_perlindungan_awal = $request->tgl_perlindungan_awal;
            $asuransi->tgl_perlindungan_akhir = $request->tgl_perlindungan_akhir;
            $asuransi->nominal = $request->nominal_tagihan;
            $asuransi->biaya_asuransi_lain = $request->biaya_lain ?? 0;

            $asuransi->save();

            $page = $request->input('current_page', 1);
            return redirect()
                ->route('asuransi.daftar_kendaraan_asuransi', ['page' => $page])
                ->with('success', 'Data asuransi berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function deleteFile(Request $request)
    {
        try {
            $asuransi = Asuransi::findOrFail($request->id);
            $fileType = $request->file_type;

            if ($fileType === 'polis' && $asuransi->polis) {
                Storage::disk('public')->delete($asuransi->polis);
                $asuransi->polis = null;
            } elseif ($fileType === 'bukti_bayar_asuransi' && $asuransi->bukti_bayar_asuransi) {
                Storage::disk('public')->delete($asuransi->bukti_bayar_asuransi);
                $asuransi->bukti_bayar_asuransi = null;
            } else {
                return response()->json(['error' => 'File tidak ditemukan'], 404);
            }

            $asuransi->save();

            return response()->json(['success' => 'File berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function hapus($id_asuransi)
    {
        try {
            $asuransi = Asuransi::findOrFail($id_asuransi);

            if ($asuransi->polis && file_exists(storage_path('app/public/' . $asuransi->polis))) {
                unlink(storage_path('app/public/' . $asuransi->polis));
            }

            if ($asuransi->bukti_bayar_asuransi && file_exists(storage_path('app/public/' . $asuransi->bukti_bayar_asuransi))) {
                unlink(storage_path('app/public/' . $asuransi->bukti_bayar_asuransi));
            }

            $asuransi->delete();

            return redirect()->route('asuransi.daftar_kendaraan_asuransi')->with('success', 'Data asuransi berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat menghapus asuransi: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data!']);
        }
    }
}

