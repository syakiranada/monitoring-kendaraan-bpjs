<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServisRutin extends Model
{
    use HasFactory;

    protected $table = 'servis_rutin';
    protected $primaryKey = 'id_servis_rutin';
    protected $fillable = [
        'id_kendaraan', 
        'user_id',
        // 'tipe', 
        'harga', 
        'kilometer', 
        'lokasi', 
        'bukti_bayar', 
        // 'bukti_fisik', 
        // 'status', 
        'tgl_servis_real', 
        'tgl_servis_selanjutnya'
    ];

    // Relasi ke tabel Kendaraan
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

    // Relasi ke tabel User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
