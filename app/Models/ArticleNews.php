<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ArticleNews extends Model
{
    protected $table = 'article_news';

    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'body',
        'active',
        'published_at',
        'user_id',
    ];

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(Attachment::class, 'article_news_attachment');
    }

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'article_news_photo');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
