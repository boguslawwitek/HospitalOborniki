<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Location;
use App\Models\Attachment;
use App\Models\Photo;
use DOMDocument;
use DOMXPath;

class ArticleController extends Controller
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
        $query = Article::query();

        $query->where('type', '!=', 'link')
              ->where('active', true);
        
        if ($slug) {
            $query->where('slug', $slug)->with(['photos', 'attachments', 'categories']);
        }
        
        $articles = $query->orderBy('published_at', 'desc')->get()->map(function ($item) {
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
            
            if ($item->type === 'article-with-map') {
                $location = Location::first();
                
                if ($location && $location->photo_id) {
                    $mapPhoto = Photo::where('id', $location->photo_id)->first();
                    
                    if ($mapPhoto) {
                        if ($mapPhoto->image_path && !str_starts_with($mapPhoto->image_path, '/storage/')) {
                            $mapPhoto->image_path = '/storage/' . ltrim($mapPhoto->image_path, '/');
                        }
                        
                        $item->map_photo = $mapPhoto;
                    }
                }
            }
            
            if ($item->body) {
                $item->body = $this->cleanFileInfo($item->body);
            }
            
            if ($item->additional_body) {
                $item->additional_body = $this->cleanFileInfo($item->additional_body);
            }
            
            if ($item->map_body) {
                $item->map_body = $this->cleanFileInfo($item->map_body);
            }
            
            return $item;
        });

        return response()->json([
            'articles' => $articles,
        ]);
    }
}