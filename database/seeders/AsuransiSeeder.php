<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsuransiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fungsi pembantu untuk mengambil user_id dengan peran admin
        $getAdminUserId = function() {
            $admin = DB::table('users')
                ->where('peran', 'admin')
                ->inRandomOrder()
                ->first(); // Ambil satu admin secara acak

            return $admin ? $admin->id : null; // Kembalikan user_id admin atau null jika tidak ada admin
        };

        // Data manual untuk pajak mobil (15 record)
        $mobilRecords = [
            [
                'id_kendaraan' => 1, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2024-05-15',
                'polis' => 'POLIS123456', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_1_2024.pdf',
                'tgl_perlindungan_awal' => '2024-05-15',
                'tgl_perlindungan_akhir' => '2025-05-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 1,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2025,
                'tgl_bayar' => '2025-05-15',
                'polis' => 'POLIS123457', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_1_2025.pdf',
                'tgl_perlindungan_awal' => '2025-05-15',
                'tgl_perlindungan_akhir' => '2026-05-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 2, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2023,
                'tgl_bayar' => '2023-12-10',
                'polis' => 'POLIS123458', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_8_2025.pdf',
                'tgl_perlindungan_awal' => '2022-12-15',
                'tgl_perlindungan_akhir' => '2023-12-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 2,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2023-12-10', // Belum dibayar
                'polis' => null, // Belum ada polis
                'bukti_bayar_asuransi' => null, // Belum ada bukti bayar
                'tgl_perlindungan_awal' => '2023-12-15',
                'tgl_perlindungan_akhir' => '2024-12-14',
                'nominal' => null, // Belum dibayar
                'biaya_asuransi_lain' => null, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 3, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2024-02-15',
                'polis' => 'POLIS123459', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_3_2024.pdf',
                'tgl_perlindungan_awal' => '2023-02-15',
                'tgl_perlindungan_akhir' => '2024-02-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 3,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2025,
                'tgl_bayar' => '2024-02-15', // Belum dibayar
                'polis' => null, // Belum ada polis
                'bukti_bayar_asuransi' => null, // Belum ada bukti bayar
                'tgl_perlindungan_awal' => '2024-02-15',
                'tgl_perlindungan_akhir' => '2025-02-14',
                'nominal' => null, // Belum dibayar
                'biaya_asuransi_lain' => null, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 4, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2024-05-15',
                'polis' => 'POLIS123456', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_1_2024.pdf',
                'tgl_perlindungan_awal' => '2024-05-15',
                'tgl_perlindungan_akhir' => '2025-05-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 4,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2025,
                'tgl_bayar' => '2025-05-15',
                'polis' => 'POLIS123457', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_1_2025.pdf',
                'tgl_perlindungan_awal' => '2025-05-15',
                'tgl_perlindungan_akhir' => '2026-05-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 5, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2023,
                'tgl_bayar' => '2023-12-10',
                'polis' => 'POLIS123458', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_2_2024.pdf',
                'tgl_perlindungan_awal' => '2022-12-15',
                'tgl_perlindungan_akhir' => '2023-12-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 5,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2023-12-10', // Belum dibayar
                'polis' => null, // Belum ada polis
                'bukti_bayar_asuransi' => null, // Belum ada bukti bayar
                'tgl_perlindungan_awal' => '2023-12-15',
                'tgl_perlindungan_akhir' => '2024-12-14',
                'nominal' => null, // Belum dibayar
                'biaya_asuransi_lain' => null, // Belum dibayar
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 6, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2024-02-15',
                'polis' => 'POLIS123459', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_3_2024.pdf',
                'tgl_perlindungan_awal' => '2023-02-15',
                'tgl_perlindungan_akhir' => '2024-02-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 6,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2025,
                'tgl_bayar' => '2024-02-15', // Belum dibayar
                'polis' => null, // Belum ada polis
                'bukti_bayar_asuransi' => null, // Belum ada bukti bayar
                'tgl_perlindungan_awal' => '2024-02-15',
                'tgl_perlindungan_akhir' => '2025-02-14',
                'nominal' => null, // Belum dibayar
                'biaya_asuransi_lain' => null, // Belum dibayar
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 7, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2024-05-15',
                'polis' => 'POLIS123456', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_1_2024.pdf',
                'tgl_perlindungan_awal' => '2024-05-15',
                'tgl_perlindungan_akhir' => '2025-05-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 7,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2025,
                'tgl_bayar' => '2025-05-15',
                'polis' => 'POLIS123457', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_1_2025.pdf',
                'tgl_perlindungan_awal' => '2025-05-15',
                'tgl_perlindungan_akhir' => '2026-05-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 8, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2023,
                'tgl_bayar' => '2023-12-10',
                'polis' => 'POLIS123458', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_2_2024.pdf',
                'tgl_perlindungan_awal' => '2022-12-15',
                'tgl_perlindungan_akhir' => '2023-12-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 8,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2023-12-10', // Belum dibayar
                'polis' => null, // Belum ada polis
                'bukti_bayar_asuransi' => null, // Belum ada bukti bayar
                'tgl_perlindungan_awal' => '2023-12-15',
                'tgl_perlindungan_akhir' => '2024-12-14',
                'nominal' => null, // Belum dibayar
                'biaya_asuransi_lain' => null, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 9, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2024-02-15',
                'polis' => 'POLIS123459', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_3_2024.pdf',
                'tgl_perlindungan_awal' => '2023-02-15',
                'tgl_perlindungan_akhir' => '2024-02-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 9,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2025,
                'tgl_bayar' => '2024-02-15', // Belum dibayar
                'polis' => null, // Belum ada polis
                'bukti_bayar_asuransi' => null, // Belum ada bukti bayar
                'tgl_perlindungan_awal' => '2024-02-15',
                'tgl_perlindungan_akhir' => '2025-02-14',
                'nominal' => null, // Belum dibayar
                'biaya_asuransi_lain' => null, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 10, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2024-05-15',
                'polis' => 'POLIS123456', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_1_2024.pdf',
                'tgl_perlindungan_awal' => '2024-05-15',
                'tgl_perlindungan_akhir' => '2025-05-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 10,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2025,
                'tgl_bayar' => '2025-05-15',
                'polis' => 'POLIS123457', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_1_2025.pdf',
                'tgl_perlindungan_awal' => '2025-05-15',
                'tgl_perlindungan_akhir' => '2026-05-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 11, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2023,
                'tgl_bayar' => '2023-12-10',
                'polis' => 'POLIS123458', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_2_2024.pdf',
                'tgl_perlindungan_awal' => '2022-12-15',
                'tgl_perlindungan_akhir' => '2023-12-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 11,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2023-12-10', // Belum dibayar
                'polis' => null, // Belum ada polis
                'bukti_bayar_asuransi' => null, // Belum ada bukti bayar
                'tgl_perlindungan_awal' => '2023-12-15',
                'tgl_perlindungan_akhir' => '2024-12-14',
                'nominal' => null, // Belum dibayar
                'biaya_asuransi_lain' => null, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 12, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2024-02-15',
                'polis' => 'POLIS123459', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_3_2024.pdf',
                'tgl_perlindungan_awal' => '2023-02-15',
                'tgl_perlindungan_akhir' => '2024-02-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 12,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2025,
                'tgl_bayar' => '2024-02-15', // Belum dibayar
                'polis' => null, // Belum ada polis
                'bukti_bayar_asuransi' => null, // Belum ada bukti bayar
                'tgl_perlindungan_awal' => '2024-02-15',
                'tgl_perlindungan_akhir' => '2025-02-14',
                'nominal' => null, // Belum dibayar
                'biaya_asuransi_lain' => null, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 13, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2024-05-15',
                'polis' => 'POLIS123456', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_1_2024.pdf',
                'tgl_perlindungan_awal' => '2024-05-15',
                'tgl_perlindungan_akhir' => '2025-05-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 13,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2025,
                'tgl_bayar' => '2025-05-15',
                'polis' => 'POLIS123457', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_1_2025.pdf',
                'tgl_perlindungan_awal' => '2025-05-15',
                'tgl_perlindungan_akhir' => '2026-05-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        // Data manual untuk pajak motor (2 record)
        $motorRecords = [
            [
                'id_kendaraan' => 14, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2023,
                'tgl_bayar' => '2023-12-10',
                'polis' => 'POLIS123458', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_2_2024.pdf',
                'tgl_perlindungan_awal' => '2022-12-15',
                'tgl_perlindungan_akhir' => '2023-12-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 14,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2023-12-10', // Belum dibayar
                'polis' => null, // Belum ada polis
                'bukti_bayar_asuransi' => null, // Belum ada bukti bayar
                'tgl_perlindungan_awal' => '2023-12-15',
                'tgl_perlindungan_akhir' => '2024-12-14',
                'nominal' => null, // Belum dibayar
                'biaya_asuransi_lain' => null, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 15, // ID kendaraan mobil
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2024,
                'tgl_bayar' => '2024-02-15',
                'polis' => 'POLIS123459', // Nomor polis asuransi
                'bukti_bayar_asuransi' => 'bukti_bayar_3_2024.pdf',
                'tgl_perlindungan_awal' => '2023-02-15',
                'tgl_perlindungan_akhir' => '2024-02-14',
                'nominal' => 2500000,
                'biaya_asuransi_lain' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kendaraan' => 15,
                'user_id' => $getAdminUserId(), // Ambil user_id admin
                'tahun' => 2025,
                'tgl_bayar' => '2024-02-15', // Belum dibayar
                'polis' => null, // Belum ada polis
                'bukti_bayar_asuransi' => null, // Belum ada bukti bayar
                'tgl_perlindungan_awal' => '2024-02-15',
                'tgl_perlindungan_akhir' => '2025-02-14',
                'nominal' => null, // Belum dibayar
                'biaya_asuransi_lain' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        // Masukkan data ke dalam tabel pajak
        DB::table('asuransi')->insert($mobilRecords);
        DB::table('asuransi')->insert($motorRecords);
    }
}