<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Diet extends Model
{
    protected $fillable = [
        'name',
        'breakfast_photo',
        'lunch_photo',
        'breakfast_body',
        'lunch_body',
        'active',
        'published_at',
        'user_id',
        'diet_attachment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
