<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function peminjamanPage()
    {
        return view('pengguna.peminjaman');
    }

}
