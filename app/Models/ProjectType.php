<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProjectType extends Model
{
    protected $table = 'project_types';
    protected $fillable = ['title', 'slug'];

    public function projectArticles(): BelongsToMany
    {
        return $this->belongsToMany(ProjectArticle::class, 'project_type_project_article');
    }
}
