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
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $subquery = DB::table('servis_rutin')
            ->select('id_kendaraan', DB::raw('MAX(id_servis_rutin) as max_id_servis_rutin'))
            ->groupBy('id_kendaraan');

        $query = Kendaraan::select(
                'kendaraan.*',
                'servis_rutin.id_servis_rutin',
                'servis_rutin.tgl_servis_real',
                'servis_rutin.tgl_servis_selanjutnya',
                'servis_rutin.updated_at as servis_updated_at'
            )
            ->leftJoinSub($subquery, 'latest', function ($join) {
                $join->on('kendaraan.id_kendaraan', '=', 'latest.id_kendaraan');
            })
            ->leftJoin('servis_rutin', function ($join) {
                $join->on('kendaraan.id_kendaraan', '=', 'servis_rutin.id_kendaraan')
                     ->on('servis_rutin.id_servis_rutin', '=', 'latest.max_id_servis_rutin');
            })
            ->whereIn('kendaraan.aset', ['guna', 'tidak guna']);

        if (!empty($search)) {
            // Logika pencarian tanggal
            if (preg_match('/^\d{4}$/', $search)) { // Hanya tahun
                $query->whereYear('servis_rutin.tgl_servis_real', $search);
            } elseif (preg_match('/^\d{2}$/', $search)) { // Hanya bulan atau tanggal
                if (strlen($search) == 2 && $search <= 12) { // Asumsikan bulan
                    $query->whereMonth('servis_rutin.tgl_servis_real', $search);
                } else { // Asumsikan tanggal
                    $query->whereDay('servis_rutin.tgl_servis_real', $search);
                }
            } elseif (preg_match('/^\d{2}-\d{4}$/', $search)) { // Bulan-Tahun
                $parts = explode('-', $search);
                $query->whereMonth('servis_rutin.tgl_servis_real', $parts[0])->whereYear('servis_rutin.tgl_servis_real', $parts[1]);
            } elseif (preg_match('/^\d{4}-\d{2}$/', $search)) { // Tahun-Bulan
                $parts = explode('-', $search);
                $query->whereYear('servis_rutin.tgl_servis_real', $parts[0])->whereMonth('servis_rutin.tgl_servis_real', $parts[1]);
            } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) { // Tanggal-Bulan-Tahun (format lengkap)
                try {
                    $searchDate = Carbon::createFromFormat('d-m-Y', $search);
                    $query->whereDate('servis_rutin.tgl_servis_real', $searchDate);
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
                            $outerQuery->whereYear('servis_rutin.tgl_servis_real', $yearTerm);
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
                                    ->orWhereRaw("CAST(YEAR(servis_rutin.tgl_servis_real) AS CHAR) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LPAD(MONTH(servis_rutin.tgl_servis_real), 2, '0') LIKE ?", ["%{$term}%"])
                                    ->orWhereRaw("LPAD(DAY(servis_rutin.tgl_servis_real), 2, '0') LIKE ?", ["%{$term}%"])
                                    ->orWhereRaw("
                                        CASE
                                            WHEN servis_rutin.tgl_servis_real IS NULL THEN 'belum pernah servis'
                                            WHEN servis_rutin.tgl_servis_real < DATE_SUB(CURDATE(), INTERVAL kendaraan.frekuensi_servis MONTH) THEN 'jatuh tempo'
                                            WHEN DATEDIFF(DATE_ADD(servis_rutin.tgl_servis_real, INTERVAL kendaraan.frekuensi_servis MONTH), CURDATE()) <= 30 THEN 'mendekati jatuh tempo'
                                            ELSE 'sudah dibayar'
                                        END LIKE ?", ["%$term%"]);
                            });
                        }
                    });
                }
            }
        }

        $servisRutins = $query
            ->orderByDesc('servis_rutin.tgl_servis_real')
            ->orderByDesc('servis_rutin.updated_at')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('admin.servisRutin', [
            'kendaraan' => Kendaraan::whereIn('aset', ['guna', 'tidak guna'])->get(),
            'servisRutins' => $servisRutins,
            'search' => $search,
            ]);
    }

    public function create(Request $request)
    {
        $kendaraan = Kendaraan::all();
        $page = $request->query('page');
        $search = $request->query('search');

        return view('admin.servisRutin-form', compact('kendaraan', 'page', 'search'));
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
        $buktiValidasi = ($servis->bukti_bayar && !$request->input('remove_bukti_bayar')) ? 'nullable' : 'required';


        $validated = $request->validate([
            'tgl_servis_real' => 'required|date_format:Y-m-d',
            'kilometer' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:200',
            'harga' => 'required|numeric|min:0',
            'bukti_bayar' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'bukti_bayar_lama' => 'nullable|string',
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

        // // Jika ada bukti bayar baru, hapus yang lama dan simpan yang baru
        // if ($request->hasFile('bukti_bayar')) {
        //     if ($servis->bukti_bayar) {
        //         Storage::disk('public')->delete($servis->bukti_bayar);
        //     }
        //     $buktiBayarPath = $request->file('bukti_bayar')->store('bukti-bayar', 'public');
        // }

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

        // $servis = ServisRutin::find($id);

        // if (!$servis) {
        //     return response()->json(['message' => 'Data tidak ditemukan'], 404);
        // }

        // $servis->delete();

        // return response()->json(['message' => 'Data berhasil dihapus'], 200);

        return redirect()->route('admin.servisRutin', ['id' => $idKendaraan])
            ->with('success', 'Data servis rutin berhasil dihapus.');
    }
    
}