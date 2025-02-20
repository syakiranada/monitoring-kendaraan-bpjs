<?php

namespace App\Http\Controllers;

use App\Models\Pajak;
use App\Models\Asuransi;
use App\Models\Kendaraan;
use App\Models\Peminjaman;
use App\Models\ServisRutin;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        return view('admin.riwayat.index');
    }

    public function peminjaman(Request $request)
    {
        $query = Peminjaman::with(['user', 'kendaraan'])->orderBy('tgl_mulai', 'desc');

        // Search functionality
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

        // Paginate results
        $riwayatPeminjaman = $query->paginate(10);
        $kendaraan = Kendaraan::all(); // Ambil semua kendaraan untuk dropdown filter

        return view('admin.riwayat.peminjaman', compact('riwayatPeminjaman', 'kendaraan'));
    }

    public function detailPeminjaman($id)
    {
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])->findOrFail($id);

        return view('admin.riwayat.detail-peminjaman', compact('peminjaman'));
    }

    public function pajak(Request $request)
    {
        // Ambil data pajak dengan relasi kendaraan dan user (admin yang input)
        $query = Pajak::with(['kendaraan', 'user'])
            ->orderBy('tgl_bayar', 'desc')
            ->orderBy('tgl_jatuh_tempo', 'desc');

        // Jika ada pencarian berdasarkan plat, merek kendaraan, tipe, atau nama admin
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('kendaraan', function ($qKendaraan) use ($search) {
                    $qKendaraan->where('plat_nomor', 'like', "%$search%")
                            ->orWhere('merk', 'like', "%$search%")
                            ->orWhere('tipe', 'like', "%$search%");
                })
                ->orWhereHas('user', function ($qUser) use ($search) {
                    $qUser->where('name', 'like', "%$search%");
                });
            });
        }

        // Paginate hasilnya
        $riwayatPajak = $query->paginate(10);

        return view('admin.riwayat.pajak', compact('riwayatPajak'));
    }

    public function detailPajak($id)
    {
        // $pajak = Pajak::with('kendaraan')->findOrFail($id);

        // return view('admin.riwayat.detail-pajak', compact('pajak'));

        $pajak = Pajak::with('kendaraan')->where('id_pajak', $id)->firstOrFail();
        $tglJatuhTempoTahunDepan = \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo)->addYear();
        $pajak->tgl_jatuh_tempo_tahun_depan = $tglJatuhTempoTahunDepan;

        return view('admin.riwayat.detail-pajak', compact('pajak'));
    }

    public function asuransi(Request $request)
    {
        $query = Asuransi::with(['kendaraan', 'user'])
            ->orderBy('tgl_bayar', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('kendaraan', function ($q) use ($search) {
                    $q->where('merk', 'like', "%$search%")
                    ->orWhere('tipe', 'like', "%$search%")
                    ->orWhere('plat_nomor', 'like', "%$search%");
                })
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhere('tahun', 'like', "%$search%")
                ->orWhere('tgl_bayar', 'like', "%$search%")
                ->orWhere('polis', 'like', "%$search%")
                ->orWhere('tgl_perlindungan_awal', 'like', "%$search%")
                ->orWhere('tgl_perlindungan_akhir', 'like', "%$search%")
                ->orWhere('nominal', 'like', "%$search%")
                ->orWhere('biaya_asuransi_lain', 'like', "%$search%");
            });
        }

        $riwayatAsuransi = $query->paginate(10);

        return view('admin.riwayat.asuransi', compact('riwayatAsuransi'));
    }

    public function detailAsuransi($id)
    {
        $asuransi = Asuransi::with('kendaraan')
            ->where('id_asuransi', $id)
            ->firstOrFail();
        
        // Ambil record asuransi sebelumnya berdasarkan id_asuransi yang lebih kecil
        $previousAsuransi = Asuransi::where('id_kendaraan', $asuransi->id_kendaraan)
            ->where('id_asuransi', '<', $id)  // Menjaga agar mengambil yang sebelumnya
            ->orderBy('id_asuransi', 'desc')  // Urutkan berdasarkan id_asuransi secara menurun
            ->first();
        
        if ($previousAsuransi) {
            $asuransi->tgl_jatuh_tempo = $previousAsuransi->tgl_perlindungan_akhir;
        } else {
            $asuransi->tgl_jatuh_tempo = null;  // Bisa disesuaikan jika tidak ada record sebelumnya
        }
    
        return view('admin.riwayat.detail-asuransi', compact('asuransi'));
    }

    public function servisRutin(Request $request)
    {
        $query = ServisRutin::with(['kendaraan', 'user'])
            ->orderBy('tgl_servis_real', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('kendaraan', function ($q) use ($search) {
                    $q->where('merk', 'like', "%$search%")
                        ->orWhere('tipe', 'like', "%$search%")
                        ->orWhere('plat_nomor', 'like', "%$search%");
                })
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhere('lokasi', 'like', "%$search%")
                ->orWhere('harga', 'like', "%$search%")
                ->orWhere('kilometer', 'like', "%$search%")
                ->orWhere('tgl_servis_real', 'like', "%$search%")
                ->orWhere('tgl_servis_selanjutnya', 'like', "%$search%");
            });
        }

        $riwayatServis = $query->paginate(10);

        return view('admin.riwayat.servis-rutin', compact('riwayatServis'));
    }

    public function detailServisRutin($id)
    {

    }

}
