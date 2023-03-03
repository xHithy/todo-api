<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1/todos')->group(function() {
    Route::get('', [TaskController::class, 'fetchTodos']);
    Route::post('', [TaskController::class, 'createTodo']);
    Route::put('/{id}', [TaskController::class, 'updateTodo']);
    Route::delete('/{id}', [TaskController::class, 'deleteTodo']);
});
