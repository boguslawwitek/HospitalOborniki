<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use DOMDocument;
use DOMXPath;
    
class HomepageController extends Controller
{
    private function cleanFileInfo(?string $html): ?string
    {
        if (!$html) {
            return $html;
        }
        
        try {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            
            $xpath = new DOMXPath($dom);
            
            $figcaptions = $xpath->query('//figcaption');
            if ($figcaptions) {
                foreach ($figcaptions as $figcaption) {
                    if ($figcaption && $figcaption->parentNode) {
                        $figcaption->parentNode->removeChild($figcaption);
                    }
                }
            }
            
            $imageLinks = $xpath->query('//a[.//img]');
            if ($imageLinks) {
                foreach ($imageLinks as $link) {
                    if (!$link || !$link->parentNode) continue;
                    
                    $images = $xpath->query('.//img', $link);
                    if (!$images) continue;
                    
                    $parent = $link->parentNode;
                    
                    foreach ($images as $img) {
                        if ($img) {
                            $link->removeChild($img);
                            $parent->insertBefore($img, $link);
                        }
                    }
                    
                    $parent->removeChild($link);
                }
            }
            
            return $dom->saveHTML();
        } catch (\Exception $e) {
            return $html;
        }
    }
    public function index()
    {
        $homepage = Homepage::first();
        $carouselPhotos = collect($homepage?->carousel_photos ?? [])
        ->filter(function ($item) {
            return $item['type'] === 'carousel_photo' && !empty($item['data']);
        })
        ->map(function ($item) {
            return [
                'name' => $item['data']['name'] ?? '',
                'photo' => '/storage/' . $item['data']['photo'] ?? null,
                'description' => $item['data']['description'] ?? null,
                'url' => $item['data']['url'] ?? '#',
                'external' => $item['data']['external'] ?? false,
            ];
        })
        ->filter(function ($carousel_photo) {
            return !empty($carousel_photo['name']) && !empty($carousel_photo['url']) && !empty($carousel_photo['photo']) && !empty($carousel_photo['description']);
        })
        ->values()
        ->all();
        
        $content = null;
        if ($homepage && $homepage->content) {
            try {
                $content = $this->cleanFileInfo($homepage->content);
            } catch (\Exception $e) {
                $content = $homepage->content;
            }
        }
        
        return response()->json([
            'title' => $homepage?->title,
            'content' => $content,
            'photo' => $homepage?->photo ? '/storage/' . $homepage->photo : null,
            'carouselPhotos' => $carouselPhotos,
        ]);
    }
}
