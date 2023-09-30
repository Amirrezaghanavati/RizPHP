<?php

namespace App;
use System\Database\ORM\Model;

class Role extends Model
{
    protected string $table = 'roles';
    protected array $fillable = ['name'];
    protected array $casts = [];

    public function users()
    {
        return $this->belongsToMany('\App\User', 'user_role', 'id', 'role_id', 'user_id', 'id');
    }
}