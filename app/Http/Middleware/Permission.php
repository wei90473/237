<?php

namespace App\Http\Middleware;

use Closure;
use Auth;


class Permission
{
    /**
     * 權限檢查
     *
     * @param $request
     * @param Closure $next
     * @param $permission
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        // 檢查權限..

        return $next($request);
    }
}
