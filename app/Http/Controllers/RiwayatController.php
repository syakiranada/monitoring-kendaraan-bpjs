<?php

namespace App\Http\Controllers;

use App\Models\BBM;
use App\Models\Pajak;
use App\Models\Asuransi;
use App\Models\Kendaraan;
use App\Models\Peminjaman;
use App\Models\ServisRutin;
use Illuminate\Http\Request;
use App\Models\ServisInsidental;
use Illuminate\Foundation\Auth\User;

class RiwayatController extends Controller
{
    public function index()
    {
        return view('admin.riwayat.index');
    }

    public function peminjaman(Request $request)
    {
        $search = $request->search;
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

        return view('admin.riwayat.peminjaman', compact('riwayatPeminjaman', 'kendaraan', 'search'));
    }

    public function detailPeminjaman($id)
    {
        $peminjaman = Peminjaman::with(['user', 'kendaraan'])->findOrFail($id);

        return view('admin.riwayat.detail-peminjaman', compact('peminjaman'));
    }

    public function pajak(Request $request)
    {
        $search = $request->search;
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

        return view('admin.riwayat.pajak', compact('riwayatPajak', 'search'));
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
        $search = $request->search;
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

        return view('admin.riwayat.asuransi', compact('riwayatAsuransi', 'search'));
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
        $search = $request->search;
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

        return view('admin.riwayat.servis-rutin', compact('riwayatServis', 'search'));
    }

    public function detailServisRutin($id)
    {
        $servis = ServisRutin::with(['kendaraan'])
            ->findOrFail($id);

        return view('admin.riwayat.detail-servis-rutin', compact('servis'));
    }

    public function servisInsidental(Request $request)
    {
        $search = $request->search;
        $query = ServisInsidental::with(['kendaraan', 'user'])
            ->orderBy('tgl_servis', 'desc');

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
                ->orWhere('tgl_servis', 'like', "%$search%");
            });
        }

        $riwayatServis = $query->paginate(10);

        return view('admin.riwayat.servis-insidental', compact('riwayatServis', 'search'));
    }

    public function detailServisInsidental($id)
    {
        $servis = ServisInsidental::with(['kendaraan'])
            ->findOrFail($id);

        return view('admin.riwayat.detail-servis-insidental', compact('servis'));
    }

    public function pengisianBBM(Request $request)
    {
        // Get the filter inputs from the request
        $kendaraan = $request->get('kendaraan');
        $pengguna = $request->get('pengguna');
        $tgl_awal = $request->get('tgl_awal');
        $tgl_akhir = $request->get('tgl_akhir');

        // Build the query
        $query = BBM::query();

        if ($kendaraan) {
            $query->whereHas('kendaraan', function($q) use ($kendaraan) {
                $q->where('plat_nomor', $kendaraan);
            });
        }

        if ($pengguna) {
            $query->whereHas('user', function($q) use ($pengguna) {
                $q->where('id', $pengguna);
            });
        }        

        if ($tgl_awal) {
            $query->whereDate('tgl_isi', '>=', $tgl_awal);
        }

        if ($tgl_akhir) {
            $query->whereDate('tgl_isi', '<=', $tgl_akhir);
        }

        // Get the filtered data
        $riwayatBBM = $query->with(['kendaraan', 'user'])->paginate(10);

        // Calculate the total transaksi
        $totalTransaksi = collect($riwayatBBM->items())->sum('nominal');

        // Get the list of vehicles and users for the filter
        $kendaraanList = Kendaraan::all();
        $penggunaList = User::all();

        return view('admin.riwayat.pengisian-bbm', [
            'riwayatBBM' => $riwayatBBM,
            'totalTransaksi' => $totalTransaksi,
            'kendaraan' => $kendaraanList,
            'penggunas' => $penggunaList,
            'filterParams' => [
                'kendaraan' => $kendaraan,
                'pengguna' => $pengguna,
                'tgl_awal' => $tgl_awal,
                'tgl_akhir' => $tgl_akhir
            ]
        ]);
    }


    public function detailPengisianBBM($id)
    {

        $bbm = BBM::with(['kendaraan'])->findOrFail($id);
        
        // Ambil semua parameter filter dari request
        $filterParams = [
            'kendaraan' => request()->query('kendaraan'),
            'pengguna' => request()->query('pengguna'),
            'tgl_awal' => request()->query('tgl_awal'),
            'tgl_akhir' => request()->query('tgl_akhir'),
            'page' => request()->query('page', 1)
        ];

        return view('admin.riwayat.detail-pengisian-bbm', compact('bbm', 'filterParams'));
    }

}
