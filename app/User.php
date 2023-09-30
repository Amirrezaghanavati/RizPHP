<?php

namespace App;
use System\Database\ORM\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['username'];
    protected array $casts = [];

    public function roles()
    {
        return $this->belongsToMany('\App\Role', 'user_role', 'id', 'user_id', 'role_id', 'id');
    }

}