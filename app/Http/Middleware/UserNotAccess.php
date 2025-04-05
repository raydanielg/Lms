<?php

namespace App\Http\Middleware;

use Closure;

class UserNotAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() and !auth()->user()->isUser()) {
            return $next($request);
        }

        abort(404);
    }
}
