<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberLevel extends Model
{
    protected $table = 'member_level';
    protected $primaryKey = 'id_level';

    protected $fillable = [
        'nama_level',
        'diskon_persen',
    ];

    // Relasi ke member
    public function members()
    {
        return $this->hasMany(Member::class, 'id_level');
    }
}
