<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model {
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';
    protected $fillable = [
        'user_id', 'id_kendaraan', 'tgl_mulai', 'tgl_selesai', 
        'jam_mulai', 'jam_selesai', 'tujuan', 'tgl_kembali_real',
        'jam_kembali_real', 'kondisi_kendaraan', 'status_pinjam', 'perpanjangan_dari', 'detail_insiden'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kendaraan() {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }
}
