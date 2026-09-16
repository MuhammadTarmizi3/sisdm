<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles;

    protected $table = 'users';
    protected $primaryKey = 'USER_ID';
    protected $keyType = 'int';
    public $incrementing = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $guarded = [];

    protected $hidden = [
        'PASSWORD',
    ];

    protected $casts = [
        'IS_ACTIVE' => 'boolean',
        'PASSWORD' => 'hashed',
    ];

    public function getAuthIdentifierName()
    {
        return 'USER_ID';
    }

    public function getAuthPasswordName()
    {
        return 'PASSWORD';
    }

    public function getAuthPassword()
    {
        return $this->PASSWORD;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'ID_ROLE', 'ID_ROLE');
    }

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'ID_PEGAWAI', 'ID_PEGAWAI');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'USER_ID', 'USER_ID');
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'ID_USER', 'USER_ID');
    }
}
