<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // UserController
    Route::post('/user/profile/update', [UserController::class, 'update']);
    Route::post('/user/profile/delete', [UserController::class, 'destroy']);
    Route::get('/user/profile/edit', [UserController::class, 'edit']);
    Route::get('/user/posts/{id?}', [UserController::class, 'show_posts_history']);
    Route::get('/user/profile/{id?}', [UserController::class, 'show_user']);

    // Forum Controller
    Route::post('/forum/post/store', [ForumController::class, 'store_post']);
    Route::post('/forum/post/{id}/bookmark', [ForumController::class, 'bookmark']);
    Route::post('/forum/comment/store', [ForumController::class, 'store_comment']);
    Route::post('/forum/{type}/{id}/delete', [ForumController::class, 'destroy_content']);
    Route::post('/forum/{type}/{id}/update', [ForumController::class, 'update_content']);
    Route::post('/forum/{type}/{id}/upvote', [ForumController::class, 'upvote_content']);
    Route::post('/forum/{type}/{id}/report', [ForumController::class, 'report_content']);
    Route::get('/forum/post/create', [ForumController::class, 'create']);
});

Route::inertia('/', 'Index')->name('index'); // requires a name
Route::inertia('/about-us', 'AboutUs');

// AuthController
Route::get('/user/login', [AuthController::class, 'get_login'])->name('login');
Route::get('/user/register', [AuthController::class, 'get_register']);
Route::get('/user/forgot-password', [AuthController::class, 'get_forgot_password']);
Route::post('/action/login', [AuthController::class, 'login_auth']);
Route::post('/action/register', [AuthController::class, 'register']);
Route::post('/action/authenticate', [AuthController::class, 'authenticate']);
Route::post('/action/logout', [AuthController::class, 'logout']);

// ForumController
Route::get('/forum', [ForumController::class, 'index']);
Route::get('/forum/post/{id}', [ForumController::class, 'show_post']);
Route::get('/forum/post/{id}/comments', [ForumController::class, 'show_comments']);

