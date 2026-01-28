<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiParkir extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_parkir';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'kode_tiket',
        'id_kendaraan',
        'id_area',
        'waktu_masuk',
        'waktu_keluar',
        'durasi_jam',
        'id_tarif',
        'id_user',
        'id_member',
        'id_metode',
        'status',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
    ];

    /**
    * =====================
    * RELASI MASTER DATA
    * =====================
    */


    public function kendaraan()
    {
    return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }


    public function areaParkir()
    {
    return $this->belongsTo(AreaParkir::class, 'id_area', 'id_area');
    }


    public function tarifParkir()
    {
    return $this->belongsTo(TarifParkir::class, 'id_tarif', 'id_tarif');
    }


    public function user()
    {
    return $this->belongsTo(User::class, 'id_user', 'id_user');
    }


    public function member()
    {
    return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }


    public function metodePembayaran()
    {
    return $this->belongsTo(MetodePembayaran::class, 'id_metode', 'id_metode');
    }
}