<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarifParkir extends Model
{
    use SoftDeletes;

    protected $table = 'tarif_parkir';
    protected $primaryKey = 'id_tarif';

    protected $fillable = [
        'id_tarif_detail',
        'id_tipe',
        'tarif',
    ];

    // Relasi ke detail parkir
    public function detailParkir()
    {
        return $this->belongsTo(DetailParkir::class, 'id_tarif_detail');
    }

    // Relasi ke tipe kendaraan
    public function tipeKendaraan()
    {
        return $this->belongsTo(TipeKendaraan::class, 'id_tipe');
    }
}
