<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    protected $fillable = ['wosp_link', 'links', 'registration_hours'];

    protected $casts = [
        'links' => 'array',
        'registration_hours' => 'array',
    ];
}
