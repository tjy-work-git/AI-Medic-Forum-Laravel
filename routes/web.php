<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // UserController
    Route::get('/account/{activeTab?}', [UserController::class, 'account_info'])->name('account');
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