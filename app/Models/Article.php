<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Article extends Model
{
    protected $fillable = ['title', 'slug', 'thumbnail', 'body', 'active', 'published_at', 'user_id', 'type', 'category_id', 'external', 'additional_body', 'map_body'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_article')
            ->using(CategoryArticle::class)
            ->withPivot('sort_order');
    }

    public function navigationItems(): MorphMany
    {
        return $this->morphMany(NavigationItem::class, 'navigable');
    }

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(Attachment::class, 'article_attachment');
    }

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'article_photo');
    }
}
