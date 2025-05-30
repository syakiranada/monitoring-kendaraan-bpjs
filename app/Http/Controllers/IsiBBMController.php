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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class IsiBBMController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        
        // Langkah 1: Dapatkan daftar kendaraan yang tersedia
        $availableVehicleIds = Kendaraan::where('status_ketersediaan', 'Tersedia')
            ->pluck('id_kendaraan');
        
        // Langkah 2: Temukan pengisian BBM terbaru untuk setiap kendaraan
        $latestBbmPerVehicle = DB::table('bbm')
            ->select('id_kendaraan', DB::raw('MAX(id_bbm) as latest_bbm_id'))
            ->whereIn('id_kendaraan', $availableVehicleIds)
            ->where('user_id', $user->id)
            ->groupBy('id_kendaraan');
        
        // Langkah 3: Gabungkan data kendaraan dengan pengisian BBM terbaru
        $query = Kendaraan::select(
                'kendaraan.*',
                'bbm.id_bbm',
                'bbm.tgl_isi',
                'bbm.updated_at as bbm_updated_at',
                'bbm.user_id'
            )
            ->leftJoinSub($latestBbmPerVehicle, 'latest_bbm', function ($join) {
                $join->on('kendaraan.id_kendaraan', '=', 'latest_bbm.id_kendaraan');
            })
            ->leftJoin('bbm', function ($join) {
                $join
                ->on('bbm.id_bbm', '=', 'latest_bbm.latest_bbm_id');
            })
            ->where('kendaraan.status_ketersediaan', '=', 'Tersedia');

        if (!empty($search)) {
            // Logika pencarian tanggal
            if (preg_match('/^\d{4}$/', $search)) { // Hanya tahun
                $query->whereYear('bbm.tgl_isi', $search);
            } elseif (preg_match('/^\d{2}$/', $search)) { // Hanya bulan atau tanggal
                if (strlen($search) == 2 && $search <= 12) { // Asumsikan bulan
                    $query->whereMonth('bbm.tgl_isi', $search);
                } else { // Asumsikan tanggal
                    $query->whereDay('bbm.tgl_isi', $search);
                }
            } elseif (preg_match('/^\d{2}-\d{4}$/', $search)) { // Bulan-Tahun
                $parts = explode('-', $search);
                $query->whereMonth('bbm.tgl_isi', $parts[0])->whereYear('bbm.tgl_isi', $parts[1]);
            } elseif (preg_match('/^\d{4}-\d{2}$/', $search)) { // Tahun-Bulan
                $parts = explode('-', $search);
                $query->whereYear('bbm.tgl_isi', $parts[0])->whereMonth('bbm.tgl_isi', $parts[1]);
            } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) { // Tanggal-Bulan-Tahun (format lengkap)
                try {
                    $searchDate = Carbon::createFromFormat('d-m-Y', $search);
                    $query->whereDate('bbm.tgl_isi', $searchDate);
                } catch (\Exception $e) {
                    Log::error("Error parsing search date:", ['search' => $search, 'error' => $e->getMessage()]);
                }
            } else {
                // Logika pencarian teks lainnya
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
                            $outerQuery->whereYear('bbm.tgl_isi', $yearTerm);
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
                                    ->orWhereRaw("CAST(YEAR(kendaraan.tgl_pembelian) AS CHAR) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.frekuensi_servis) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LOWER(kendaraan.status_ketersediaan) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("CAST(YEAR(bbm.tgl_isi) AS CHAR) LIKE ?", ["%$term%"])
                                    ->orWhereRaw("LPAD(MONTH(bbm.tgl_isi), 2, '0') LIKE ?", ["%{$term}%"])
                                    ->orWhereRaw("LPAD(DAY(bbm.tgl_isi), 2, '0') LIKE ?", ["%{$term}%"]);
                            });
                        }
                    });
                }
            }
        }

        // Mengurutkan berdasarkan id kendaraan untuk memastikan entri unik
        // Dan kemudian berdasarkan tanggal pengisisan untuk mendapatkan yang terbaru
        $pengisianBBMs = $query
            // ->orderBy('kendaraan.id_kendaraan')
            ->orderBy('bbm.tgl_isi', 'desc')
            ->orderBy('bbm.updated_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('admin.pengisianBBM', [
            'kendaraanTersedia' => Kendaraan::where('status_ketersediaan', 'Tersedia')->get(),
            'pengisianBBMs' => $pengisianBBMs
        ]);
    }

    public function create()
    {
        $kendaraan = Kendaraan::all();
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
            'jenis_bbm' => 'required|string|in:Pertalite,Pertamax,Pertamax Turbo,Dexlite,Pertamina Dex,Solar,Bio Solar',
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
                ->orderBy('updated_at', 'desc')
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