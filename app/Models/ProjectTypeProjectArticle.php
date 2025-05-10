<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectTypeProjectArticle extends Pivot
{
    protected $table = 'project_type_project_article';

    protected $fillable = [
        'project_type_id',
        'project_article_id'
    ];

    public $timestamps = false;

    use HasFactory;
}