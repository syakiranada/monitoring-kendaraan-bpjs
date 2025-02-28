<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\ServisInsidental;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ServisInsidentalController extends Controller
{
    public function index(Request $request)
    {
        // Query untuk kendaraan tersedia
        $kendaraanTersedia = Kendaraan::where('status_ketersediaan', 'Tersedia');
        
        // Query untuk servis insidental
        $query = ServisInsidental::with(['kendaraan']);

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
        $servisInsidentals = $query->orderBy('tgl_servis', 'desc')
                                ->paginate(10);

        return view('admin.servisInsidental', compact('kendaraanTersedia', 'servisInsidentals'));
    }

    public function create()
    {
        $kendaraan = Kendaraan::all(); // Ambil semua data kendaraan
        return view('admin.servisInsidental-form', compact('kendaraan'));
    }

    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'id_peminjaman' => 'nullable|exists:peminjaman,id_peminjaman',
            'tgl_servis' => 'required|date',
            'harga' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:100',
            'deskripsi' => 'required|string|max:200',
            'bukti_bayar' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048', 
            'bukti_fisik' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048', 
        ]);
    
        // Pastikan user login
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->back()->withErrors(['error' => 'User tidak terautentikasi.']);
        }
    
        try {
            // Simpan bukti bayar jika ada
            $buktiBayarPath = $request->hasFile('bukti_bayar') 
                ? $request->file('bukti_bayar')->store('bukti-bayar', 'public') 
                : null;
    
            // Simpan bukti fisik jika ada
            $buktiFisikPath = $request->hasFile('bukti_fisik') 
                ? $request->file('bukti_fisik')->store('bukti-fisik', 'public') 
                : null;
    
            $data = [
                'id_kendaraan' => $request->id_kendaraan,
                'user_id' => $userId, // Use the authenticated user ID
                'harga' => $request->harga,
                'lokasi' => $request->lokasi,
                'deskripsi' => $request->deskripsi,
                'tgl_servis' => $request->tgl_servis,
                'bukti_bayar' => $buktiBayarPath, // Add file paths to data array
                'bukti_fisik' => $buktiFisikPath, // Add file paths to data array
            ];
            
            // Tambahkan id_peminjaman hanya jika tidak null
            if ($request->filled('id_peminjaman')) {
                $data['id_peminjaman'] = $request->id_peminjaman;
            }
            
            ServisInsidental::create($data);
    
            // Redirect ke halaman admin.servisInsidental setelah berhasil
            return redirect()->route('admin.servisInsidental')
                ->with('success', 'Data servis insidental berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function detail($id)
    {
        $servis = ServisInsidental::with(['kendaraan'])
            ->findOrFail($id);

        return view('admin.servisInsidental-detail', compact('servis'));
    }

    // public function create()
    // {
    //     $kendaraan = Kendaraan::all();
    //     return view('admin.servisRutin-form', compact('kendaraan'));
    // }
}
