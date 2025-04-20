<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengajuanPeminjamanController extends Controller
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
    
    public function index(Request $request)
    {
        $search = $request->input('search');
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])
            ->where('status_pinjam', 'Menunggu Persetujuan');

        // Search functionality
        // SEARCH 1 KOLOM
        // if ($request->filled('search')) {
        //     $search = $request->search;
        //     $peminjaman->where(function ($q) use ($search) {
        //         $q->whereHas('user', function ($qUser) use ($search) {
        //             $qUser->where('name', 'like', "%$search%");
        //         })
        //         ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
        //             $qKendaraan->where('merk', 'like', "%$search%")
        //                     ->orWhere('tipe', 'like', "%$search%")
        //                     ->orWhere('plat_nomor', 'like', "%$search%");
        //         })
        //         ->orWhere('tujuan', 'like', "%$search%")
        //         // ->orWhere('tgl_mulai', 'like', "%$search%")
        //         // ->orWhere('tgl_selesai', 'like', "%$search%");
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_mulai', $search);
        //         })
        //         ->orWhere(function ($q) use ($search) {
        //             $this->buildDateSearch($q, 'tgl_selesai', $search);
        //         });
        //     });
        // }

        if ($request->filled('search')) {
            $searchWords = explode(' ', $request->search); // pisahkan jadi array kata
        
            $peminjaman->where(function ($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->where(function ($q2) use ($word) {
                        $q2->whereHas('user', function ($qUser) use ($word) {
                                $qUser->where('name', 'like', "%$word%");
                            })
                            ->orWhereHas('kendaraan', function ($qKendaraan) use ($word) {
                                $qKendaraan->where('merk', 'like', "%$word%")
                                           ->orWhere('tipe', 'like', "%$word%")
                                           ->orWhere('plat_nomor', 'like', "%$word%");
                            })
                            ->orWhere('tujuan', 'like', "%$word%")
                            ->orWhere(function ($q3) use ($word) {
                                $this->buildDateSearch($q3, 'tgl_mulai', $word);
                            })
                            ->orWhere(function ($q3) use ($word) {
                                $this->buildDateSearch($q3, 'tgl_selesai', $word);
                            });
                    });
                }
            });
        }
        

        // pagination
        $peminjaman = $peminjaman->paginate(10);

        return view('admin.pengajuan-peminjaman.index', compact('peminjaman', 'search'));
    }


    public function detail($id)
    {
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])->findOrFail($id);
        return view('admin.pengajuan-peminjaman.detail', compact('peminjaman'));
    }

    public function setujui($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Cek apakah ada peminjaman lain dengan kendaraan yang sama dan waktu yang beririsan
        $bentrok = Peminjaman::where('id_kendaraan', $peminjaman->id_kendaraan)
        ->where('status_pinjam', 'Disetujui')
        ->where(function ($query) use ($peminjaman) {
            $query->whereBetween('tgl_mulai', [$peminjaman->tgl_mulai, $peminjaman->tgl_selesai])
                ->orWhereBetween('tgl_selesai', [$peminjaman->tgl_mulai, $peminjaman->tgl_selesai])
                ->orWhere(function ($q) use ($peminjaman) {
                    $q->where('tgl_mulai', '<', $peminjaman->tgl_mulai)
                        ->where('tgl_selesai', '>', $peminjaman->tgl_selesai);
                });
        })
        ->exists();

        if ($bentrok) {
            return redirect()->route('admin.pengajuan-peminjaman.index')
                ->with('error', 'Peminjaman tidak dapat disetujui karena kendaraan sudah dipakai pada waktu yang sama atau beririsan.');
        }

        // Jika tidak bentrok, setujui peminjaman
        $peminjaman->status_pinjam = 'Disetujui';
        $peminjaman->save();

        return redirect()->route('admin.pengajuan-peminjaman.index')
            ->with('success', 'Peminjaman berhasil disetujui.');
    }

    public function tolak($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status_pinjam = 'Ditolak';
        $peminjaman->save();
        if ($peminjaman->perpanjangan_dari) {
            $peminjamanAwal = Peminjaman::find($peminjaman->perpanjangan_dari);
            if ($peminjamanAwal) {
                $peminjamanAwal->status_pinjam = 'Disetujui';
                $peminjamanAwal->save();
            }
        }

        return redirect()->route('admin.pengajuan-peminjaman.index')->with('success', 'Peminjaman telah ditolak');
    }
   

}
