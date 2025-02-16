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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class DaftarKendaraanAdminController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    // Ambil semua data kendaraan
    $dataKendaraanQuery = Kendaraan::select('kendaraan.*');

    // 🔍 Filter berdasarkan pencarian (plat nomor, merk, tipe)
    if (!empty($search)) {
        $dataKendaraanQuery->where(function ($query) use ($search) {
            $query->where('plat_nomor', 'LIKE', "%$search%")
                  ->orWhere('merk', 'LIKE', "%$search%")
                  ->orWhere('tipe', 'LIKE', "%$search%")
                  ->orWhere(DB::raw("CONCAT(merk, ' ', tipe)"), 'LIKE', "%$search%");
        });
    }

    // Paginasi
    $dataKendaraan = $dataKendaraanQuery->paginate(10); 

    // Kembalikan data ke view
    return view('admin.kendaraan.daftar_kendaraan', compact('dataKendaraan', 'search'));
}


public function tambah()
{
    $user_id = Auth::id();// Mengambil ID user yang sedang login
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

        // Cek aset_guna untuk menentukan status ketersediaan
        $statusKetersediaan = ($request->aset_guna === 'Guna') ? 'TERSEDIA' : 'TIDAK TERSEDIA';

        // Menyimpan data kendaraan
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
            'status_ketersediaan' => $statusKetersediaan, // Ditentukan otomatis
        ]);

        // Menyimpan data pajak untuk kendaraan
        Pajak::create([
            'user_id' => Auth::id(),
            'id_kendaraan' => $kendaraan->id_kendaraan, // Perbaikan dari id_kendaraan ke id
            'tgl_bayar' => date('Y-m-d', strtotime($request->tanggal_bayar_pajak)),
            'tgl_jatuh_tempo' => date('Y-m-d', strtotime($request->tanggal_jatuh_tempo_pajak)),
            'tahun' => date('Y', strtotime($request->tanggal_bayar_pajak)),
        ]);

        // Menyimpan data asuransi untuk kendaraan
        Asuransi::create([
            'user_id' => Auth::id(),
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'tgl_bayar' => $request->tanggal_asuransi,
            'tahun' => date('Y', strtotime($request->tanggal_perlindungan_akhir)),
            'tgl_perlindungan_awal' => $request->tanggal_perlindungan_awal,
            'tgl_perlindungan_akhir' => $request->tanggal_perlindungan_akhir,
        ]);

        ServisRutin::create([
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'user_id' => Auth::id()
        ]);
        BBM::create([
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'user_id' => Auth::id()
        ]);


        // Menyimpan data cek fisik untuk kendaraan
        CekFisik::create([
            'user_id' => Auth::id(),
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'tgl_cek_fisik' => $request->tanggal_cek_fisik,
        ]);

        // Hitung halaman terakhir untuk redirect
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


// Menampilkan halaman edit pajak
public function edit($id_kendaraan)
{
    // Ambil data kendaraan berdasarkan id_kendaraan
    $kendaraan = Kendaraan::findOrFail($id_kendaraan);
    
    // Ambil data pajak, asuransi, cek_fisik, servis_rutin secara terpisah
    $pajak = Pajak::where('id_kendaraan', $id_kendaraan)->first();
    $asuransi = Asuransi::where('id_kendaraan', $id_kendaraan)->first();
    $cekFisik = CekFisik::where('id_kendaraan', $id_kendaraan)->first();
    // Kirim semua data ke view
    return view('admin.kendaraan.edit', compact('kendaraan', 'pajak', 'asuransi', 'cekFisik'));
}



public function update(Request $request, $id)
{
    try {
        Log::info('DEBUG: Incoming update request', ['request_data' => $request->all()]);

        // Validate the request
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

        // Find the vehicle
        $kendaraan = Kendaraan::findOrFail($id);

        // Menentukan status_ketersediaan berdasarkan status kendaraan
        $statusKetersediaan = $request->aset_guna;
        
        // Debug: Log status pinjam yang diterima
        Log::info('DEBUG: Status kendaraan yang diterima', ['status_pinjam' => $request->aset_guna]);

        if (in_array($request->aset_guna, ['Lelang', 'Jual', 'Tidak Guna'])) {
            $statusKetersediaan = 'TIDAK TERSEDIA';
        }

        // Debug: Log status ketersediaan yang akan diterapkan
        Log::info('DEBUG: Status ketersediaan yang diterapkan', ['status_ketersediaan' => $statusKetersediaan]);

        // Update kendaraan data
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
            'status_ketersediaan' => $statusKetersediaan,  // Status ketersediaan diatur otomatis
        ]);

        // Update or create pajak data
        Pajak::updateOrCreate(
            ['id_kendaraan' => $kendaraan->id_kendaraan],
            [
                'user_id' => Auth::id(),
                'tgl_bayar' => date('Y-m-d', strtotime($request->tanggal_bayar_pajak)),
                'tgl_jatuh_tempo' => date('Y-m-d', strtotime($request->tanggal_jatuh_tempo_pajak)),
                'tahun' => date('Y', strtotime($request->tanggal_bayar_pajak)),
            ]
        );

        // Update or create asuransi data
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

        // Update or create cek fisik data
        CekFisik::updateOrCreate(
            ['id_kendaraan' => $kendaraan->id_kendaraan],
            [
                'user_id' => Auth::id(),
                'tgl_cek_fisik' => $request->tanggal_cek_fisik,
            ]
        );

        ServisRutin::create([
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'user_id' => Auth::id()
        ]);
        BBM::create([
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'user_id' => Auth::id()
        ]);
        // Get the current page for redirect
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
    // Ambil data kendaraan
    $kendaraan = Kendaraan::findOrFail($id_kendaraan);

    // Ambil data cek fisik terbaru
    $cekFisik = CekFisik::where('id_kendaraan', $id_kendaraan)->latest('tgl_cek_fisik')->first();

    // Ambil data pajak terbaru
    $pajak = Pajak::where('id_kendaraan', $id_kendaraan)->latest('tgl_bayar')->first();

    // Ambil data asuransi terbaru
    $asuransi = Asuransi::where('id_kendaraan', $id_kendaraan)->latest('tgl_bayar')->first();

    // Ambil data bbm terbaru
    $bbm = BBM::where('id_kendaraan', $id_kendaraan)->latest('tgl_isi')->first(); // Sesuaikan dengan kolom yang ada

    // Ambil data servis rutin terbaru
    $servisRutin = ServisRutin::where('id_kendaraan', $id_kendaraan)->latest('tgl_servis_real')->first(); // Sesuaikan dengan kolom yang ada

    return view('admin.kendaraan.detail', compact('kendaraan', 'cekFisik', 'pajak', 'asuransi', 'bbm', 'servisRutin'));
}


public function hapus($id_kendaraan, Request $request)
{
    try {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);

        // Hapus semua data terkait kendaraan ini
        Pajak::where('id_kendaraan', $id_kendaraan)->delete();
        Asuransi::where('id_kendaraan', $id_kendaraan)->delete();
        CekFisik::where('id_kendaraan', $id_kendaraan)->delete();
        ServisRutin::where('id_kendaraan', $id_kendaraan)->delete();
        ServisInsidental::where('id_kendaraan', $id_kendaraan)->delete();
        BBM::where('id_kendaraan', $id_kendaraan)->delete();
        Peminjaman::where('id_kendaraan', $id_kendaraan)->delete();

        // Setelah data terkait dihapus, hapus kendaraan
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