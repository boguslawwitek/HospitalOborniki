<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homepage extends Model
{
    protected $fillable = ['title', 'photo', 'content', 'carousel_photos'];

    protected $casts = [
        'carousel_photos' => 'array',
    ];
}
