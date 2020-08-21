<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\OperationLog;
use App\Models\T35tb;
use Session;
use DB;
class QueryListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        // if('172.16.10.100'==Session::get('admin_ip')){
            // Escape Query
            $sql = $event->sql;
            // Binding Data
            $bindings = $event->bindings;
            // Spend Time
            $time = $event->time;

            // 針對 Binding 資料進行格式的處理
            // 例如字串就加上引號
            foreach ($bindings as $index => $binding) {
                if (is_bool($binding)) {
                    $bindings[$index] = ($binding) ? ('1') : ('0');
                } elseif (is_string($binding)) {
                    $bindings[$index] = "'$binding'";
                }
            }

            // 依據將 ? 取代成 Binding Data
            $sql = preg_replace_callback('/\?/', function () use (&$bindings) {
                return array_shift($bindings);
            }, $sql);
            $before_data = '';//修改前的資料轉成JSON存
            if('select' == substr($sql , 0 , 6)){
                $type = 'R';//查詢
            }else if('update' == substr($sql , 0 , 6)){
                $type = 'U';//修改
               
            }else if('delete' == substr($sql , 0 , 6)){
                $type = 'D';//刪除
                $before_arr  = explode(" ",$sql);
                $before_sql  = 'select * ';
                foreach($before_arr as $value){
                    if($value!='delete'){
                        $before_sql .= ($value.' ');
                    }
                }

                $result      = DB::select($before_sql);
                // dd($result);
                $before_data = json_encode($result);//修改前的資料轉成JSON存
                $uid          = Session::get('admin_uid');
                $admin_path   = Session::get('admin_path');
                $admin_method = Session::get('admin_method');
                $admin_input  = Session::get('admin_input');
                $admin_ip     = Session::get('admin_ip');
    
                $OperationLog = new OperationLog();
                $OperationLog->uid    = $uid;
                // $OperationLog->sql    = $log;
                $OperationLog->ip     = $admin_ip;
                $OperationLog->path   = $admin_path;
                $OperationLog->method = $admin_method;
                $OperationLog->input  =  $admin_input;
                $OperationLog->after_sql = $sql; //修改後的SQL
                $OperationLog->before_data = $before_data; //修改前的資料
                
                   
               $OperationLog->save();
    
                // dd( $OperationLog);
            }else if('insert' == substr($sql , 0 , 6)){
                $type = 'I';//新增
            }
    
        // }
            



        // $sql = str_replace("?", "'%s'", $event->sql);
        // $log = vsprintf($sql, $event->bindings);
        // Log::info($log);





        // $user_data = \Auth::user();
        // dd( $user_data);
        // // logdate	    異動日期
        // // logtime	    異動時間
        // // userid	    使用者帳號
        // // progid	    程式代號
        // // type	        異動類別  I:新增 U:修改 D:刪除 B:批次作業 R:查詢
        // // logtable	    異動主資料表
        // // content	    異動內容
        
        // $content = '';
        // $rule = array('progid'=>'new','logtable'=>'Edu_classdemand');
        // $logarray = $this->getlog('I',$content,$rule);
        // T35tb::create($logarray);


    //     //  dd($event);
            // $uid          = Session::get('admin_uid');
            // $admin_path   = Session::get('admin_path');
            // $admin_method = Session::get('admin_method');
            // $admin_input  = Session::get('admin_input');
            // $admin_ip     = Session::get('admin_ip');
            // $sql = str_replace("?", "'%s'", $event->sql);
            // $sql = vsprintf($sql, $event->bindings);
            // $type  = '';
            // $before_data = '';//修改前的資料

            // if('172.16.10.100'==Session::get('admin_ip')){
            //     if('select' == substr($sql , 0 , 6)){
            //         $type = 'R';//查詢
            //     }else if('update' == substr($sql , 0 , 6)){
            //         $type = 'U';//修改
            //         $before_data = '';//修改前的資料轉成JSON存
            //     }else if('delete' == substr($sql , 0 , 6)){
            //         $type = 'D';//刪除
            //         $before_arr  = explode(" ",$sql);
            //         $before_sql  = 'select * ';
            //         foreach($before_arr as $value){
            //             if($value!='delete'){
            //                 $before_sql .= ($value.' ');
            //             }
            //         }
            //         $result = DB::select($before_sql);
            //         // dd($result);
            //         // die($before_sql);
               

            //         $before_data = json_encode($result);//修改前的資料轉成JSON存
            //     }else if('insert' == substr($sql , 0 , 6)){
            //         $type = 'I';//新增
            //     }
            //     $OperationLog = new OperationLog();
            //     $OperationLog->uid    = $uid;
            //     // $OperationLog->sql    = $log;
            //     $OperationLog->ip     = $admin_ip;
            //     $OperationLog->path   = $admin_path;
            //     $OperationLog->method = $admin_method;
            //     $OperationLog->input  =  $admin_input;
            //     $OperationLog->after_sql = $sql; //修改後的SQL
            //     $OperationLog->before_data = $before_data; //修改前的資料
               
            // //    $OperationLog->save();
            // }
            
    //     $uid          = Session::get('admin_uid');
    //     $admin_path   = Session::get('admin_path');
    //     $admin_method = Session::get('admin_method');
    //     $admin_input  = Session::get('admin_input');
    //     $admin_ip     = Session::get('admin_ip');
    //         // dd($admin_path);
    //     }
        
    //     //注意以下問題vsprintf(): Too few arguments
    //     // $log = vsprintf($sql, $event->bindings);
    //     $log = $sql;
    //     // $uid          = isset($_SERVER['admin_uid']) ? $_SERVER['admin_uid'] : 0;
    //     // $admin_path   = isset($_SERVER['admin_path']) ? $_SERVER['admin_path'] : 0;
    //     // $admin_method = isset($_SERVER['admin_method']) ? $_SERVER['admin_method'] : 0;
    //     // $admin_input  = isset($_SERVER['admin_input']) ? $_SERVER['admin_input'] : 0;
    //     // $admin_ip     = isset($_SERVER['admin_ip']) ? $_SERVER['admin_ip'] : 0;


    // //     // if('select' != substr($log , 0 , 6)){
    //         if(!strstr($log ,'operation_log')){
    //             // dd($log);
    //             $OperationLog = new OperationLog();
    //             $OperationLog->uid    = $uid;
    //             $OperationLog->sql    = $log;
    //             $OperationLog->ip     = $admin_ip;
    //             $OperationLog->path   = $admin_path;
    //             $OperationLog->method = $admin_method;
    //             $OperationLog->input  =  $admin_input;
    //            // $OperationLog->save();
    //         }
    //     // }
    }

    
    private function getlog($type,$content=NULL,$rule=[]){
        $logarray['logdate'] = (date('Y')-1911).date('md');
        $ns = substr(microtime(),2,4);
        $logarray['logtime'] = date('H:i:s:').$ns;
        $logarray['userid'] = Auth::guard('managers')->user()->userid;
        $logarray['progid'] = isset($rule['progid'])? $rule['progid']:'CSDI6030';
        $logarray['type'] = $type;
        $logarray['logtable'] = isset($rule['logtable'])? $rule['logtable']:'t23tb';
        if(!is_null($content)){
            $logarray['content'] = $content;
        }
        return $logarray;
    }
}
