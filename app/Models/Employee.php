<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['sort_order', 'section', 'employees', 'slug'];

    protected $casts = [
        'employees' => 'array',
    ];
}
