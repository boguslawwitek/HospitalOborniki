<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use App\Models\Article;

class FooterController extends Controller
{
    public function index()
    {
        $footer = Footer::first();
        $links = collect($footer?->links ?? [])
            ->filter(function ($item) {
                return $item['type'] === 'link' && !empty($item['data']);
            })
            ->map(function ($item) {
                return [
                    'name' => $item['data']['name'] ?? '',
                    'url' => $item['data']['url'] ?? '#',
                    'external' => $item['data']['external'] ?? false,
                ];
            })
            ->filter(function ($link) {
                return !empty($link['name']) && !empty($link['url']);
            })
            ->values()
            ->all();

        $registration_hours = collect($footer?->registration_hours ?? [])
            ->filter(function ($item) {
                return $item['type'] === 'registration_hour' && !empty($item['data']);
            })
            ->map(function ($item) {
                return [
                    'day' => $item['data']['day'] ?? '',
                    'hours' => $item['data']['hours'] ?? '',
                ];
            })
            ->filter(function ($registration_hour) {
                return !empty($registration_hour['day']) && !empty($registration_hour['hours']);
            })
            ->values()
            ->all();

        $wospArticleId = $footer?->wosp_link;
        $wospArticleSlug = null;
        
        if ($wospArticleId) {
            $article = Article::with('categories')->find($wospArticleId);
            if ($article) {
                if ($article->categories->count() > 0) {
                    $firstCategory = $article->categories->first();
                    $wospArticleSlug = '/informacje/' . $firstCategory->slug . '/' . $article->slug;
                } else {
                    $wospArticleSlug = '/informacje/' . $article->slug;
                }
            }
        }

        return response()->json([
            'wosp_link' => $wospArticleSlug,
            'registration_hours' => $registration_hours,
            'links' => $links,
        ]);
    }
}