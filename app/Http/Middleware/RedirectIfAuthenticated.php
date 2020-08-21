<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {

            if ($guard == 'managers') {
                // 後台已登入跳到後台首頁
                return redirect('/admin/home');
            } else {
                // 前台已登入跳到首頁
                return redirect('/');
            }
        }

        return $next($request);
    }
}
