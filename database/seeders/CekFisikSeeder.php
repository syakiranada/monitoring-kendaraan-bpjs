<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;


class CekFisikSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $faker->seed(12345);

        $kendaraanIds = DB::table('kendaraan')->pluck('id_kendaraan')->toArray();
        $kondisi = ['Baik', 'Rusak'];

        $catatanRusak = [
            "Ban kempes, perlu penggantian.",
            "Mesin tidak bisa menyala, perlu perbaikan.",
            "Air radiator bocor, harus diisi ulang.",
            "Pengharum kendaraan habis, perlu penggantian.",
            "Body penyok parah, perlu perbaikan.",
            "Accu lemah, harus diganti.",
            "Air wiper tidak keluar, perlu pengecekan.",
            "Kondisi kendaraan baik, namun perlu pengecekan rutin.",
            "Ban tergores, perlu penggantian.",
            "Mesin ada suara aneh, perlu diperiksa lebih lanjut."
        ];

        foreach ($kendaraanIds as $kendaraanId) {
            $tglCekFisik = Carbon::now()->subWeeks(rand(1, 4)); // Mulai dari beberapa minggu yang lalu
            
            $cekFisikCount = rand(2, 4);

            for ($i = 0; $i < $cekFisikCount; $i++) {
                $mesin = $kondisi[array_rand($kondisi)];
                $accu = $kondisi[array_rand($kondisi)];
                $airRadiator = $kondisi[array_rand($kondisi)];
                $airWiper = $kondisi[array_rand($kondisi)];
                $body = $kondisi[array_rand($kondisi)];
                $ban = $kondisi[array_rand($kondisi)];
                $pengharum = $kondisi[array_rand($kondisi)];
                $kondisiKeseluruhan = $kondisi[array_rand($kondisi)];

                $catatan = "";
                if ($mesin === 'Rusak') {
                    $catatan .= $catatanRusak[array_rand($catatanRusak)] . " ";
                }
                if ($accu === 'Rusak') {
                    $catatan .= "Accu lemah, harus diganti. ";
                }
                if ($body === 'Rusak') {
                    $catatan .= "Body penyok parah. ";
                }
                if ($airRadiator === 'Rusak') {
                    $catatan .= "Air radiator bocor, harus diperbaiki. ";
                }
                if ($airWiper === 'Rusak') {
                    $catatan .= "Air wiper tidak keluar, perlu pengecekan. ";
                }
                if ($ban === 'Rusak') {
                    $catatan .= "Ban kempes, perlu penggantian. ";
                }
                if ($pengharum === 'Rusak') {
                    $catatan .= "Pengharum kendaraan habis, perlu penggantian. ";
                }

                if ($catatan === "" && $kondisiKeseluruhan === 'Baik') {
                    $catatan = "Kondisi kendaraan baik, pengecekan rutin diperlukan.";
                }

                $userId = $faker->randomElement([1, 2, 3]);

                DB::table('cek_fisik')->insert([
                    'id_kendaraan' => $kendaraanId,
                    'user_id' => $userId,
                    'tgl_cek_fisik' => $tglCekFisik->format('Y-m-d'),
                    'mesin' => $mesin,
                    'accu' => $accu,
                    'air_radiator' => $airRadiator,
                    'air_wiper' => $airWiper,
                    'body' => $body,
                    'ban' => $ban,
                    'pengharum' => $pengharum,
                    'kondisi_keseluruhan' => $kondisiKeseluruhan,
                    'catatan' => $catatan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $tglCekFisik->addWeek(); // Tambah 7 hari untuk cek fisik berikutnya
            }
        }
    }
}