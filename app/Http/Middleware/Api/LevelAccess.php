<?php

namespace App\Http\Middleware\Api;

use Closure;

class LevelAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $level)
    {
        $user = apiAuth();
        $user_level = $user->role_name;
       // dd($user->id) ;
        $level_access = [
            'user' => ['user', 'teacher', 'organization'],
            'teacher' => ['organization', 'teacher'],
            'organization' => ['organization'],

        ];
        $levels = array_keys($level_access);
        if (in_array($level, $levels) && in_array($user_level, $level_access[$level])) {

            return $next($request);
        }
        return apiResponse2(0, 'forbidden', 'Auth user has not access to this level');

    }
}
