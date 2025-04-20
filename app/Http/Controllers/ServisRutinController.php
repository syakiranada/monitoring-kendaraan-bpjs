<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\ServisRutin;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ServisRutinController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Cara 1: Gunakan query builder yang lebih eksplisit
    //     $subquery = DB::table('servis_rutin')
    //         ->selectRaw('MAX(id_servis_rutin) as max_id')
    //         ->groupBy('id_kendaraan');
        
    //     $ids = $subquery->pluck('max_id')->toArray();
        
    //     $query = ServisRutin::with(['kendaraan'])
    //         ->whereIn('id_servis_rutin', $ids);
        
    //     if ($request->has('search')) {
    //         $search = $request->search;
    //         $query->whereHas('kendaraan', function($q) use ($search) {
    //             $q->where('merk', 'like', "%{$search}%")  // Perhatikan perubahan 'merek' menjadi 'merk'
    //               ->orWhere('tipe', 'like', "%{$search}%")
    //               ->orWhere('plat_nomor', 'like', "%{$search}%");
    //         });
    //     }
        
    //     // Filter untuk kendaraan dengan aset 'guna' atau 'tidak guna'
    //     $query->whereHas('kendaraan', function($q) {
    //         $q->whereIn('aset', ['guna', 'tidak guna']);
    //     });
    
    //     $count = $query->count(); // Periksa jumlah record sebelum pagination
        
    //     $servisRutins = $query->orderBy('tgl_servis_real', 'desc')
    //                           ->paginate(10);
        
    //     // Tambahkan log untuk debugging
    //     Log::info("Total records: {$count}, Page records: {$servisRutins->count()}");
        
    //     return view('admin.servisRutin', compact('servisRutins'));
    // }

    public function index(Request $request)
    {
        // Get the latest service record for each vehicle
        $subquery = DB::table('servis_rutin')
            ->selectRaw('MAX(id_servis_rutin) as max_id')
            ->groupBy('id_kendaraan');
        
        $ids = $subquery->pluck('max_id')->toArray();
        
        // Start with ServisRutin model and preload kendaraan relationship
        $query = ServisRutin::with(['kendaraan'])
            ->whereIn('id_servis_rutin', $ids);
        
        // Filter to only include vehicles with asset type 'guna' or 'tidak guna'
        $query->whereHas('kendaraan', function($q) {
            $q->whereIn('aset', ['guna', 'tidak guna']);
        });
    
        // Debugging to verify we're getting the search parameter
        $search = $request->input('search');
        Log::info("Search parameter received: " . ($search ?: 'empty'));
        
        if (!empty($search)) {
            // Logika pencarian tanggal
            if (preg_match('/^\d{4}$/', $search)) { // Hanya tahun
                $query->whereYear('servis_rutin.tgl_servis_real', $search);
                Log::info("Applying year search: {$search}");
            } elseif (preg_match('/^\d{2}$/', $search)) { // Hanya bulan atau tanggal
                if (strlen($search) == 2 && $search <= 12) { // Asumsikan bulan
                    $query->whereMonth('servis_rutin.tgl_servis_real', $search);
                    Log::info("Applying month search: {$search}");
                } else { // Asumsikan tanggal
                    $query->whereDay('servis_rutin.tgl_servis_real', $search);
                    Log::info("Applying day search: {$search}");
                }
            } elseif (preg_match('/^\d{2}-\d{4}$/', $search)) { // Bulan-Tahun
                $parts = explode('-', $search);
                $query->whereMonth('servis_rutin.tgl_servis_real', $parts[0])->whereYear('servis_rutin.tgl_servis_real', $parts[1]);
                Log::info("Applying month-year search: {$parts[0]}-{$parts[1]}");
            } elseif (preg_match('/^\d{4}-\d{2}$/', $search)) { // Tahun-Bulan
                $parts = explode('-', $search);
                $query->whereYear('servis_rutin.tgl_servis_real', $parts[0])->whereMonth('servis_rutin.tgl_servis_real', $parts[1]);
                Log::info("Applying year-month search: {$parts[0]}-{$parts[1]}");
            } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) { // Tanggal-Bulan-Tahun (format lengkap)
                try {
                    $searchDate = Carbon::createFromFormat('d-m-Y', $search);
                    $query->whereDate('servis_rutin.tgl_servis_real', $searchDate);
                    Log::info("Applying full date search: {$searchDate}");
                } catch (\Exception $e) {
                    Log::error("Error parsing search date:", ['search' => $search, 'error' => $e->getMessage()]);
                }
            } else {
                // Logika pencarian teks lainnya
                $searchTerms = explode(' ', strtolower($search));
                $searchTerms = array_filter($searchTerms); // filter empty terms
                Log::info("Search terms: " . implode(', ', $searchTerms));
    
                if (!empty($searchTerms)) {
                    $query->where(function ($outerQuery) use ($searchTerms) {
                        // Check if any term is a year format
                        $yearTerm = null;
                        foreach ($searchTerms as $index => $term) {
                            if (preg_match('/^\d{4}$/', $term)) {
                                $yearTerm = $term;
                                unset($searchTerms[$index]); // Remove from regular search terms
                                Log::info("Year term found: {$yearTerm}");
                                break;
                            }
                        }
    
                        if ($yearTerm) {
                            $outerQuery->whereYear('servis_rutin.tgl_servis_real', $yearTerm);
                        }
                        
                        // Continue with regular text search for remaining terms
                        foreach ($searchTerms as $term) {
                            Log::info("Applying text search for term: {$term}");
                            $outerQuery->where(function ($innerQuery) use ($term) {
                                // OR antar kolom - Important change here, we need to reference the relationship correctly
                                $innerQuery->whereHas('kendaraan', function($vehicleQuery) use ($term) {
                                    $vehicleQuery->where(function($q) use ($term) {
                                        $q->whereRaw("LOWER(plat_nomor) LIKE ?", ["%{$term}%"])
                                            ->orWhereRaw("LOWER(merk) LIKE ?", ["%{$term}%"])
                                            ->orWhereRaw("LOWER(tipe) LIKE ?", ["%{$term}%"])
                                            ->orWhereRaw("LOWER(warna) LIKE ?", ["%{$term}%"])
                                            ->orWhereRaw("LOWER(jenis) LIKE ?", ["%{$term}%"])
                                            ->orWhereRaw("LOWER(aset) LIKE ?", ["%{$term}%"])
                                            ->orWhereRaw("LOWER(bahan_bakar) LIKE ?", ["%{$term}%"])
                                            ->orWhereRaw("LOWER(no_mesin) LIKE ?", ["%{$term}%"])
                                            ->orWhereRaw("LOWER(no_rangka) LIKE ?", ["%{$term}%"])
                                            ->orWhereRaw("CAST(kapasitas AS CHAR) LIKE ?", ["%{$term}%"])
                                            ->orWhereRaw("LOWER(frekuensi_servis) LIKE ?", ["%{$term}%"]);
                                    });
                                });
                                // Also search in servis_rutin date fields
                                $innerQuery->orWhereRaw("CAST(YEAR(servis_rutin.tgl_servis_real) AS CHAR) LIKE ?", ["%{$term}%"])
                                    ->orWhereRaw("LPAD(MONTH(servis_rutin.tgl_servis_real), 2, '0') LIKE ?", ["%{$term}%"])
                                    ->orWhereRaw("LPAD(DAY(servis_rutin.tgl_servis_real), 2, '0') LIKE ?", ["%{$term}%"]);

                            });
                        }
                    });
                }
            }
        }
    
        // Get the SQL query for debugging
        $sqlQuery = $query->toSql();
        $bindings = $query->getBindings();
        Log::info("Generated SQL: " . $sqlQuery);
        Log::info("Query bindings: ", $bindings);
    
        $count = $query->count(); // Periksa jumlah record sebelum pagination
        
        $servisRutins = $query->orderBy('servis_rutin.tgl_servis_real', 'desc')
                            ->orderBy('servis_rutin.updated_at', 'desc')
                            ->paginate(10);
        
        // Tambahkan log untuk debugging
        Log::info("Total records: {$count}, Page records: {$servisRutins->count()}");
        
        return view('admin.servisRutin', [
            'kendaraan' => Kendaraan::whereIn('aset', ['guna', 'tidak guna'])->get(),
            'servisRutins' => $servisRutins,
            'search' => $search // Pass the search back to the view
        ]);
    }
    

    public function create()
    {
        $kendaraan = Kendaraan::all();
        return view('admin.servisRutin-form', compact('kendaraan'));
    }
    
    public function store(Request $request)
    {
        $request->merge([
            'harga' => str_replace('.', '', $request->harga),
            'kilometer' => str_replace('.', '', $request->kilometer),
        ]);   

        $validated = $request->validate([
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'tgl_servis_real' => 'required|date',
            'tgl_servis_selanjutnya' => 'required|date',
            'kilometer' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:200',
            'harga' => 'required|numeric|min:0',
            'bukti_bayar' => 'required|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ]);
    
        $buktiBayarPath = $request->file('bukti_bayar')->store('bukti-bayar', 'public');
    
        $tglServisSelanjutnya = $validated['tgl_servis_selanjutnya'];
    
        // Create new service record
        ServisRutin::create([
            'id_kendaraan' => $validated['id_kendaraan'],
            'user_id' => Auth::id(),
            'harga' => $validated['harga'],
            'kilometer' => $validated['kilometer'],
            'lokasi' => $validated['lokasi'],
            'bukti_bayar' => $buktiBayarPath,
            'tgl_servis_real' => $validated['tgl_servis_real'],
            'tgl_servis_selanjutnya' => $tglServisSelanjutnya
        ]);
    
        return redirect()->route('admin.servisRutin')
            ->with('success', 'Data servis rutin berhasil disimpan.');
    }

    public function detail($id)
    {
        $servis = ServisRutin::with(['kendaraan'])
            ->findOrFail($id);

        return view('admin.servisRutin-detail', compact('servis'));
    }

    public function getKendaraan($id) {
        return response()->json(Kendaraan::find($id));
    }
    
    public function getServisTerbaru($id_kendaraan) {
        $servis = ServisRutin::where('id_kendaraan', $id_kendaraan)
                    ->orderBy('tgl_servis_real', 'desc')
                    ->first();
        return response()->json(['tgl_servis_selanjutnya' => $servis->tgl_servis_selanjutnya ?? null]);
    }
    
    public function getFrekuensi($id_kendaraan) {
        $kendaraan = Kendaraan::find($id_kendaraan);
        return response()->json(['frekuensi' => $kendaraan->frekuensi ?? 0]);
    }

    public function edit($id)
    {
        $servis = ServisRutin::with('kendaraan')->findOrFail($id);
        
        return view('admin.servisRutin-edit', [
            'servis' => $servis,
            'merk' => $servis->kendaraan->merk ?? 'Tidak Diketahui',
            'tipe' => $servis->kendaraan->tipe ?? '',
            'plat' => $servis->kendaraan->plat_nomor ?? '-',
            'jadwal_servis' => $servis->tgl_servis_selanjutnya ?? '-'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'harga' => str_replace('.', '', $request->harga),
            'kilometer' => str_replace('.', '', $request->kilometer),
        ]);

        $servis = ServisRutin::findOrFail($id);
        $buktiValidasi = $servis->bukti_bayar ? 'nullable' : 'required';  

        $validated = $request->validate([
            'tgl_servis_real' => 'required|date_format:Y-m-d',
            'kilometer' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:200',
            'harga' => 'required|numeric|min:0',
            'bukti_bayar' => $buktiValidasi . '|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
            'remove_bukti_bayar' => 'nullable|boolean',
        ]);

        // Hitung ulang tanggal servis selanjutnya berdasarkan frekuensi servis kendaraan
        $frekuensiServis = $servis->kendaraan->frekuensi_servis ?? 1; // Default ke 1 bulan jika tidak ada
        $tglServisSelanjutnya = Carbon::parse($validated['tgl_servis_real'])->addMonths($frekuensiServis);

        $buktiBayarPath = $servis->bukti_bayar;

        if (isset($validated['remove_bukti_bayar']) && $validated['remove_bukti_bayar'] == 1) {
            if ($servis->bukti_bayar) {
                Storage::disk('public')->delete($servis->bukti_bayar);
            }
            $buktiBayarPath = null;
        }

        // Jika ada bukti bayar baru, hapus yang lama dan simpan yang baru
        if ($request->hasFile('bukti_bayar')) {
            if ($servis->bukti_bayar) {
                Storage::disk('public')->delete($servis->bukti_bayar);
            }
            $buktiBayarPath = $request->file('bukti_bayar')->store('bukti-bayar', 'public');
        }

        // Update data servis rutin
        $servis->update([
            'tgl_servis_real' => $validated['tgl_servis_real'],
            'tgl_servis_selanjutnya' => $tglServisSelanjutnya,
            'kilometer' => $validated['kilometer'],
            'lokasi' => $validated['lokasi'],
            'harga' => $validated['harga'],
            'bukti_bayar' => $buktiBayarPath,
        ]);

        return redirect()->route('admin.servisRutin')
            ->with('success', 'Data servis rutin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $servis = ServisRutin::findOrFail($id);
    
        // Simpan ID Kendaraan sebelum menghapus
        $idKendaraan = $servis->id_kendaraan;
    
        // Hapus bukti bayar jika ada
        if ($servis->bukti_bayar) {
            Storage::disk('public')->delete($servis->bukti_bayar);
        }
    
        // Hapus data dari database
        $servis->delete();
    
        // Cari record servis sebelumnya untuk kendaraan yang sama
        $servisSebelumnya = ServisRutin::where('id_kendaraan', $idKendaraan)
            ->orderBy('tgl_servis_real', 'desc')
            ->first();
    
        if ($servisSebelumnya) {
            return redirect()->route('admin.servisRutin', ['id' => $servisSebelumnya->id_servis_rutin])
                ->with('success', 'Data servis rutin berhasil dihapus.');
        }

        $servis = ServisRutin::find($id);

        if (!$servis) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $servis->delete();

        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }
    
}