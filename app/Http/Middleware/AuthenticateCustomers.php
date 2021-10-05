<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateCustomers
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('customers')->user()) {
            return response('Unauthorized', 401);
        }
        return $next($request);
    }
}
