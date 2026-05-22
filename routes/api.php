<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserCategoryController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::post('/auth', [UserController::class, 'auth']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/comments', [CommentController::class, 'index']);
Route::get('/comments/{category}', [CommentController::class, 'byCategory']);
Route::get('/comment/{comment}', [CommentController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UserController::class, 'user']);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::get('/users', [UserController::class, 'users']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::post('/useredit/{user}', [UserController::class, 'update']);

    Route::get('/services', [UserCategoryController::class, 'index']);
    Route::post('/serviceadd', [UserCategoryController::class, 'store']);
    Route::delete('/servicedel/{service}', [UserCategoryController::class, 'destroy']);

    Route::post('/commentadd', [CommentController::class, 'store']);
    Route::post('/commentedit/{comment}', [CommentController::class, 'update']);
    Route::delete('/commentdel/{comment}', [CommentController::class, 'destroy']);
    Route::post('/like/{comment}', [CommentController::class, 'like']);
    Route::post('/answer/{comment}', [CommentController::class, 'answer']);
    Route::post('/answeredit/{answer}', [CommentController::class, 'answerEdit']);
    Route::delete('/answerdel/{answer}', [CommentController::class, 'answerDel']);
});
