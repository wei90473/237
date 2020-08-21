<?php
namespace App\Repositories;

use App\Models\T01tb;//班別基本資料檔
use App\Models\T04tb;//開班資料檔
use App\Models\T08tb;
use App\Models\T09tb;//講座任課資料檔
use App\Models\T13tb;//班別學員資料檔
use App\Models\T47tb;//班別學員資料檔
use App\Models\M01tb;//講座基本資料檔
use App\Models\M02tb;//學員基本資料檔
use App\Models\M09tb;
use App\Models\M13tb;//機關基本資料檔
use App\Models\M16tb;
use App\Models\S01tb;
use Auth;
use DB;


class LibraryExportRepository
{
    

    //取得認證時數-匯出時數資料(regist1.csv)
    public function get_regist_sql($class,$term,$sdate,$edate)
    {   
        //t47tb:A t01tb:B t04tb:C t13tb:D m02tb:E t15tb:F
        $query=T47tb::select('t47tb.class','t47tb.term','t13tb.idno','m02tb.cname','t47tb.grade','t15tb.totscr','t01tb.classified','t13tb.status','t04tb.diploma',
                             't13tb.elearning','t01tb.elearnhr','t04tb.sdate','t04tb.edate','t01tb.trainday',DB::raw('(SELECT COUNT( * ) FROM t14tb WHERE class = t47tb.class AND term = t47tb.term AND idno = t13tb.idno) as sick'),
                             't01tb.classhr',DB::raw("(SELECT SUM( HOUR ) FROM t14tb WHERE class = t47tb.class AND term = t47tb.term AND idno = t13tb.idno ) as hour"),
                             't13tb.docno','t14tb.sdate as leave_sdate','t14tb.edate as leave_edate','t14tb.stime as leave_stime','t14tb.etime as leave_etime','t14tb.hour as leave_total_hour'
                             ,'t47tb.leave','t14tb.type');
        
        
        $query->join('t01tb','t01tb.class','=','t47tb.class');
        $query->join('t04tb',function($join){
            $join->on('t47tb.class','=','t04tb.class');
            $join->on('t47tb.term','=','t04tb.term');
        });

        $query->join('t13tb',function($join){
            $join->on('t47tb.class','=','t13tb.class');
            $join->on('t47tb.term','=','t13tb.term');
        });
        $query->leftJoin('m02tb','m02tb.idno','=','t13tb.idno');
        $query->leftJoin('t15tb',function($join){
            $join->on('t15tb.class','=','t47tb.class');
            $join->on('t15tb.term','=','t47tb.term');
            $join->on('t15tb.idno','=','t13tb.idno');
        });

        $query->leftJoin('t14tb',function($join){
            $join->on('t47tb.class','t14tb.class');
            $join->on('t47tb.term','t14tb.term');
            $join->on('t13tb.idno','t14tb.idno');
        });
        
        //if($control=='regist1'){
            $query->where('t01tb.upload1','Y');
        /*}else{
            $query->join('t06tb',function($join){
                $join->on('t06tb.class','=','t47tb.class');
                $join->on('t06tb.term','=','t47tb.term');
            });
            $query->where('t01tb.upload1','N');
        }*/
        
        $query->where('t13tb.authorize','Y')->where('t13tb.status','1');
        $query->where(DB::raw("not (t01tb.type='13' and t13tb.race in('2','3'))"));
        $query->whereRaw("((t04tb.edate between {$sdate} and {$edate}))");
        //$query->where('t47tb.class',$class)->where('t47tb.term',$term);
        
        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }

    public function get_csv_sql($sdate,$edate,$control)
    {
        if($control[0]=='class'){
            $query=T04tb::select("t04tb.class","t01tb.name","t01tb.branch");
        }else{
            $query=T04tb::select("t04tb.class","t13tb.idno","t01tb.name","m02tb.cname","m02tb.offtela1","m02tb.offtelb1","m02tb.offtelc1","m02tb.offaddr1","m02tb.offaddr2",
                                 "m02tb.homaddr1","m02tb.homaddr2","m02tb.mobiltel","m02tb.email","t04tb.sdate","t04tb.edate","t01tb.branch");
        }
        
        $query->join('t01tb','t01tb.class','=','t04tb.class');
        $query->join('t13tb','t13tb.class','=','t04tb.class');
        $query->join('m02tb','t13tb.idno','=','m02tb.idno');

        $query->whereRaw("((t04tb.sdate between {$sdate} and {$edate}) OR (t04tb.edate between {$sdate} and {$edate}))");
        
        if($control[1]=='taipei'){
            $query->where('t01tb.branch','1');
        }else{
            $query->where('t01tb.branch','2');
        }

        if($control[0]=='class'){
            $query->groupBy("t01tb.class");
        }

        
        
        $data=$query->get()->toArray();
        return $data;
                            
    }

    
   
}
