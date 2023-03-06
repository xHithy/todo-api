<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $verify = session('verified');
        if(!$verify) return response()->json([
            'code' => 401,
            'error' => 'Unauthorized'
        ]);
        return $next($request);
    }
}
