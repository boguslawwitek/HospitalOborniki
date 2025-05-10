<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticlePhoto extends Model
{
    protected $table = 'article_photo';

    protected $fillable = [
        'article_id',
        'photo_id',
    ];

    use HasFactory;
}
