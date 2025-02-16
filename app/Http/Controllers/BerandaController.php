<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;
use App\Models\Pajak;
use App\Models\Asuransi;
use App\Models\ServisRutin;
use App\Models\CekFisik;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    public function pengguna()
{
    $user = Auth::user();
    $peminjaman = Peminjaman::where('user_id', $user->id)
        ->whereRaw("UPPER(status_pinjam) = 'DISETUJUI'")
        ->with('kendaraan')
        ->get();
        
    $now = Carbon::now('Asia/Jakarta');
    $latePeminjaman = [];

    foreach ($peminjaman as $pinjam) {
        $tanggalKembali = Carbon::parse($pinjam->tgl_selesai . ' ' . $pinjam->jam_selesai, 'Asia/Jakarta');

        if ($now->greaterThan($tanggalKembali)) {
                
            // Update the model instance too
            $pinjam->status_pinjam = 'BELUM DIKEMBALIKAN';
            $latePeminjaman[] = $pinjam;
        }
    }

    return view('pengguna.beranda_pengguna', compact('user', 'peminjaman', 'latePeminjaman'));
}

public function admin()
{
    $user = Auth::user();
    $sekarang = Carbon::now('Asia/Jakarta');

    // Ambil daftar kendaraan yang terlambat dikembalikan
    $peminjamanTerlambat = Peminjaman::with(['kendaraan', 'user'])
        ->whereRaw("UPPER(status_pinjam) = 'DISETUJUI'")
        ->where(function ($query) use ($sekarang) {
            $query->where('tgl_selesai', '<', $sekarang->format('Y-m-d'))
                  ->orWhereRaw("CONCAT(tgl_selesai, ' ', jam_selesai) < ?", [$sekarang->format('Y-m-d H:i:s')]);
        })
        ->get();

    // Ambil kendaraan yang perlu cek fisik (jika terakhir cek lebih dari 1 minggu yang lalu)
    $butuhCekFisik = CekFisik::whereIn('id_cek_fisik', function ($query) {
            $query->selectRaw('MAX(id_cek_fisik)')->from('cek_fisik')->groupBy('id_kendaraan');
        })
        ->whereDate('tgl_cek_fisik', '<', Carbon::now()->subWeek())
        ->with('kendaraan')
        ->get();

    // Kumpulan batas waktu penting dalam 1 bulan ke depan
    $batasWaktu = collect();

    // Pajak kendaraan (ambil pajak terbaru untuk tiap kendaraan)
    $pajak = Pajak::whereIn('id_pajak', function ($query) {
            $query->selectRaw('MAX(id_pajak)')->from('pajak')->groupBy('id_kendaraan');
        })
        ->whereDate(DB::raw("DATE_ADD(tgl_jatuh_tempo, INTERVAL 1 YEAR)"), '<=', Carbon::now()->addMonth())
        ->with('kendaraan')
        ->get()
        ->map(function ($pajak) {
            return [
                'tipe' => 'Pajak',
                'id_kendaraan' => $pajak->id_kendaraan,
                'kendaraan' => $pajak->kendaraan,
                'batas_waktu' => Carbon::parse($pajak->tgl_jatuh_tempo)->addYear(),
            ];
        });
    $batasWaktu = $batasWaktu->concat($pajak);

    // Servis rutin (ambil servis terbaru untuk tiap kendaraan)
    $servis = ServisRutin::whereIn('id_servis_rutin', function ($query) {
            $query->selectRaw('MAX(id_servis_rutin)')->from('servis_rutin')->groupBy('id_kendaraan');
        })
        ->whereDate('tgl_servis_selanjutnya', '<=', Carbon::now()->addMonth())
        ->with('kendaraan')
        ->get()
        ->map(function ($servis) {
            return [
                'tipe' => 'Servis',
                'id_kendaraan' => $servis->id_kendaraan,
                'kendaraan' => $servis->kendaraan,
                'batas_waktu' => $servis->tgl_servis_selanjutnya,
            ];
        });
    $batasWaktu = $batasWaktu->concat($servis);

    // Asuransi (ambil asuransi terbaru untuk tiap kendaraan)
    $asuransi = Asuransi::whereIn('id_asuransi', function ($query) {
            $query->selectRaw('MAX(id_asuransi)')->from('asuransi')->groupBy('id_kendaraan');
        })
        ->whereDate('tgl_perlindungan_akhir', '<=', Carbon::now()->addMonth())
        ->with('kendaraan')
        ->get()
        ->map(function ($asuransi) {
            return [
                'tipe' => 'Asuransi',
                'id_kendaraan' => $asuransi->id_kendaraan,
                'kendaraan' => $asuransi->kendaraan,
                'batas_waktu' => $asuransi->tgl_perlindungan_akhir,
            ];
        });
    $batasWaktu = $batasWaktu->concat($asuransi);

    // Urutkan semua batas waktu berdasarkan tanggal terdekat
    $batasWaktu = $batasWaktu->sortBy('batas_waktu');

    return view('admin.beranda_admin', compact(
        'user',
        'peminjamanTerlambat',
        'butuhCekFisik',
        'batasWaktu'
    ));
}

}