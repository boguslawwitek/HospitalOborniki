<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NavigationItem;
use App\Models\Category;
use App\Models\Article;

class NavigationItemController extends Controller
{
    public function index()
    {
        $items = NavigationItem::orderBy('sort_order')->get();
        $result = $items->map(function ($item) {

            $type = $item->navigable_type;
            if ($type === 'Article') {
                $article = Article::find($item->navigable_id);
                $name = $item->name ?? ($article ? $article->title : null);
                if ($article && $article->type !== 'link' && $article->slug) {
                    $articleWithCategories = Article::with('categories')->find($article->id);
                    
                    if ($articleWithCategories->categories->count() > 0) {
                        $firstCategory = $articleWithCategories->categories->first();
                        $article->slug = '/informacje/' . $firstCategory->slug . '/' . ltrim($article->slug, '/');
                    } else {
                        $article->slug = '/informacje/' . ltrim($article->slug, '/');
                    }
                }
                return [
                'id' => $item->id,
                'name' => $name,
                'sort_order' => $item->sort_order,
                'type' => 'article',
                'article' => $article,
            ];
            } elseif ($type === 'Category') {
                $category = Category::find($item->navigable_id);
                $articles = $category
            ? $category->articles()->orderBy('category_article.sort_order')->get()->map(function($article) use ($category) {
                if ($article->type !== 'link' && $article->slug) {
                    $article->slug = '/informacje/' . $category->slug . '/' . ltrim($article->slug, '/');
                }
                return $article;
            })
            : [];
                $name = $item->name ?? ($category ? $category->title : null);
                return [
                    'id' => $item->id,
                    'name' => $name,
                    'sort_order' => $item->sort_order,
                    'type' => 'category',
                    'articles' => $articles,
                ];
            } else {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sort_order' => $item->sort_order,
                    'type' => $type
                ];
            }
        });
        return response()->json($result);
    }
}
