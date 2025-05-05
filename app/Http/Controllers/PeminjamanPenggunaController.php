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
   
private function buildDateSearch($query, $column, $search)
{
    // Check if search might be a date in various formats
    // Format: d (day only - 1 to 31)
    if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])$/', $search)) {
        $day = (int) $search;
        $query->orWhereRaw("DAY($column) = ?", [$day]);
    }

    // Format: m (month only - 1 to 12)
    if (preg_match('/^(0?[1-9]|1[0-2])$/', $search)) {
        $month = (int) $search;
        $query->orWhereRaw("MONTH($column) = ?", [$month]);
    }

    // Format: Y (year only - 4 digits)
    if (preg_match('/^(20\d{2})$/', $search)) {
        $year = (int) $search;
        $query->orWhereRaw("YEAR($column) = ?", [$year]);
    }

    // Format: d-m (day-month)
    if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])[\-\/](0?[1-9]|1[0-2])$/', $search)) {
        $parts = preg_split('/[\-\/]/', $search);
        $day = (int) $parts[0];
        $month = (int) $parts[1];
        $query->orWhereRaw("DAY($column) = ? AND MONTH($column) = ?", [$day, $month]);
    }

    // Format: m-Y (month-year)
    if (preg_match('/^(0?[1-9]|1[0-2])[\-\/](20\d{2})$/', $search)) {
        $parts = preg_split('/[\-\/]/', $search);
        $month = (int) $parts[0];
        $year = (int) $parts[1];
        $query->orWhereRaw("MONTH($column) = ? AND YEAR($column) = ?", [$month, $year]);
    }

    // Format: d-m-Y (day-month-year)
    if (preg_match('/^(0?[1-9]|[12][0-9]|3[01])[\-\/](0?[1-9]|1[0-2])[\-\/](20\d{2})$/', $search)) {
        $parts = preg_split('/[\-\/]/', $search);
        $day = (int) $parts[0];
        $month = (int) $parts[1];
        $year = (int) $parts[2];
        $query->orWhereRaw("DAY($column) = ? AND MONTH($column) = ? AND YEAR($column) = ?", [$day, $month, $year]);
    }

    // Also try the default LIKE search for backward compatibility
    $query->orWhere($column, 'like', "%$search%");
}

public function peminjamanPage(Request $request)
{
    $userId = Auth::id();
    $page = $request->input('page', 1);
    $search = $request->input('search');
    $daftarPeminjaman = Peminjaman::with(['user', 'kendaraan'])
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc');

    // If search is filled, apply search filter
    if ($request->filled('search')) {
        $searchWords = explode(' ', $request->search); // split into array of words

        $daftarPeminjaman->where(function ($q) use ($searchWords) {
            foreach ($searchWords as $word) {
                $q->where(function ($q2) use ($word) {
                    // Search based on user and vehicle relations
                    $q2->whereHas('user', function ($qUser) use ($word) {
                            $qUser->where('name', 'like', "%$word%");
                        })
                        ->orWhereHas('kendaraan', function ($qKendaraan) use ($word) {
                            $qKendaraan->where('merk', 'like', "%$word%")
                                       ->orWhere('tipe', 'like', "%$word%")
                                       ->orWhere('plat_nomor', 'like', "%$word%");
                        })
                        ->orWhere('tujuan', 'like', "%$word%")
                        ->orWhere('status_pinjam', 'like', "%$word%")
                        ->orWhere('kondisi_kendaraan', 'like', "%$word%")
                        ->orWhere('detail_insiden', 'like', "%$word%");

                    // Apply date search for fields like 'tgl_mulai', 'tgl_selesai', 'tgl_kembali_real'
                    $this->buildDateSearch($q2, 'tgl_mulai', $word);
                    $this->buildDateSearch($q2, 'tgl_selesai', $word);
                    $this->buildDateSearch($q2, 'tgl_kembali_real', $word);
                });
            }
        });
    }

    // pagination
    $daftarPeminjaman = $daftarPeminjaman->paginate(10)->appends(['search' => request('search')]);
    return view('pengguna.peminjaman', compact('daftarPeminjaman','search', 'page'));
}



    public function showForm(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');
        return view('pengguna.formPeminjaman', [
        'page' => $page,
        'search' => $search
        ]);
    }
    // public function getAvailableKendaraan(Request $request)
    // {
    //     // Gabungkan tanggal & waktu dari request 
    //     $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_mulai} {$request->jam_mulai}");
    //     $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_selesai} {$request->jam_selesai}");

    //     $availableKendaraan = Kendaraan::where('status_ketersediaan', 'Tersedia')
    //         ->where('aset', 'Guna')
    //         ->whereNotIn('id_kendaraan', function ($query) use ($startDateTime, $endDateTime) {
    //             $query->select('id_kendaraan')
    //                 ->from('peminjaman')
    //                 ->where('status_pinjam', ['Disetujui', 'Diperpanjang']) // Hanya peminjaman yang disetujui
    //                 ->where(function ($q) use ($startDateTime, $endDateTime) {
    //                     $q->where(function ($subQuery) use ($startDateTime, $endDateTime) {
    //                         $startDateTimeStr = $startDateTime->format('Y-m-d H:i');
    //                         $endDateTimeStr = $endDateTime->format('Y-m-d H:i');
    //                         $subQuery->whereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), CONCAT(tgl_selesai, " ", jam_selesai))', [$startDateTime])
    //                             ->orWhereRaw('? BETWEEN CONCAT(tgl_mulai, " ", jam_mulai) AND IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), CONCAT(tgl_selesai, " ", jam_selesai))', [$endDateTime])
    //                             ->orWhereRaw('CONCAT(tgl_mulai, " ", jam_mulai) <= ? AND IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), CONCAT(tgl_selesai, " ", jam_selesai)) >= ?', [$startDateTime, $endDateTime]);
    //                     })
    //                     // Jika kendaraan sudah dikembalikan, kendaraan bisa dipinjam lagi setelah pengembalian
    //                     ->whereRaw('IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), CONCAT(tgl_selesai, " ", jam_selesai)) > ?', [$startDateTime]);
    //                 });
    //         })->get();


    //     return response()->json($availableKendaraan);
    // }
    public function getAvailableKendaraan(Request $request)
{
    // Gabungkan tanggal & waktu dari request 
    $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_mulai} {$request->jam_mulai}");
    $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$request->tgl_selesai} {$request->jam_selesai}");

    $availableKendaraan = Kendaraan::where('status_ketersediaan', 'Tersedia')
        ->where('aset', 'Guna')
        ->whereNotIn('id_kendaraan', function ($query) use ($startDateTime, $endDateTime) {
            $query->select('id_kendaraan')
                ->from('peminjaman')
                ->whereIn('status_pinjam', ['Disetujui', 'Diperpanjang']) // Perbaikan: gunakan whereIn untuk multiple values
                ->where(function ($q) use ($startDateTime, $endDateTime) {
                    // Format tanggal sebagai string untuk query
                    $startDateTimeStr = $startDateTime->format('Y-m-d H:i');
                    $endDateTimeStr = $endDateTime->format('Y-m-d H:i');
                    
                    // Deteksi overlap/bentrok:
                    // 1. Waktu mulai peminjaman berada dalam rentang waktu yang diminta
                    $q->whereRaw('CONCAT(tgl_mulai, " ", jam_mulai) BETWEEN ? AND ?', 
                               [$startDateTimeStr, $endDateTimeStr])
                    // 2. Waktu selesai peminjaman berada dalam rentang waktu yang diminta
                    ->orWhereRaw('IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), 
                                CONCAT(tgl_selesai, " ", jam_selesai)) BETWEEN ? AND ?', 
                              [$startDateTimeStr, $endDateTimeStr])
                    // 3. Peminjaman mencakup seluruh rentang waktu yang diminta
                    ->orWhereRaw('CONCAT(tgl_mulai, " ", jam_mulai) <= ? AND 
                                IFNULL(CONCAT(tgl_kembali_real, " ", jam_kembali_real), 
                                      CONCAT(tgl_selesai, " ", jam_selesai)) >= ?', 
                              [$startDateTimeStr, $endDateTimeStr]);
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
    
    public function detail($id, Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])
            ->findOrFail($id);

        return view('pengguna.detailPeminjaman', compact('peminjaman', 'page', 'search'));
    }
    

public function batal($id,Request $request)
{
    DB::beginTransaction();
    try {
        // Cari data peminjaman berdasarkan ID
        $peminjaman = Peminjaman::findOrFail($id);

        // Update status menjadi "Dibatalkan"
        $peminjaman->status_pinjam = 'Dibatalkan';
        $peminjaman->save();
        $page = $request->query('page', 1);
        $search = $request->query('search', '');

        // Jika peminjaman merupakan perpanjangan, kembalikan status peminjaman awal menjadi "Disetujui"
        if ($peminjaman->perpanjangan_dari) { 
            $peminjamanAwal = Peminjaman::find($peminjaman->perpanjangan_dari);
            if ($peminjamanAwal) {
                $peminjamanAwal->status_pinjam = 'Disetujui';
                $peminjamanAwal->save();
            }
        }

        DB::commit();

        // Kirimkan respons JSON dengan status sukses
        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil dibatalkan.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        // Jika terjadi error, kirimkan respons error
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat membatalkan peminjaman.'
        ]);
    }
}

    public function showFormPengembalian($id, Request $request)
    {
        // Ambil peminjaman yang sedang berlangsung berdasarkan user yang login
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $peminjaman = Peminjaman::where('id_peminjaman', $id)
            ->where('user_id', Auth::id())
            ->with('kendaraan')
            ->firstOrFail();
        
            return view('pengguna.formPengembalian',  compact('peminjaman', 'search', 'page'));
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

        //     if ($request->ajax()) {
        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Pengembalian kendaraan berhasil disimpan.'
        //         ]);
        //     }

        //     return redirect()
        //         ->route('peminjaman')
        //         ->with('success', 'Pengembalian kendaraan berhasil disimpan.');

        // } catch (\Exception $e) {
        //     DB::rollBack();
            
        //     if ($request->ajax()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        //         ], 500);
        //     }

        //     return back()
        //         ->withInput()
        //         ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        return response()->json([
            'success' => true,
            'message' => 'Pengembalian kendaraan berhasil disimpan.',
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
        // Debugging
        Log::error('Terjadi kesalahan: ' . $e->getMessage());  // Menambahkan log error
        dd($e);  // Menampilkan exception dan pesan kesalahan di layar atau browser

        DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'type' => 'error'
        ], 500);

        }
    }
    public function showFormPerpanjangan($id, Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');
        // Ambil peminjaman yang sedang berlangsung berdasarkan user yang login
        $peminjaman = Peminjaman::where('id_peminjaman', $id)
            ->where('user_id', Auth::id())
            ->with('kendaraan')
            ->firstOrFail();
        
            return view('pengguna.formPerpanjangan',  compact('peminjaman', 'page', 'search'));
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

        // Initialize conflict array
        $conflicts = [];

        // Check for conflicting bookings
        $conflictingBookings = Peminjaman::where('id_kendaraan', $oldPeminjaman->id_kendaraan)
            ->where('id_peminjaman', '!=', $oldPeminjaman->id_peminjaman) // Menghindari memeriksa peminjaman yang sama
            ->whereIn('status_pinjam', ['Disetujui']) // Pastikan statusnya valid
            ->get();

        foreach ($conflictingBookings as $b) {
            $mulaiExisting = Carbon::parse($b->tgl_mulai . ' ' . $b->jam_mulai);
            $selesaiExisting = Carbon::parse($b->tgl_selesai . ' ' . $b->jam_selesai);

            // Check if there is any overlap
            if (
                ($startDateTime->between($mulaiExisting, $selesaiExisting)) || 
                ($endDateTime->between($mulaiExisting, $selesaiExisting)) ||
                ($mulaiExisting->between($startDateTime, $endDateTime)) || 
                ($selesaiExisting->between($startDateTime, $endDateTime))
            ) {
                $conflicts[] = [
                    'peminjam' => $b->user->name ?? 'N/A',
                    'mulai' => Carbon::parse($b->tgl_mulai)->format('d-m-Y') . ' ' . Carbon::parse($b->jam_mulai)->format('H.i'),
                    'selesai' => Carbon::parse($b->tgl_selesai)->format('d-m-Y') . ' ' . Carbon::parse($b->jam_selesai)->format('H.i'),
                ];
            }
        }

        // If there are conflicts, return the conflicts array
        if (count($conflicts) > 0) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terdapat bentrok jadwal peminjaman',
                'type' => 'conflict',
                'conflicts' => $conflicts
            ], 422);
        }

        // Log query result for debugging
        Log::info('Conflicting bookings:', $conflictingBookings->toArray()); // Menyimpan hasil query dalam log

        // Proceed with creating a new peminjaman record
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
        // Debugging
        Log::error('Terjadi kesalahan: ' . $e->getMessage());  // Menambahkan log error
        dd($e);  // Menampilkan exception dan pesan kesalahan di layar atau browser

        DB::rollback();
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'type' => 'error'
        ], 500);
    }
}

}
