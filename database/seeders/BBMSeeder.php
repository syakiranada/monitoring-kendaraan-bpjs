<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BbmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua data kendaraan
        $kendaraans = DB::table('kendaraan')->get();

        // Ambil semua user dengan peran admin atau pengguna
        $users = DB::table('users')
            ->whereIn('peran', ['admin', 'pengguna'])
            ->get();

        foreach ($kendaraans as $kendaraan) {
            // Pilih user secara acak (baik admin maupun pengguna)
            $user = $users->random();

            // Tentukan jenis BBM berdasarkan bahan bakar kendaraan
            $jenis_bbm = $kendaraan->bahan_bakar;

            // Buat beberapa record pengisian BBM untuk setiap kendaraan
            for ($i = 0; $i < 3; $i++) {
                // Tentukan rentang tanggal antara Desember 2024 dan Januari 2025
                $startDate = Carbon::create(2024, 12, 1); // Mulai dari 1 Desember 2024
                $endDate = Carbon::create(2025, 1, 31);   // Sampai 31 Januari 2025
            
                // Generate tanggal acak dalam rentang tersebut
                $tgl_isi = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp))->format('Y-m-d');
            
                // Nominal pengisian BBM antara 200.000 hingga 1.000.000
                $nominal = rand(200000, 1000000);
            
                // Insert data ke tabel bbm
                DB::table('bbm')->insert([
                    'user_id' => $user->id,
                    'id_kendaraan' => $kendaraan->id_kendaraan,
                    'nominal' => $nominal,
                    'jenis_bbm' => $jenis_bbm,
                    'tgl_isi' => $tgl_isi,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}