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
                -- Drop existing events
                DROP EVENT IF EXISTS hitung_depresiasi_kendaraan;
                DROP EVENT IF EXISTS hitung_depresiasi_realtime;
                
                -- Event yang jalan setiap detik untuk real-time update (seperti kode lama)
                CREATE EVENT hitung_depresiasi_realtime
                ON SCHEDULE EVERY 1 SECOND
                DO 
                BEGIN
                    UPDATE kendaraan
                    SET nilai_buku = 
                        CASE 
                            -- Jika sudah lewat 5 tahun, nilai buku = nilai residu
                            WHEN TIMESTAMPDIFF(MONTH, tgl_pembelian, NOW()) >= 60 THEN
                                CASE 
                                    WHEN jenis = 'Sedan' THEN nilai_perolehan * 0.25
                                    WHEN jenis = 'Non Sedan' THEN nilai_perolehan * 0.20
                                    WHEN jenis = 'Motor' THEN nilai_perolehan * 0.15
                                    ELSE nilai_perolehan * 0.20
                                END
                            -- Jika masih dalam masa depresiasi
                            ELSE 
                                GREATEST(
                                    nilai_perolehan - (
                                        -- Hitung nilai yang dapat disusutkan
                                        (nilai_perolehan - (
                                            CASE 
                                                WHEN jenis = 'Sedan' THEN nilai_perolehan * 0.25      -- Residu 25%
                                                WHEN jenis = 'Non Sedan' THEN nilai_perolehan * 0.20  -- Residu 20%
                                                WHEN jenis = 'Motor' THEN nilai_perolehan * 0.10      -- Residu 10%
                                                ELSE nilai_perolehan * 0.20                           -- Default 20%
                                            END
                                        )) 
                                        -- Dibagi 60 bulan, dikali jumlah bulan sejak pembelian
                                        / 60 * TIMESTAMPDIFF(MONTH, tgl_pembelian, NOW())
                                    ),
                                    -- Nilai minimum adalah nilai residu
                                    CASE 
                                        WHEN jenis = 'Sedan' THEN nilai_perolehan * 0.25
                                        WHEN jenis = 'Non Sedan' THEN nilai_perolehan * 0.20
                                        WHEN jenis = 'Motor' THEN nilai_perolehan * 0.10
                                        ELSE nilai_perolehan * 0.20
                                    END
                                )
                        END;
                END;
                
                -- Event bulanan untuk logging dan maintenance
                CREATE EVENT hitung_depresiasi_kendaraan
                ON SCHEDULE EVERY 1 MONTH
                DO 
                BEGIN
                    INSERT INTO event_log (keterangan, waktu_eksekusi)
                    VALUES ('Event depresiasi bulanan dijalankan', NOW());
                END;
            ");
            Log::info('Event depresiasi berhasil dibuat - Real-time dan bulanan.');
        } catch (\Exception $e) {
            Log::error('Gagal membuat event depresiasi: ' . $e->getMessage());
        }
    }
}