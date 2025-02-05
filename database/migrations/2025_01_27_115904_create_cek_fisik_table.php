<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cek_fisik', function (Blueprint $table) {
            $table->id('id_cek_fisik'); // Primary Key
            $table->unsignedBigInteger('user_id'); // Foreign Key ke tabel users
            $table->unsignedBigInteger('id_kendaraan'); // Foreign Key ke tabel kendaraan
            $table->date('tgl_cek_fisik')->nullable(); // Nullable
            $table->string('mesin', 20)->nullable(); // Nullable
            $table->string('accu', 20)->nullable(); // Nullable
            $table->string('air_radiator', 20)->nullable(); // Nullable
            $table->string('air_wiper', 20)->nullable(); // Nullable
            $table->string('body', 20)->nullable(); // Nullable
            $table->string('ban', 20)->nullable(); // Nullable
            $table->string('pengharum', 20)->nullable(); // Nullable
            $table->string('kondisi_keseluruhan', 20)->nullable(); // Nullable
            $table->string('catatan', 255)->nullable(); // Nullable
            $table->timestamps(); // Menambahkan kolom created_at dan updated_at

            // Foreign Key Constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('kendaraan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cek_fisik');
    }
};
