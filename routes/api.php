<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DirectoryController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PersonalFeedController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class)->name('api.register');
Route::post('/posts', PostController::class)->name('api.posts.store');
Route::get('/feed', FeedController::class)->name('api.feed');
Route::get('/directory', DirectoryController::class)->name('api.directory');

// Likes
Route::post('/posts/{id}/like', LikeController::class)->name('api.posts.like');

// Profils
Route::put('/profile', [ProfileController::class, 'update'])->name('api.profile.update');
Route::get('/profiles', [ProfileController::class, 'index'])->name('api.profiles.index');
Route::get('/profiles/{pseudo}', [ProfileController::class, 'show'])->name('api.profiles.show');

// Commentaires
Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->name('api.comments.store');
Route::get('/posts/{id}/comments', [CommentController::class, 'index'])->name('api.comments.index');
Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('api.comments.destroy');

// Follows
Route::post('/follow/{pseudo}', [FollowController::class, 'toggle'])->name('api.follow.toggle');
Route::get('/followers', [FollowController::class, 'followers'])->name('api.followers');
Route::get('/following', [FollowController::class, 'following'])->name('api.following');

// Feed personnel
Route::get('/feed/personal', PersonalFeedController::class)->name('api.feed.personal');

// Messages
Route::post('/messages', [MessageController::class, 'store'])->name('api.messages.store');
Route::get('/messages/unread/count', [MessageController::class, 'unreadCount'])->name('api.messages.unread');
Route::get('/messages/{pseudo}', [MessageController::class, 'conversation'])->name('api.messages.conversation');
Route::get('/messages', [MessageController::class, 'inbox'])->name('api.messages.inbox');
