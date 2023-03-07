<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Authentication API endpoints
Route::prefix('v1/auth')->group(function() {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout'])->middleware('verify.auth');
});

// Task API endpoints
Route::prefix('v1/todos')->middleware(['throttle:10', 'verify.auth'])->group(function() {
    Route::get('', [TaskController::class, 'fetchTodos']);
    Route::post('', [TaskController::class, 'createTodo']);
    Route::put('', [TaskController::class, 'updateTodo']);
    Route::delete('', [TaskController::class, 'deleteTodo']);
});

Route::fallback(function () {
    return response()->json([
        "code" => "404",
        "message" => "This endpoint doesn't exist"
    ], 404);
});
