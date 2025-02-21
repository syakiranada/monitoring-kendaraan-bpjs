<?php

// 1. Buat file baru di: app/Console/Commands/HitungDepresiasiKendaraan.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kendaraan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HitungDepresiasiKendaraan extends Command
{
    protected $signature = 'kendaraan:hitung-depresiasi';
    protected $description = 'Menghitung depresiasi untuk semua kendaraan';

    public function handle()
{
    $this->info('Mulai menghitung depresiasi kendaraan...');

    $kendaraans = Kendaraan::all();
    // $tanggalSekarang = Carbon::now();
    $tanggalSekarang = Carbon::createFromFormat('d/m/Y', '16/11/2025'); // Debug tanggal manual

    foreach ($kendaraans as $kendaraan) {
        try {
            $tanggalBeli = Carbon::parse($kendaraan->tgl_pembelian);
            
            // Hitung jumlah tahun sejak pembelian
            $tahun = $tanggalBeli->diffInYears($tanggalSekarang);

            if ($tahun <= 0) {
                continue; // Skip kalau belum 1 tahun sejak pembelian
            }

            // Tentukan persentase depresiasi
            $persentaseMap = [
                'Sedan' => 0.20,
                'Non Sedan' => 0.25,
                'Motor' => 0.10
            ];
            $persentaseDepresiasi = $persentaseMap[$kendaraan->jenis] ?? 0.20;

            // Hitung nilai buku
            $nilaiBuku = $kendaraan->nilai_perolehan;

            for ($i = 0; $i < $tahun; $i++) {
                $nilaiBuku -= $nilaiBuku * $persentaseDepresiasi;
            }

            // Update database
            $kendaraan->nilai_buku = $nilaiBuku;
            $kendaraan->save();

            $this->info("✅ Berhasil update depresiasi kendaraan ID: {$kendaraan->id_kendaraan}, Nilai Buku: {$nilaiBuku}");

        } catch (\Exception $e) {
            Log::error("❌ Error saat menghitung depresiasi kendaraan ID: {$kendaraan->id_kendaraan}", [
                'error' => $e->getMessage()
            ]);
            $this->error("⚠️ Gagal update kendaraan ID: {$kendaraan->id_kendaraan}");
        }
    }

    $this->info('🔄 Proses depresiasi selesai!');
}

}
