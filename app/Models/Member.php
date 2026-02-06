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

    protected $casts = [
        'berlaku_mulai'  => 'date',
        'berlaku_hingga' => 'date',
        'deleted_at'     => 'datetime',
    ];

    // RELASI
    public function pemilik()
    {
        return $this->belongsTo(Pemilik::class, 'id_pemilik');
    }

    public function level()
    {
        return $this->belongsTo(MemberLevel::class, 'id_level');
    }

    public function transaksiParkir()
    {
        return $this->hasMany(TransaksiParkir::class, 'id_member', 'id_member');
    }

    // LOGIC
    public function isExpired(): bool
    {
        return now()->gt($this->berlaku_hingga);
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
