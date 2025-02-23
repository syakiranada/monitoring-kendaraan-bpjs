<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\Kendaraan;
use App\Models\BBM;

class IsiBBMController extends Controller
{
    public function index(Request $request)
    {
        // Query untuk kendaraan tersedia
        $kendaraanTersedia = Kendaraan::where('status_ketersediaan', 'Tersedia');
        
        // Query untuk servis insidental
        $query = BBM::with(['kendaraan']);

        // Implementasi pencarian jika ada
        if ($request->has('search')) {
            $search = $request->search;
            
            // Terapkan pencarian ke kendaraan tersedia
            $kendaraanTersedia->where(function($q) use ($search) {
                $q->where('merek', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%")
                ->orWhere('plat_nomor', 'like', "%{$search}%");
            });
            
            // Terapkan pencarian ke servis insidental
            $query->whereHas('kendaraan', function($q) use ($search) {
                $q->where('merek', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%")
                ->orWhere('plat_nomor', 'like', "%{$search}%");
            });
        }

        // Eksekusi queries
        $kendaraanTersedia = $kendaraanTersedia->get();
        $pengisianBBMs = $query->orderBy('tgl_isi', 'desc')
                                ->paginate(10);

        return view('admin.pengisianBBM', compact('kendaraanTersedia', 'pengisianBBMs'));
    }

    public function create()
    {
        $kendaraan = Kendaraan::all(); // Ambil semua data kendaraan
        return view('admin.pengisianBBM-form', compact('kendaraan'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'nominal' => str_replace('.', '', $request->nominal),
        ]);   

        // Validasi input dari form
        $validated = $request->validate([
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'id_peminjaman' => 'nullable|exists:peminjaman,id_peminjaman',
            'tgl_isi' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'jenis_bbm' => 'required|string|in:Pertalite,Pertamax,Pertamax Turbo,Dexlite,Pertamina Dex',
        ]);
    
        // Pastikan user login
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->back()->withErrors(['error' => 'User tidak terautentikasi.']);
        }
    
        try {
            $data = [
                'id_kendaraan' => $validated['id_kendaraan'],
                'user_id' => $userId,
                'tgl_isi' => $validated['tgl_isi'],
                'nominal' => $validated['nominal'],
                'jenis_bbm' => $validated['jenis_bbm'], // 👈 Tambahkan ini
            ];
            
            if (!empty($validated['id_peminjaman'])) {
                $data['id_peminjaman'] = $validated['id_peminjaman'];
            }
            
            // // Tambahkan id_peminjaman hanya jika tidak null
            // if ($request->filled('id_peminjaman')) {
            //     $data['id_peminjaman'] = $request->id_peminjaman;
            // }
            
            BBM::create($data);
    
            // Redirect ke halaman admin.servisInsidental setelah berhasil
            return redirect()->route('admin.pengisianBBM')
                ->with('success', 'Data pengisian BBM berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function detail($id)
    {
        $bbm = BBM::with(['kendaraan'])
            ->findOrFail($id);

        return view('admin.pengisianBBM-detail', compact('bbm'));
    }

}
