<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\ServisRutin;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ServisRutinController extends Controller
{
    public function index(Request $request)
    {
        $query = ServisRutin::with(['kendaraan']);

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
            'kilometer' => 'required|numeric|min:0',
            'lokasi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'bukti_bayar' => 'required|image|max:2048', // Max 2MB
            'bukti_fisik' => 'required|image|max:2048', // Max 2MB
        ]);
    
        // Store payment proof image
        $buktiBayarPath = $request->file('bukti_bayar')->store('bukti-bayar', 'public');
        
        // Store physical proof image
        $buktiFisikPath = $request->file('bukti_fisik')->store('bukti-fisik', 'public');
    
        // Calculate next service date (assuming 1 month from real service date)
        $tglServisSelanjutnya = Carbon::parse($validated['tgl_servis_real'])->addMonth();
    
        // Create new service record
        ServisRutin::create([
            'id_kendaraan' => $validated['id_kendaraan'],
            'user_id' => Auth::id(),
            'harga' => $validated['harga'],
            'kilometer' => $validated['kilometer'],
            'lokasi' => $validated['lokasi'],
            'bukti_bayar' => $buktiBayarPath,
            'bukti_fisik' => $buktiFisikPath,
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

    /**
     * Show form for editing service record
     */
    // public function edit($id)
    // {
    //     $servisRutin = ServisRutin::findOrFail($id);
    //     $kendaraan = Kendaraan::all();
        
    //     return view('admin.servis-rutin.edit', compact('servisRutin', 'kendaraan'));
    // }

    /**
     * Update the specified service record
     */
    // public function update(Request $request, $id)
    // {
    //     $servisRutin = ServisRutin::findOrFail($id);

    //     $validated = $request->validate([
    //         'kendaraan_id' => 'required|exists:kendaraans,id',
    //         'tanggal_servis' => 'required|date',
    //         'kilometer_sekarang' => 'required|numeric|min:0',
    //         'jenis_servis' => 'required|string',
    //         'deskripsi' => 'required|string',
    //         'biaya' => 'required|numeric|min:0',
    //         'bengkel' => 'required|string',
    //         'nota_servis' => 'nullable|image|max:2048', // Optional on update
    //         'keterangan' => 'nullable|string',
    //     ]);

    //     // Handle new receipt upload if provided
    //     if ($request->hasFile('nota_servis')) {
    //         // Delete old receipt
    //         if ($servisRutin->nota_servis) {
    //             Storage::disk('public')->delete($servisRutin->nota_servis);
    //         }
    //         $notaPath = $request->file('nota_servis')->store('nota-servis', 'public');
    //         $validated['nota_servis'] = $notaPath;
    //     }

    //     $servisRutin->update($validated);

    //     // Update next service schedule if date changed
    //     if ($servisRutin->wasChanged('tanggal_servis')) {
    //         $this->updateNextServiceSchedule($validated['kendaraan_id'], $validated['tanggal_servis']);
    //     }

    //     return redirect()->route('admin.servis-rutin.index')
    //         ->with('success', 'Data servis rutin berhasil diperbarui.');
    // }

    // /**
    //  * Remove the specified service record
    //  */
    // public function destroy($id)
    // {
    //     $servisRutin = ServisRutin::findOrFail($id);
        
    //     // Delete receipt file
    //     if ($servisRutin->nota_servis) {
    //         Storage::disk('public')->delete($servisRutin->nota_servis);
    //     }

    //     $servisRutin->delete();

    //     return redirect()->route('admin.servis-rutin.index')
    //         ->with('success', 'Data servis rutin berhasil dihapus.');
    // }

    // /**
    //  * Update next service schedule for vehicle
    //  */
    // private function updateNextServiceSchedule($kendaraanId, $lastServiceDate)
    // {
    //     $kendaraan = Kendaraan::find($kendaraanId);
        
    //     // Set next service date (assuming 3 months interval)
    //     $nextServiceDate = Carbon::parse($lastServiceDate)->addMonths(3);
        
    //     $kendaraan->update([
    //         'jadwal_servis_berikutnya' => $nextServiceDate,
    //         'status_servis' => 'BELUM'
    //     ]);
    // }
}