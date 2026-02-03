<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailParkir extends Model
{
    use SoftDeletes;

    protected $table = 'detail_parkir';
    protected $primaryKey = 'id_tarif_detail';

    public $timestamps = false;

    protected $fillable = [
        'jam_min',
        'jam_max',
    ];

    protected $dates = ['deleted_at'];

    public function tarifParkir()
    {
        return $this->hasMany(TarifParkir::class, 'id_tarif_detail', 'id_tarif_detail');
    }
}
