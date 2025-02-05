<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DaftarKendaraanPenggunaController;


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
    Route::get('/peminjaman', [PeminjamanController::class, 'peminjamanPage'])->name('peminjaman');
    Route::get('/kendaraan', [DaftarKendaraanPenggunaController::class, 'daftarKendaraanPage'])->name('kendaraan');
});

// Route Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/beranda', [HomeController::class, 'berandaAdmin'])->name('admin.beranda');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
