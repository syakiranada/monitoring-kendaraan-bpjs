<?php

namespace App\Http\Controllers;

use App\Imports\UserImport;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class KelolaAkunController extends Controller
{
    // Menampilkan daftar akun
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('peran', 'like', "%$search%");
        })->paginate(10);

        return view('admin.kelola-akun.index', compact('users', 'search'));
    }

    // Proses import Excel/CSV
    public function import(Request $request)
    {
        // $request->validate([
        //     'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        // ]);

        $request->validate([
            'file' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:2048',
        ]);
        

        try {
            Excel::import(new UserImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data pengguna berhasil diimpor');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();

        $page = $request->input('page', 1);
        $search = $request->input('search');

        return redirect()->route('admin.kelola-akun.index', [
            'page' => $page,
            'search' => $search
        ])->with('success', 'Status akun berhasil diperbarui.');
    }


    // public function downloadTemplate()
    // {
    //     $path = storage_path('app/templates/user_import_template.xlsx');
    //     return response()->download($path, 'template_import_pengguna.xlsx');
    // }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.kelola-akun.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'peran' => 'required|in:admin,pengguna',
        ]);

        // $user->update([
        //     'name' => $request->name,
        //     'email' => $request->email,
        // ]);

        // Gunakan setter langsung daripada mass assignment
        $user->name = $request->name;
        $user->email = $request->email;
        $user->peran = $request->peran;
        $user->save();

        $page = $request->input('page', 1);
        $search = $request->input('search');

        return redirect()
            ->route('admin.kelola-akun.index', [
                'page' => $page,
                'search' => $search
            ])
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $newPassword = '12345678'; // Bisa diganti atau di-generate otomatis

        // $user->update([
        //     'password' => Hash::make($newPassword),
        // ]);
        
        // Gunakan setter langsung daripada mass assignment
        $user->password = Hash::make($newPassword);
        $user->save();

        $page = $request->input('page', 1);
        $search = $request->input('search');

        return redirect()
            ->route('admin.kelola-akun.index', [
                'page' => $page,
                'search' => $search
            ])
            ->with('success', 'Password berhasil direset');
    }
}
