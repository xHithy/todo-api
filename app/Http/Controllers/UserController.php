<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends ResponseController
{
    public static function register(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'username' => 'required|min:3|unique:users',
            'password' => 'required|min:8',
        ]);

        if($validator->fails()) return self::validationFail($validator->messages());

        $user = User::create([
            'username' => request('username'),
            'password' => Hash::make(request('password')),
            'created_at' => time(),
            'last_login' => time()
        ]);

        if($user) {
            session(['verified' => $user['id'] ]);
            return self::successWithMessage('message','Registration successful');
        }

        // Most likely database related error, throw ERR CODE 500
        return self::errorWithMessage('Something went wrong whilst attempting to register');
    }

    public static function login(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'username' => 'required|exists:users',
            'password' => 'required'
        ]);

        if($validator->fails()) return self::validationFail($validator->messages());

        $user = User::where('username', request('username'))->first();

        if($user) {
            if(Hash::check(request('password'), $user['password'])) {
                session(['verified' => $user['id'] ]);
                return self::successWithMessage('message', 'Login successful');
            }
            return self::invalidCredentials();
        }

        // Most likely database related error, throw ERR CODE 500
        return self::errorWithMessage('Something went wrong whilst attempting to login');
    }

    public static function logout(): JsonResponse
    {
        session()->forget('verified');
        return self::successWithMessage('message', 'Logout succesful');
    }
}
