<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pajak;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PajakController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');
    $statusFilter = $request->input('status');

    $dataKendaraanQuery = Kendaraan::select(
        'kendaraan.*',
        'pajak.id_pajak',
        'pajak.user_id',
        'pajak.tahun',
        'pajak.tgl_bayar',
        'pajak.tgl_jatuh_tempo',
        'pajak.bukti_bayar_pajak',
        'pajak.nominal',
        'pajak.biaya_pajak_lain',
        DB::raw('DATE_ADD(latest_pajak.max_jatuh_tempo, INTERVAL 1 YEAR) as tgl_jatuh_tempo_seharusnya')
    )
    ->distinct()
    ->leftJoin(DB::raw('(SELECT id_kendaraan, MAX(tgl_bayar) as max_bayar, MAX(tgl_jatuh_tempo) as max_jatuh_tempo 
                    FROM pajak GROUP BY id_kendaraan) as latest_pajak'), function ($join) {
        $join->on('kendaraan.id_kendaraan', '=', 'latest_pajak.id_kendaraan');
    })
    ->leftJoin('pajak', function ($join) {
        $join->on('kendaraan.id_kendaraan', '=', 'pajak.id_kendaraan')
            ->on('pajak.tgl_bayar', '=', 'latest_pajak.max_bayar')
            ->on('pajak.tgl_jatuh_tempo', '=', 'latest_pajak.max_jatuh_tempo');
    })
    ->where('kendaraan.aset', 'guna');

    $dataKendaraan = $dataKendaraanQuery->get();

    foreach ($dataKendaraan as $item) {
        $today = now(); 
        $dueDate = $item->tgl_jatuh_tempo_seharusnya ? \Carbon\Carbon::parse($item->tgl_jatuh_tempo_seharusnya) : null;

        if (!$dueDate) {
            $item->status = 'BELUM ADA DATA PAJAK';
        } elseif ($today->diffInDays($dueDate, false) <= 0) { 
            $item->status = 'JATUH TEMPO';
        } elseif ($today->diffInDays($dueDate, false) <= 30) {
            $item->status = 'MENDEKATI JATUH TEMPO';
        } else {
            $item->status = 'SUDAH DIBAYAR';
        }
    }

    if (!empty($search)) {
        $dataKendaraan = $dataKendaraan->filter(function ($item) use ($search) {
            $search = strtolower($search);
            $formatTanggal = function ($tanggal) {
                return $tanggal ? \Carbon\Carbon::parse($tanggal)->format('d-m-Y') : null;
            };

            $tgl_bayar = $formatTanggal($item->tgl_bayar);
            $tgl_jatuh_tempo = $formatTanggal($item->tgl_jatuh_tempo);
            $tgl_jatuh_tempo_seharusnya = $formatTanggal($item->tgl_jatuh_tempo_seharusnya);

            $normalizeNumber = function ($number) {
                return preg_replace('/[.,]/', '', (string) $number);
            };

            $nominal = $normalizeNumber($item->nominal);
            $biayaPajakLain = $normalizeNumber($item->biaya_pajak_lain);
            $searchNumeric = $normalizeNumber($search);

            $kombinasiPencarian = [
                strtolower($item->merk . ' ' . $item->tipe . ' ' . $item->plat_nomor),
                strtolower($item->tipe . ' ' . $item->plat_nomor),
                strtolower($item->merk . ' ' . $item->tipe . ' ' . $item->tahun),
                strtolower($item->merk . ' ' . $item->tipe . ' ' . $item->status),
                strtolower($item->merk . ' ' . $item->plat_nomor),
                strtolower($item->tipe . ' ' . $item->tahun),
                strtolower($item->plat_nomor . ' ' . $item->tahun),
                strtolower($item->merk . ' ' . $item->tipe . ' ' . $tgl_jatuh_tempo),
                strtolower($item->merk . ' ' . $item->tipe . ' ' . $nominal),
                strtolower($item->merk . ' ' . $item->tipe . ' ' . $biayaPajakLain),
                strtolower($item->merk . ' ' . $item->tipe . ' ' . $tgl_bayar),
                strtolower($item->merk . ' ' . $item->tipe . ' ' . $tgl_jatuh_tempo_seharusnya),
            ];

            foreach ($kombinasiPencarian as $kombinasi) {
                if (stripos($kombinasi, $search) !== false) {
                    return true;
                }
            }

            return stripos($item->status, $search) !== false ||
                stripos((string) $item->tahun, $search) !== false ||
                stripos((string) $tgl_bayar, $search) !== false ||
                stripos((string) $tgl_jatuh_tempo, $search) !== false ||
                stripos((string) $tgl_jatuh_tempo_seharusnya, $search) !== false ||
                stripos($nominal, $searchNumeric) !== false ||
                stripos($biayaPajakLain, $searchNumeric) !== false;
        });
    }

    if (!empty($statusFilter)) {
        $dataKendaraan = $dataKendaraan->filter(function ($item) use ($statusFilter) {
            return strtolower($item->status) == strtolower($statusFilter);
        });
    }

    $dataKendaraan = new \Illuminate\Pagination\LengthAwarePaginator(
        $dataKendaraan->forPage($request->page, 10), 
        $dataKendaraan->count(), 
        10, 
        $request->page, 
        ['path' => $request->url(), 'query' => $request->query()] 
    );

    return view('admin.pajak.daftar_kendaraan_pajak', compact('dataKendaraan', 'search', 'statusFilter'));
}

    public function kelola($id_kendaraan)
    {
        $kendaraan = Kendaraan::findOrFail($id_kendaraan);
        
        $lastPajak = Pajak::where('id_kendaraan', $kendaraan->id_kendaraan)->latest('tgl_jatuh_tempo')->first();
        
        if ($lastPajak) {
            $tgl_jatuh_tempo = date('Y-m-d', strtotime($lastPajak->tgl_jatuh_tempo . ' +1 year'));
        } else {
            $tgl_jatuh_tempo = null;
        }

        return view('admin.pajak.kelola', compact('kendaraan', 'tgl_jatuh_tempo'));
    }


    public function store(Request $request)
    {
        try {
            Log::info('DEBUG_PAJAK: Incoming request', ['request_data' => $request->all()]);

            $request->validate([
                'id_kendaraan' => 'required|exists:kendaraan,id_kendaraan',
                'tanggal_bayar' => 'required|date',
                'nominal_tagihan' => 'required|regex:/^[0-9,]+$/',
                'foto' => 'required|mimes:jpeg,jpg,png,pdf|max:5120',
                'tanggal_jatuh_tempo' => 'required|date',
            ]);

            Log::info('DEBUG_PAJAK: Validation passed');

            $kendaraan = Kendaraan::where('id_kendaraan', $request->id_kendaraan)->first();
            if (!$kendaraan) {
                Log::warning('DEBUG_PAJAK: Kendaraan not found', ['id_kendaraan' => $request->id_kendaraan]);
                return redirect()->back()->with('error', 'Kendaraan tidak ditemukan!');
            }

            Log::info('DEBUG_PAJAK: Kendaraan found', ['kendaraan' => $kendaraan]);

            $path = null;
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('bukti_bayar_pajak', 'public');
                Log::info('DEBUG_PAJAK: File uploaded', ['file_path' => $path]);
            }

            $newPajak = Pajak::create([
                'user_id' => Auth::id(),
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'tahun' => date('Y', strtotime($request->tanggal_bayar)),
                'tgl_bayar' => $request->tanggal_bayar,
                'bukti_bayar_pajak' => $path,
                'tgl_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'biaya_pajak_lain' => $request->biaya_lain ? preg_replace('/[^0-9]/', '', $request->biaya_lain) : null,
                'nominal' => preg_replace('/[^0-9]/', '', $request->nominal_tagihan),
            ]);

            $page = $request->input('current_page', 1);

            if ($newPajak) {
                Log::info('DEBUG_PAJAK: Success - Redirecting to daftar_kendaraan_pajak');

                return redirect()
                    ->route('pajak.daftar_kendaraan_pajak', ['page' => $page])
                    ->with('success', 'Data pajak berhasil diperbarui!');
            } else {
                Log::error('DEBUG_PAJAK: Failed to create new pajak');
                return redirect()->back()->with('error', 'Gagal menyimpan data pajak!');
            }

        } catch (\Exception $e) {
            Log::error('DEBUG_PAJAK: Exception occurred', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit($id_pajak)
    {
        $pajak = Pajak::with('kendaraan')->where('id_pajak', $id_pajak)->firstOrFail();
        return view('admin.pajak.edit', compact('pajak'));
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('DEBUG_PAJAK_UPDATE: Incoming request', ['request_data' => $request->all()]);
            $request->merge([
                'nominal_tagihan' => preg_replace('/\D/', '', $request->nominal_tagihan),
                'biaya_lain' => preg_replace('/\D/', '', $request->biaya_lain),
            ]);
            
            $request->validate([
                'tanggal_bayar' => 'sometimes|date',
                'nominal_tagihan' => 'sometimes|regex:/^[0-9,]+$/',
                'biaya_lain' => 'nullable|regex:/^[0-9,]+$/',
                'foto' => 'nullable|mimes:jpeg,jpg,png,pdf|max:5120',
                'tanggal_jatuh_tempo' => 'required|date',
            ]);

            Log::info('DEBUG_PAJAK_UPDATE: Validation passed');

            $pajak = Pajak::findOrFail($id);
            Log::info('DEBUG_PAJAK_UPDATE: Pajak found', ['pajak' => $pajak]);

            if ($request->has('tanggal_bayar')) {
                $pajak->tgl_bayar = $request->tanggal_bayar;
            }
            if ($request->has('tanggal_jatuh_tempo')) {
                $pajak->tgl_jatuh_tempo = $request->tanggal_jatuh_tempo;
            }
            if ($request->has('nominal_tagihan')) {
                $pajak->nominal = preg_replace('/[^0-9]/', '', $request->nominal_tagihan);
            }
            if ($request->has('biaya_lain')) {
                $biayaLain = preg_replace('/[^0-9]/', '', $request->biaya_lain);
                $pajak->biaya_pajak_lain = $biayaLain !== '' ? $biayaLain : null; // Set null jika kosong
            }            

            if ($request->hasFile('foto')) {
                if ($pajak->bukti_bayar_pajak) {
                    $oldFile = storage_path('app/public/' . $pajak->bukti_bayar_pajak);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $path = $request->file('foto')->store('bukti_bayar_pajak', 'public');
                Log::info('DEBUG_PAJAK_UPDATE: File uploaded', ['file_path' => $path]);

                $pajak->bukti_bayar_pajak = $path;
            }

            $pajak->save();
            Log::info('DEBUG_PAJAK_UPDATE: Data updated successfully');

            $page = $request->input('current_page', 1);

            return redirect()
                ->route('pajak.daftar_kendaraan_pajak', ['page' => $page])
                ->with('success', 'Data pajak berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('DEBUG_PAJAK_UPDATE: Exception occurred', ['error' => $e->getMessage()]);
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()]);
        }
    }


    public function detail($id_pajak) {
        $pajak = Pajak::with('kendaraan')->where('id_pajak', $id_pajak)->firstOrFail();
        $tglJatuhTempoTahunDepan = \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo)->addYear();
        $pajak->tgl_jatuh_tempo_tahun_depan = $tglJatuhTempoTahunDepan;

        return view('admin.pajak.detail', compact('pajak'));
    }

    public function hapus($id_pajak, Request $request)
    {
        try {
            $pajak = Pajak::findOrFail($id_pajak);

            if ($pajak->bukti_bayar_pajak) {
                $filePath = storage_path('app/public/' . $pajak->bukti_bayar_pajak);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $pajak->delete();

            Log::info('DEBUG_PAJAK_DELETE: Data pajak berhasil dihapus', ['id_pajak' => $id_pajak]);

            $page = request()->query('page');


            return redirect()->route('pajak.daftar_kendaraan_pajak', ['page' => $page])->with('success', 'Data pajak berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('DEBUG_PAJAK_DELETE: Error saat menghapus', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data!']);
        }
    }

    public function deleteFile(Request $request)
    {
        try {
            $pajak = Pajak::findOrFail($request->id);
            $fileType = $request->file_type;

            if ($fileType === 'bukti_bayar_pajak' && $pajak->bukti_bayar_pajak) {
                Storage::disk('public')->delete($pajak->bukti_bayar_pajak);
                $pajak->bukti_bayar_pajak = null;
            } else {
                return response()->json(['error' => 'File tidak ditemukan'], 404);
            }

            $pajak->save();

            return response()->json(['success' => 'File berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
