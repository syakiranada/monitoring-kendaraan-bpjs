<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tambahkan 1 Admin
        DB::table('users')->insert([
            'name' => 'Sahrul',
            'email' => 'sahrul@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // Password dienkripsi
            'peran' => 'admin',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Doni',
            'email' => 'Doni@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // Password dienkripsi
            'peran' => 'admin',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Tambahkan 2 Pengguna Nyata
        DB::table('users')->insert([
            [
                'name' => 'Silla',
                'email' => 'silla@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'), // Password dienkripsi
                'peran' => 'pengguna',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Panila',
                'email' => 'panila@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'), // Password dienkripsi
                'peran' => 'pengguna',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Nama-nama untuk 12 pengguna tambahan
        $names = [
            'Dewi Ayu', 'Ahmad Sutikno', 'Carli xcx', 'Siti Diana',
            'Eva Turner', 'Fani Lala', 'Gres', 'Heri Tono',
            'Isla Tuti', 'Joni Tantono', 'Karina Dewi', 'Budi Brian'
        ];

        // Tambahkan 12 Pengguna dengan Nama Nyata
        foreach ($names as $key => $name) {
            DB::table('users')->insert([
                'name' => $name,
                'email' => Str::slug($name, '') . '@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'), // Password dienkripsi
                'peran' => 'pengguna',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
