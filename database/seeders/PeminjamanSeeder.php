<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $faker->seed(12345);

        $cities = [
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Yogyakarta', 'Bali', 'Malang', 'Makassar', 'Semarang', 'Batam',
            'Palembang', 'Denpasar', 'Samarinda', 'Pontianak', 'Banjarmasin', 'Cirebon', 'Tasikmalaya', 'Padang', 'Manado', 'Ambon'
        ];

        $userIds = DB::table('users')->where('peran', 'pengguna')->pluck('id')->toArray();
        $kendaraanIds = DB::table('kendaraan')->pluck('id_kendaraan')->toArray();

        $statusPinjam = ['Disetujui', 'Ditolak', 'Dibatalkan', 'Diperpanjang', 'Menunggu Persetujuan', 'Telah Dikembalikan'];

        $kondisiKendaraan = ['Baik', 'Terjadi Insiden'];
        $insidenDeskripsi = [
            'Ban Kempes', 'Mesin Mogok', 'Kecelakaan Kecil', 'Lampu Depan Rusak', 'Kaca Pecah', 'Bocor', 'Pengereman Tidak Berfungsi'
        ];

        // Buat 20 data dengan status "Telah Dikembalikan"
        for ($i = 0; $i < 20; $i++) {
            $status = 'Telah Dikembalikan';

            // Pilih kondisi kendaraan, ada yang "Baik" dan ada yang "Terjadi Insiden"
            if ($faker->boolean(70)) { // 70% kemungkinan kondisi "Baik"
                $kondisi = 'Baik';
                $detailInsiden = null;
            } else { // 30% kemungkinan kondisi "Terjadi Insiden"
                $kondisi = 'Terjadi Insiden';
                $detailInsiden = $insidenDeskripsi[array_rand($insidenDeskripsi)];
            }

            DB::table('peminjaman')->insert([
                'user_id' => $userIds[array_rand($userIds)],
                'id_kendaraan' => $kendaraanIds[array_rand($kendaraanIds)],
                'tgl_mulai' => Carbon::now()->subDays($faker->numberBetween(1, 30))->format('Y-m-d'),
                'tgl_selesai' => Carbon::now()->addDays($faker->numberBetween(1, 10))->format('Y-m-d'),
                'jam_mulai' => Carbon::now()->subHours($faker->numberBetween(1, 5))->format('H:i:s'),
                'jam_selesai' => Carbon::now()->addHours($faker->numberBetween(1, 5))->format('H:i:s'),
                'tujuan' => $cities[array_rand($cities)],
                'tgl_kembali_real' => Carbon::now()->format('Y-m-d'),
                'jam_kembali_real' => Carbon::now()->format('H:i:s'),
                'kondisi_kendaraan' => $kondisi, // Kondisi kendaraan hanya diisi untuk "Telah Dikembalikan"
                'status_pinjam' => $status,
                'detail_insiden' => $detailInsiden, // Detail insiden hanya diisi untuk "Terjadi Insiden"
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buat 30 data dengan status kombinasi lainnya
        for ($i = 0; $i < 30; $i++) {
            // Pilih status selain "Telah Dikembalikan"
            $status = $statusPinjam[array_rand($statusPinjam)];

            // Kondisi kendaraan dan detail insiden tidak diisi untuk status selain "Telah Dikembalikan"
            DB::table('peminjaman')->insert([
                'user_id' => $userIds[array_rand($userIds)],
                'id_kendaraan' => $kendaraanIds[array_rand($kendaraanIds)],
                'tgl_mulai' => Carbon::now()->subDays($faker->numberBetween(1, 30))->format('Y-m-d'),
                'tgl_selesai' => Carbon::now()->addDays($faker->numberBetween(1, 10))->format('Y-m-d'),
                'jam_mulai' => Carbon::now()->subHours($faker->numberBetween(1, 5))->format('H:i:s'),
                'jam_selesai' => Carbon::now()->addHours($faker->numberBetween(1, 5))->format('H:i:s'),
                'tujuan' => $cities[array_rand($cities)],
                'tgl_kembali_real' => null, // Tidak ada tanggal kembali untuk yang status selain "Telah Dikembalikan"
                'jam_kembali_real' => null, // Tidak ada jam kembali untuk yang status selain "Telah Dikembalikan"
                'kondisi_kendaraan' => null, // Kondisi kendaraan tidak diisi untuk status selain "Telah Dikembalikan"
                'status_pinjam' => $status,
                'detail_insiden' => null, // Detail insiden tidak diisi untuk status selain "Telah Dikembalikan"
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}