<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Header;

class HeaderController extends Controller
{
    public function index()
    {
        $header = Header::first();
        $links = collect($header?->links ?? [])
            ->filter(function ($item) {
                return $item['type'] === 'link' && !empty($item['data']);
            })
            ->map(function ($item) {
                return [
                    'name' => $item['data']['name'] ?? '',
                    'icon' => $item['data']['icon'] ?? null,
                    'icon-alt' => $item['data']['icon-alt'] ?? null,
                    'url' => $item['data']['url'] ?? '#',
                    'external' => $item['data']['external'] ?? false,
                ];
            })
            ->filter(function ($link) {
                return !empty($link['name']) && !empty($link['url']);
            })
            ->values()
            ->all();

        return response()->json([
            'title1' => $header?->title1,
            'title2' => $header?->title2,
            'subtitle' => $header?->subtitle,
            'logo' => $header?->logo ? '/storage/' . $header->logo : null,
            'telephone' => $header?->telephone,
            'links' => $links,
        ]);
    }
}
