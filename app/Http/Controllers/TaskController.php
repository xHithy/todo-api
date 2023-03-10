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

        if($validator->fails()) {
            return self::validationFail($validator->messages());
        }

        request()->input('limit') ? $limit = request()->input('limit') : $limit = 20;
        request()->input('offset') ? $offset = request()->input('offset') : $offset = 0;

        /*
         Gap between offset and limit cannot exceed 50
         If the rule is ignored, limits the result count to 50
        */
        if($limit - $offset > 50) {
            $limit = $offset + 50;
        }

        $user_id = session('verified');

        $tasks = Task::query()->where('user_id', $user_id)->offset($offset)->limit($limit)->get();
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

        $task = Task::query()->create([
            'user_id' => session('verified'),
            'title' => request('title'),
            'text' => request('text'),
            'created_at' => time()
        ]);

        if($task) return self::successWithMessage('task', $task);

        // Most likely database related error, throw ERR CODE 500
        return self::errorWithMessage('Something went wrong whilst creating the task');
    }

    public static function updateTodo(): JsonResponse
    {
        $validation = Validator::make(request()->all(), [
            'id' => 'required|exists:tasks',
            'title' => 'required',
            'text' => 'required',
        ]);

        if($validation->fails()) {
            return self::validationFail($validation->messages());
        }

        $id = request('id');

        if(!Task::verifyAuthor($id)) {
            return self::invalidAuthor();
        }

        Task::query()->where('id', $id)->update([
            'title' => request('title'),
            'text' => request('text'),
            'updated_at' => time()
        ]);

        $updated_task = Task::query()->where('id', $id)->first();

        return self::successWithMessage('task', $updated_task);
    }

    public static function deleteTodo(): JsonResponse
    {
        $validation = Validator::make(request()->all(), [
            'id' => 'required|exists:tasks',
        ]);

        if($validation->fails()) {
            return self::validationFail($validation->messages());
        }

        $id = request('id');

        if(!Task::verifyAuthor($id)) {
            return self::invalidAuthor();
        }

        Task::query()->where('id', $id)->delete();

        return self::emptySuccess();
    }
}
