<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengajuanPeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])
            ->where('status_pinjam', 'Menunggu Persetujuan');

        // Search functionality
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
        //         ->orWhere('tgl_mulai', 'like', "%$search%")
        //         ->orWhere('tgl_selesai', 'like', "%$search%")
        //         ;
        //     });
        // }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;

            // Cek apakah format input search cocok dengan d-m-Y
            if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $search)) {
                try {
                    // Convert ke format Y-m-d untuk pencarian tanggal penuh
                    $convertedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
                } catch (\Exception $e) {
                    $convertedDate = null;
                }
            } else {
                $convertedDate = null;
            }

            // Cek apakah format input search cocok dengan d-m (tanpa tahun)
            if (preg_match('/^\d{1,2}-\d{1,2}$/', $search)) {
                try {
                    // Pecah menjadi hari dan bulan
                    [$day, $month] = explode('-', $search);
                    // Pastikan formatnya dua digit
                    $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                    $month = str_pad($month, 2, '0', STR_PAD_LEFT);

                    // Ubah jadi format untuk LIKE query
                    $partialDate = "%-$month-$day";
                } catch (\Exception $e) {
                    $partialDate = null;
                }
            } else {
                $partialDate = null;
            }

            $peminjaman->where(function ($q) use ($search, $convertedDate, $partialDate) {
                $q->whereHas('user', function ($qUser) use ($search) {
                    $qUser->where('name', 'like', "%$search%");
                })
                ->orWhereHas('kendaraan', function ($qKendaraan) use ($search) {
                    $qKendaraan->where('merk', 'like', "%$search%")
                            ->orWhere('tipe', 'like', "%$search%")
                            ->orWhere('plat_nomor', 'like', "%$search%");
                })
                ->orWhere('tujuan', 'like', "%$search%");

                // Pencarian tanggal lengkap jika formatnya sesuai d-m-Y
                if ($convertedDate) {
                    $q->orWhere('tgl_mulai', $convertedDate)
                    ->orWhere('tgl_selesai', $convertedDate);
                }

                // Pencarian parsial pada tanggal (contoh: 18-02)
                if ($partialDate) {
                    $q->orWhere('tgl_mulai', 'like', $partialDate)
                    ->orWhere('tgl_selesai', 'like', $partialDate);
                }

                // Pencarian parsial umum (contoh: 18 saja)
                $q->orWhere('tgl_mulai', 'like', "%$search%")
                ->orWhere('tgl_selesai', 'like', "%$search%");
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
