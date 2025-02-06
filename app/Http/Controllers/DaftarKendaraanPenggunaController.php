<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DaftarKendaraanPenggunaController extends Controller
{
    public function daftarKendaraanPage()
    {
        return view('pengguna.daftarKendaraan');
    }
}
