<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\ServisInsidental;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServisInsidentalPenggunaController extends Controller
{
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
    
        $servisQuery = ServisInsidental::where('user_id', $userId)
            ->with(['kendaraan', 'peminjaman']);
        
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
    
            $servisQuery->where(function ($query) use ($textTerms, $dateTerms) {
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
                                    $dateQuery->orWhereDate('tgl_servis', $date);
                                } catch (\Exception $e) {
                                    // Handle date parsing error
                                }
                            } elseif (preg_match('/^\d{4}-\d{2}$/', $term)) {
                                try {
                                    $date = Carbon::parse($term . '-01'); // Add first day of month
                                    $dateQuery->orWhere(function($q) use ($date) {
                                        $q->whereYear('tgl_servis', $date->year)
                                          ->whereMonth('tgl_servis', $date->month);
                                    });
                                } catch (\Exception $e) {
                                    // Handle date parsing error
                                }
                            } elseif (preg_match('/^\d{4}$/', $term)) {
                                $dateQuery->orWhereYear('tgl_servis', $term);
                            } elseif (preg_match('/^\d{1,2}$/', $term)) {
                                $dateQuery->orWhere(function ($dayMonthQuery) use ($term) {
                                    $dayMonthQuery->whereDay('tgl_servis', $term)
                                        ->orWhereMonth('tgl_servis', $term);
                                });
                            }
                        }
                    });
                }
            });
    
            // Status search
            if ($statusTerm) {
                if (stripos('tidak terkait peminjaman', $statusTerm) !== false) {
                    $servisQuery->whereNull('id_peminjaman');
                } else {
                    $servisQuery->whereHas('peminjaman', function ($q) use ($statusTerm) {
                        $q->whereRaw("LOWER(status_pinjam) LIKE ?", ["%$statusTerm%"]);
                    });
                }
            }
        }
        
        $servisInsidentals = $servisQuery->orderBy('tgl_servis', 'desc')->paginate(10);
    
        return view('pengguna.servisInsidental', compact('peminjamans', 'servisInsidentals', 'searchDaftar', 'searchRiwayat'));
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

        // Ambil semua data kendaraan
        $kendaraan = Kendaraan::all();

        return view('pengguna.servisInsidental-form', compact('kendaraan', 'peminjaman'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'harga' => str_replace('.', '', $request->harga),
        ]);  
        
        $validated = $request->validate([
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'id_peminjaman' => 'nullable|exists:peminjaman,id_peminjaman', // Ubah menjadi nullable
            'tgl_servis' => 'required|date',
            'harga' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:100',
            'deskripsi' => 'required|string|max:200',
            'bukti_bayar' => 'required|mimes:jpg,jpeg,png,pdf|max:2048', // 5MB
            'bukti_fisik' => 'required|mimes:jpg,jpeg,png,pdf|max:2048', // 5MB
        ]);
    
        // Pastikan user login
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->back()->withErrors(['error' => 'User tidak terautentikasi.']);
        }
    
        try {
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
    
            // Buat array data
            $data = [
                'id_kendaraan' => $validated['id_kendaraan'],
                'user_id' => $userId,
                'harga' => $validated['harga'],
                'lokasi' => $validated['lokasi'],
                'deskripsi' => $validated['deskripsi'],
                'bukti_bayar' => $buktiBayarPath,
                'bukti_fisik' => $buktiFisikPath,
                'tgl_servis' => $validated['tgl_servis'],
            ];
    
            // Tambahkan id_peminjaman ke data jika ada
            if ($request->has('id_peminjaman') && $request->id_peminjaman) {
                $data['id_peminjaman'] = $request->id_peminjaman;
            }
    
            // Simpan data ke database
            ServisInsidental::create($data);
    
            return redirect()->route('servisInsidental.index')
                ->with('success', 'Data servis insidental berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function detail($id)
    {
        $servis = ServisInsidental::with(['kendaraan'])
            ->findOrFail($id);

        return view('pengguna.servisInsidental-detail', compact('servis'));
    }

    public function edit($id)
    {
        $servis = ServisInsidental::with(['kendaraan', 'peminjaman'])->findOrFail($id); // include relasi peminjaman
        $kendaraan = Kendaraan::all();
    
        return view('pengguna.servisInsidental-edit', [
            'servis' => $servis,
            'kendaraan' => $kendaraan
        ]);
    }

    public function update(Request $request, $id)
    {
        $servis = ServisInsidental::findOrFail($id);

        $request->merge([
            'harga' => str_replace('.', '', $request->harga),
        ]);

        $validated = $request->validate([
            'tgl_servis' => 'required|date',
            'harga' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:100',
            'deskripsi' => 'required|string|max:200',
            'bukti_bayar' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'bukti_fisik' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'bukti_bayar_lama' => 'nullable|string',
            'bukti_fisik_lama' => 'nullable|string',
        ]);

        try {
            // Proses Bukti Bayar
            if ($request->hasFile('bukti_bayar')) {
                // Hapus file lama jika ada
                if ($servis->bukti_bayar && Storage::disk('public')->exists($servis->bukti_bayar)) {
                    Storage::disk('public')->delete($servis->bukti_bayar);
                }
                $buktiBayarPath = $request->file('bukti_bayar')->store('bukti-bayar', 'public');
            } else {
                // Gunakan file lama jika tidak ada file baru diupload
                $buktiBayarPath = $validated['bukti_bayar_lama'] ?? $servis->bukti_bayar;
            }

            // Proses Bukti Fisik
            if ($request->hasFile('bukti_fisik')) {
                // Hapus file lama jika ada
                if ($servis->bukti_fisik && Storage::disk('public')->exists($servis->bukti_fisik)) {
                    Storage::disk('public')->delete($servis->bukti_fisik);
                }
                $buktiFisikPath = $request->file('bukti_fisik')->store('bukti-fisik', 'public');
            } else {
                // Gunakan file lama jika tidak ada file baru diupload
                $buktiFisikPath = $validated['bukti_fisik_lama'] ?? $servis->bukti_fisik;
            }

            // Update data servis
            $servis->update([
                'tgl_servis' => $validated['tgl_servis'],
                'harga' => $validated['harga'],
                'lokasi' => $validated['lokasi'],
                'deskripsi' => $validated['deskripsi'],
                'bukti_bayar' => $buktiBayarPath,
                'bukti_fisik' => $buktiFisikPath,
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => 'Data servis berhasil diperbarui']);
            }

            return redirect()->route('servisInsidental.index')
                ->with('success', 'Data servis berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update servis: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan saat memperbarui data'], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $servis = ServisInsidental::findOrFail($id);
    
        try {
            // Hapus file terkait jika ada
            if ($servis->bukti_bayar) {
                Storage::delete('public/' . $servis->bukti_bayar);
            }
    
            if ($servis->bukti_fisik) {
                Storage::delete('public/' . $servis->bukti_fisik);
            }
    
            // Hapus data dari database
            $servis->delete();
    
            // Cek jika request dari AJAX
            if (request()->ajax()) {
                return response()->json(['success' => 'Data berhasil dihapus']);
            }
    
            return redirect()->route('servisInsidental.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            // Tangani error agar tidak mengembalikan HTML penuh
            if (request()->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan saat menghapus data'], 500);
            }
    
            return redirect()->route('servisInsidental.index')->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    } }
