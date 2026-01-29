<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;    

    protected $table = 'roles';
    protected $primaryKey = 'id_role';

    protected $fillable = [
        'role_user',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_role', 'id_role');
    }
}
