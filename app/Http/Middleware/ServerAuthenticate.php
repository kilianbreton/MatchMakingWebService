<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class ServerAuthenticate extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = ['server'];
        }

        foreach ($guards as $guard) {
            if (auth($guard)->check()) {
                auth()->shouldUse($guard);
                return;
            }
        }

        $this->unauthenticated($request, $guards);
    }

    protected function unauthenticated($request, array $guards)
    {
        // For API JWT: return JSON 401 instead of redirect
        abort(response()->json([
            'message' => 'Unauthenticated'
        ], 401));
    }
   
}
