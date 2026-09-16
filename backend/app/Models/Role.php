<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'ID_ROLE';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'ID_ROLE', 'ID_ROLE');
    }
}
