<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserCategoryController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/commentaries', [CommentController::class, 'index']);
Route::get('/commentsFiltered/{id}', [CommentController::class, 'create']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/users', [UserController::class, 'getAll']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/userCats', [UserCategoryController::class, 'index']);
    Route::get('/userShow/{id}', [UserCategoryController::class, 'show']);
    Route::post('/userCatAdd/{id}', [UserCategoryController::class, 'store']);
    Route::delete('/userCatDel/{id}', [UserCategoryController::class, 'destroy']);
    Route::post('/userCommAdd', [CommentController::class, 'store']);
    Route::post('/userCommEdit/{id}', [CommentController::class, 'update']);
    Route::delete('/userCommDel/{id}', [CommentController::class, 'destroy']);
    Route::get('/comment/{id}', [CommentController::class, 'show']);
    Route::post('/like/{id}', [CommentController::class, 'like']);
    Route::post('/answer/{id}', [CommentController::class, 'addReply']);
    Route::post('/answerEdit/{id}', [CommentController::class, 'updateReply']);
    Route::delete('/answerDel/{id}', [CommentController::class, 'deleteReply']);
});
