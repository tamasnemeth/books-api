<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\StatisticsController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth.token')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Books CRUD
    Route::get('/books/search', [BookController::class, 'search']);
    Route::apiResource('books', BookController::class);
    // Authors CRUD
    Route::apiResource('authors', AuthorController::class);

    // Categories CRUD
    Route::apiResource('categories', CategoryController::class);

    // Statistics
    Route::get('/statistics/expensive-books', [StatisticsController::class, 'expensiveBooks']);
    Route::get('/statistics/popular-categories', [StatisticsController::class, 'popularCategories']);
    Route::get('/statistics/top-fantasy-and-sci-fi', [StatisticsController::class, 'topFantasyAndSciFi']);
});
