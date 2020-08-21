<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OperationLog;
use DB;
use Session;

class AdminOperationLog
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
        $response = $next($request); 
        // // 啟用 Query Log 功能
        // DB::connection()->enableQueryLog();            
        // // 取得資料庫查詢的 Qeury Log
        // $queries = DB::getQueryLog();

        $user_id = 0;
        
        if(Auth::check()) {
            $user_id = (int) Auth::id();       //取得操作者
        }

        $input = $request->all();   
        Session::put('admin_uid', $user_id);
        Session::put('admin_path',$request->path());
        Session::put('admin_method',  $request->method());
        Session::put('admin_ip', $request->ip());
        Session::put('admin_input', json_encode($input, JSON_UNESCAPED_UNICODE));




        // if('GET' != $request->method()){ 
            
            $sql   = '';
            // if (!empty($queries)) {
            //     foreach ($queries as &$query) {
            //         $sql   =  vsprintf(str_replace('?', '%s', $query['query']), $query['bindings']);
            //     }
            // }
          
              // // $log          = new OperationLog(); #紀錄操作的model
            // $log->uid     = $user_id;
            // $log->path    = $request->path();
            // $log->method  = $request->method();
            // $log->ip      = $request->ip();
            // $log->sql     = $sql;
            // $log->input   = json_encode($input, JSON_UNESCAPED_UNICODE);
            // // $log->save();   
        // }
        return $response;
    }
}