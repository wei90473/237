<?php

namespace App\Http\Middleware;

use Closure;
use Auth;


class Active
{
    /**
     * 檢查是否被停用
     *
     * @param $request
     * @param Closure $next
     * @param $permission
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        $active = Auth::guard('managers')->user()->active;

        if ( ! $active) {
            // 已被停用,登出
            return redirect('/admin/logout');
        }

        return $next($request);
    }
}
