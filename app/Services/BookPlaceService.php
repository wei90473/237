<?php
namespace App\Services;

use App\Repositories\BookPlaceRepository;
use DB;
use App\Models\M14tb;
use App\Models\Edu_holiday;
use App\Models\Edu_classroom;
use Auth;

class BookPlaceService
{  
    public function __construct(BookPlaceRepository $bookplacerepository)
    {
        $this->bpr = $bookplacerepository;
    }

    public function getPlace($type=null)
    {
        return $this->bpr->getPlace($type);
    }
    /*public function getT22tb($condition)
    {
        return $this->bpr->getT22tb($condition);
    }*/

    public function getlist($condition)
    {
        $s=str_replace("/","-",$condition["sdate"]);
        $s_y=(int)substr($s,0,3)+1911;
        $s=$s_y.substr($s,3,3).substr($s,6,3);
        $s=strtotime($s);
        //計算假日
        $holiday = Edu_holiday::whereBetween('holiday',array($condition["sdate"],$condition["edate"]))->get()->toarray();
        $holiday_arr = array();
        foreach ($holiday as  $value) {
            $holiday_arr[] = $value['holiday'];
        }
        //算天數有幾天
        $diff_time=(int)str_replace("/","",$condition["edate"])-(int)str_replace("/","",$condition["sdate"])+1;

        //以classroom當作array的index
        $result=[];
        if(!isset($condition["site"])){ //全部列出
            $classroom = $this->bpr->getclassroom($condition["sdate"],$condition["edate"],$condition["branch"]);
            
            foreach ($classroom as $key => $value) {
                $classroom[$key] = $value['site'];
            }

        }else{
            $classroom = explode(",",$condition["site"]);
        }

        for($i=0;$i<count($classroom);$i++){
            $placedata = $this->bpr->getplacedata($condition,$classroom[$i]);
            if(empty($placedata)){
                if($condition['branch']=='1'){
                    $result[$classroom[$i]] = M14tb::select('site','name')->where("site",$classroom[$i])->get()->toarray();
                }else{
                    $result[$classroom[$i]] = Edu_classroom::select(DB::RAW('roomno as site,roomname as name'))->where("roomno",$classroom[$i])->get()->toarray();
                }
            }else{
                $result[$classroom[$i]] = $placedata;
            }
        }
        // dd($result);
        // if($condition["branch"]=='1'){
        //     for($i=0;$i<count($classroom);$i++){
        //         $result[$classroom[$i]]=$this->bpr->getT22tb($condition,$classroom[$i]);
        //     }
        // }else{
        //     for($i=0;$i<count($classroom);$i++){
        //         $result[$classroom[$i]]=$this->bpr->getT97tb($condition,$classroom[$i]);
        //     }
        // }
        //將所有的天數放到一個array
        $time_result=[];
        for($i=0;$i<$diff_time;$i++){
            $time=date("Ymd",($s+($i*60*60*24)));
            $temp_y=substr($time,0,4)-1911;
            $time_result[$i]=$temp_y.substr($time,4,2).substr($time,6,2);
        }

        $final_result=$this->adjustArray($result,$time_result,$holiday_arr);
        // dd($final_result);
        return $final_result;
    }

    //將$result[$classroom[$i]]裡面的內容轉變為
    /*
    array (size=4)
        classroom => 
            array (size)
                date => 
                    array (size=3)
                        'A' => 訂時段A教室資訊
                        'B' => 訂時段B教室資訊
                        'C' => 訂時段C教室資訊
    */ 
    public function adjustArray($result,$time_result,$holiday_arr=array())
    {
        $final_result=[];
        $i=0;
        
        foreach($result as $key => $row){
            // if(empty($row)){
            //     $final_result[$key] = array();
            //     continue;
            // }
            // $A=0;$B=0;$C=0;
            foreach($row as $temp){
                $final_result[$key]['name'] = isset($temp["name"])?$temp["name"]:'';
                for($z=0;$z<count($time_result);$z++){
                    $final_result[$key][$time_result[$z]]["is_holiday"] = (in_array($time_result[$z], $holiday_arr))? 1:0;
                    if( isset($temp["date"])&& $temp["date"]==$time_result[$z] ){
                        if($temp["time"]=='A'){
                            $final_result[$key][$time_result[$z]]["A"]=$temp;
                        }else if($temp["time"]=='B'){
                            $final_result[$key][$time_result[$z]]["B"]=$temp;
                        }else if($temp["time"]=='C'){
                            $final_result[$key][$time_result[$z]]["C"]=$temp;
                        }else if($temp["time"]=='E'){
                            $final_result[$key][$time_result[$z]]["B"]=$temp;
                            $final_result[$key][$time_result[$z]]["C"]=$temp;
                        }else if($temp["time"]=='F'){
                            $final_result[$key][$time_result[$z]]["C"]=$temp;
                        }else{

                            $stime=$this->hourtomin($temp["stime"]);
                            $etime=$this->hourtomin($temp["etime"]);
                            $morning=$this->hourtomin('0900');
                            $afternoon=$this->hourtomin("1200");
                            $night=$this->hourtomin('1800');
                            
                            if(($etime-$stime)>=($night-$morning)){
                                $final_result[$key][$time_result[$z]]["A"]=$temp;
                                $final_result[$key][$time_result[$z]]["B"]=$temp;
                                $final_result[$key][$time_result[$z]]["C"]=$temp;
                            }else if(($etime-$stime)>=($night-$afternoon)){
                                if($stime<=$morning){
                                    $final_result[$key][$time_result[$z]]["A"]=$temp;
                                    $final_result[$key][$time_result[$z]]["B"]=$temp;
                                }else{
                                    $final_result[$key][$time_result[$z]]["B"]=$temp;
                                    $final_result[$key][$time_result[$z]]["C"]=$temp;
                                }
                            }else{
                                if($stime<=$morning){
                                    $final_result[$key][$time_result[$z]]["A"]=$temp;
                                }else if($stime>$morning && $stime<=$afternoon){
                                    $final_result[$key][$time_result[$z]]["B"]=$temp;
                                }else{
                                    $final_result[$key][$time_result[$z]]["C"]=$temp;
                                }
                            }
                            
                        }
                    }
                }
            }
            $i++;
            // var_dump($final_result);exit();
        }
        for($h=0;$h<count($time_result);$h++){
            foreach($final_result as & $row2){
                if(!isset($row2[$time_result[$h]])){
                    $row2[$time_result[$h]]["A"]=null;
                    $row2[$time_result[$h]]["B"]=null;
                    $row2[$time_result[$h]]["C"]=null;
                }else{
                    if(!isset($row2[$time_result[$h]]["A"])){
                        $row2[$time_result[$h]]["A"]=null;
                    }
                    if(!isset($row2[$time_result[$h]]["B"])){
                        $row2[$time_result[$h]]["B"]=null;
                        //echo'B';
                    }
                    if(!isset($row2[$time_result[$h]]["C"])){
                        $row2[$time_result[$h]]["C"]=null;
                        //echo'C';
                    }
                }
            }
        }
        return $final_result;
    }

    public function hourtomin($time)
    {
        $h=(int)substr($time,0,2)*60;
        $m=(int)substr($time,3,2);
        $total=$h+$m;
        return $total;
    }

    public function _insert($post)
    {
        unset($post["_token"]);
        // $fuhua_arr=['101','103','201','202','203','204','205','C01','C02','C14'];
        // if(in_array($post['site'],$fuhua_arr)){
        //     $message[0]='無法對福華場地做修改';
        //     return $message;
        // }
        if($post["setweek"]!=''){ //週期性預約
            $check=$this->checkTime($post["setweek"],$post["site"],$post["branch"]);
            $message=[];
            if(!empty($check)){
                $i=0;
                foreach($check as $temp){
                    if($temp!=null){
                        $message[$i]=$temp->date.' 已有課堂預定';
                    }
                    $i++;
                }
                return $message;
            }

            $condition=explode(",",$post["setweek"]);
            $condition[count($condition)-1]=$this->bpr->convertYear($condition[count($condition)-1],'twtovids');
            $condition[count($condition)-2]=$this->bpr->convertYear($condition[count($condition)-2],'twtovids');
            $diff=(strtotime($condition[count($condition)-1]) - strtotime($condition[count($condition)-2]))/ (60*60*24)+1; //計算相差之天數
            
            for($i=0;$i<$diff;$i++){
                $date=date('Y/m/d',strtotime($condition[count($condition)-2])+($i*60*60*24));
                $post["date"]=$date;
                $this->bpr->insertPlace($post);
            }
        }else{
            //處理日期格式
            $time= array();
            $date=str_replace("/","",$post["date"]);
            if($post["time"]=='G'){
                $time=['A','B'];
            }else if($post["time"]=='H'){
                $time=['A','B','C'];
            }else{
                $time[]=$post["time"];
            }
            if($post['branch']=='1'){
                $check=DB::table("t22tb")->where("site",$post["site"])->where("date",$date)->whereIn("time",$time)->get()->toArray();
            }else{
                $check=DB::table("t97tb")->where("site",$post["site"])->where("date",$date)->whereIn("time",$time)->get()->toArray();    
            }
            
            $message=[];
            if(!empty($check)){
                $i=0;
                foreach($check as $temp){
                    if($temp!=null){
                        $message[$i]=$temp->date.' 已有課堂預定';
                    }
                    $i++;
                }
                return $message;
            }

            $this->bpr->insertPlace($post);
            for($i=1;$i<=2;$i++){
                $this->bpr->insertT37tb($post,$i); 
            }
            
        }
        
        return $message;
    }

    public function _update($update)
    {
        unset($update["_token"]);
        unset($update["_method"]);
        $cant=['101','103','201','202','203','204','205',"C01","C02"];
        $message=[];
        if(in_array($update["site"],$cant)){
            return $message[0]='不可對福華場地做操作';
        }
        $date=str_replace("/","",$update["date"]);
        if($update['branch']=='1'){
            $check=DB::table("t22tb")->where("site",$update["site"])->where("date",$date)->where("time",$update['time'])->get()->toArray();
        }else{
            $check=DB::table("t97tb")->where("site",$update["site"])->where("date",$date)->where("time",$update['time'])->get()->toArray();
        }
        if(!empty($check)){
            $i=0;
            foreach($check as $temp){
                if($temp->class == $update["class"] && $temp->term == $update["term"]){
                    continue;
                }elseif($temp!=null){
                    $message[$i]=$temp->date.' 已有課堂預定';
                }
                $i++;
            }
            return $message;
        }
        $this->bpr->updatePlace($update);
        return $message;
    }

    public function getInfo($arr)
    {
        return $this->bpr->getInfo($arr);
    }
    

    //檢查日期是否有預定
    function checkTime($setweek,$site,$branch=1)
    {
        if(empty($setweek)){
            return null;
        }
        // var_dump($setweek);
        $condition=explode(",",$setweek);
      
        $condition[count($condition)-1]=$this->bpr->convertYear($condition[count($condition)-1],'twtovids');
        $condition[count($condition)-2]=$this->bpr->convertYear($condition[count($condition)-2],'twtovids');
        $e=date("w",strtotime($condition[count($condition)-2]));
        $sunday=date('Y/m/d', strtotime($condition[count($condition)-2]."-{$e} days"));//取得禮拜日
        $choice_date=[];
        $diff=(strtotime($condition[count($condition)-1]) - strtotime($condition[count($condition)-2]))/ (60*60*24)+1; //計算相差之天數

        for($i=0;$i<$diff;$i++){
            $choice_date[date('Y/m/d',strtotime($condition[count($condition)-2])+($i*60*60*24))]=date("w",strtotime($condition[count($condition)-2])+($i*60*60*24));
        }
        $check=[];
        if($branch=='1'){
            foreach($choice_date as $key => $row){
                if(in_array($row,$condition)){
                    $date=$this->bpr->convertYear($key,'vidstotw');
                    $check[$date]=DB::table("t22tb")->where("site",$site)->where("date",$date)->first();
                }
            }
        }else{
            foreach($choice_date as $key => $row){
                if(in_array($row,$condition)){
                    $date=$this->bpr->convertYear($key,'vidstotw');
                    $check[$date]=DB::table("t97tb")->where("site",$site)->where("date",$date)->first();
                }
            }
        }

        if(!empty($check)){
            return $check;
        }
        return null;
    }
    
    public function getMeetList()
    {
        return $this->bpr->getMeetList();
    }

    public function getClassList()
    {
        return $this->bpr->getClassList();
    }

    public function getplacedata($condition,$classroom=NULL)
    {
        return $this->bpr->getplacedata($condition,$classroom);
    }

    public function batchUpdate($condition,$branch)
    {
        return $this->bpr->batchUpdate($condition,$branch);
    }

    public function seatUpdate($condition)
    {
        $siteadm=Auth::user()->siteadm;
        $now=date('Y/m/d');
        $now=$this->bpr->convertYear($now,'vidstotw');
        $time=date('Y-m-d H:i:s');
        $update=['upddate'=>$time,'seattype'=>$condition['seattype']];
        if($siteadm=='Y'){
            DB::table('t22tb')->where("class",$condition["class"])->where("term",$condition['term'])->update($update);
        }else{
            DB::table('t22tb')->where("class",$condition["class"])->where("term",$condition['term'])->where('affirm','>=',$now)->update($update);
        }
        $update=['upddate'=>$time,'t37tb.seattype'=>$condition['seattype']];

        $result=DB::table('t37tb')->join("t22tb",function($join){
            $join->on('t22tb.date','=','t37tb.date');
            $join->on('t22tb.site','=','t37tb.site');
            $join->on('t22tb.stime','=','t37tb.stime');
            $join->on('t22tb.etime','=','t37tb.etime');
        })->where("t37tb.term",$condition['term'])->where('t37tb.class',$condition['class'])->where('t37tb.type','1')->where('t22tb.request','>=',$now)->update($update);
        
        DB::table('t37tb')->join("t22tb",function($join){
            $join->on('t22tb.date','=','t37tb.date');
            $join->on('t22tb.site','=','t37tb.site');
            $join->on('t22tb.stime','=','t37tb.stime');
            $join->on('t22tb.etime','=','t37tb.etime');
        })->where("t37tb.term",$condition['term'])->where('t37tb.class',$condition['class'])->where('t37tb.type','2')->where('t22tb.affirm','>=',$now)->update($update);
    }
  

}
