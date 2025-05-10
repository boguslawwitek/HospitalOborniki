<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Price;
use App\Models\Article;

class PriceController extends Controller
{
    public function index()
    {
        $price = Price::first();
        $attachment = null;
        $categoryTitle = null;
        
        if ($price && $price->attachment_id) {
            $attachment = Attachment::find($price->attachment_id);
            
            if ($attachment && $attachment->file_path && !str_starts_with($attachment->file_path, '/storage/')) {
                $attachment->file_path = '/storage/' . ltrim($attachment->file_path, '/');
            }
        }
        
        $priceArticle = Article::where('type', 'link')
            ->where(function($query) {
                $query->where('slug', 'like', '/cennik-badan');
            })
            ->first();
        
        if ($priceArticle) {
            $categories = $priceArticle->categories;
            
            if ($categories && $categories->count() > 0) {
                $categoryTitle = $categories->first()->title;
            } else {
                $categoryTitle = null;
            }
        }

        return response()->json([
            'attachment' => $attachment,
            'category' => $categoryTitle
        ]);
    }
}