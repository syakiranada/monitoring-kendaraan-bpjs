<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\BBM;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class IsiBBMPenggunaController extends Controller{
    public function index(Request $request)
{
    $userId = Auth::id();
    $searchDaftar = $request->input('search_daftar');
    $searchRiwayat = $request->input('search_riwayat');

    // Query for peminjamans (Daftar Kendaraan Dipinjam)
    $peminjamansQuery = Peminjaman::where('user_id', $userId)
        ->with('kendaraan');
    
    // Apply search for peminjamans if search_daftar is provided
    if (!empty($searchDaftar)) {
        $searchDaftar = strtolower($searchDaftar);
        $searchTerms = explode(' ', $searchDaftar);
        
        $peminjamansQuery->where(function ($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->where(function ($innerQuery) use ($term) {
                    // Search in kendaraan table
                    $innerQuery->whereHas('kendaraan', function ($kendaraanQuery) use ($term) {
                        $kendaraanQuery->whereRaw("LOWER(merk) LIKE ?", ["%$term%"])
                            ->orWhereRaw("LOWER(tipe) LIKE ?", ["%$term%"])
                            ->orWhereRaw("LOWER(plat_nomor) LIKE ?", ["%$term%"]);
                    });
                    
                    // Search in status_pinjam
                    $innerQuery->orWhereRaw("LOWER(status_pinjam) LIKE ?", ["%$term%"]);
                });
            }
        });
    }
    
    $peminjamans = $peminjamansQuery->orderBy('created_at', 'desc')->get();

    // Query for BBM (Riwayat Pengisian BBM)
    $bbmQuery = BBM::where('user_id', $userId)
        ->with(['kendaraan', 'peminjaman']);
    
    // Apply search for BBM if search_riwayat is provided
    if (!empty($searchRiwayat)) {
        $searchRiwayat = strtolower($searchRiwayat);
        $searchTerms = explode(' ', $searchRiwayat);

        $textTerms = [];
        $dateTerms = [];
        $statusTerm = null;

        // Status list - add both lowercase and uppercase versions for matching
        $statusList = [
            'telah dikembalikan', 'dibatalkan', 'ditolak', 'diperpanjang', 'disetujui',
            'tidak terkait peminjaman'
        ];

        foreach ($searchTerms as $key => $term) {
            // Check if this term is a status
            $isStatus = false;
            foreach ($statusList as $status) {
                if (stripos($status, $term) !== false) {
                    $statusTerm = $term;
                    $isStatus = true;
                    break;
                }
            }

            // If it's a status, we'll handle it separately
            if ($isStatus) {
                unset($searchTerms[$key]);
                continue;
            }

            // Check if it's a date format
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $term) || preg_match('/^\d{4}-\d{2}$/', $term) || 
                preg_match('/^\d{4}$/', $term) || preg_match('/^\d{1,2}$/', $term)) {
                $dateTerms[] = $term;
            } else {
                $textTerms[] = $term;
            }
        }

        $bbmQuery->where(function ($query) use ($textTerms, $dateTerms) {
            // Text search for brand, type, and license plate
            if (!empty($textTerms)) {
                $query->where(function ($textQuery) use ($textTerms) {
                    foreach ($textTerms as $term) {
                        $textQuery->whereHas('kendaraan', function ($kendaraanQuery) use ($term) {
                            $kendaraanQuery->whereRaw("LOWER(merk) LIKE ?", ["%$term%"])
                                ->orWhereRaw("LOWER(tipe) LIKE ?", ["%$term%"])
                                ->orWhereRaw("LOWER(plat_nomor) LIKE ?", ["%$term%"]);
                        });
                    }
                });
            }

            // Date search
            if (!empty($dateTerms)) {
                $query->where(function ($dateQuery) use ($dateTerms) {
                    foreach ($dateTerms as $term) {
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $term)) {
                            try {
                                $date = Carbon::parse($term);
                                $dateQuery->orWhereDate('tgl_isi', $date);
                            } catch (\Exception $e) {
                                // Handle date parsing error
                            }
                        } elseif (preg_match('/^\d{4}-\d{2}$/', $term)) {
                            try {
                                $date = Carbon::parse($term . '-01'); // Add first day of month
                                $dateQuery->orWhere(function($q) use ($date) {
                                    $q->whereYear('tgl_isi', $date->year)
                                      ->whereMonth('tgl_isi', $date->month);
                                });
                            } catch (\Exception $e) {
                                // Handle date parsing error
                            }
                        } elseif (preg_match('/^\d{4}$/', $term)) {
                            $dateQuery->orWhereYear('tgl_isi', $term);
                        } elseif (preg_match('/^\d{1,2}$/', $term)) {
                            $dateQuery->orWhere(function ($dayMonthQuery) use ($term) {
                                $dayMonthQuery->whereDay('tgl_isi', $term)
                                    ->orWhereMonth('tgl_isi', $term);
                            });
                        }
                    }
                });
            }
        });

        // Status search
        if ($statusTerm) {
            if (stripos('tidak terkait peminjaman', $statusTerm) !== false) {
                $bbmQuery->whereNull('id_peminjaman');
            } else {
                $bbmQuery->whereHas('peminjaman', function ($q) use ($statusTerm) {
                    $q->whereRaw("LOWER(status_pinjam) LIKE ?", ["%$statusTerm%"]);
                });
            }
        }
    }
    
    $peminjamans = Peminjaman::where('status_pinjam', 'Disetujui')->paginate(5, ['*'], 'peminjamans_page');
    $pengisianBBMs = $bbmQuery->orderBy('tgl_isi', 'desc')->paginate(5, ['*'], 'pengisian_page');    

    return view('pengguna.pengisianBBM', compact('peminjamans', 'pengisianBBMs', 'searchDaftar', 'searchRiwayat'));
}

    public function create(Request $request)
    {
        // Ambil id_peminjaman dari request (misalnya dari query string atau URL)
        $id_peminjaman = $request->input('id_peminjaman');
        
        // Cek apakah id_peminjaman ada, dan jika tidak, tampilkan error atau redirect
        if (!$id_peminjaman) {
            // Misalnya, redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'ID Peminjaman tidak ditemukan.');
        }

        // Ambil data peminjaman berdasarkan id_peminjaman
        $peminjaman = Peminjaman::findOrFail($id_peminjaman);

        // Pastikan kamu mem-passing data ke view dengan benar
        return view('pengguna.pengisianBBM-form', [
            'peminjaman' => $peminjaman, 
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
            'jenis_bbm' => 'required|string|in:Pertalite,Pertamax,Pertamax Turbo,Dexlite,Pertamina Dex,Solar,Bio Solar',
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

    public function edit($id)
    {
        $bbm = BBM::with(['kendaraan', 'peminjaman'])->findOrFail($id); // include relasi peminjaman
        $kendaraan = Kendaraan::all();
    
        return view('pengguna.pengisianBBM-edit', [
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
            'jenis_bbm' => 'required|string|in:Pertalite,Pertamax,Pertamax Turbo,Dexlite,Pertamina Dex,Solar,Bio Solar',
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
    
            return redirect()->route('pengguna.pengisianBBM')
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
                ->orderBy('updated_at', 'desc')
                ->first();
            
            if ($bbmSebelumnya) {
                return redirect()->route('pengguna.pengisianBBM', ['id' => $bbmSebelumnya->id])
                    ->with('success', 'Data pengisian BBM berhasil dihapus.');
            }
            
            // Jika tidak ada data BBM sebelumnya, kembali ke daftar
            return redirect()->route('pengguna.pengisianBBM')
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