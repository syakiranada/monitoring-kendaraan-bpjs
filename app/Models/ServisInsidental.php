<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServisInsidental extends Model
{
    use HasFactory;

    protected $table = 'servis_insidental';
    protected $primaryKey = 'id_servis_insidental';
    protected $fillable = [
        'id_kendaraan',
        'user_id',
        // 'tipe',
        'harga',
        'lokasi',
        'bukti_bayar',
        'bukti_fisik',
        'tgl_servis',
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
