<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pajak extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'pajak';

    // Specify the primary key (optional, as Laravel will automatically use 'id_pajak' as primary key)
    protected $primaryKey = 'id_pajak';

    // Set the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'id_kendaraan',
        'tahun',
        'tgl_bayar',
        'tgl_jatuh_tempo',
        'bukti_bayar_pajak',
        'nominal',
        'biaya_pajak_lain',
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
}