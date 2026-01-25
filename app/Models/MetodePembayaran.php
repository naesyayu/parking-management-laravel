<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetodePembayaran extends Model
{
    use SoftDeletes;

    protected $table = 'metode_pembayaran';
    protected $primaryKey = 'id_metode';

    protected $fillable = [
        'metode_bayar',
    ];

    public function transaksiParkir()
    {
        return $this->hasMany(TransaksiParkir::class, 'id_metode', 'id_metode');
    }
}