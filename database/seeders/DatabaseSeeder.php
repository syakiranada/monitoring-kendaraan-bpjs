<?php

namespace Database\Seeders;

use App\Models\ServisInsidental;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(UsersSeeder::class);
        // $this->call(KendaraanSeeder::class);
        $this->call(Kendaraan2Seeder::class);
        // $this->call(PeminjamanSeeder::class);
        // $this->call(CekFisikSeeder::class);
        // $this->call(ServisInsidentalSeeder::class);
        // $this->call(ServisRutinSeeder::class);
        // $this->call(BBMSeeder::class);
        // $this->call(AsuransiSeeder::class);
        // $this->call(PajakSeeder::class);
        $this->call(HitungDepresiasiEventSeeder::class);
    }
}
