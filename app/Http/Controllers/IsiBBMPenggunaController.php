<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\BBM;
use App\Models\Kendaraan;

class IsiBBMPenggunaController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
    
        // Get peminjaman data
        $peminjamans = Peminjaman::where('user_id', $userId)
                                ->with('kendaraan')
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        // Initialize query for pengisian BBM
        $query = BBM::where('user_id', $userId)
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
        $pengisianBBMs = $query->orderBy('tgl_isi', 'desc')
                              ->paginate(10);

        return view('pengguna.pengisianBBM', compact('peminjamans', 'pengisianBBMs'));
    }

    public function create(Request $request)
    {
        return view('pengguna.pengisianBBM-form', [
            'id_peminjaman' => $request->id_peminjaman,
            'id_kendaraan' => $request->id_kendaraan,
            'merk' => $request->merk,
            'tipe' => $request->tipe,
            'plat' => $request->plat
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'nominal' => str_replace('.', '', $request->nominal),
        ]);        

        $validated = $request->validate([
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'id_peminjaman' => 'nullable|exists:peminjaman,id_peminjaman',
            'tgl_isi' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'jenis_bbm' => 'required|string|in:Pertalite,Pertamax,Pertamax Turbo,Dexlite,Pertamina Dex',
        ]);        
    
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
    
            BBM::create($data);
    
            return redirect()->route('pengisianBBM')
                ->with('success', 'Data pengisian BBM berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function detail($id)
    {
        $bbm = BBM::with(['kendaraan', 'peminjaman'])
                                    ->findOrFail($id);
        
        return view('pengguna.pengisianBBM-detail', compact('bbm'));
    }
}