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
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ServisInsidentalController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Query kendaraan dengan servis insidental terakhir (jika ada)
    //     $query = Kendaraan::select(
    //             'kendaraan.*',
    //             'servis_insidental.id_servis_insidental',
    //             'servis_insidental.tgl_servis',
    //             'servis_insidental.updated_at as servis_updated_at'
    //         )
    //         ->leftJoin(DB::raw('(SELECT id_kendaraan, MAX(updated_at) as max_jam FROM servis_insidental GROUP BY id_kendaraan) as latest'), function ($join) {
    //             $join->on('kendaraan.id_kendaraan', '=', 'latest.id_kendaraan');
    //         })
    //         ->leftJoin('servis_insidental', function ($join) {
    //             $join->on('kendaraan.id_kendaraan', '=', 'servis_insidental.id_kendaraan')
    //                 ->on('servis_insidental.updated_at', '=', 'latest.max_jam');
    //         })
    //         ->where('kendaraan.status_ketersediaan', '=', 'Tersedia')
    //         ->orderBy('servis_insidental.tgl_servis', 'desc')
    //         ->orderBy('servis_insidental.updated_at', 'desc')
    //         ->paginate(10);

    //     return view('admin.servisInsidental', [
    //         'kendaraanTersedia' => Kendaraan::where('status_ketersediaan', 'Tersedia')->get(),
    //         'servisInsidentals' => $query
    //     ]);
    // }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Kendaraan::select(
            'kendaraan.*',
            'servis_insidental.id_servis_insidental',
            'servis_insidental.tgl_servis',
            'servis_insidental.updated_at as servis_updated_at'
        )
        ->leftJoin(DB::raw('(SELECT id_kendaraan, MAX(updated_at) as max_jam FROM servis_insidental GROUP BY id_kendaraan) as latest'), function ($join) {
            $join->on('kendaraan.id_kendaraan', '=', 'latest.id_kendaraan');
        })
        ->leftJoin('servis_insidental', function ($join) {
            $join->on('kendaraan.id_kendaraan', '=', 'servis_insidental.id_kendaraan')
                ->on('servis_insidental.updated_at', '=', 'latest.max_jam');
        })
        ->where('kendaraan.status_ketersediaan', '=', 'Tersedia');

        if (!empty($search)) {
            // Logika pencarian tanggal
            if (preg_match('/^\d{4}$/', $search)) { // Hanya tahun
                $query->whereYear('servis_insidental.tgl_servis', $search);
            } elseif (preg_match('/^\d{2}$/', $search)) { // Hanya bulan atau tanggal
                if (strlen($search) == 2 && $search <= 12) { // Asumsikan bulan
                    $query->whereMonth('servis_insidental.tgl_servis', $search);
                } else { // Asumsikan tanggal
                    $query->whereDay('servis_insidental.tgl_servis', $search);
                }
            } elseif (preg_match('/^\d{2}-\d{4}$/', $search)) { // Bulan-Tahun
                $parts = explode('-', $search);
                $query->whereMonth('servis_insidental.tgl_servis', $parts[0])->whereYear('servis_insidental.tgl_servis', $parts[1]);
            } elseif (preg_match('/^\d{4}-\d{2}$/', $search)) { // Tahun-Bulan
                $parts = explode('-', $search);
                $query->whereYear('servis_insidental.tgl_servis', $parts[0])->whereMonth('servis_insidental.tgl_servis', $parts[1]);
            } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) { // Tanggal-Bulan-Tahun (format lengkap)
                try {
                    $searchDate = Carbon::createFromFormat('d-m-Y', $search);
                    $query->whereDate('servis_insidental.tgl_servis', $searchDate);
                } catch (\Exception $e) {
                    Log::error("Error parsing search date:", ['search' => $search, 'error' => $e->getMessage()]);
                }
            } else {
                // Logika pencarian teks lainnya
                // Inside your search logic, modify this part
                $searchTerms = explode(' ', strtolower($search));
                $searchTerms = array_filter($searchTerms); // filter empty terms

                if (!empty($searchTerms)) {
                    $query->where(function ($outerQuery) use ($searchTerms) {
                        // Check if any term is a year format
                        $yearTerm = null;
                        foreach ($searchTerms as $index => $term) {
                            if (preg_match('/^\d{4}$/', $term)) {
                                $yearTerm = $term;
                                unset($searchTerms[$index]); // Remove from regular search terms
                                break;
                            }
                        }

                        if ($yearTerm) {
                            $outerQuery->whereYear('servis_insidental.tgl_servis', $yearTerm);
                        }
                        
                        // Continue with regular text search for remaining terms
                        foreach ($searchTerms as $term) {
                            $outerQuery->where(function ($innerQuery) use ($term) {
                                // OR antar kolom
                                $innerQuery->orWhereRaw("LOWER(kendaraan.plat_nomor) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.merk) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.tipe) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.warna) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.jenis) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.aset) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.bahan_bakar) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.no_mesin) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.no_rangka) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("CAST(kendaraan.kapasitas AS CHAR) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.frekuensi_servis) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("CAST(YEAR(servis_insidental.tgl_servis) AS CHAR) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LPAD(MONTH(servis_insidental.tgl_servis), 2, '0') LIKE ?", ["%{$term}%"])
                                    ->orWhereRaw("LPAD(DAY(servis_insidental.tgl_servis), 2, '0') LIKE ?", ["%{$term}%"]);
                            });
                        }
                    });
                }
            }
        }

        $servisInsidentals = $query->orderBy('servis_insidental.tgl_servis', 'desc')
            ->orderBy('servis_insidental.updated_at', 'desc')
            ->paginate(10);

        return view('admin.servisInsidental', [
            'kendaraanTersedia' => Kendaraan::where('status_ketersediaan', 'Tersedia')->get(),
            'servisInsidentals' => $servisInsidentals
        ]);
    }


    public function create()
    {
        $kendaraan = Kendaraan::all(); // Ambil semua data kendaraan
        return view('admin.servisInsidental-form', compact('kendaraan'));
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

        return view('admin.servisInsidental-detail', compact('servis'));
    }

    public function edit($id)
    {
        $servis = ServisInsidental::with('kendaraan')->findOrFail($id);

        return view('admin.servisInsidental-edit', [
            'servis' => $servis,
            'merk' => $servis->kendaraan->merk ?? 'Tidak Diketahui',
            'tipe' => $servis->kendaraan->tipe ?? '',
            'plat' => $servis->kendaraan->plat_nomor ?? '-',
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

            return redirect()->route('admin.servisInsidental.index')
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
    }    
}

    // public function update(Request $request, $id)
    // {

    //     $request->merge([
    //         'harga' => str_replace('.', '', $request->harga),
    //     ]);

    //     $servis = ServisInsidental::findOrFail($id);

    //     // Kondisi untuk validasi bukti_bayar
    //     $buktiValidasiBayar = $request->hasFile('bukti_bayar') ? 'required|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|mimes:jpg,jpeg,png,pdf|max:2048';
    //     //Kondisi untuk validasi bukti_fisik
    //     $buktiValidasiFisik = $request->hasFile('bukti_fisik') ? 'required|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|mimes:jpg,jpeg,png,pdf|max:2048';

    //     $validated = $request->validate([
    //         'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
    //         'id_peminjaman' => 'nullable|exists:peminjaman,id_peminjaman',
    //         'tgl_servis' => 'required|date',
    //         'harga' => 'required|numeric|min:0',
    //         'lokasi' => 'required|string|max:100',
    //         'deskripsi' => 'required|string|max:200',
    //         'bukti_bayar' => $buktiValidasiBayar,
    //         'bukti_fisik' => $buktiValidasiFisik,
    //         'remove_bukti_bayar' => 'nullable|boolean',
    //         'remove_bukti_fisik' => 'nullable|boolean',
    //     ]);

    //     $buktiBayarPath = $servis->bukti_bayar; // Inisialisasi dengan path lama
    //     $buktiFisikPath = $servis->bukti_fisik; // Inisialisasi dengan path lama

    //     if (isset($validated['remove_bukti_bayar']) && $validated['remove_bukti_bayar'] == 1) {
    //         if ($servis->bukti_bayar) {
    //             Storage::disk('public')->delete($servis->bukti_bayar);
    //         }
    //         $buktiBayarPath = null;
    //     }

    //     if (isset($validated['remove_bukti_fisik']) && $validated['remove_bukti_fisik'] == 1) {
    //         if ($servis->bukti_fisik) {
    //             Storage::disk('public')->delete($servis->bukti_fisik);
    //         }
    //         $buktiFisikPath = null;
    //     }

    //     // Hanya ubah path jika ada file baru diunggah
    //     if ($request->hasFile('bukti_bayar')) {
    //         if ($servis->bukti_bayar) {
    //             Storage::disk('public')->delete($servis->bukti_bayar);
    //         }
    //         $buktiBayarPath = $request->file('bukti_bayar')->store('bukti-bayar', 'public');
    //     }

    //     if ($request->hasFile('bukti_fisik')) {
    //         if ($servis->bukti_fisik) {
    //             Storage::disk('public')->delete($servis->bukti_fisik);
    //         }
    //         $buktiFisikPath = $request->file('bukti_fisik')->store('bukti-fisik', 'public');
    //     }

    //     try {
    //         $servis->update([
    //             'id_kendaraan' => $validated['id_kendaraan'],
    //             'id_peminjaman' => $validated['id_peminjaman'] ?? null,
    //             'tgl_servis' => $validated['tgl_servis'],
    //             'harga' => $validated['harga'],
    //             'lokasi' => $validated['lokasi'],
    //             'deskripsi' => $validated['deskripsi'],
    //             'bukti_bayar' => $buktiBayarPath,
    //             'bukti_fisik' => $buktiFisikPath,
    //         ]);

    //         return redirect()->route('admin.servisRutin')
    //         ->with('success', 'Data servis rutin berhasil diperbarui.');
    // } catch (\Exception $e) {
    //     Log::error('Error updating servis insidental: ' . $e->getMessage());
    //     return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()]);
    // }
            
    // }

