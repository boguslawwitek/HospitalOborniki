<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home');
})->name('home');

Route::get('kontakt', function () {
    return Inertia::render('contact');
})->name('contact');

Route::post('kontakt/wyslij', [\App\Http\Controllers\Api\ContactController::class, 'sendMessage'])->name('contact.send');

Route::get('administracja/{slug}', function ($slug) {
    return Inertia::render('employees', ['slug' => $slug]);
})->name('employees');

Route::get('aktualnosci', function () {
    return Inertia::render('news/news', ['page' => request()->query('strona', 1)]);
})->name('news');

Route::get('aktualnosci/{slug}', function ($slug) {
    return Inertia::render('news/news-details', ['slug' => $slug]);
})->name('news-details');

Route::get('oferty-pracy', function () {
    return Inertia::render('joboffers');
})->name('joboffers');

Route::get('cennik-badan', function () {
    return Inertia::render('price');
})->name('price');

Route::get('diety', function () {
    return Inertia::render('diets', ['page' => request()->query('strona', 1)]);
})->name('diets');

Route::get('telefony', function () {
    return Inertia::render('telephones');
})->name('telephones');

Route::get('informacje/{slug}', function ($slug) {
    return Inertia::render('article', ['slug' => $slug, 'categorySlug' => null]);
})->name('article');

Route::get('informacje/{categorySlug}/{slug}', function ($categorySlug, $slug) {
    $category = \App\Models\Category::where('slug', $categorySlug)->first();
    if (!$category) {
        return abort(404);
    }
    
    return Inertia::render('article', ['slug' => $slug, 'categorySlug' => $categorySlug]);
})->name('category.article');

Route::get('projekty/{typeSlug?}', function ($typeSlug = null) {
    return Inertia::render('projects/projects', ['typeSlug' => $typeSlug]);
})->name('projects');

Route::get('projekty/{typeSlug}/{projectSlug}', function ($typeSlug, $projectSlug) {
    $projectType = \App\Models\ProjectType::where('slug', $typeSlug)->first();
    if (!$projectType) {
        return abort(404);
    }
    
    return Inertia::render('projects/project-details', [
        'typeSlug' => $typeSlug,
        'projectSlug' => $projectSlug
    ]);
})->name('project.details');