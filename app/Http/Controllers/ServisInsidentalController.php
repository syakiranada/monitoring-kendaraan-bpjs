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
        return view('pengguna.servisInsidental-form', compact('kendaraan'));
    }

    /**
     * Menyimpan data servis insidental ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'tgl_servis' => 'required|date',
            'harga' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:100',
            'deskripsi' => 'required|string|max:200',
            'bukti_bayar' => 'nullable|image|max:2048', // Maks 2MB
            'bukti_fisik' => 'nullable|image|max:2048', // Maks 2MB
        ]);

        // Simpan gambar bukti bayar jika ada
        $buktiBayarPath = null;
        if ($request->hasFile('bukti_bayar')) {
            $buktiBayarPath = $request->file('bukti_bayar')->store('bukti-bayar', 'public');
        }

        // Simpan gambar bukti fisik jika ada
        $buktiFisikPath = null;
        if ($request->hasFile('bukti_fisik')) {
            $buktiFisikPath = $request->file('bukti_fisik')->store('bukti-fisik', 'public');
        }

        // Simpan data ke database
        ServisInsidental::create([
            'id_kendaraan' => $validated['id_kendaraan'],
            'user_id' => Auth::id(),
            'harga' => $validated['harga'],
            'lokasi' => $validated['lokasi'],
            'deskripsi' => $validated['deskripsi'],
            'bukti_bayar' => $buktiBayarPath,
            'bukti_fisik' => $buktiFisikPath,
            'tgl_servis' => $validated['tgl_servis'],
        ]);

        // Redirect ke halaman pengguna dengan pesan sukses
        return redirect()->route('servisInsidental')
            ->with('success', 'Data servis insidental berhasil disimpan.');
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
