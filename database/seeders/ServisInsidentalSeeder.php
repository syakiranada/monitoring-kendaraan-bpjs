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
            // Langsung ambil peran dari users dengan join untuk menghindari query tambahan
            $userRole = DB::table('users')
                ->where('id', $peminjaman->user_id)
                ->value('peran');
            
            $servisData = [
                'id_kendaraan' => $peminjaman->id_kendaraan,
                'user_id' => $peminjaman->user_id,
                'harga' => $faker->numberBetween(500000, 5000000),
                'lokasi' => $faker->city,
                'deskripsi' => $faker->sentence,
                'bukti_bayar' => null,
                'bukti_fisik' => null,
                'tgl_servis' => $peminjaman->tgl_kembali_real,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Jika peran pengguna adalah "pengguna", tambahkan id_peminjaman
            if ($userRole == 'pengguna') {
                // Menggunakan id_peminjaman yang adalah property dari objek $peminjaman
                $servisData['id_peminjaman'] = $peminjaman->id_peminjaman;
            }
            
            DB::table('servis_insidental')->insert($servisData);
        }
    }
}