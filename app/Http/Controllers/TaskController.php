<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TaskController extends ResponseController
{
    public static function fetchTodos(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'limit' => 'numeric|integer|gte:0',
            'offset' => 'numeric|integer|gte:0'
        ]);

        if($validator->fails()) return self::validationFail($validator->messages());

        request()->input('limit') ? $limit = request()->input('limit') : $limit = 20;
        request()->input('offset') ? $offset = request()->input('offset') : $offset = 0;

        /*
         Gap between offset and limit cannot exceed 50
         If the rule is ignored, limits the result count to 50
        */
        if($limit - $offset > 50) $limit = $offset + 50;

        $tasks = Task::offset($offset)->limit($limit)->get();
        if($tasks) {
            return response()->json([
                'code' => 200,
                'tasks' => $tasks,
                'paging' => [
                    'total' => $tasks->count(),
                    'limit' => (int)$limit,
                    'offset' => (int)$offset
                ]
            ]);
        }

        // Most likely database related error, throw ERR CODE 500
        return self::errorWithMessage('Something went wrong whilst fetching tasks');
    }

    public static function createTodo(): JsonResponse
    {
        $validation = Validator::make(request()->all(), [
            'title' => 'required',
            'text' => 'required',
        ]);

        if($validation->fails()) return self::validationFail($validation->messages());

        $task = Task::create([
            'title' => request('title'),
            'text' => request('text'),
            'created_at' => time()
        ]);

        if($task) return self::createdSuccessWithMessage('task', $task);

        // Most likely database related error, throw ERR CODE 500
        return self::errorWithMessage('Something went wrong');
    }

    public static function updateTodo()
    {

    }

    public static function deleteTodo()
    {

    }
}
