<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DietController extends Controller
{
    public function index()
    {
        $page = request('page', 1);
        $perPage = request('per_page', 7);
        
        $query = Diet::query()->where('active', true);
        
        $total = $query->count();
        $diets = $query->orderBy('published_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($diet) {
                return $this->transformDiet($diet);
            });
        
        return response()->json([
            'diets' => $diets,
            'pagination' => [
                'total' => $total,
                'per_page' => (int)$perPage,
                'current_page' => (int)$page,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }

    private function transformDiet($diet)
    {
        return [
            'id' => $diet->id,
            'name' => $diet->name,
            'breakfast_photo' => $diet->breakfast_photo ? Storage::url($diet->breakfast_photo) : null,
            'lunch_photo' => $diet->lunch_photo ? Storage::url($diet->lunch_photo) : null,
            'breakfast_body' => $diet->breakfast_body,
            'lunch_body' => $diet->lunch_body,
            'published_at' => $diet->published_at,
            'diet_attachment' => $diet->diet_attachment ? Storage::url($diet->diet_attachment) : null,
        ];
    }
}
