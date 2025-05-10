<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArticleNews;
use DOMDocument;
use DOMXPath;

class ArticleNewsController extends Controller
{
    private function cleanFileInfo(?string $html): ?string
    {
        if (!$html) {
            return $html;
        }
        
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        
        $figcaptions = $xpath->query('//figcaption');
        foreach ($figcaptions as $figcaption) {
            $figcaption->parentNode->removeChild($figcaption);
        }
        
        $imageLinks = $xpath->query('//a[.//img]');
        foreach ($imageLinks as $link) {
            $images = $xpath->query('.//img', $link);
            $parent = $link->parentNode;
            
            foreach ($images as $img) {
                $link->removeChild($img);
                $parent->insertBefore($img, $link);
            }
            
            $parent->removeChild($link);
        }
        
        return $dom->saveHTML();
    }

    public function index()
    {
        $slug = request('slug');
        $page = request('page', 1);
        $perPage = request('per_page', 10);

        $query = ArticleNews::query()->where('active', true);
        
        if ($slug) {
            $query->where('slug', $slug)->with(['photos', 'attachments']);
            
            $articleNews = $query->orderBy('published_at', 'desc')->get()->map(function ($item) {
                if ($item->thumbnail && !str_starts_with($item->thumbnail, '/storage/')) {
                    $item->thumbnail = '/storage/' . ltrim($item->thumbnail, '/');
                }
                
                if ($item->photos) {
                    $item->photos->transform(function ($photo) {
                        if ($photo->image_path && !str_starts_with($photo->image_path, '/storage/')) {
                            $photo->image_path = '/storage/' . ltrim($photo->image_path, '/');
                        }
                        return $photo;
                    });
                }
                
                if ($item->attachments) {
                    $item->attachments->transform(function ($attachment) {
                        if ($attachment->file_path && !str_starts_with($attachment->file_path, '/storage/')) {
                            $attachment->file_path = '/storage/' . ltrim($attachment->file_path, '/');
                        }
                        return $attachment;
                    });
                }
                
                if ($item->body) {
                    $item->body = $this->cleanFileInfo($item->body);
                }
                
                return $item;
            });
            
            return response()->json([
                'articleNews' => $articleNews,
            ]);
        } else {
            $total = $query->count();
            $articleNews = $query->orderBy('published_at', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get()
                ->map(function ($item) {
                    if ($item->thumbnail && !str_starts_with($item->thumbnail, '/storage/')) {
                        $item->thumbnail = '/storage/' . ltrim($item->thumbnail, '/');
                    }
                    return $item;
                });
            
            return response()->json([
                'articleNews' => $articleNews,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($total / $perPage),
                ],
            ]);
        }
    }
}