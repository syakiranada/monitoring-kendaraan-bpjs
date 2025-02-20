<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\ServisInsidental;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ServisInsidentalPenggunaController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
    
        // Get peminjaman data filtered by user_id
        $peminjamans = Peminjaman::where('user_id', $userId)
                                ->with('kendaraan')
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        // Build query for servis insidental with search functionality
        $query = ServisInsidental::where('user_id', $userId)
                                ->with(['kendaraan', 'peminjaman']);

        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('kendaraan', function($q) use ($search) {
                $q->where('merk', 'like', "%{$search}%")
                  ->orWhere('tipe', 'like', "%{$search}%")
                  ->orWhere('plat_nomor', 'like', "%{$search}%");
            });
        }

        // Get paginated results
        $servisInsidentals = $query->orderBy('tgl_servis', 'desc')
                            ->paginate(10);

        return view('pengguna.servisInsidental', compact('peminjamans', 'servisInsidentals'));
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

        return view('pengguna.servisInsidental-detail', compact('servis'));
    }
}
