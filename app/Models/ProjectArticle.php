<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProjectArticle extends Model
{
    protected $table = 'project_articles';
    protected $fillable = ['title', 'slug', 'logo', 'body', 'active', 'published_at', 'user_id', 'project_type_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projectTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProjectType::class, 'project_type_project_article');
    }

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(Attachment::class, 'project_article_attachment');
    }

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'project_article_photo');
    }
}
