<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeminjamanPenggunaController;
use App\Http\Controllers\DaftarKendaraanPenggunaController;

use App\Http\Controllers\PengajuanPeminjamanController;

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
    Route::get('/beranda', [HomeController::class, 'berandaPengguna'])->name('beranda');

    Route::get('/peminjaman', [PeminjamanPenggunaController::class, 'peminjamanPage'])->name('peminjaman');
    Route::get('/kendaraan', [DaftarKendaraanPenggunaController::class, 'daftarKendaraan'])->name('kendaraan');
    Route::get('/kendaraan/{id}', [DaftarKendaraanPenggunaController::class, 'detail'])->name('kendaraan.detail');
    Route::get('/formPeminjaman', [PeminjamanPenggunaController::class, 'form'])->name('peminjaman.form');


    // Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');
    // Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    // Route::get('/servis-insidental', [ServisController::class, 'insidental'])->name('servis.insidental');
    // Route::get('/pengisian-bbm', [BBMController::class, 'index'])->name('bbm.index');


});

// Route Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/beranda', [HomeController::class, 'berandaAdmin'])->name('admin.beranda');
    // Route::get('/admin/kendaraan', [KendaraanController::class, 'adminIndex'])->name('admin.kendaraan');
    // Route::get('/admin/pengajuan-peminjaman', [PeminjamanController::class, 'adminPengajuan'])->name('admin.peminjaman');
    // Route::get('/admin/pajak', [PajakController::class, 'index'])->name('admin.pajak');
    // Route::get('/admin/asuransi', [AsuransiController::class, 'index'])->name('admin.asuransi');
    // Route::get('/admin/servis-rutin', [ServisController::class, 'rutin'])->name('admin.servis-rutin');
    // Route::get('/admin/pengisian-bbm', [BBMController::class, 'adminIndex'])->name('admin.bbm');
    // Route::get('/admin/cek-fisik', [CekFisikController::class, 'index'])->name('admin.cek-fisik');
    // Route::get('/admin/riwayat', [RiwayatController::class, 'index'])->name('admin.riwayat');
    Route::get('/admin/pengajuan-peminjaman', [PengajuanPeminjamanController::class, 'index'])->name('admin.pengajuan-peminjaman.index');
    Route::get('/admin/pengajuan-peminjaman/{id}', [PengajuanPeminjamanController::class, 'detail'])->name('admin.pengajuan-peminjaman.detail');
    Route::post('/admin/pengajuan-peminjaman/setujui/{id}', [PengajuanPeminjamanController::class, 'setujui'])->name('admin.pengajuan-peminjaman.setujui');
    Route::post('/admin/pengajuan-peminjaman/tolak/{id}', [PengajuanPeminjamanController::class, 'tolak'])->name('admin.pengajuan-peminjaman.tolak');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
