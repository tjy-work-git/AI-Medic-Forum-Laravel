<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // UserController
    Route::get('/user/profile/edit', [UserController::class, 'edit'])->name('user.profile.edit');
    Route::post('/user/profile/update', [UserController::class, 'update'])->name('user.profile.update');
    Route::post('/user/profile/delete', [UserController::class, 'destroy'])->name('user.profile.delete');
    Route::get('/user/posts/{id?}', [UserController::class, 'show_posts'])->name('user.posts');
    Route::get('/user/profile/{id?}', [UserController::class, 'show_user'])->name('user.profile');
});

Route::inertia('/', 'Index')->name('index');
Route::inertia('/about-us', 'AboutUs')->name('aboutus');

// AuthController
Route::get('/user/login', [AuthController::class, 'get_login'])->name('login');
Route::get('/user/register', [AuthController::class, 'get_register'])->name('register');
Route::get('/user/forgot-password', [AuthController::class, 'get_forgot_password'])->name('forgot_password');
Route::post('/action/login', [AuthController::class, 'login_auth'])->name('action.login_auth');
Route::post('/action/register', [AuthController::class, 'register'])->name('action.register');
Route::post('/action/authenticate', [AuthController::class, 'authenticate'])->name('action.authenticate');
Route::post('/action/logout', [AuthController::class, 'logout'])->name('action.logout');

// ForumController
Route::get('/forum', [ForumController::class, 'index'])->name('forum');
Route::get('/forum/post/{id}', [ForumController::class, 'show']);

