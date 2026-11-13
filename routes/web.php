<?php

use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class);

Route::post('/contact', [ContactFormController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('contact.submit');

Route::get('/feeds', [FeedController::class, 'info'])->name('feed.info');
Route::get('/rss', [FeedController::class, 'main'])->name('feed.main');
Route::get('/rss/category/{slug}', [FeedController::class, 'category'])->name('feed.category');

Route::get('/{url}', [FrontendController::class, 'show'])
    ->where('url', '.*');
