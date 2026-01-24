<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;


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

    public function isExpired(): bool
    {
        return Carbon::now()->gt($this->berlaku_hingga);
    }

    protected static function booted()
    {
        static::retrieved(function ($member) {
            if (
                $member->status === 'aktif' &&
                now()->gt($member->berlaku_hingga)
            ) {
                $member->updateQuietly([
                    'status' => 'expired'
                ]);
            }
        });
    }

}
