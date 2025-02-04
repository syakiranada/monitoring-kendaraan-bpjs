<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function berandaPengguna()
    {
        return view('pengguna.beranda');
    }

    public function berandaAdmin()
    {
        return view('admin.beranda');
    }
}
