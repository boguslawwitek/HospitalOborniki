<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleAttachment extends Model
{
    protected $table = 'article_attachment';

    protected $fillable = [
        'article_id',
        'attachment_id',
    ];

    use HasFactory;
    
}
