<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attachment extends Model
{
    protected $fillable = ['file_name', 'file_path'];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_attachment');
    }

    public function articlesNews(): BelongsToMany
    {
        return $this->belongsToMany(ArticleNews::class, 'article_news_attachment');
    }

    public function jobOffers(): BelongsToMany
    {
        return $this->belongsToMany(JobOffers::class, 'job_offers_attachment');
    }

    public function projectArticles(): BelongsToMany
    {
        return $this->belongsToMany(ProjectArticle::class, 'project_article_attachment');
    }
}
