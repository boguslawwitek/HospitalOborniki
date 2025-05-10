<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HeaderController;
use App\Http\Controllers\Api\HomepageController;
use App\Http\Controllers\Api\FooterController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\NavigationItemController;
use App\Http\Controllers\Api\ArticleNewsController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\JobOffersController;
use App\Http\Controllers\Api\PriceController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TelephoneController;
use App\Http\Controllers\Api\DietController;

Route::middleware('web')->group(function () {
    Route::get('/header', [HeaderController::class, 'index']);
    Route::get('/homepage', [HomepageController::class, 'index']);
    Route::get('/footer', [FooterController::class, 'index']);
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::get('/navigation-items', [NavigationItemController::class, 'index']);
    Route::get('/news', [ArticleNewsController::class, 'index']);
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/contact', [ContactController::class, 'index']);
    Route::post('/contact/send', [ContactController::class, 'sendMessage']);
    Route::get('/joboffers', [JobOffersController::class, 'index']);
    Route::get('/price', [PriceController::class, 'index']);
    Route::get('/projects/{typeSlug?}', [ProjectController::class, 'index']);
    Route::get('/projects/{typeSlug}/{projectSlug}', [ProjectController::class, 'show']);
    Route::get('/telephones', [TelephoneController::class, 'index']);
    Route::get('/diets', [DietController::class, 'index']);
});
