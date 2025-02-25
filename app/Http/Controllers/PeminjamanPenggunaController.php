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
    // public function peminjamanPage(Request $request)
    // {
    //     $userId = Auth::id(); // Ambil ID user yang sedang login
    //     $daftarPeminjaman = Peminjaman::with('kendaraan') // Ambil data kendaraan juga
    //         ->where('user_id', $userId)
    //         ->orderBy('created_at', 'desc') // Urutkan dari terbaru
    //         ->paginate(2);
         
        
    //     $search = $request->search;
    //     $query = Peminjaman::with(['user', 'kendaraan'])->orderBy('tgl_mulai', 'desc');

    //     // Search functionality
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->whereHas('user', function ($qUser) use ($search) {
    //                 $qUser->where('name', 'like', "%$search%");
    //             })
    //             ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
    //                 $qKendaraan->where('merk', 'like', "%$search%")
    //                         ->orWhere('tipe', 'like', "%$search%")
    //                         ->orWhere('plat_nomor', 'like', "%$search%");
    //             })
    //             ->orWhere('tujuan', 'like', "%$search%")
    //             ->orWhere('status_pinjam', 'like', "%$search%");
    //         });
    //     }    
    //     return view('pengguna.peminjaman', compact('daftarPeminjaman'));
    // }
    public function peminjamanPage(Request $request)
    {
        $userId = Auth::id(); // Ambil ID user yang sedang login
        $search = $request->search;
        // Buat query dasar dengan filter user
        $query = Peminjaman::with(['user', 'kendaraan'])
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc');

        // Jika ada input pencarian, tambahkan filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($qUser) use ($search) {
                        $qUser->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
                        $qKendaraan->where('merk', 'like', "%$search%")
                                ->orWhere('tipe', 'like', "%$search%")
                                ->orWhere('plat_nomor', 'like', "%$search%");
                    })
                    ->orWhere('tujuan', 'like', "%$search%")
                    ->orWhere('status_pinjam', 'like', "%$search%");
            });
        }

        // Atur urutan dan paginate hasil query
        $daftarPeminjaman = $query->paginate(2);

        return view('pengguna.peminjaman', compact('daftarPeminjaman', 'search'));
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
    DB::beginTransaction();
    try {
        // Cari data peminjaman berdasarkan ID
        $peminjaman = Peminjaman::findOrFail($id);

        // Update status menjadi "Dibatalkan"
        $peminjaman->status_pinjam = 'Dibatalkan';
        $peminjaman->save();

        // Jika peminjaman merupakan perpanjangan, kembalikan status peminjaman awal menjadi "Disetujui"
        if ($peminjaman->perpanjangan_dari) { 
            $peminjamanAwal = Peminjaman::find($peminjaman->perpanjangan_dari);
            if ($peminjamanAwal) {
                $peminjamanAwal->status_pinjam = 'Disetujui';
                $peminjamanAwal->save();
            }
        }

        DB::commit();

        // Redirect ke halaman detail peminjaman dengan pesan sukses
        return redirect()->route('pengguna.detailPeminjaman', ['id' => $id])
            ->with('success', 'Peminjaman berhasil dibatalkan.');

    } catch (\Exception $e) {
        DB::rollBack();
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

    public function perpanjangan(Request $request)
    {
        $peminjaman = Peminjaman::find($request->id_peminjaman);

        if (!$peminjaman) {
            return response()->json(['message' => 'Peminjaman tidak ditemukan'], 404);
        }
        try {
            // Validate request
            $validated = $request->validate([
                'tgl_selesai' => 'required|date',
                'jam_selesai' => 'required',
                'tujuan' => 'required|string|max:255',
            ]);

            DB::beginTransaction();

            // Get existing peminjaman
            $oldPeminjaman = Peminjaman::find($request->id_peminjaman);
            if (!$oldPeminjaman) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peminjaman tidak ditemukan',
                    'type' => 'error'
                ], 404);
            }

            // Create Carbon instances for time comparison
            $startDateTime = Carbon::parse($oldPeminjaman->tgl_selesai . ' ' . $oldPeminjaman->jam_selesai);
            $endDateTime = Carbon::parse($request->tgl_selesai . ' ' . $request->jam_selesai);

            // Validate end time is after start time
            if ($endDateTime <= $startDateTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu selesai harus setelah waktu mulai peminjaman',
                    'type' => 'validation'
                ], 422);
            }

            // Check if vehicle is available (include time check)
            $conflictingBookings = Peminjaman::where('id_kendaraan', $oldPeminjaman->id_kendaraan)
            ->where('id_peminjaman', '!=', $oldPeminjaman->id_peminjaman)
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->whereBetween('tgl_mulai', [$startDateTime->format('Y-m-d'), $endDateTime->format('Y-m-d')])
                        ->orWhereBetween('tgl_selesai', [$startDateTime->format('Y-m-d'), $endDateTime->format('Y-m-d')]);
                })->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->where(function ($q2) use ($startDateTime, $endDateTime) {
                        $q2->whereBetween('jam_mulai', [$startDateTime->format('H:i:s'), $endDateTime->format('H:i:s')])
                            ->orWhereBetween('jam_selesai', [$startDateTime->format('H:i:s'), $endDateTime->format('H:i:s')]);
                    });
                })->whereIn('status_pinjam', ['Disetujui', 'Menunggu Persetujuan']);
            })->get();

            if ($conflictingBookings->count() > 0) {
            // Buat array detail konflik untuk ditampilkan di pop-up
            $conflicts = [];
            foreach ($conflictingBookings as $conflict) {
                $conflicts[] = [
                    'tanggal' => $conflict->tgl_mulai . ' s/d ' . $conflict->tgl_selesai,
                    'waktu'   => $conflict->jam_mulai . ' s/d ' . $conflict->jam_selesai,
                    'status'  => $conflict->status_pinjam,
                    // Jika relasi user ada, misalnya $conflict->user->name
                    'peminjam'=> $conflict->user->name ?? 'N/A'
                ];
            }

            DB::rollback(); // Explicit rollback
            return response()->json([
                'success'   => false,
                'message'   => 'Terdapat bentrok jadwal peminjaman',
                'type'      => 'conflict',
                'conflicts' => $conflicts
            ], 422);
            }


            // Create new peminjaman record
            $newPeminjaman = new Peminjaman();
            $newPeminjaman->fill([
                'tgl_mulai' => $oldPeminjaman->tgl_selesai,
                'jam_mulai' => $oldPeminjaman->jam_selesai,
                'tgl_selesai' => $request->tgl_selesai,
                'jam_selesai' => $request->jam_selesai,
                'id_kendaraan' => $oldPeminjaman->id_kendaraan,
                'user_id' => auth()->id(),
                'tujuan' => $request->tujuan,
                'status_pinjam' => 'Menunggu Persetujuan',
                'perpanjangan_dari' => $oldPeminjaman->id_peminjaman
            ]);

            // Update status of old peminjaman
            $oldPeminjaman->status_pinjam = 'Diperpanjang';
            $oldPeminjaman->save();
            $newPeminjaman->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perpanjangan peminjaman berhasil diajukan',
                'redirect' => route('peminjaman')
            ]);

        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'type' => 'validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
    
//     public function perpanjangan(Request $request)
// {
//     $peminjaman = Peminjaman::find($request->id_peminjaman);

//     if (!$peminjaman) {
//         return response()->json(['message' => 'Peminjaman tidak ditemukan'], 404);
//     }
//     try {
//         // Validate request
//         $validated = $request->validate([
//             'tgl_selesai' => 'required|date',
//             'jam_selesai' => 'required',
//             'tujuan' => 'required|string|max:255',
//         ]);

//         DB::beginTransaction();

//         // Get existing peminjaman
//         $oldPeminjaman = Peminjaman::find($request->id_peminjaman);
//         if (!$oldPeminjaman) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Peminjaman tidak ditemukan',
//                 'type' => 'error'
//             ], 404);
//         }

//         // Create Carbon instances for time comparison
//         $startDateTime = Carbon::parse($oldPeminjaman->tgl_selesai . ' ' . $oldPeminjaman->jam_selesai);
//         $endDateTime = Carbon::parse($request->tgl_selesai . ' ' . $request->jam_selesai);
//         $currentDateTime = Carbon::now();

//         // Check if current time is before the end time of old peminjaman
//         if ($currentDateTime > $startDateTime) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Tidak dapat mengajukan perpanjangan setelah waktu selesai peminjaman',
//                 'type' => 'validation'
//             ], 422);
//         }

//         // Validate end time is after start time
//         if ($endDateTime <= $startDateTime) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Waktu selesai harus setelah waktu mulai peminjaman',
//                 'type' => 'validation'
//             ], 422);
//         }

//         // Check if vehicle is available (include time check)
//         $conflictingBookings = Peminjaman::where('id_kendaraan', $oldPeminjaman->id_kendaraan)
//             ->where('id_peminjaman', '!=', $oldPeminjaman->id_peminjaman)
//             ->where(function ($query) use ($startDateTime, $endDateTime) {
//                 $query->where(function ($q) use ($startDateTime, $endDateTime) {
//                     $q->whereBetween('tgl_mulai', [$startDateTime->format('Y-m-d'), $endDateTime->format('Y-m-d')])
//                         ->orWhereBetween('tgl_selesai', [$startDateTime->format('Y-m-d'), $endDateTime->format('Y-m-d')]);
//                 })->where(function ($q) use ($startDateTime, $endDateTime) {
//                     $q->where(function ($q2) use ($startDateTime, $endDateTime) {
//                         $q2->whereBetween('jam_mulai', [$startDateTime->format('H:i:s'), $endDateTime->format('H:i:s')])
//                         ->orWhereBetween('jam_selesai', [$startDateTime->format('H:i:s'), $endDateTime->format('H:i:s')]);
//                     });
//                 })->whereIn('status_pinjam', ['Disetujui', 'Menunggu Persetujuan']);
//             })->get();

//         if ($conflictingBookings->count() > 0) {
//             DB::rollback();
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Terdapat bentrok jadwal peminjaman',
//                 'type' => 'conflict'
//             ], 422);
//         }

//         // Create new peminjaman record
//         $newPeminjaman = new Peminjaman();
//         $newPeminjaman->fill([
//             'tgl_mulai' => $oldPeminjaman->tgl_selesai,
//             'jam_mulai' => $oldPeminjaman->jam_selesai,
//             'tgl_selesai' => $request->tgl_selesai,
//             'jam_selesai' => $request->jam_selesai,
//             'id_kendaraan' => $oldPeminjaman->id_kendaraan,
//             'user_id' => auth()->id(),
//             'tujuan' => $request->tujuan,
//             'status_pinjam' => 'Menunggu Persetujuan',
//             'perpanjangan_dari' => $oldPeminjaman->id_peminjaman
//         ]);

//         // Update status of old peminjaman to 'Diperpanjang'
//         $oldPeminjaman->status_pinjam = 'Diperpanjang';
//         $oldPeminjaman->save();
//         $newPeminjaman->save();

//         DB::commit();

//         return response()->json([
//             'success' => true,
//             'message' => 'Perpanjangan peminjaman berhasil diajukan',
//             'redirect' => route('peminjaman')
//         ]);

//     } catch (ValidationException $e) {
//         DB::rollback();
//         return response()->json([
//             'success' => false,
//             'message' => 'Validasi gagal',
//             'type' => 'validation',
//             'errors' => $e->errors()
//         ], 422);
//     } catch (\Exception $e) {
//         DB::rollback();
//         return response()->json([
//             'success' => false,
//             'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
//             'type' => 'error'
//         ], 500);
//     }
// }


// Updated rejection handler
// public function tolakPerpanjangan(Request $request)
// {
//     try {
//         DB::beginTransaction();

//         // Cari peminjaman perpanjangan yang akan ditolak
//         $perpanjangan = Peminjaman::findOrFail($request->id_peminjaman);
        
//         // Cek apakah ini memang perpanjangan (memiliki id peminjaman sebelumnya)
//         if ($perpanjangan->perpanjangan_dari) {
//             // Cari peminjaman sebelumnya
//             $peminjamanSebelumnya = Peminjaman::findOrFail($perpanjangan->perpanjangan_dari);
            
//             // Ubah status peminjaman sebelumnya kembali menjadi Disetujui
//             $peminjamanSebelumnya->status_pinjam = 'Disetujui';
//             $peminjamanSebelumnya->save();
//         }

//         // Ubah status perpanjangan menjadi Ditolak
//         $perpanjangan->status_pinjam = 'Ditolak';
//         $perpanjangan->save();

//         DB::commit();

//         return response()->json([
//             'success' => true,
//             'message' => 'Perpanjangan ditolak dan status peminjaman sebelumnya dikembalikan',
//             'redirect' => route('peminjaman')
//         ]);

//     } catch (\Exception $e) {
//         DB::rollback();
//         return response()->json([
//             'success' => false,
//             'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
//             'type' => 'error'
//         ], 500);
//     }
// }

}
