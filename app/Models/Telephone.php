<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telephone extends Model
{
    protected $fillable = ['sort_order', 'section', 'telephones'];

    protected $casts = [
        'telephones' => 'array',
    ];
}
