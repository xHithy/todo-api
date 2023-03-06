<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tasks';

    protected $fillable = [
        'user_id',
        'title',
        'text',
        'created_at'
    ];


    public static function verifyAuthor($id): bool
    {
        $task = Task::where('id', $id)->first();
        $task_author = $task['user_id'];

        if($task_author == session('verified')) {
            return true;
        } else {
            return false;
        }
    }
}
