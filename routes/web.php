<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\CekFisikController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\PengajuanPeminjamanController;
use App\Http\Controllers\PeminjamanPenggunaController;
use App\Http\Controllers\DaftarKendaraanPenggunaController;
use App\Http\Controllers\AsuransiController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\DaftarKendaraanAdminController;
use App\Http\Controllers\BerandaController;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login');
});

// ======ORIGINAL LARAVEL======
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route Pengguna
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/beranda', [BerandaController::class, 'pengguna'])->name('beranda');

    Route::get('/peminjaman', [PeminjamanPenggunaController::class, 'peminjamanPage'])->name('peminjaman');
    Route::get('/kendaraan', [DaftarKendaraanPenggunaController::class, 'daftarKendaraan'])->name('kendaraan');
    Route::get('/kendaraan/{id}', [DaftarKendaraanPenggunaController::class, 'detail'])->name('kendaraan.detail');
    Route::get('/peminjaman/form', [PeminjamanPenggunaController::class, 'showForm'])->name('peminjaman.showForm');
    Route::post('/peminjaman/simpan', [PeminjamanPenggunaController::class, 'simpan'])->name('peminjaman.simpan');
    Route::get('/get-kendaraan', [PeminjamanPenggunaController::class, 'getAvailableKendaraan'])->name('peminjaman.getKendaraan');
    Route::get('/peminjaman/{id}/detail', [PeminjamanPenggunaController::class, 'detail'])->name('peminjaman.detail');
    Route::get('/peminjaman/{id}/batal', [PeminjamanPenggunaController::class, 'batal'])->name('peminjaman.batal');
    Route::get('/peminjaman/{id}/formPengembalian', [PeminjamanPenggunaController::class, 'showFormPengembalian'])->name('peminjaman.showFormPengembalian');
    Route::post('/peminjaman/pengembalian/{id}', [PeminjamanPenggunaController::class, 'simpanPengembalian'])->name('peminjaman.pengembalian');
    Route::get('/peminjaman/{id}/formPerpanjangan', [PeminjamanPenggunaController::class, 'showFormPerpanjangan'])->name('peminjaman.showFormPerpanjangan');
    Route::post('/peminjaman/perpanjangan', [PeminjamanPenggunaController::class, 'perpanjangan'])->name('peminjaman.perpanjang');

   



    // Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');
    // Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    // Route::get('/servis-insidental', [ServisController::class, 'insidental'])->name('servis.insidental');
    // Route::get('/pengisian-bbm', [BBMController::class, 'index'])->name('bbm.index');

});

// Route Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/beranda', [BerandaController::class, 'admin'])->name('admin.beranda');
    // Route::get('/admin/kendaraan', [KendaraanController::class, 'adminIndex'])->name('admin.kendaraan');
    // Route::get('/admin/pengajuan-peminjaman', [PeminjamanController::class, 'adminPengajuan'])->name('admin.peminjaman');
    // Route::get('/admin/pajak', [PajakController::class, 'index'])->name('admin.pajak');
    // Route::get('/admin/asuransi', [AsuransiController::class, 'index'])->name('admin.asuransi');
    // Route::get('/admin/servis-rutin', [ServisController::class, 'rutin'])->name('admin.servis-rutin');
    // Route::get('/admin/pengisian-bbm', [BBMController::class, 'adminIndex'])->name('admin.bbm');
    // Route::get('/admin/riwayat', [RiwayatController::class, 'index'])->name('admin.riwayat');
    Route::get('/admin/pengajuan-peminjaman', [PengajuanPeminjamanController::class, 'index'])->name('admin.pengajuan-peminjaman.index');
    Route::get('/admin/pengajuan-peminjaman/{id}', [PengajuanPeminjamanController::class, 'detail'])->name('admin.pengajuan-peminjaman.detail');
    Route::post('/admin/pengajuan-peminjaman/setujui/{id}', [PengajuanPeminjamanController::class, 'setujui'])->name('admin.pengajuan-peminjaman.setujui');
    Route::post('/admin/pengajuan-peminjaman/tolak/{id}', [PengajuanPeminjamanController::class, 'tolak'])->name('admin.pengajuan-peminjaman.tolak');

    Route::get('/admin/cek-fisik', [CekFisikController::class, 'index'])->name('admin.cek-fisik.index');
    Route::get('/admin/cek-fisik/{id_kendaraan}', [CekFisikController::class, 'detail'])->name('admin.cek-fisik.detail');
    // Route untuk menambahkan (catat) cek fisik
    Route::get('/admin/cek-fisik/catat/{id_kendaraan}', [CekFisikController::class, 'create'])->name('admin.cek-fisik.create');
    Route::post('/admin/cek-fisik/simpan', [CekFisikController::class, 'store'])->name('admin.cek-fisik.store');
    // Route untuk mengedit cek fisik terakhir
    Route::get('/admin/cek-fisik/edit/{id_kendaraan}', [CekFisikController::class, 'edit'])->name('admin.cek-fisik.edit');
    Route::put('/admin/cek-fisik/update/{id_kendaraan}', [CekFisikController::class, 'update'])->name('admin.cek-fisik.update');
    // Route untuk menghapus cek fisik terakhir
    Route::delete('/admin/cek-fisik/hapus/{id_kendaraan}', [CekFisikController::class, 'destroy'])->name('admin.cek-fisik.destroy');

    Route::get('/admin/riwayat', [RiwayatController::class, 'index'])->name('admin.riwayat.index');
    Route::get('/admin/riwayat/peminjaman', [RiwayatController::class, 'peminjaman'])->name('admin.riwayat.peminjaman');
    Route::get('/admin/riwayat/detail-peminjaman/{id}', [RiwayatController::class, 'detailPeminjaman'])->name('admin.riwayat.detail-peminjaman');
    Route::get('/admin/riwayat/pajak', [RiwayatController::class, 'pajak'])->name('admin.riwayat.pajak');
    Route::get('/admin/riwayat/detail-pajak/{id}', [RiwayatController::class, 'detailPajak'])->name('admin.riwayat.detail-pajak');
    Route::get('/admin/riwayat/asuransi', [RiwayatController::class, 'asuransi'])->name('admin.riwayat.asuransi');
    Route::get('/admin/riwayat/detail-asuransi/{id}', [RiwayatController::class, 'detailAsuransi'])->name('admin.riwayat.detail-asuransi');
    Route::get('/admin/riwayat/servis-rutin', [RiwayatController::class, 'servisRutin'])->name('admin.riwayat.servis-rutin');
    Route::get('/admin/riwayat/detail-servis-rutin/{id}', [RiwayatController::class, 'detailServisRutin'])->name('admin.riwayat.detail-servis-rutin');


    //PAJAK
    Route::get('/pajak', [PajakController::class, 'index'])->name('pajak.daftar_kendaraan_pajak');
    Route::get('pajak/kelola/{id_kendaraan}', [PajakController::class, 'kelola'])->name('pajak.kelola');
    Route::get('/pajak/edit/{id_pajak}', [PajakController::class, 'edit'])->name('pajak.edit');
    Route::put('/pajak/update/{id}', [PajakController::class, 'update'])->name('pajak.update');
    Route::get('/pajak/detail/{id_pajak}', [PajakController::class, 'detail'])->name('pajak.detail');
    Route::delete('/pajak/hapus/{id_pajak}', [PajakController::class, 'hapus'])->name('pajak.hapus');
    Route::post('/pajak/store', [PajakController::class, 'store'])->name('pajak.store');
    Route::post('/pajak/delete-file', [PajakController::class, 'deleteFile'])->name('pajak.deleteFile');
    
    //ASURANSI
    Route::get('/asuransi', [AsuransiController::class, 'index'])->name('asuransi.daftar_kendaraan_asuransi');
    Route::get('/asuransi/kelola/{id_kendaraan}', [AsuransiController::class, 'kelola'])->name('asuransi.kelola');
    Route::get('/asuransi/edit/{id_asuransi}', [AsuransiController::class, 'edit'])->name('asuransi.edit');
    Route::put('/asuransi/update/{id}', [AsuransiController::class, 'update'])->name('asuransi.update');
    Route::get('/asuransi/detail/{id_asuransi}', [AsuransiController::class, 'detail'])->name('asuransi.detail');
    Route::delete('/asuransi/hapus/{id_asuransi}', [AsuransiController::class, 'hapus'])->name('asuransi.hapus');
    Route::post('/asuransi/store', [AsuransiController::class, 'store'])->name('asuransi.store');
    Route::post('/asuransi/delete-file', [AsuransiController::class, 'deleteFile'])->name('asuransi.deleteFile');

    //DATA KENDARAAN
    Route::get('/admin/kendaraan', [DaftarKendaraanAdminController::class, 'index'])->name('kendaraan.daftar_kendaraan');
    Route::get('/admin/kendaraan/tambah', [DaftarKendaraanAdminController::class, 'tambah'])->name('kendaraan.tambah');
    Route::get('/admin/kendaraan/edit/{id_kendaraan}', [DaftarKendaraanAdminController::class, 'edit'])->name('kendaraan.edit');
    Route::put('/admin/kendaraan/update/{id}', [DaftarKendaraanAdminController::class, 'update'])->name('kendaraan.update');
    Route::get('/admin/kendaraan/detail/{id_kendaraan}', [DaftarKendaraanAdminController::class, 'detail'])->name('kendaraan.detail');
    Route::delete('/admin/kendaraan/hapus/{id_kendaraan}', [DaftarKendaraanAdminController::class, 'hapus'])->name('kendaraan.hapus');
    Route::post('/admin/kendaraan/store', [DaftarKendaraanAdminController::class, 'store'])->name('kendaraan.store');
    Route::post('/admin/kendaraan/{id}/hitung-depresiasi', [DaftarKendaraanAdminController::class, 'hitungDepresiasi'])->name('kendaraan.hitungDepresiasi');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
