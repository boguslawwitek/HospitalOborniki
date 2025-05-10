<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectArticleAttachment extends Model
{
    protected $table = 'project_article_attachment';

    protected $fillable = [
        'project_article_id',
        'attachment_id',
    ];

    use HasFactory;
}
