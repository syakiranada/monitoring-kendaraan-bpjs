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
        // Ambil data peminjaman yang disetujui
        $peminjamans = DB::table('peminjaman')
            ->where('status_pinjam', 'Disetujui')
            ->get();

        // Buat BBM untuk peminjaman yang ada
        foreach ($peminjamans as $peminjaman) {
            // Ambil data kendaraan dan user
            $kendaraan = DB::table('kendaraan')
                ->where('id_kendaraan', $peminjaman->id_kendaraan)
                ->first();

            // Buat 1-3 record pengisian BBM untuk setiap peminjaman
            $jumlahPengisian = rand(1, 3);
            
            for ($i = 0; $i < $jumlahPengisian; $i++) {
                // Generate tanggal dalam periode peminjaman
                $startDate = Carbon::parse($peminjaman->tgl_mulai);
                $endDate = Carbon::parse($peminjaman->tgl_selesai);
                
                // Pastikan tanggal pengisian dalam rentang peminjaman
                $tgl_isi = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                )->format('Y-m-d');
                
                // Insert data BBM dengan id_peminjaman
                DB::table('bbm')->insert([
                    'user_id' => $peminjaman->user_id,
                    'id_kendaraan' => $peminjaman->id_kendaraan,
                    'id_peminjaman' => $peminjaman->id_peminjaman,
                    'nominal' => rand(200000, 1000000),
                    'jenis_bbm' => $kendaraan->bahan_bakar,
                    'tgl_isi' => $tgl_isi,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Buat beberapa BBM untuk admin (tanpa id_peminjaman)
        $admins = DB::table('users')
            ->where('peran', 'admin')
            ->get();
        
        $kendaraans = DB::table('kendaraan')->get();

        foreach ($admins as $admin) {
            foreach ($kendaraans as $kendaraan) {
                // Buat 1-2 record untuk setiap kendaraan oleh admin
                $jumlahPengisian = rand(1, 2);
                
                for ($i = 0; $i < $jumlahPengisian; $i++) {
                    $startDate = Carbon::create(2024, 12, 1);
                    $endDate = Carbon::create(2025, 1, 31);
                    
                    $tgl_isi = Carbon::createFromTimestamp(
                        rand($startDate->timestamp, $endDate->timestamp)
                    )->format('Y-m-d');

                    DB::table('bbm')->insert([
                        'user_id' => $admin->id,
                        'id_kendaraan' => $kendaraan->id_kendaraan,
                        'id_peminjaman' => null,
                        'nominal' => rand(200000, 1000000),
                        'jenis_bbm' => $kendaraan->bahan_bakar,
                        'tgl_isi' => $tgl_isi,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}