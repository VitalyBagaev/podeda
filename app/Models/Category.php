<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function userServices()
    {
        return $this->hasMany(UserService::class);
    }
}
