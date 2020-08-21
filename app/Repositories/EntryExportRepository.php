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


class EntryExportRepository
{

    public function select_class($class_info)
    {
        //dd($class_info);
        $query=T01tb::select('t04tb.class','t04tb.term','t01tb.name');
        $query->join('t04tb','t04tb.class','=','t01tb.class');
        $query->where('t01tb.upload1','Y');
        //$query->whereBetween('t04tb.sdate',array($class_info['final_sdate'],$class_info['final_edate']));
        $query->whereRaw("((t04tb.sdate between {$class_info['final_sdate']} and {$class_info['final_edate']}) OR (t04tb.edate between {$class_info['final_sdate']} and {$class_info['final_edate']}))");
        $query->whereExists(function ($query) {
            $query->select(DB::raw('*'))
                  ->from('t47tb')
                  ->whereRaw('t47tb.class = t04tb.class and t47tb.term=t04tb.term');
        });
        $query->orderBy('t04tb.class','asc')->orderBy('t04tb.term','asc');
        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }

    public function get_class_info($class_info)
    {

        $query=T47tb::select('t47tb.class','t47tb.term','t01tb.name','t47tb.degree','t01tb.category','t01tb.type','t47tb.enroll','t47tb.validdate','t47tb.county','t47tb.site',
                             't04tb.quota','t47tb.sdate','t47tb.edate','t01tb.special','t01tb.trainhour','t47tb.restriction','t47tb.lodging','t47tb.meal','t01tb.target',
                             't01tb.classified','t01tb.elearnhr','t01tb.classhr','t04tb.sdate','t04tb.edate','t01tb.trainday','t01tb.object','t04tb.fee');
        $query->join('t01tb','t01tb.class','=','t47tb.class');
        $query->join('t04tb','t04tb.class','=','t47tb.class');
        $query->where('t47tb.class',$class_info['class'])->where('t47tb.term',$class_info['term']);


        $query->groupBy('t47tb.class','t47tb.term');
        $data=$query->get();
        $data=$data->toArray();
        $data=$data[0];


        return $data;
    }

    public function get_teacher_info($class_info,$sdate,$edate)
    {
        $query=T08tb::select('m01tb.serno','t08tb.idno','m01tb.cname','m01tb.sex','m01tb.dept','m01tb.position','m01tb.offtela1','m01tb.offtelb1','m01tb.offtela2','m01tb.offtelb2'
                            ,'m01tb.major1','m01tb.major2','m01tb.major3','m01tb.major4','m01tb.major5','m01tb.major6','m01tb.major7','m01tb.major8','m01tb.major9','m01tb.major10'
                            ,'m01tb.email');

        $query->join('m01tb','t08tb.idno','=','m01tb.idno');
        $query->join('t04tb',function($join){
            $join->on('t08tb.class','=','t04tb.class');
            $join->on('t08tb.term','=','t04tb.term');
        });
        $query->join('t06tb','t04tb.class','=','t06tb.class');
        $query->join('t09tb','t09tb.class','=','t08tb.class');
        $query->where('t08tb.class',$class_info['class']);
        $query->where('t08tb.term',$class_info['term']);
        $query->where('t04tb.publish2','Y');
        $query->where('t09tb.type','1');
        $query->whereNotNull('t06tb.date');
        $query->whereRaw("((t04tb.sdate between {$sdate} and {$edate}) OR (t04tb.edate between {$sdate} and {$edate}))");
        $query->whereNotExists(function ($query) {
            $query->select(DB::raw('*'))
                  ->from('t09tb')
                  ->whereRaw("t09tb.class = t06tb.class and t09tb.term=t06tb.term and t09tb.course=t06tb.course");
        });
        $query->whereExists(function ($query) {
            $query->select(DB::raw('*'))
                  ->from('t01tb')
                  ->whereRaw("t01tb.class = t08tb.class and upload1='Y'");
        });
        $query->groupBy('t08tb.class');
        $query->groupBy('t08tb.term');
        $query->groupBy('serno');
        $query->groupBy('idno');
        $data=$query->get();
        $data=$data->toArray();
        for($i=0;$i<count($data);$i++){
            $data[$i]['special1']=$this->get_special_code($data[$i],1);
            $data[$i]['special2']=$this->get_special_code($data[$i],2);
            $data[$i]['special3']=$this->get_special_code($data[$i],3);
            $data[$i]['special4']=$this->get_special_code($data[$i],4);
            $data[$i]['special5']=$this->get_special_code($data[$i],5);
        }
        //dd($data);
        return $data;
    }

    public function get_special_code($data,$no)
    {
        $query=S01tb::select('category');
        $query->where('type','B');
        $query->whereIn('code',function($query)use($data,$no){
            $query->select('specialty')
            ->from('m16tb')
            ->where('idno', $data['idno'])
            ->where('no', $no);
        });
        $data=$query->get();
        $data=$data->toArray();
        if(!empty($data)){
            return $data[0]['category'];
        }
        return 0;
    }

    public function get_course_info($sdate,$edate)
    {
        $query=T04tb::select('t04tb.class','t06tb.course','t06tb.name','t04tb.term');
        $query->join('t06tb',function($join){
            $join->on('t06tb.class','=','t04tb.class');
            $join->on('t06tb.term','=','t04tb.term');
        });
        $query->where('t04tb.publish2','Y');
        $query->whereNotNull('t06tb.date');
        $query->whereRaw("((t04tb.sdate between {$sdate} and {$edate}) OR (t04tb.edate between {$sdate} and {$edate}))");

        $query->whereExists(function ($query) {
            $query->select(DB::raw('*'))
                  ->from('t01tb')
                  ->whereRaw("t01tb.class = t04tb.class and upload1='Y'");
        });
        $query->groupBy('t04tb.class','t06tb.course','t04tb.term');
        $query->orderBy('t04tb.class','asc');
        $query->orderBy('t04tb.term','asc');
        $query->orderBy('t06tb.course','asc');
        $data=$query->get();
        $data=$data->toArray();

        return $data;

    }

    //匯出記錄查詢
    public function search($sdate,$edate,$sponsor=null)
    {
        $query=T47tb::select('t01tb.name','t47tb.class','t47tb.term','t04tb.sdate','t04tb.edate','t04tb.publish2','t01tb.upload1','t47tb.upload2','t47tb.file1','t47tb.file2','t47tb.file3','t47tb.file5');
        $query->join('t04tb',function($join){
            $join->on('t47tb.class','=','t04tb.class');
            $join->on('t47tb.term','=','t04tb.term');
        });
        $query->join('t01tb','t01tb.class','=','t47tb.class');
        if(isset($sponsor)){
            $query->where('t04tb.sponsor',$sponsor);
            $query->whereRaw("((t04tb.edate between {$sdate} and {$edate}))");
        }else{
            $query->whereRaw("((t04tb.sdate between {$sdate} and {$edate}) OR (t04tb.edate between {$sdate} and {$edate}))");
        }

        $query->orderBy('t47tb.class','asc');
        $query->orderBy('t47tb.term','asc');

        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }

    public function lockClassInfo($lock_class)
    {
        $query=T47tb::select('t01tb.name','t47tb.class','t47tb.term','t04tb.sdate','t04tb.edate','t04tb.publish2','t01tb.upload1','t47tb.upload2','t47tb.file1','t47tb.file2','t47tb.file3','t47tb.file5');
        $query->join('t04tb',function($join){
            $join->on('t47tb.class','=','t04tb.class');
            $join->on('t47tb.term','=','t04tb.term');
        });
        $query->join('t01tb','t01tb.class','=','t47tb.class');
        $query->where('t47tb.class',$lock_class['class']);
        $query->where('t47tb.term',$lock_class['term']);

        $query->orderBy('t47tb.class','asc');
        $query->orderBy('t47tb.term','asc');

        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }

    //取得認證實數資料匯出-辦班人員
    public function getuser()
    {
        $query=M09tb::select('username','userid');
        $query->distinct();
        $query->orderByRaw('binary(convert(`username` using big5)  )');
        $data=$query->get();
        $data=$data->toArray();
        $result=[];
        for($i=0;$i<count($data);$i++){
            $result[$i]=[$data[$i]['userid']=>$data[$i]['username']];
        }

        return $result;
    }

    //取得認證時數-匯出時數資料(regist1.csv)
    public function get_regist_sql($class,$term,$sdate,$edate,$control)
    {
        //t47tb:A t01tb:B t04tb:C t13tb:D m02tb:E t15tb:F
        if($control=='regist1'){
            $query=T47tb::select('t47tb.class','t47tb.term','t13tb.idno','m02tb.cname','t47tb.grade','t15tb.totscr','t01tb.classified','t13tb.status','t04tb.diploma',
                             't13tb.elearning','t01tb.elearnhr','t04tb.sdate','t04tb.edate','t01tb.trainday',DB::raw('(SELECT COUNT( * ) FROM t14tb WHERE class = t47tb.class AND term = t47tb.term AND idno = t13tb.idno) as sick'),
                             't01tb.classhr',DB::raw("(SELECT SUM( HOUR ) FROM t14tb WHERE class = t47tb.class AND term = t47tb.term AND idno = t13tb.idno ) as hour"),
                             't13tb.docno','t14tb.sdate as leave_sdate','t14tb.edate as leave_edate','t14tb.stime as leave_stime','t14tb.etime as leave_etime','t14tb.hour as leave_total_hour'
                             ,'t47tb.leave','t14tb.type');
        }else{
            $query=T47tb::select('t47tb.class','t47tb.term','t13tb.idno','m02tb.cname','t47tb.grade','t15tb.totscr','t01tb.classified','t13tb.status','t04tb.diploma',
                             't13tb.elearning','t01tb.elearnhr','t04tb.sdate','t04tb.edate','t01tb.trainday',DB::raw('(SELECT COUNT( * ) FROM t14tb WHERE class = t47tb.class AND term = t47tb.term AND idno = t13tb.idno) as sick'),
                             't01tb.classhr',DB::raw("(SELECT SUM( HOUR ) FROM t14tb WHERE class = t47tb.class AND term = t47tb.term AND idno = t13tb.idno ) as hour"),
                             't13tb.docno','t06tb.course','t06tb.name','t06tb.hour as course_hour','t06tb.date','t06tb.stime','t06tb.etime','t47tb.degree','t01tb.category',
                             't47tb.county','t14tb.sdate as leave_sdate','t14tb.edate as leave_edate','t14tb.stime as leave_stime','t14tb.etime as leave_etime','t14tb.hour as leave_total_hour'
                             ,'t47tb.leave','t14tb.type');
        }

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

        if($control=='regist1'){
            $query->where('t01tb.upload1','Y');
        }else{
            $query->join('t06tb',function($join){
                $join->on('t06tb.class','=','t47tb.class');
                $join->on('t06tb.term','=','t47tb.term');
            });
            $query->where('t01tb.upload1','N');
        }

        $query->where('t13tb.authorize','Y');
        $query->where(DB::raw("not (t01tb.type='13' and t13tb.race in('2','3'))"));
        $query->whereRaw("((t04tb.edate between {$sdate} and {$edate}))");
        $query->where('t47tb.class',$class)->where('t47tb.term',$term);

        $data=$query->get();
        $data=$data->toArray();
        return $data;
    }



}
