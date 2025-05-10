<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectArticle;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;

class ProjectController extends Controller
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
    public function index(Request $request, $typeSlug = null)
    {
        $query = ProjectArticle::query()
            ->where('active', true)
            ->with(['projectTypes', 'photos', 'attachments']);
        
        if ($typeSlug) {
            $projectType = ProjectType::where('slug', $typeSlug)->first();
            
            if ($projectType) {
                $query->whereHas('projectTypes', function ($q) use ($projectType) {
                    $q->where('project_types.id', $projectType->id);
                });
            }
        }
        
        $projects = $query->orderBy('published_at', 'desc')
            ->get()
            ->map(function ($item) {
                if ($item->logo && !str_starts_with($item->logo, '/storage/')) {
                    $item->logo = '/storage/' . ltrim($item->logo, '/');
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
            'projects' => $projects,
            'projectType' => $typeSlug ? ProjectType::where('slug', $typeSlug)->first() : null
        ]);
    }
    
    public function show(Request $request, $typeSlug, $projectSlug)
    {
        $projectType = ProjectType::where('slug', $typeSlug)->first();
        
        if (!$projectType) {
            return response()->json([
                'error' => 'Nie znaleziono typu projektu'
            ], 404);
        }
        
        $project = ProjectArticle::where('slug', $projectSlug)
            ->where('active', true)
            ->whereHas('projectTypes', function ($query) use ($projectType) {
                $query->where('project_types.id', $projectType->id);
            })
            ->with(['projectTypes', 'photos', 'attachments'])
            ->first();
        
        if (!$project) {
            return response()->json([
                'error' => 'Nie znaleziono projektu'
            ], 404);
        }
        
        if ($project->logo && !str_starts_with($project->logo, '/storage/')) {
            $project->logo = '/storage/' . ltrim($project->logo, '/');
        }
        
        if ($project->photos) {
            $project->photos->transform(function ($photo) {
                if ($photo->image_path && !str_starts_with($photo->image_path, '/storage/')) {
                    $photo->image_path = '/storage/' . ltrim($photo->image_path, '/');
                }
                return $photo;
            });
        }
        
        if ($project->attachments) {
            $project->attachments->transform(function ($attachment) {
                if ($attachment->file_path && !str_starts_with($attachment->file_path, '/storage/')) {
                    $attachment->file_path = '/storage/' . ltrim($attachment->file_path, '/');
                }
                return $attachment;
            });
        }
        
        if ($project->body) {
            $project->body = $this->cleanFileInfo($project->body);
        }
        
        return response()->json([
            'project' => $project,
            'projectType' => $projectType
        ]);
    }
}