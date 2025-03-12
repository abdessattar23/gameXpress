<?php


use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \Illuminate\Auth\Middleware\Authorize;


// Auth api routes
Route::post('/register', [AuthController::class, 'Register'])->name('register');
Route::post('/login', [AuthController::class, 'Login'])->name('login');
Route::post('/logout', [AuthController::class, 'Logout'])->middleware('auth:sanctum');

// dashboard routes
Route::get('/v1/admin/dashboard', [AdminController::class, 'index'])->middleware(['auth:sanctum', 'can:view_dashboard']);

// product routes
Route::apiResource('products', ProductController::class);
