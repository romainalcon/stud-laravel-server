<?php

use App\Http\Controllers\Api\DirectoryController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class)->name('api.register');
Route::post('/posts', PostController::class)->name('api.posts.store');
Route::get('/feed', FeedController::class)->name('api.feed');
Route::get('/directory', DirectoryController::class)->name('api.directory');
