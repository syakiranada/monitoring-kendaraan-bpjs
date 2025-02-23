<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bbm extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'bbm';

    // Specify the primary key (optional, as Laravel will automatically use 'id_bbm' as primary key)
    protected $primaryKey = 'id_bbm';

    // Set the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'id_kendaraan',
        'id_peminjaman',
        'nominal',
        'jenis_bbm',
        'tgl_isi',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Define the relationship with the Kendaraan model
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman', 'id_peminjaman');
    }
}
