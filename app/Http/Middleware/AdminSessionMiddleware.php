<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class AdminSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Set admin session config
        Config::set('session', config('session.admin'));

        return $next($request);
    }
}
