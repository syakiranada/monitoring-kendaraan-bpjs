<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\Kendaraan;
use App\Models\BBM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IsiBBMController extends Controller
{
    public function index(Request $request)
    {
        // Query untuk kendaraan tersedia
        $kendaraanTersedia = Kendaraan::where('status_ketersediaan', 'Tersedia');
        
        // Query untuk pengisian BBM terakhir dari setiap kendaraan
        $query = BBM::select('b.*')
            ->from(DB::raw('(SELECT id_kendaraan, MAX(tgl_isi) as max_tgl FROM bbm GROUP BY id_kendaraan) as latest'))
            ->join('bbm as b', function($join) {
                $join->on('b.id_kendaraan', '=', 'latest.id_kendaraan')
                     ->on('b.tgl_isi', '=', 'latest.max_tgl');
            })
            ->with(['kendaraan']);

        // Implementasi pencarian jika ada
        if ($request->has('search')) {
            $search = $request->search;
            
            // Terapkan pencarian ke kendaraan tersedia
            $kendaraanTersedia->where(function($q) use ($search) {
                $q->where('merek', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%")
                ->orWhere('plat_nomor', 'like', "%{$search}%");
            });
            
            // Terapkan pencarian ke pengisian BBM
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
                'jenis_bbm' => $validated['jenis_bbm'],
            ];
            
            if (!empty($validated['id_peminjaman'])) {
                $data['id_peminjaman'] = $validated['id_peminjaman'];
            }
            
            BBM::create($data);
    
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

    public function edit($id)
    {
        $bbm = BBM::with('kendaraan')->findOrFail($id);
        $kendaraan = Kendaraan::all();
        
        return view('admin.pengisianBBM-edit', [
            'bbm' => $bbm,
            'kendaraan' => $kendaraan
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $request->merge([
            'nominal' => str_replace('.', '', $request->nominal),
        ]);
        
        $validated = $request->validate([
            'tgl_isi' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'jenis_bbm' => 'required|string|in:Pertalite,Pertamax,Pertamax Turbo,Dexlite,Pertamina Dex',
        ]);
    
        $bbm = BBM::findOrFail($id);
    
        try {
            $bbm->update([
                'tgl_isi' => $validated['tgl_isi'],
                'nominal' => $validated['nominal'],
                'jenis_bbm' => $validated['jenis_bbm'],
            ]);
    
            if ($request->ajax()) {
                return response()->json(['success' => 'Data pengisian BBM berhasil diperbarui']);
            }
    
            return redirect()->route('admin.pengisianBBM')
                ->with('success', 'Data pengisian BBM berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }
    
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }    
    
    public function destroy($id)
    {
        try {
            $bbm = BBM::findOrFail($id);
            
            // Simpan ID Kendaraan sebelum menghapus
            $idKendaraan = $bbm->id_kendaraan;
            
            // Hapus bukti bayar jika ada
            if ($bbm->bukti_bayar) {
                Storage::disk('public')->delete($bbm->bukti_bayar);
            }
            
            // Hapus data dari database
            $bbm->delete();
            
            // Periksa jika request adalah AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => 'Data berhasil dihapus'], 200);
            }
            
            // Cari record BBM sebelumnya untuk kendaraan yang sama
            $bbmSebelumnya = BBM::where('id_kendaraan', $idKendaraan)
                ->orderBy('tgl_isi', 'desc')
                ->first();
            
            if ($bbmSebelumnya) {
                return redirect()->route('admin.pengisianBBM', ['id' => $bbmSebelumnya->id])
                    ->with('success', 'Data pengisian BBM berhasil dihapus.');
            }
            
            // Jika tidak ada data BBM sebelumnya, kembali ke daftar
            return redirect()->route('admin.pengisianBBM')
                ->with('success', 'Data pengisian BBM berhasil dihapus.');
                
        } catch (\Exception $e) {
            // Jika request AJAX, berikan response JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}