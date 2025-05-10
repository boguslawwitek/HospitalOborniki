<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOffers;
use DOMDocument;
use DOMXPath;

class JobOffersController extends Controller
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
        $slug = request('slug');

        $query = JobOffers::query()->where('active', true);
        
        if ($slug) {
            $query->where('slug', $slug);
        }
        
        $query->with(['photos', 'attachments']);
        
        $jobOffers = $query->orderBy('published_at', 'desc')->get()->map(function ($item) {
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
            if ($item->additional_information) {
                $item->additional_information = $this->cleanFileInfo($item->additional_information);
            }
            
            return $item;
        });
        
        return response()->json([
            'jobOffers' => $jobOffers
        ]);
    }
}