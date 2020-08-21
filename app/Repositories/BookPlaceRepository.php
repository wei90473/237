<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\M14tb;
use App\Models\Edu_classroom;
use App\Models\T22tb;
use App\Models\T97tb;
use App\Models\S02tb;
use DB;
use Auth;

class BookPlaceRepository extends Repository
{
    public function getPlace($type=null){   
        if($type==1){
            $query = M14tb::select( DB::raw('site,name,"1" as branch') ) ;
        }elseif($type==2){
            $query = Edu_classroom::select( DB::raw('roomno as site, roomname as name ,"2" as branch') );
        }else{
            $first = M14tb::select( DB::raw('site,name,"1" as branch') );
            $query = Edu_classroom::select( DB::raw('roomno as site, roomname as name ,"2" as branch') );
            $query->union($first);
        }
        
        $data = $query->orderby('branch')->get()->toArray();
        return $data;
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;

        // $sql = "SELECT  X.class, RTRIM(X.name) AS `name` ,X.term,X.sort
        //         FROM ( 
        //         SELECT A.class, A.`name`,B.term, '0' AS sort 
        //         FROM t01tb A 
        //         INNER JOIN t04tb as B ON A.class=B.class
        //         INNER JOIN m09tb as C ON B.sponsor=C.userid
        //         WHERE UPPER(B.sponsor)='".$uesr."'
        //         AND  A.type<>'13' 
        //         GROUP BY A.class,A.`name`
        //         UNION ALL
        //         SELECT class,`name`,'' AS term ,'1' AS sort FROM t01tb  WHERE type <> '13' 
        //                 UNION ALL
        //                 SELECT meet as class ,`name`, serno as term,'2' AS sort FROM t38tb WHERE  meet not like 'I%'
                        
        //                 ) as X 
        //         ORDER BY X.sort ASC,X.class DESC";
        $sql = "SELECT A.class, A.`name`,B.term, '0' AS sort 
                FROM t01tb A 
                INNER JOIN t04tb as B ON A.class=B.class
                INNER JOIN m09tb as C ON B.sponsor=C.userid
                WHERE UPPER(B.sponsor)='".$uesr."'
                GROUP BY A.class,A.`name`
                ORDER BY sort ASC,class DESC";        
        return DB::select($sql);
    }
    /**
     * 取得會議列表
     *
     * @return mixed
     */
    public function getMeetList()
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;
        $sql = "SELECT meet as class ,`name`, serno as term FROM t38tb WHERE  meet not like 'I%' AND sponsor ='".$uesr."' group by class order by class DESC" ;
        return DB::select($sql);
    }
    // 場地預約資料
    public function getplacedata($condition,$classroom=NULL)    {
        if($condition['branch']=='1'){
            $query=T22tb::select("*");
            $query->join("m14tb","m14tb.site","t22tb.site");

            if(isset($condition['sdate']) && isset($condition['edate']) ){
                $sdate=str_replace("/","",$condition["sdate"]);
                $edate=str_replace("/","",$condition["edate"]);
                $query->whereBetween("date",array($sdate,$edate));
            }

            if(!is_null($classroom) && $classroom!=''){
                $query->where("t22tb.site",$classroom);
            }
            
            if(isset($condition['class']) && $condition['class'] ){
                $query->where('t22tb.class',$condition['class']);
            }
            
            if(isset($condition['term']) && $condition['term']){
                $query->where('t22tb.term',$condition['term']);
            }

            $query->orderBy('t22tb.date','DESC')->orderBy('t22tb.time');
        }else{
            $query=T97tb::select(DB::raw("t97tb.site,t97tb.date,t97tb.stime,t97tb.etime,t97tb.time,t97tb.class,t97tb.term,edu_classroom.roomname as name")
            );
            $query->join("edu_classroom","edu_classroom.roomno","t97tb.site");

            if(isset($condition['sdate']) && isset($condition['edate']) ){
                $sdate=str_replace("/","",$condition["sdate"]);
                $edate=str_replace("/","",$condition["edate"]);
                $query->whereBetween("date",array($sdate,$edate));
            }

            if(!is_null($classroom)&& $classroom!=''){
                $query->where("t97tb.site",$classroom);
            }
            
            if(isset($condition['class']) && $condition['class'] ){
                $query->where('t97tb.class',$condition['class']);
            }
            
            if(isset($condition['term']) && $condition['term']){
                $query->where('t97tb.term',$condition['term']);
            }

            $query->orderBy('t97tb.date','DESC')->orderBy('t97tb.time');
        }
        
        $data = $query->get()->toArray();
        // var_dump($data);exit();
        return $data;
    }
    public function getT97tb($condition,$classroom=NULL)    {
        
        $query=T97tb::select(DB::raw('t97tb.site,t97tb.stime,t97tb.date,t97tb.etime,t97tb.time,t97tb.class,t97tb.term,edu_classroom.roomname as name'));
        $query->join("edu_classroom","edu_classroom.roomno","t97tb.site");

        if(isset($condition['sdate']) && isset($condition['edate']) ){
            $sdate=str_replace("/","",$condition["sdate"]);
            $edate=str_replace("/","",$condition["edate"]);
            $query->whereBetween("date",array($sdate,$edate));
        }

        if(!is_null($classroom)){
            $query->where("t97tb.site",$classroom);
        }
        
        if(isset($condition['class']) && $condition['class'] ){
            $query->where('t97tb.class',$condition['class']);
        }
        
        if(isset($condition['term']) && $condition['term']){
            $query->where('t97tb.term',$condition['term']);
        }

        $query->orderBy('t97tb.date','DESC')->orderBy('t97tb.time');
        $data = $query->get()->toArray();
        return $data;
    }

    public function getclassroom($sdate,$edate,$branch){
        $sdate=str_replace("/","",$sdate);
        $edate=str_replace("/","",$edate);
        if($branch=='1'){
            // $query=T22tb::select('site')->whereBetween("date",array($sdate,$edate))->groupby('site');
            $query=T22tb::select('site')->groupby('site');
        }else{
            // $query=T97tb::select('site')->whereBetween("date",array($sdate,$edate))->groupby('site');
            $query=T97tb::select('site')->groupby('site');
        }
        $data = $query->get()->toArray();
        return $data;
    }

    public function getS02tb()
    {
        $query=S02tb::select("*");
        $data=$query->get();
        $data=$data->toArray();
        return $data[0];
    }

    public function insertPlace($post)
    {
        unset($post["setweek"]);
        $date=$this->convertYear($post["date"],'twtovids');
        
        
        $post["date"]=substr($post["date"],0,3).substr($post["date"],4,2).substr($post["date"],7,2);
        
        
        if($post['branch']=='1'){
            $DB= 't22tb';
            $result=$this->getRequestandAffirm($date);
            $post["request"]=$result[1];
            $post["affirm"]=$result[0];
            if($post["site"]==404 || $post["site"]==405){
                $post["status"]='Y';
            }else{
                $post["status"]='N';
            }
            $post["reserve"]=Auth::user()->userid;
        $post['upddate']=date('Y-m-d H:i:s');
        }else{
            $DB= 't97tb';
            unset($post['branch'],$post['type']);
        }
        unset($post['branch'],$post['type']);
        switch ($post["time"]) {
                case 'A':
                    $post["stime"]='0830';
                    $post["etime"]='1200';
                    $result=DB::table($DB)->insert($post);
                    break;
                case 'B':
                    $post["stime"]='1300';
                    $post["etime"]='1630';
                    $result=DB::table($DB)->insert($post);
                    break;
                case 'C':
                    $post["stime"]='1800';
                    $post["etime"]='2130';
                    $result=DB::table($DB)->insert($post);
                    break;
                case 'E':
                    $post["stime"]='1630';
                    $post["etime"]='1930';
                    $result=DB::table($DB)->insert($post);
                    break;
                case 'F':
                    $post["stime"]='2000';
                    $post["etime"]='2300';
                    $result=DB::table($DB)->insert($post);
                    break;
                case 'G':
                    $post_A=$post;
                    $post_B=$post;
                    $post_A['time']='A';
                    $post_A["stime"]='0830';
                    $post_A["etime"]='1200';

                    $post_B['time']='B';
                    $post_B["stime"]='1300';
                    $post_B["etime"]='1630';
                    DB::table($DB)->insert($post_A);
                    $result=DB::table($DB)->insert($post_B);

                    break;
                case 'H':
                    $post_A=$post;
                    $post_B=$post;
                    $post_C=$post;
                    $post_A['time']='A';
                    $post_A["stime"]='0830';
                    $post_A["etime"]='1200';

                    $post_B['time']='B';
                    $post_B["stime"]='1300';
                    $post_B["etime"]='1630';

                    $post_C['time']='C';
                    $post_C["stime"]='1630';
                    $post_C["etime"]='1930';
                    DB::table($DB)->insert($post_A);
                    DB::table($DB)->insert($post_B);
                    $result=DB::table($DB)->insert($post_C);
                    break;
                default:
                    $result=DB::table($DB)->insert($post);
        }
    }

    public function insertT37tb($post,$i)
    {
        unset($post["setweek"]);
        $date=$this->convertYear($post["date"],'twtovids');
        $result=$this->getRequestandAffirm($date);
        $post["request"]=$result[1];
        //$post["affirm"]=$result[0];
        
        $post["date"]=substr($post["date"],0,3).substr($post["date"],4,2).substr($post["date"],7,2);
        $post["reserve"]=Auth::user()->userid;
        $post['upddate']=date('Y-m-d H:i:s');
        if($post["site"]==404 || $post["site"]==405){
            $post["status"]='Y';
        }else{
            $post["status"]='N';
        }
        $post['type']=$i;
        unset($post['usertype']);
        unset($post['upddate']);
        unset($post['status'],$post['branch']);

        switch ($post["time"]) {
                case 'A':
                    $post["stime"]='0830';
                    $post["etime"]='1200';
                    $result=DB::table("t37tb")->insert($post);
                    break;
                case 'B':
                    $post["stime"]='1300';
                    $post["etime"]='1630';
                    $result=DB::table("t37tb")->insert($post);
                    break;
                case 'C':
                    $post["stime"]='1800';
                    $post["etime"]='2130';
                    $result=DB::table("t37tb")->insert($post);
                    break;
                case 'E':
                    $post["stime"]='1630';
                    $post["etime"]='1930';
                    $result=DB::table("t37tb")->insert($post);
                    break;
                case 'F':
                    $post["stime"]='2000';
                    $post["etime"]='2300';
                    $result=DB::table("t37tb")->insert($post);
                    break;
                case 'G':
                    $post_A=$post;
                    $post_B=$post;
                    $post_A['time']='A';
                    $post_A["stime"]='0830';
                    $post_A["etime"]='1200';

                    $post_B['time']='B';
                    $post_B["stime"]='1300';
                    $post_B["etime"]='1630';
                    DB::table("t37tb")->insert($post_A);
                    $result=DB::table("t37tb")->insert($post_B);

                    break;
                case 'H':
                    $post_A=$post;
                    $post_B=$post;
                    $post_C=$post;
                    $post_A['time']='A';
                    $post_A["stime"]='0830';
                    $post_A["etime"]='1200';

                    $post_B['time']='B';
                    $post_B["stime"]='1300';
                    $post_B["etime"]='1630';

                    $post_C['time']='C';
                    $post_C["stime"]='1800';
                    $post_C["etime"]='2130';
                    DB::table("t37tb")->insert($post_A);
                    DB::table("t37tb")->insert($post_B);
                    $result=DB::table("t37tb")->insert($post_C);
                    break;
                default:
                    $result=DB::table("t37tb")->insert($post);
        }
    }

    /*public function checkDateRange($post)
    {
        $data=DB::table('t22tb')->where('site',$post['site'])->where('date',$post['date'])->where('stime',$post['stime'])->where('etime',$post['etime'])->first();
        var_dump($data);
        $now=date('Y/m/d');
        $now=$this->convertYear($now,'vidstotw');
        if($data->request>=$now){
            $type=1;
        }else if($data->affirm>=$now){
            $type=2;
        }else{
            $type=3;
        }
        return $type;
        dd($post);
        
    }*/

    public function updatePlace($update)
    {
        $date=$this->convertYear($update["date"],'twtovids');
        $result=$this->getRequestandAffirm($date);
        $update["request"]=$result[1];
        $update["affirm"]=$result[0];
        $origin_date=$update["origin_date"];
        $origin_time=$update["origin_time"];
        unset($update["origin_date"]);
        unset($update["origin_time"]);
        $update["date"]=substr($update["date"],0,3).substr($update["date"],4,2).substr($update["date"],7,2);
        $update["reserve"]=Auth::user()->userid;
        $update['upddate']=date('Y-m-d H:i:s');
        // 院區
        if($update['branch']=='1'){
            $DB= 't22tb';
        }else{
            $DB= 't97tb';
            unset($update['request'],$update['affirm']);
        }
        unset($update['branch'],$update['type']);
        switch ($update["time"]) {
                case 'A':
                    $update["stime"]='0830';
                    $update["etime"]='1200';
                    $result=DB::table($DB)->where("site",$update["site"])->where("date",$origin_date)->where("time",$origin_time)->update($update);
                    break;
                case 'B':
                    $update["stime"]='1300';
                    $update["etime"]='1630';
                    $result=DB::table($DB)->where("site",$update["site"])->where("date",$origin_date)->where("time",$origin_time)->update($update);
                    break;
                case 'C':
                    $update["stime"]='1800';
                    $update["etime"]='2130';
                    $result=DB::table($DB)->where("site",$update["site"])->where("date",$origin_date)->where("time",$origin_time)->update($update);
                    break;
                case 'E':
                    $update["stime"]='1630';
                    $update["etime"]='1930';
                    $result=DB::table($DB)->where("site",$update["site"])->where("date",$origin_date)->where("time",$origin_time)->update($update);
                    break;
                case 'F':
                    $update["stime"]='2000';
                    $update["etime"]='2300';
                    $result=DB::table($DB)->where("site",$update["site"])->where("date",$origin_date)->where("time",$origin_time)->update($update);
                    break;
                /*case 'G':
                    $post_A=$update;
                    $post_B=$update;
                    $post_A['time']='A';
                    $post_A["stime"]='0830';
                    $post_A["etime"]='1200';

                    $post_B['time']='B';
                    $post_B["stime"]='1300';
                    $post_B["etime"]='1630';

                    DB::table("t22tb")->where("site",$post_A["site"])->where("date",$origin_date)->where("time",$origin_time)->update($post_A);
                    $result=DB::table("t22tb")->where("site",$post_B["site"])->where("date",$origin_date)->where("time",$origin_time)->update($post_B);

                    break;
                case 'H':
                    $post_A=$update;
                    $post_B=$update;
                    $post_C=$update;
                    $post_A['time']='A';
                    $post_A["stime"]='0830';
                    $post_A["etime"]='1200';

                    $post_B['time']='B';
                    $post_B["stime"]='1300';
                    $post_B["etime"]='1630';

                    $post_C['time']='C';
                    $post_C["stime"]='1630';
                    $post_C["etime"]='1930';

                    DB::table("t22tb")->where("site",$post_A["site"])->where("date",$origin_date)->where("time",$origin_time)->update($post_A);
                    DB::table("t22tb")->where("site",$post_B["site"])->where("date",$origin_date)->where("time",$origin_time)->update($post_B);
                    $result=DB::table("t22tb")->where("site",$post_C["site"])->where("date",$origin_date)->where("time",$origin_time)->update($post_C);
                    break;*/
                default:
                    $result=DB::table($DB)->where("site",$update["site"])->where("date",$origin_date)->where("time",$origin_time)->update($update);
            }
    }

    //取得request 和 affirm
    public function getRequestandAffirm($date)
    {   
        $last_week=date('Y/m/d', strtotime($date.'-7 days'));//申請日的上週
        $e=date("w",strtotime($last_week));
        $sys=$this->getS02tb();
        $diff=$e-$sys["weekly"]+1;
        $affirm=date('Y/m/d', strtotime($last_week."-{$diff} days"));
        $affirm=$this->convertYear($affirm,'vidstotw');
        
        $last_month=date('Y/m/d', strtotime($date.'-1 month'));
        $firstday = date('Y/m/01', strtotime($last_month));
        $diff_req=$sys["monthly"]-1;
        $reques=date('Y/m/d', strtotime($firstday."+{$diff_req} days"));
        $reques=$this->convertYear($reques,'vidstotw');
        
        return [$affirm,$reques];
    }

    //西元民國互轉
    public function convertYear($year,$mode)
    {
        if($mode=='twtovids'){
            $result=(int)substr($year,0,3)+1911;
            $result=$result.substr($year,3,6);
        }
        if($mode=='vidstotw'){
            $result=(int)substr($year,0,4)-1911;
            $result=$result.substr($year,5,2).substr($year,8,2);
        }
        return $result;
    }

    public function getInfo($arr)
    {
        if($arr["branch"]=='1'){
            $DB='t22tb';
        }else{
            $DB='t97tb';
        }
        switch ($arr["time"]) {
                case 'A':
                    $arr["stime"]='0830';
                    $arr["etime"]='1200';
                    break;
                case 'B':
                    $arr["stime"]='1300';
                    $arr["etime"]='1630';
                    break;
                case 'C':
                    $arr["stime"]='1800';
                    $arr["etime"]='2130';
                    break;
                case 'E':
                    $arr["stime"]='1630';
                    $arr["etime"]='1930';
                    break;
                case 'F':
                    $arr["stime"]='2000';
                    $arr["etime"]='2300';
                    break;
                default:
        }
        $info=DB::table($DB)->where("site",$arr["site"])->where("date",$arr["date"])->where("stime",$arr["stime"])->where("etime",$arr["etime"])->where("time",$arr["time"])->where("class",$arr["class"])->where("term",$arr["term"])->first();
        return $info;
    }

    public function batchUpdate($condition,$branch)
    {
        $change_condition1=explode("_",$condition[0]);
        $change_condition2=explode("_",$condition[1]);
        $DB = ($branch=='1')?'t22tb':'t97tb';
        //抓取舊資料
        $item1=DB::table($DB)->where("date",$change_condition1[0])->where("site",$change_condition1[1])->where("stime",$change_condition1[2])->where("etime",$change_condition1[3])->first();
        $item2=DB::table($DB)->where("date",$change_condition2[0])->where("site",$change_condition2[1])->where("stime",$change_condition2[2])->where("etime",$change_condition2[3])->first(); 
        // if(isset($item1->affirm) && $item1->affirm < (date('Y', strtotime('now'))-1911).date('md', strtotime('now')) ){
        //     return back()->with('result', '1')->with('message', '場地日期已過確認凍結日期，無法修改');    
        // }
        // if(isset($item2->affirm) && $item2->affirm < (date('Y', strtotime('now'))-1911).date('md', strtotime('now')) ){
        //     return back()->with('result', '1')->with('message', '場地日期已過確認凍結日期，無法修改');    
        // }
        
        $update1=['class'=>$item2->class,"term"=>$item2->term];
        $update2=['class'=>$item1->class,"term"=>$item1->term];
        
        DB::beginTransaction();
        try{
            DB::table($DB)->where("date",$item1->date)->where("site",$item1->site)->where("stime",$item1->stime)->where("etime",$item1->etime)->update($update1);
            DB::table($DB)->where("date",$item2->date)->where("site",$item2->site)->where("stime",$item2->stime)->where("etime",$item2->etime)->update($update2);
            DB::commit();                    
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '新增失敗，請稍後再試!'); 
        }
            //** oldcode**//
            // $item1=DB::table($DB1)->where("date",$change_condition1[0])->where("site",$change_condition1[1])->where("stime",$change_condition1[2])->where("etime",$change_condition1[3])->first();
            // $item2=DB::table($DB2)->where("date",$change_condition2[0])->where("site",$change_condition2[1])->where("stime",$change_condition2[2])->where("etime",$change_condition2[3])->first();        
            
            
    }
    


}