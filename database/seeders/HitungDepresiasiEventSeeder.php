<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HitungDepresiasiEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::unprepared("
                DROP EVENT IF EXISTS hitung_depresiasi_kendaraan;

                CREATE EVENT hitung_depresiasi_kendaraan
                ON SCHEDULE EVERY 1 SECOND
                DO 
                BEGIN
                    UPDATE kendaraan
                    SET nilai_buku = nilai_perolehan * 
                        (CASE 
                            WHEN jenis = 'Sedan' THEN POWER(0.80, TIMESTAMPDIFF(YEAR, tgl_pembelian, NOW()))
                            WHEN jenis = 'Non Sedan' THEN POWER(0.75, TIMESTAMPDIFF(YEAR, tgl_pembelian, NOW()))
                            WHEN jenis = 'Motor' THEN POWER(0.90, TIMESTAMPDIFF(YEAR, tgl_pembelian, NOW()))
                            ELSE POWER(0.80, TIMESTAMPDIFF(YEAR, tgl_pembelian, NOW()))
                        END);

                    INSERT INTO event_log (keterangan, waktu_eksekusi)
                    VALUES ('Event depresiasi dijalankan', NOW());
                END;
            ");
            Log::info('Event hitung_depresiasi_kendaraan berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Gagal membuat event hitung_depresiasi_kendaraan: ' . $e->getMessage());
        }
    }
}
