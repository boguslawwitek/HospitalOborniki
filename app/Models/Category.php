<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
    protected $fillable = ['title', 'slug'];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'category_article')
            ->using(CategoryArticle::class)
            ->withPivot('sort_order')
            ->orderBy('category_article.sort_order');
    }

    public function navigationItems(): MorphMany
    {
        return $this->morphMany(NavigationItem::class, 'navigable');
    }
}
