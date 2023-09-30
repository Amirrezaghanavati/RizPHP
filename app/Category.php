<?php

namespace App;

use System\Database\ORM\Model;

class Category extends Model
{
    protected string $table = 'categories';
    protected array $fillable = ['name'];
    protected array $casts = [];

    public function posts()
    {
        return $this->hasMany('\App\Post', 'cat_id', 'id');
    }
}