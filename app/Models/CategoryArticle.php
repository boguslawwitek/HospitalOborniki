<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryArticle extends Pivot
{
    protected $table = 'category_article';

    protected $fillable = [
        'category_id',
        'article_id',
        'sort_order',
    ];

    public $timestamps = false;

    use HasFactory;
}