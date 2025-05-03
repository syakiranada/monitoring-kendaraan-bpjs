<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;
use App\Models\Pajak;
use App\Models\Asuransi;
use App\Models\ServisRutin;
use App\Models\CekFisik;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class BerandaController extends Controller
{
    public function pengguna()
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $threeHoursLater = $now->copy()->addHours(3);
        
        // First get all the necessary data without pagination
        $allPeminjaman = Peminjaman::where('user_id', $user->id)
            ->whereRaw("UPPER(status_pinjam) = 'DISETUJUI'")
            ->with('kendaraan')
            ->get();
        
        $latePeminjaman = [];
        $upcomingPeminjaman = [];
        
        // Process all items to categorize them
        foreach ($allPeminjaman as $pinjam) {
            $tanggalKembali = Carbon::parse($pinjam->tgl_selesai . ' ' . $pinjam->jam_selesai, 'Asia/Jakarta');
            
            if ($now->greaterThan($tanggalKembali)) {
                $pinjam->status_pinjam = 'BELUM DIKEMBALIKAN';
                $latePeminjaman[] = $pinjam;
            } elseif ($tanggalKembali->between($now, $threeHoursLater)) {
                $upcomingPeminjaman[] = $pinjam;
            }
        }
        
        // Create a custom paginator for the collection
        $page = request()->get('page', 1); // Get the current page from the request
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        $paginatedItems = $allPeminjaman->slice($offset, $perPage);
        
        $peminjaman = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $allPeminjaman->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        return view('pengguna.beranda', compact('user', 'peminjaman', 'latePeminjaman', 'upcomingPeminjaman'));
    }

    public function admin()
    {
        $user = Auth::user();
        $sekarang = Carbon::now('Asia/Jakarta');
        $threeHoursLater = $sekarang->copy()->addHours(3);

        $peminjamanTerlambat = Peminjaman::with(['kendaraan', 'user'])
            ->whereRaw("UPPER(status_pinjam) = 'DISETUJUI'")
            ->whereRaw("CONCAT(tgl_selesai, ' ', jam_selesai) < ?", [$sekarang->format('Y-m-d H:i:s')])
            ->get();

        $peminjamanAkanJatuhTempo = Peminjaman::with(['kendaraan', 'user'])
            ->whereRaw("UPPER(status_pinjam) = 'DISETUJUI'")
            ->whereRaw("CONCAT(tgl_selesai, ' ', jam_selesai) BETWEEN ? AND ?", [
                $sekarang->format('Y-m-d H:i:s'),
                $threeHoursLater->format('Y-m-d H:i:s')
            ])
            ->get();

        $butuhCekFisik = CekFisik::whereIn('id_cek_fisik', function ($query) {
                $query->selectRaw('MAX(id_cek_fisik)')->from('cek_fisik')->groupBy('id_kendaraan');
            })
            ->whereDate('tgl_cek_fisik', '<', Carbon::now()->subWeek())
            ->with('kendaraan')
            ->get();

        $batasWaktu = collect();

        // Pajak: Kecuali kendaraan aset = 'lelang'
        $pajak = Pajak::whereIn('id_pajak', function ($query) {
                $query->selectRaw('MAX(id_pajak)')->from('pajak')->groupBy('id_kendaraan');
            })
            ->whereHas('kendaraan', function ($query) {
                $query->where('aset', '!=', 'lelang');
            })
            ->whereDate(DB::raw("DATE_ADD(tgl_jatuh_tempo, INTERVAL 1 YEAR)"), '<=', Carbon::now()->addMonth())
            ->with('kendaraan')
            ->get()
            ->map(function ($pajak) {
                return [
                    'tipe' => 'Pajak',
                    'id_kendaraan' => $pajak->id_kendaraan,
                    'kendaraan' => $pajak->kendaraan,
                    'batas_waktu' => Carbon::parse($pajak->tgl_jatuh_tempo)->addYear(),
                ];
            });
        $batasWaktu = $batasWaktu->concat($pajak);

        // Servis: Kecuali kendaraan aset = 'jual' atau 'lelang'
        $servis = ServisRutin::whereIn('id_servis_rutin', function ($query) {
                $query->selectRaw('MAX(id_servis_rutin)')->from('servis_rutin')->groupBy('id_kendaraan');
            })
            ->whereHas('kendaraan', function ($query) {
                $query->whereNotIn('aset', ['lelang', 'jual']);
            })
            ->whereDate('tgl_servis_selanjutnya', '<=', Carbon::now()->addMonth())
            ->with('kendaraan')
            ->get()
            ->map(function ($servis) {
                return [
                    'tipe' => 'Servis',
                    'id_kendaraan' => $servis->id_kendaraan,
                    'kendaraan' => $servis->kendaraan,
                    'batas_waktu' => $servis->tgl_servis_selanjutnya,
                ];
            });
        $batasWaktu = $batasWaktu->concat($servis);

        // Asuransi: Kecuali kendaraan aset = 'lelang'
        $asuransi = Asuransi::whereIn('id_asuransi', function ($query) {
                $query->selectRaw('MAX(id_asuransi)')->from('asuransi')->groupBy('id_kendaraan');
            })
            ->whereHas('kendaraan', function ($query) {
                $query->where('aset', '!=', 'lelang');
            })
            ->whereDate('tgl_perlindungan_akhir', '<=', Carbon::now()->addMonth())
            ->with('kendaraan')
            ->get()
            ->map(function ($asuransi) {
                return [
                    'tipe' => 'Asuransi',
                    'id_kendaraan' => $asuransi->id_kendaraan,
                    'kendaraan' => $asuransi->kendaraan,
                    'batas_waktu' => $asuransi->tgl_perlindungan_akhir,
                ];
            });
        $batasWaktu = $batasWaktu->concat($asuransi);

        $batasWaktu = $batasWaktu->sortBy('batas_waktu');

        return view('admin.beranda', compact(
            'user',
            'peminjamanTerlambat',
            'peminjamanAkanJatuhTempo',
            'butuhCekFisik',
            'batasWaktu'
        ));
    }
}