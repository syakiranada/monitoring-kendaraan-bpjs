<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


use App\Models\Kendaraan;
use App\Models\Asuransi;
use App\Models\BBM;
use App\Models\CekFisik;
use App\Models\Pajak;
use App\Models\Peminjaman;
use App\Models\ServisInsidental;
use App\Models\ServisRutin;

class PeminjamanPenggunaController extends Controller
{
    public function peminjamanPage()
    {
        $userId = Auth::id(); // Ambil ID user yang sedang login
        $daftarPeminjaman = Peminjaman::with('kendaraan') // Ambil data kendaraan juga
            ->where('user_id', $userId)
            ->orderBy('tgl_mulai', 'desc') // Urutkan dari terbaru
            ->get();
        
        return view('pengguna.peminjaman', compact('daftarPeminjaman'));
    }
    public function showForm()
    {
        return view('pengguna.formPeminjaman');
    }
    public function getAvailableKendaraan(Request $request)
    {
        // Gabungkan tanggal & waktu dari request 
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_mulai} {$request->jam_mulai}");
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_selesai} {$request->jam_selesai}");

        // Query kendaraan yang TIDAK sedang dipinjam dalam rentang waktu yang dipilih GET YANG DI TABEL PEMINJAMAN STATUSNYA "DISETUJUI"
        // Query kendaraan yang TIDAK sedang dipinjam dalam rentang waktu yang dipilih DAN memiliki status "Tersedia"
        $availableKendaraan = Kendaraan::where('status_ketersediaan', 'Tersedia')
            // Hanya kendaraan dengan status "Tersedia"
            ->where('aset', 'Guna')
            ->whereNotIn('id_kendaraan', function ($query) use ($startDateTime, $endDateTime) {
                $query->select('id_kendaraan')
                    ->from('peminjaman')
                    ->where('status_pinjam', 'Disetujui') // Hanya peminjaman yang disetujui
                    ->where(function ($q) use ($startDateTime, $endDateTime) {
                        $q->whereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND CONCAT(tgl_selesai, " ", jam_selesai)', [$startDateTime])
                            ->orWhereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND CONCAT(tgl_selesai, " ", jam_selesai)', [$endDateTime])
                            ->orWhereRaw('CONCAT(tgl_mulai, " ", jam_mulai) <= ? AND CONCAT(tgl_selesai, " ", jam_selesai) >= ?', [$startDateTime, $endDateTime]);
                    });
            })->get();

        return response()->json($availableKendaraan);
    }
    public function simpan(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'tgl_mulai' => 'required|date',
                'jam_mulai' => 'required',
                'tgl_selesai' => 'required|date',
                'jam_selesai' => 'required',
                'kendaraan' => 'required|exists:kendaraan,id_kendaraan',
                'tujuan' => 'required|string|max:255',
            ]);

            // Gabungkan tanggal dan jam untuk validasi
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_mulai} {$request->jam_mulai}");
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_selesai} {$request->jam_selesai}");

            // Validasi waktu
            if ($endDateTime <= $startDateTime) {
                return response()->json([
                    'message' => 'Waktu selesai harus setelah waktu mulai'
                ], 422);
            }

            // Cek ketersediaan kendaraan
            $isKendaraanBooked = Peminjaman::where('id_kendaraan', $request->kendaraan)
                ->where('status_pinjam', 'Disetujui')
                ->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->whereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND CONCAT(tgl_selesai, " ", jam_selesai)', [$startDateTime])
                        ->orWhereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND CONCAT(tgl_selesai, " ", jam_selesai)', [$endDateTime])
                        ->orWhereRaw('CONCAT(tgl_mulai, " ", jam_mulai) <= ? AND CONCAT(tgl_selesai, " ", jam_selesai) >= ?', [$startDateTime, $endDateTime]);
                })->exists();

            if ($isKendaraanBooked) {
                return response()->json([
                    'message' => 'Kendaraan tidak tersedia untuk waktu yang dipilih'
                ], 422);
            }

            // Simpan peminjaman
            $peminjaman = new Peminjaman();
            $peminjaman->user_id = Auth::id();
            $peminjaman->id_kendaraan = $request->kendaraan;
            $peminjaman->tgl_mulai = $request->tgl_mulai;
            $peminjaman->jam_mulai = $request->jam_mulai;
            $peminjaman->tgl_selesai = $request->tgl_selesai;
            $peminjaman->jam_selesai = $request->jam_selesai;
            $peminjaman->tujuan = $request->tujuan;
            $peminjaman->status_pinjam = 'Menunggu Persetujuan';
            $peminjaman->save();

            return response()->json([
                'message' => 'Peminjaman berhasil disimpan',
                'data' => $peminjaman
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan peminjaman'
            ], 500);
        }
    }
    
    public function detail($id)
    {
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])
            ->findOrFail($id);

        return view('pengguna.detailPeminjaman', compact('peminjaman'));
    }
    
    public function batal($id)
    {
        try {
            // Cari data peminjaman berdasarkan ID
            $peminjaman = Peminjaman::findOrFail($id);

            // Update status menjadi "dibatalkan"
            $peminjaman->status_pinjam = 'Dibatalkan';
            $peminjaman->save();

            // Redirect ke halaman detail peminjaman dengan pesan sukses
            return redirect()->route('pengguna.detailPeminjaman', ['id' => $id])
                ->with('success', 'Peminjaman berhasil dibatalkan.');

        } catch (\Exception $e) {
            // Jika terjadi error, redirect dengan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membatalkan peminjaman.');
        }
    }
    public function showFormPengembalian($id)
    {
        // Ambil peminjaman yang sedang berlangsung berdasarkan user yang login
        $peminjaman = Peminjaman::where('id_peminjaman', $id)
            ->where('user_id', Auth::id())
            ->with('kendaraan')
            ->firstOrFail();
        
            return view('pengguna.formPengembalian',  compact('peminjaman'));
    }
    public function simpanPengembalian(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'tgl' => 'required|date',
                'jam' => 'required',
                'kondisi_kendaraan' => 'required|in:Baik,Terjadi Insiden',
                'detail' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            // Ambil data peminjaman
            $peminjaman = Peminjaman::where('id_peminjaman', $id)
                ->where('user_id', Auth::id())
                ->where('status_pinjam', 'Disetujui')
                ->with('kendaraan')
                ->firstOrFail();

            // Update peminjaman
            $peminjaman->update([
                'tgl_kembali_real' => $request->tgl,
                'jam_kembali_real' => $request->jam,
                'kondisi_kendaraan' => $request->kondisi_kendaraan,
                'detail_insiden' => $request->detail,
                'status_pinjam' => 'Telah Dikembalikan',
                'updated_at' => now(),
            ]);

            // Update status kendaraan
            // $peminjaman->kendaraan()->update([
            //     'status_ketersediaan' => 'Tersedia',
            //     'updated_at' => now(),
            // ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengembalian kendaraan berhasil disimpan.'
                ]);
            }

            return redirect()
                ->route('peminjaman')
                ->with('success', 'Pengembalian kendaraan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    public function showFormPerpanjangan($id)
    {
        // Ambil peminjaman yang sedang berlangsung berdasarkan user yang login
        $peminjaman = Peminjaman::where('id_peminjaman', $id)
            ->where('user_id', Auth::id())
            ->with('kendaraan')
            ->firstOrFail();
        
            return view('pengguna.formPerpanjangan',  compact('peminjaman'));
    }

}
