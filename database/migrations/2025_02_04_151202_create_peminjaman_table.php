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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('id_peminjaman'); // Primary Key
            $table->unsignedBigInteger('user_id'); // Foreign Key ke tabel users
            $table->unsignedBigInteger('id_kendaraan'); // Foreign Key ke tabel kendaraan
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->string('tujuan', 50);
            $table->date('tgl_kembali_real')->nullable(); // Nullable untuk kasus belum kembali
            $table->string('kondisi_kendaraan', 30)->nullable(); // Nullable jika kondisi belum di-update
            $table->string('status_pinjam', 30);
            $table->string('detail_insiden', 100)->nullable(); // Nullable untuk insiden opsional
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->time('jam_kembali_real')->nullable(); // Nullable jika waktu kembali belum ada
            $table->timestamps(); // Menambahkan created_at dan updated_at

            // Foreign Key Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('kendaraan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * 
     */
    public function down()
    {
        Schema::dropIfExists('peminjaman');
    }
};