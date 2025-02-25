<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\ServisRutin;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServisRutinController extends Controller
{
    public function index(Request $request)
    {
        $query = ServisRutin::with(['kendaraan'])
            ->whereIn('id_servis_rutin', function ($subquery) {
                $subquery->selectRaw('MAX(id_servis_rutin)')
                    ->from('servis_rutin')
                    ->groupBy('id_kendaraan');
            });
    
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('kendaraan', function($q) use ($search) {
                $q->where('merek', 'like', "%{$search}%")
                  ->orWhere('tipe', 'like', "%{$search}%")
                  ->orWhere('plat_nomor', 'like', "%{$search}%");
            });
        }
    
        $servisRutins = $query->orderBy('tgl_servis_real', 'desc')
                              ->paginate(10);
    
        return view('admin.servisRutin', compact('servisRutins'));
    }
    

    public function create()
    {
        $kendaraan = Kendaraan::all();
        return view('admin.servisRutin-form', compact('kendaraan'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
            'tgl_servis_real' => 'required|date',
            'tgl_servis_selanjutnya' => 'required|date',
            'kilometer' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:200',
            'harga' => 'required|numeric|min:0',
            'bukti_bayar' => 'required|image|max:2048', // Max 2MB
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
        $validated = $request->validate([
            'tgl_servis_real' => 'required|date_format:Y-m-d',
            'kilometer' => 'required|numeric|min:0',
            'lokasi' => 'required|string|max:200',
            'harga' => 'required|numeric|min:0',
            'bukti_bayar' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $servis = ServisRutin::findOrFail($id);

        // Hitung ulang tanggal servis selanjutnya berdasarkan frekuensi servis kendaraan
        $frekuensiServis = $servis->kendaraan->frekuensi_servis ?? 1; // Default ke 1 bulan jika tidak ada
        $tglServisSelanjutnya = Carbon::parse($validated['tgl_servis_real'])->addMonths($frekuensiServis);

        // Jika ada bukti bayar baru, hapus yang lama dan simpan yang baru
        if ($request->hasFile('bukti_bayar')) {
            if ($servis->bukti_bayar) {
                ServisRutin::disk('public')->delete($servis->bukti_bayar);
            }
            $buktiBayarPath = $request->file('bukti_bayar')->store('bukti-bayar', 'public');
            $validated['bukti_bayar'] = $buktiBayarPath;
        }

        // Update data servis rutin
        $servis->update([
            'tgl_servis_real' => $validated['tgl_servis_real'],
            'tgl_servis_selanjutnya' => $tglServisSelanjutnya,
            'kilometer' => $validated['kilometer'],
            'lokasi' => $validated['lokasi'],
            'harga' => $validated['harga'],
            'bukti_bayar' => $validated['bukti_bayar'] ?? $servis->bukti_bayar,
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
    
        return redirect()->route('admin.servisRutin')
            ->with('success', 'Data servis rutin berhasil dihapus.');
    }
    
}