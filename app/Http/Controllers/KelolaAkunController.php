<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;

class KelolaAkunController extends Controller
{
    // Menampilkan daftar akun
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.kelola-akun.index', compact('users'));
    }

    // Proses import Excel/CSV
    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,csv',
    //     ]);

    //     Excel::import(new UserImport, $request->file('file'));

    //     return redirect()->route('admin.kelola-akun')->with('success', 'Akun berhasil diimpor.');
    // }
}
