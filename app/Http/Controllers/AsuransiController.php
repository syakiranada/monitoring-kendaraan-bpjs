<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asuransi;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class AsuransiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
    
        // Ambil data kendaraan dengan left join ke tabel asuransi
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
            DB::raw('MAX(asuransi.tgl_perlindungan_akhir) as tgl_jatuh_tempo') // Tidak perlu +1 tahun, cukup MAX(tgl_perlindungan_akhir)
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
    
        // Tentukan status untuk setiap kendaraan berdasarkan tgl_jatuh_tempo
        $dataKendaraan = $dataKendaraanQuery->get();
    
        foreach ($dataKendaraan as $item) {
            $today = now(); 
            $dueDate = $item->tgl_jatuh_tempo ? \Carbon\Carbon::parse($item->tgl_jatuh_tempo) : null;
    
            // Tentukan status berdasarkan perhitungan tanggal jatuh tempo
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
    
        // Filter berdasarkan pencarian (plat nomor, merk, tipe, dan status)
        if (!empty($search)) {
            $dataKendaraan = $dataKendaraan->filter(function ($item) use ($search) {
                $kendaraanInfo = strtolower($item->merk . ' ' . $item->tipe);
                return stripos($kendaraanInfo, strtolower($search)) !== false ||
                       stripos($item->plat_nomor, strtolower($search)) !== false ||
                       stripos($item->status, strtolower($search)) !== false;
            });
        }
    
        // Filter berdasarkan status (dropdown filter)
        if (!empty($statusFilter)) {
            $dataKendaraan = $dataKendaraan->filter(function ($item) use ($statusFilter) {
                return strtolower($item->status) == strtolower($statusFilter);
            });
        }
    
        // Paginasi data setelah filtering
        $dataKendaraan = new \Illuminate\Pagination\LengthAwarePaginator(
            $dataKendaraan->forPage($request->page, 10), // 10 items per page
            $dataKendaraan->count(), // Total items
            10, // Items per page
            $request->page, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Preserve URL query parameters
        );
    
        // Kembalikan data ke view
        return view('admin.asuransi.daftar_kendaraan_asuransi', compact('dataKendaraan', 'search', 'statusFilter'));
    }
    

    public function kelola($id_kendaraan)
    {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);
        return view('admin.asuransi.kelola', compact('kendaraan'));
    }

    public function detail($id_asuransi) {
        $asuransi = Asuransi::with('kendaraan')
            ->where('id_asuransi', $id_asuransi)
            ->firstOrFail();
        
        // Ambil record asuransi sebelumnya berdasarkan id_asuransi yang lebih kecil
        $previousAsuransi = Asuransi::where('id_kendaraan', $asuransi->id_kendaraan)
            ->where('id_asuransi', '<', $id_asuransi)  // Menjaga agar mengambil yang sebelumnya
            ->orderBy('id_asuransi', 'desc')  // Urutkan berdasarkan id_asuransi secara menurun
            ->first();
        
        if ($previousAsuransi) {
            $asuransi->tgl_jatuh_tempo = $previousAsuransi->tgl_perlindungan_akhir;
        } else {
            $asuransi->tgl_jatuh_tempo = null;  // Bisa disesuaikan jika tidak ada record sebelumnya
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

        // Pastikan halaman yang sedang digunakan
        $page = $request->input('current_page', 1);

        // Cari kendaraan
        $kendaraan = Kendaraan::findOrFail($request->id_kendaraan);

        // Simpan file ke penyimpanan publik
        $polisPath = $request->file('foto_polis')->store('polis', 'public');
        $buktiPath = $request->file('bukti_bayar_asuransi')->store('bukti_bayar_asuransi', 'public');

        // Simpan data asuransi
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
        // return response()->json([
        //     'success' => true,
        //     'redirect' => route('asuransi.daftar_kendaraan_asuransi', ['page' => $page]),
        //     'message' => 'Data asuransi berhasil disimpan!'
        // ]);

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

        // 🛑 Cek jika pengguna ingin menghapus file polis
        if ($request->has('delete_polis') && $request->delete_polis == 1) {
            if ($asuransi->polis && Storage::disk('public')->exists($asuransi->polis)) {
                Storage::disk('public')->delete($asuransi->polis);
            }
            $asuransi->polis = null;
        }

        // 🛑 Cek jika pengguna ingin menghapus file bukti bayar
        if ($request->has('delete_bukti_bayar_asuransi') && $request->delete_bukti_bayar_asuransi == 1) {
            if ($asuransi->bukti_bayar_asuransi && Storage::disk('public')->exists($asuransi->bukti_bayar_asuransi)) {
                Storage::disk('public')->delete($asuransi->bukti_bayar_asuransi);
            }
            $asuransi->bukti_bayar_asuransi = null;
        }

        // 📂 Handle Upload Polis (Jika Ada)
        if ($request->hasFile('foto_polis')) {
            if ($asuransi->polis && Storage::disk('public')->exists($asuransi->polis)) {
                Storage::disk('public')->delete($asuransi->polis);
            }
            $polisPath = $request->file('foto_polis')->store('polis', 'public');
            $asuransi->polis = $polisPath;
        }

        // 📂 Handle Upload Bukti Pembayaran (Jika Ada)
        if ($request->hasFile('bukti_bayar_asuransi')) {
            if ($asuransi->bukti_bayar_asuransi && Storage::disk('public')->exists($asuransi->bukti_bayar_asuransi)) {
                Storage::disk('public')->delete($asuransi->bukti_bayar_asuransi);
            }
            $buktiPath = $request->file('bukti_bayar_asuransi')->store('bukti_bayar_asuransi', 'public');
            $asuransi->bukti_bayar_asuransi = $buktiPath;
        }

        // ✏ Update Data Lainnya
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
