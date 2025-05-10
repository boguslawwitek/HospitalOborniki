<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $fillable = ['name', 'sort_order', 'navigable_type', 'navigable_id'];

    public function navigable()
    {
        return $this->morphTo();
    }
}
