<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $table = 'member';
    protected $primaryKey = 'id_member';

    protected $fillable = [
        'id_pemilik',
        'id_level',
        'berlaku_mulai',
        'berlaku_hingga',
        'status',
    ];

    protected $dates = [
        'berlaku_mulai',
        'berlaku_hingga',
        'deleted_at',
    ];

    // Relasi ke pemilik
    public function pemilik()
    {
        return $this->belongsTo(Pemilik::class, 'id_pemilik');
    }

    // Relasi ke member_level
    public function level()
    {
        return $this->belongsTo(MemberLevel::class, 'id_level');
    }
}
