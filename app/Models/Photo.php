<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Photo extends Model
{
    protected $fillable = ['image_name', 'image_desc', 'image_path'];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_photo');
    }

    public function articlesNews(): BelongsToMany
    {
        return $this->belongsToMany(ArticleNews::class, 'article_news_photo');
    }

    public function jobOffers(): BelongsToMany
    {
        return $this->belongsToMany(JobOffers::class, 'job_offers_photo');
    }

    public function projectArticles(): BelongsToMany
    {
        return $this->belongsToMany(ProjectArticle::class, 'project_article_photo');
    }
}
