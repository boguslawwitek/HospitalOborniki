<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    protected $fillable = ['telephone', 'links', 'logo', 'title1', 'title2', 'subtitle'];

    protected $casts = [
        'links' => 'array',
    ];
}
