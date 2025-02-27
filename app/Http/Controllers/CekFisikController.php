<?php

namespace App\Http\Controllers;

use App\Models\CekFisik;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CekFisikController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $convertedDate = null;
        $partialDate = null;

        if ($search) {
            // Jika pencarian adalah tanggal lengkap (d-m-Y)
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) {
                try {
                    // Mengubah tanggal yang dicari ke format Y-m-d
                    $convertedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
                } catch (\Exception $e) {
                    $convertedDate = null;
                }
            }

            // Jika pencarian adalah tanggal parsial (misalnya d-m atau d saja)
            if (preg_match('/^\d{2}-\d{2}$/', $search)) {
                // Format pencarian parsial d-m (misalnya "18-02")
                $partialDate = '%-' . $search . '%';
            } elseif (preg_match('/^\d{2}$/', $search)) {
                // Format pencarian parsial hanya hari (misalnya "18")
                $partialDate = '%-' . $search . '%';
            } elseif (preg_match('/^\d{1,2}$/', $search)) {
                // Format pencarian parsial hanya bulan (misalnya "02")
                $partialDate = '%' . $search . '-%';
            } elseif (preg_match('/^\d{4}$/', $search)) {
                // Format pencarian tahun saja (misalnya "2025")
                $partialDate = $search . '%';
            }
        }

        $kendaraan = Kendaraan::leftJoin('cek_fisik', 'kendaraan.id_kendaraan', '=', 'cek_fisik.id_kendaraan')
            ->select(
                'kendaraan.id_kendaraan',
                'kendaraan.merk',
                'kendaraan.tipe',
                'kendaraan.plat_nomor',
                DB::raw('(SELECT tgl_cek_fisik FROM cek_fisik WHERE cek_fisik.id_kendaraan = kendaraan.id_kendaraan ORDER BY tgl_cek_fisik DESC LIMIT 1) as tgl_cek_fisik_terakhir')
            )
            ->groupBy('kendaraan.id_kendaraan', 'kendaraan.merk', 'kendaraan.tipe', 'kendaraan.plat_nomor')
            ->when($search, function ($query, $search) use ($convertedDate, $partialDate) {
                return $query->where(function ($q) use ($search, $convertedDate, $partialDate) {
                    // Pencarian berdasarkan tanggal lengkap
                    if ($convertedDate) {
                        $q->orWhere(DB::raw('(SELECT tgl_cek_fisik FROM cek_fisik WHERE cek_fisik.id_kendaraan = kendaraan.id_kendaraan ORDER BY tgl_cek_fisik DESC LIMIT 1)'), $convertedDate);
                    }

                    // Pencarian berdasarkan tanggal parsial (misalnya 18-02 atau 18)
                    if ($partialDate) {
                        $q->orWhere(DB::raw('(SELECT tgl_cek_fisik FROM cek_fisik WHERE cek_fisik.id_kendaraan = kendaraan.id_kendaraan ORDER BY tgl_cek_fisik DESC LIMIT 1)'), 'like', $partialDate);
                    }

                    // Pencarian berdasarkan merk, tipe, atau plat nomor kendaraan
                    $q->orWhere('kendaraan.merk', 'LIKE', "%{$search}%")
                        ->orWhere('kendaraan.tipe', 'LIKE', "%{$search}%")
                        ->orWhere('kendaraan.plat_nomor', 'LIKE', "%{$search}%");
                });
            })
            ->paginate(10)
            ->appends(['search' => $search]); // Append search parameter to pagination links

        return view('admin.cek-fisik.index', compact('kendaraan'));
    }


    public function create($id_kendaraan)
    {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);
        return view('admin.cek-fisik.create', compact('kendaraan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'tgl_cek_fisik' => 'required|date',
            'mesin' => 'required',
            'accu' => 'required',
            'air_radiator' => 'required',
            'air_wiper' => 'required',
            'body' => 'required',
            'ban' => 'required',
            'pengharum' => 'required',
            'kondisi_keseluruhan' => 'required',
            'catatan' => 'nullable|string'
        ]);

        CekFisik::create(array_merge($request->all(), ['user_id' => Auth::id()]));

        return redirect()->route('admin.cek-fisik.index', [
            'page' => $request->input('page', 1),
            'search' => $request->input('search')
        ])->with('success', 'Cek fisik berhasil dicatat.');
    }

    public function detail($id_kendaraan)
    {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);
        
        // Ambil cek fisik terakhir untuk kendaraan
        $cekFisik = CekFisik::where('id_kendaraan', $id_kendaraan)
            ->orderBy('tgl_cek_fisik', 'desc')
            ->first();

        return view('admin.cek-fisik.detail', compact('kendaraan', 'cekFisik'));
    }

    public function edit($id_kendaraan)
    {
        $search = request()->query('search');
        $page = request()->query('page', 1);

        // Ambil cek fisik terakhir untuk kendaraan
        $cekFisik = CekFisik::where('id_kendaraan', $id_kendaraan)
            ->orderBy('tgl_cek_fisik', 'desc')
            ->first();

        if (!$cekFisik) {
            return redirect()->route('admin.cek-fisik.index', [
                'page' => $page,
                'search' => $search
            ])->with('error', 'Cek fisik tidak ditemukan.');
        }
    
        return view('admin.cek-fisik.edit', compact('cekFisik', 'search', 'page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_cek_fisik' => 'required|date',
            'mesin' => 'required',
            'accu' => 'required',
            'air_radiator' => 'required',
            'air_wiper' => 'required',
            'body' => 'required',
            'ban' => 'required',
            'pengharum' => 'required',
            'kondisi_keseluruhan' => 'required',
            'catatan' => 'nullable|string'
        ]);

        $cekFisik = CekFisik::findOrFail($id);
        $cekFisik->update($request->all());

        $page = $request->input('page', 1);
        $search = $request->input('search');

        return redirect()
            ->route('admin.cek-fisik.index', [
                'page' => $page,
                'search' => $search
            ])
            ->with('success', 'Data cek fisik berhasil diperbarui.');
    }

    public function destroy($id_kendaraan)
    {
        // Ambil cek fisik terakhir untuk kendaraan
        $cekFisikTerakhir = CekFisik::where('id_kendaraan', $id_kendaraan)
            ->orderBy('tgl_cek_fisik', 'desc')
            ->first();

        if ($cekFisikTerakhir) {
            $cekFisikTerakhir->delete();
        }

        $page = request()->query('page', 1);
        $search = request()->query('search');

        return redirect()->route('admin.cek-fisik.index', [
            'page' => $page,
            'search' => $search
        ])->with('success', 'Cek fisik terakhir berhasil dihapus.');
    }
}
