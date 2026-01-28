<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailParkir extends Model
{
    protected $table = 'detail_parkir';
    protected $primaryKey = 'id_tarif_detail';

    public $timestamps = false;

    protected $fillable = [
        'jam_min',
        'jam_max',
    ];

    public function tarifParkir()
    {
        return $this->hasMany(TarifParkir::class, 'id_tarif_detail', 'id_tarif_detail');
    }
}
