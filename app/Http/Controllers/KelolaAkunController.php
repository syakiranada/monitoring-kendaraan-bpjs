<?php

namespace App\Http\Controllers;

use App\Imports\UserImport;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Maatwebsite\Excel\Facades\Excel;

class KelolaAkunController extends Controller
{
    // Menampilkan daftar akun
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.kelola-akun.index', compact('users'));
    }

    // Proses import Excel/CSV
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new UserImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data pengguna berhasil diimpor');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    // public function downloadTemplate()
    // {
    //     $path = storage_path('app/templates/user_import_template.xlsx');
    //     return response()->download($path, 'template_import_pengguna.xlsx');
    // }
}
