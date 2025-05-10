<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JobOffers extends Model
{
    protected $table = 'job_offers';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'active',
        'published_at',
        'user_id',
    ];

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(Attachment::class, 'job_offers_attachment');
    }

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'job_offers_photo');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
