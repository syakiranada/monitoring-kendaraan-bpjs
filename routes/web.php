<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeminjamanPenggunaController;
use App\Http\Controllers\DaftarKendaraanPenggunaController;
use App\Http\Controllers\ServisRutinController;
use App\Http\Controllers\ServisInsidentalController;
use App\Http\Controllers\ServisInsidentalPenggunaController;
use App\Http\Controllers\IsiBBMPenggunaController;

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

    Route::get('/pengisianBBM', [IsiBBMPenggunaController::class, 'index'])->name('pengisianBBM');

    Route::get('/servisInsidental', [ServisInsidentalPenggunaController::class, 'index'])->name('servisInsidental');
    Route::get('/servisInsidental/create', [ServisInsidentalPenggunaController::class, 'create'])->name('servisInsidental.create');
    Route::post('/servisInsidental', [ServisInsidentalPenggunaController::class, 'store'])->name('servisInsidental.store');
    Route::get('/servisInsidental/{id}', [ServisInsidentalPenggunaController::class, 'detail'])->name('servisInsidental.detail');



    // Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');
    // Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    // Route::get('/servis-insidental', [ServisController::class, 'insidental'])->name('servis.insidental');
    


});

// Route Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/beranda', [HomeController::class, 'berandaAdmin'])->name('admin.beranda');

    Route::get('/admin/servisRutin', [ServisRutinController::class, 'index'])->name('admin.servisRutin');
    Route::get('/admin/servisRutin/create', [ServisRutinController::class, 'create'])->name('admin.servisRutin.create');
    Route::post('/admin/servisRutin', [ServisRutinController::class, 'store'])->name('admin.servisRutin.store');
    Route::get('/admin/servisRutin/{id}', [ServisRutinController::class, 'detail'])->name('admin.servisRutin.detail');
    Route::get('/api/kendaraan/{id}', [ServisRutinController::class, 'getKendaraan']);
    Route::get('/api/servis_terbaru/{id_kendaraan}', [ServisRutinController::class, 'getServisTerbaru']);
    Route::get('/api/frekuensi/{id_kendaraan}', [ServisRutinController::class, 'getFrekuensi']);

    Route::get('/admin/servisInsidental', [ServisInsidentalController::class, 'index'])->name('admin.servisInsidental');
    Route::get('/admin/servisInsidental/create', [ServisInsidentalController::class, 'create'])->name('admin.servisInsidental.create');
    Route::post('/admin/servisInsidental', [ServisInsidentalController::class, 'store'])->name('admin.servisInsidental.store');
    Route::get('/admin/servisInsidental/{id}', [ServisInsidentalController::class, 'detail'])->name('admin.servisInsidental.detail');

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
