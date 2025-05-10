<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleNewsAttachment extends Model
{
    protected $table = 'article_news_attachment';

    protected $fillable = [
        'article_news_id',
        'attachment_id',
    ];

    use HasFactory;
}
