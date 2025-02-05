<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServisRutinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $faker->seed(12345);

        // Ambil semua kendaraan dari database
        $kendaraanList = DB::table('kendaraan')->get();

        // Ambil user_id dari user dengan peran 'admin'
        $adminUsers = DB::table('users')->where('peran', 'admin')->pluck('id')->toArray();

        if (empty($adminUsers)) {
            $this->command->warn('Tidak ada user dengan peran "admin", seeder tidak akan dijalankan.');
            return;
        }

        $servisData = [];
        $kendaraanStatusUpdate = [];

        foreach ($kendaraanList as $kendaraan) {
            $intervalBulan = $kendaraan->frekuensi_servis ?? 6; // Default 6 bulan jika tidak ada nilai
            $tanggalPembelian = new \DateTime($kendaraan->tgl_pembelian);
            
            // Mulai servis setelah tanggal pembelian
            $tanggalServisAwal = clone $tanggalPembelian;
            $tanggalServisAwal->modify("+{$intervalBulan} months");

            // Pastikan kilometer awal tidak nol
            $kilometer = $faker->numberBetween(1000, 10000);

            // Tentukan jumlah servis antara 1-3 kali per kendaraan
            $jumlahServis = rand(1, 3);

            for ($i = 0; $i < $jumlahServis; $i++) {
                // Ambil admin secara acak
                $adminId = $faker->randomElement($adminUsers);

                // Tanggal servis real dengan kemungkinan mundur sehari atau tepat waktu
                $tglServisReal = clone $tanggalServisAwal;
                if (rand(0, 1)) {
                    $tglServisReal->modify('+1 day');
                }

                // Tanggal servis selanjutnya berdasarkan interval
                $tglServisSelanjutnya = clone $tglServisReal;
                $tglServisSelanjutnya->modify("+{$intervalBulan} months");

                $servisData[] = [
                    'id_kendaraan' => $kendaraan->id_kendaraan,
                    'user_id' => $adminId,
                    'harga' => $faker->numberBetween(500000, 5000000),
                    'kilometer' => $kilometer,
                    'lokasi' => $faker->company . ' Service Center Semarang',
                    'bukti_bayar' => null,
                    'bukti_fisik' => null,
                    'tgl_servis_real' => $tglServisReal->format('Y-m-d'),
                    'tgl_servis_selanjutnya' => $tglServisSelanjutnya->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Tambah kilometer untuk servis berikutnya
                $kilometer += $faker->numberBetween(5000, 15000);

                // Update tanggal untuk servis berikutnya
                $tanggalServisAwal = clone $tglServisSelanjutnya;
            }
        }

        // Insert data servis ke tabel servis_rutin
        DB::table('servis_rutin')->insert($servisData);
    }
}
