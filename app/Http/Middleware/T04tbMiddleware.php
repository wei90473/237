<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\T04tb;

class T04tbMiddleware
{
    /**
     * 執行請求過濾器。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $httpMethods = $request->route()->getMethods();

        $t04tbKey['class'] = $request->route()->parameter('class');
        $t04tbKey['term'] = $request->route()->parameter('term');            
        $t04tb = T04tb::where($t04tbKey)->first();
        
        if (empty($t04tb)){
            return redirect('/admin/home')->with('result', 0)->with('message', '該班期不存在');
        }

        $request->merge(['t04tb' => $t04tb]);
        return $next($request);
    }

}