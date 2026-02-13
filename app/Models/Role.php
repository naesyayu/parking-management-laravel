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

    public function isOwner(): bool
    {
        return strtolower($this->role_user) === 'owner';
    }

    public function isAdmin(): bool
    {
        return strtolower($this->role_user) === 'admin';
    }

    public function isPetugas(): bool
    {
        return in_array(strtolower($this->role_user), ['petugas parkir', 'petugas']);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->permissions && is_array($this->permissions)) {
            return isset($this->permissions[$permission]) && $this->permissions[$permission] === true;
        }

        return $this->generatePermissionByRole($permission);
    }

    private function generatePermissionByRole(string $permission): bool
    {
        if ($this->isOwner()) {
            return !in_array($permission, ['user_management', 'transaksi', 'master_data', 'activity_log']); // Owner TIDAK bisa kelola user & transaksi masuk/keluar
        }

        if ($this->isAdmin()) {
            return !in_array($permission, ['transaksi']); // Admin TIDAK bisa transaksi masuk/keluar
        }

        if ($this->isPetugas()) {
            return !in_array($permission, ['user_management', 'master_data', 'activity_log', 'laporan']); // Petugas TIDAK bisa kelola user & master data
        }

        return false;
    }

    public function getPermissions(): array
    {
        if ($this->permissions && is_array($this->permissions)) {
            return $this->permissions;
        }

        if ($this->isOwner()) {
            return [
                'transaksi' => false,          // Owner TIDAK bisa input transaksi
                'master_data' => false,         // Owner kelola master data
                'laporan' => true,             // Owner akses laporan
                'activity_log' => false,        // Owner tidak lihat activity log
                'user_management' => false,    // Owner TIDAK kelola user
                'change_password' => true,     // Owner ganti password
                'detail_transaksi' => true,    // Owner lihat detail
                'table_master' => true,        // Owner lihat view master
                'lobby_display' => true,      // Owner akses lobby display
            ];
        }

        if ($this->isAdmin()) {
            return [
                'transaksi' => false,          // Admin TIDAK input transaksi
                'master_data' => true,         // Admin kelola master data
                'laporan' => true,             // Admin filter & export laporan
                'activity_log' => true,        // Admin lihat activity log
                'user_management' => true,     // Admin kelola user
                'change_password' => true,     // Admin ganti password
                'detail_transaksi' => true,    // Admin lihat detail
                'table_master' => true,        // Admin lihat view master
                'lobby_display' => true,      // Admin akses lobby display
            ];
        }

        if ($this->isPetugas()) {
            return [
                'transaksi' => true,           // Petugas input transaksi
                'master_data' => false,        // Petugas TIDAK kelola master
                'laporan' => false,             // Petugas tidak lihat laporan (read-only)
                'activity_log' => false,       // Petugas TIDAK lihat activity log
                'user_management' => false,    // Petugas TIDAK kelola user
                'change_password' => true,     // Petugas ganti password
                'detail_transaksi' => true,    // Petugas lihat detail transaksi sendiri
                'table_master' => true,        // Petugas lihat view master (read-only)
                'lobby_display' => true,      // Petugas akses lobby display
            ];
        }

        return [];
    }
}