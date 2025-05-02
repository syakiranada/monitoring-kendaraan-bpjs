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
        
        // Cek apakah pencarian mengandung kata kunci status
        $statusKeywords = [
            'JATUH TEMPO' => ['jatuh tempo', 'jatuhtempo', 'jatuh', 'tempo'],
            'MENDEKATI JATUH TEMPO' => ['mendekati jatuh tempo', 'mendekati', 'dekat', 'hampir', 'mendekat'],
            'BELUM ADA DATA ASURANSI' => ['belum ada data', 'belum', 'data', 'asuransi', 'tidak ada', 'kosong'],
            'SUDAH DIBAYAR' => ['sudah bayar', 'sudah', 'bayar', 'dibayar', 'lunas', 'terbayar', 'byr']
        ];
        
        // Ekstrak kata kunci status dari pencarian jika ada
        $searchLower = strtolower($search);
        $detectedStatus = null;
        $cleanSearch = $search;
        
        // Mencari frasa lengkap terlebih dahulu (seperti "jatuh tempo") 
        // sebelum mencari kata-kata individual
        foreach ($statusKeywords as $status => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($searchLower, $keyword) !== false) {
                    $detectedStatus = $status;
                    // Hapus kata kunci status dari pencarian
                    $cleanSearch = trim(preg_replace('/\b'.preg_quote($keyword, '/').'\b/ui', '', $search));
                    
                    // Hapus spasi ganda yang mungkin dihasilkan
                    $cleanSearch = preg_replace('/\s+/', ' ', $cleanSearch);
                    break 2;
                }
            }
        }
        
        // Base query dengan join tabel yang diperlukan di awal
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
        ->leftJoin(DB::raw('
            (SELECT id_kendaraan, MAX(tgl_perlindungan_akhir) as tgl_jatuh_tempo 
            FROM asuransi 
            GROUP BY id_kendaraan) as latest_asuransi'), function ($join) {
            $join->on('kendaraan.id_kendaraan', '=', 'latest_asuransi.id_kendaraan');
        })
        ->leftJoin('asuransi', function ($join) {
            $join->on('kendaraan.id_kendaraan', '=', 'asuransi.id_kendaraan')
                ->on('asuransi.tgl_perlindungan_akhir', '=', 'latest_asuransi.tgl_jatuh_tempo');
        })
        ->where('kendaraan.aset', '!=', 'lelang');
        
        // Pencarian untuk data kendaraan (menggunakan cleanSearch tanpa kata kunci status)
        if (!empty($cleanSearch)) {
            $keywords = preg_split('/\s+/', $cleanSearch); // Pecah pencarian berdasarkan spasi
            
            $dataKendaraanQuery->where(function($q) use ($keywords) {
                foreach ($keywords as $word) {
                    if (!empty($word)) { // Skip kata kosong
                        $q->where(function($q2) use ($word) {
                            // Pencarian dasar
                            $q2->where('kendaraan.merk', 'like', "%{$word}%")
                            ->orWhere('kendaraan.tipe', 'like', "%{$word}%")
                            ->orWhere('kendaraan.plat_nomor', 'like', "%{$word}%")
                            ->orWhere('asuransi.polis', 'like', "%{$word}%");
                            
                            // Pencarian numerik
                            $numericWord = preg_replace('/[.,]/', '', $word);
                            if (is_numeric($numericWord)) {
                                $q2->orWhere('asuransi.tahun', 'like', "%{$numericWord}%")
                                ->orWhere('asuransi.nominal', 'like', "%{$numericWord}%")
                                ->orWhere('asuransi.biaya_asuransi_lain', 'like', "%{$numericWord}%");
                            }
                            
                            // Pencarian tanggal dengan format yang berbeda
                            $q2->orWhereRaw('DATE_FORMAT(asuransi.tgl_bayar, "%d-%m-%Y") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_bayar, "%d/%m/%Y") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_bayar, "%Y") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_bayar, "%m") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_bayar, "%d") like ?', ["%{$word}%"]);
                            
                            $q2->orWhereRaw('DATE_FORMAT(asuransi.tgl_perlindungan_awal, "%d-%m-%Y") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_perlindungan_awal, "%d/%m/%Y") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_perlindungan_awal, "%Y") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_perlindungan_awal, "%m") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_perlindungan_awal, "%d") like ?', ["%{$word}%"]);
                            
                            $q2->orWhereRaw('DATE_FORMAT(asuransi.tgl_perlindungan_akhir, "%d-%m-%Y") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_perlindungan_akhir, "%d/%m/%Y") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_perlindungan_akhir, "%Y") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_perlindungan_akhir, "%m") like ?', ["%{$word}%"])
                            ->orWhereRaw('DATE_FORMAT(asuransi.tgl_perlindungan_akhir, "%d") like ?', ["%{$word}%"]);
                        });
                    }
                }
            });
        } else if (!empty($detectedStatus) && empty($cleanSearch)) {
            // Jika hanya ada kata kunci status tanpa kata kunci pencarian lainnya,
            // ambil semua data agar bisa difilter berdasarkan status nanti
        }
        
        // Group by untuk menghindari duplikasi
        $dataKendaraanQuery->groupBy(
            'kendaraan.id_kendaraan', 
            'asuransi.id_asuransi', 
            'asuransi.user_id', 
            'asuransi.tahun', 
            'asuransi.tgl_bayar', 
            'asuransi.tgl_perlindungan_awal', 
            'asuransi.tgl_perlindungan_akhir', 
            'asuransi.polis', 
            'asuransi.bukti_bayar_asuransi', 
            'asuransi.nominal', 
            'asuransi.biaya_asuransi_lain'
        );
        
        // Dapatkan data
        $dataKendaraan = $dataKendaraanQuery->get();
        
        // Menentukan status kendaraan
        $today = now();
        foreach ($dataKendaraan as $item) {
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

        // Filter berdasarkan status yang terdeteksi dari pencarian
        if ($detectedStatus) {
            $dataKendaraan = $dataKendaraan->filter(function ($item) use ($detectedStatus) {
                return $item->status == $detectedStatus;
            });
        }
        
        // Filter berdasarkan status dari dropdown jika ada
        if (!empty($statusFilter)) {
            $dataKendaraan = $dataKendaraan->filter(function ($item) use ($statusFilter) {
                return strtolower($item->status) == strtolower($statusFilter);
            });
        }
        
        // Debug: Tambahkan debugging khusus untuk pencarian "toyota avanza b1234xxx jatuh tempo"
        if (stripos($search, 'toyota') !== false && stripos($search, 'avanza') !== false && 
            stripos($search, 'jatuh tempo') !== false) {
            
            // Cari "toyota avanza" tanpa filter status dulu
            $debugQuery = Kendaraan::select('kendaraan.*')->where(function($q) {
                $q->where('kendaraan.merk', 'like', '%toyota%')
                ->orWhere('kendaraan.tipe', 'like', '%avanza%');
            })->get();
        }
        
        // Paginasi hasil
        $dataKendaraan = new \Illuminate\Pagination\LengthAwarePaginator(
            $dataKendaraan->forPage($request->page ?? 1, 10), 
            $dataKendaraan->count(), 
            10, 
            $request->page ?? 1, 
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
        $asuransi = Asuransi::with(['kendaraan', 'user']) 
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
            ->route('asuransi.daftar_kendaraan_asuransi', [
                'page' => $page,
                'search' => $request->query('search', request()->input('search', ''))
            ])
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
            $search = $request->query('search', request()->input('search', ''));
            
            return redirect()
                ->route('asuransi.daftar_kendaraan_asuransi', [
                    'page' => $page,
                    'search' => $search ?: null 
                ])
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
            $fileDeleted = false;
    
            if ($fileType === 'polis' && $asuransi->polis) {
                if (Storage::disk('public')->exists($asuransi->polis)) {
                    Storage::disk('public')->delete($asuransi->polis);
                }
                $asuransi->polis = null;
                $fileDeleted = true;
            } elseif ($fileType === 'bukti_bayar_asuransi' && $asuransi->bukti_bayar_asuransi) {
                if (Storage::disk('public')->exists($asuransi->bukti_bayar_asuransi)) {
                    Storage::disk('public')->delete($asuransi->bukti_bayar_asuransi);
                }
                $asuransi->bukti_bayar_asuransi = null;
                $fileDeleted = true;
            }
    
            if ($fileDeleted) {
                $asuransi->save();
                return response()->json(['success' => 'File berhasil dihapus']);
            }
    
            return response()->json(['success' => 'File sudah kosong, tidak perlu dihapus lagi']);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function hapus($id_asuransi, Request $request)
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
            $search = $request->query('search', request()->input('search', ''));
            $search = preg_replace('/\?page=\d+/', '', $search); 

            return redirect()->route('asuransi.daftar_kendaraan_asuransi', [
                'page' => $request->query('page', 1),
                'search' => $search ?: null
            ])->with('success', 'Data asuransi berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat menghapus asuransi: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data!']);
        }
    }
}