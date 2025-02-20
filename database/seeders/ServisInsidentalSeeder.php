<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServisInsidentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $faker->seed(12345);

        // Ambil data peminjaman dengan kondisi "Terjadi Insiden"
        $peminjamanInsiden = DB::table('peminjaman')
            ->where('kondisi_kendaraan', 'Terjadi Insiden')
            ->whereNotNull('tgl_kembali_real')
            ->limit(30)
            ->get();

        foreach ($peminjamanInsiden as $peminjaman) {
            DB::table('servis_insidental')->insert([
                'id_kendaraan' => $peminjaman->id_kendaraan,
                'user_id' => $peminjaman->user_id,
                'harga' => $faker->numberBetween(500000, 5000000), // Harga random antara 500 ribu - 5 juta
                'lokasi' => $faker->city,
                'deskripsi' => $faker->sentence,
                'bukti_bayar' => null,
                'bukti_fisik' => null,
                'tgl_servis' => $peminjaman->tgl_kembali_real, // Gunakan tanggal kembali sebagai tanggal servis
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
