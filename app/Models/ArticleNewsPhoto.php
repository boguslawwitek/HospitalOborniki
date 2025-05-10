<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleNewsPhoto extends Model
{
    protected $table = 'article_news_photo';

    protected $fillable = [
        'article_news_id',
        'photo_id',
    ];

    use HasFactory;
}
