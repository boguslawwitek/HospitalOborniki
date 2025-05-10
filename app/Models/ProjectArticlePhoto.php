<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectArticlePhoto extends Model
{
    protected $table = 'project_article_photo';

    protected $fillable = [
        'project_article_id',
        'photo_id',
    ];

    use HasFactory;
}
