<?php
namespace App\Repositories;
use App\Models\itineracy;
use App\Models\itineracy_schedule;
use App\Models\itineracy_sitting;
use App\Models\itineracy_survey;
use App\Models\itineracy_annual;
use App\Models\T01tb;
use App\Models\T04tb;
use DB;

class ItineracyRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getItineracyList($queryData = [])
    {
        $query = itineracy_sitting::select('id', 'type','code', 'name');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['class'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('code');
        }

        // 名稱
        if ( isset($queryData['name']) && $queryData['name'] ) {
            
            $query->where('name', 'like', '%'.$queryData['name'].'%');
        }

        // 代號
        if ( isset($queryData['code']) && $queryData['code'] ) {

            $query->where('code', $queryData['code']);
        }
        // 類型
        if ( isset($queryData['type']) && $queryData['type'] ) {

            $query->where('type', $queryData['type']);
        }
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    /* 取得代號最大值
     *
     * @param $type 關鍵字
     * @return Max
     */
    public function getItineracyMax($type){
        if ($type==NULL) return false;

        $result = itineracy_sitting::where('type', $type)->max('code');
        return $result;
    }

    //寫入代碼庫
    public function insertItineracy($queryData = []){
        $result = itineracy_sitting::insert($queryData);
        if($result){
            return TRUE;
        }else{
            return false;
        }
    }

    /**
     * 取得年度主題列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getAnnualList($queryData = [])
    {
        $query = itineracy::select('yerly','term','name','surveysdate','surveyedate','sdate','edate','topics');
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['class'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('yerly','DESC');
        }

        // 名稱
        if ( isset($queryData['name']) && $queryData['name'] ) {
            
            $query->where('name', 'like', '%'.$queryData['name'].'%');
        }

        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {

            $query->where('yerly', $queryData['yerly']);
        }
        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {

            $query->where('term', $queryData['term']);
        }
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    /**
     * 取得年度主題當年度最新期別
     *
     * @param $yerly 關鍵字
     * @return Maxterm
     */
    public function getAnnualMax($yerly)
    {
      if ($yerly==NULL) return false;

        $result = itineracy::where('yerly', $yerly)->max('term');
        return $result;  

    }
    /**
     * 取得該年度主題列表內容
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getAnnual($queryData = []){
        $sql = "select A.id,A.yerly,A.term,A.items,A.type1,B.`name` as name1,A.type2,C.`name` as name2,A.type3,D.`name` as name3  ";

        if(isset($queryData['class']) && $queryData['class'] ) {  
            $sql .= " ,E.remake,IF(E.class='".$queryData['class']."','1','0') as serch "; 
        }
        $sql .= " from `itineracy_annual` as A 
                LEFT JOIN itineracy_sitting as B
                on B.type = '1' AND B.`code` = A.type1
                LEFT JOIN itineracy_sitting as C
                on C.type = '2' AND C.`code` = A.type2
                LEFT JOIN itineracy_sitting as D
                on D.type = '3' AND D.`code` = A.type3 ";
        if(isset($queryData['class']) && $queryData['class'] ) {  
            $sql .= "left JOIN itineracy_course as E on A.id = E.annual_id AND E.class = '".$queryData['class']."'";
        }      
        $sql .= " WHERE A.yerly = ".$queryData['yerly']." and A.term = ".$queryData['term']." ORDER BY A.items,A.id  ";
        return DB::select($sql);
    }
    /**
     * 取得該年度填報資料
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getList($queryData = []){
        $query = itineracy_survey::select('id','yerly','term','city','presetdate','sponsor','phone1','phone2','mail','fax','day','actualdate','actualdays');
        if( isset($queryData['id']) && $queryData['id'] ) {
            $query->where('id', $queryData['id']);
        }else{
            $query->where('yerly', $queryData['yerly']);
            $query->where('term', $queryData['term']);
            //縣市別
            if ( isset($queryData['city']) && $queryData['city'] ) {

               $query->where('city', $queryData['city']);
            }
        }
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 20);
        return $data;
    }
    /**
     * 取得該年度填報資料(縣市別)
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getScheduleList($queryData = []){
        $query = itineracy_schedule::select('class','yerly','term','city','presetdate','actualdate','quota','staff','address','fee');
        $query->where('yerly', $queryData['yerly']);
        $query->where('term', $queryData['term']);
        $query->where('city', $queryData['city']);
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 20);
        return $data;
    }
    /**
     * 取得日程表列印資料(縣市別)
     *
     * @param array $queryData 關鍵字
     * @return array
     */
    public function getSchedulePrintList($queryData = []){
        $sql = "select A.yerly,A.term,A.city,B.actualdate,B.quota,B.staff,B.address,B.class,C.remake,D.type1,E.`name` from itineracy_survey as A 
                INNER JOIN itineracy_schedule as B on
                A.yerly = B.yerly AND A.term = B.term AND A.city = B.city
                LEFT JOIN itineracy_course as C on
                B.class = C.class
                LEFT JOIN itineracy_annual as D on
                C.annual_id = D.id
                LEFT JOIN itineracy_sitting as E on
                D.type1 = E.`code` AND E.type = '1'
                where ";
        if( isset($queryData['yerly']) && $queryData['yerly'] && isset($queryData['term']) && $queryData['term'] ) {
            $sql .= "A.yerly = '".$queryData['yerly']."' AND A.term = '".$queryData['term']."'";
        } elseif(isset($queryData['id']) ){
            $sql .= "A.id = '".$queryData['id']."' ";
        }
        $sql .= " AND B.actualdate is not null GROUP BY B.class ORDER By B.actualdate DESC" ;     
        return DB::select($sql);

    }
    public function updateSurvey($queryData = []){
        $olddata = itineracy_schedule::select('presetdate','actualdate')->where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->where('city',$queryData['city'])->get()->toArray();
        $presetdate = '';
        $actualdate = '';
        $day = 0;
        $actualdays = 0;
        if(count($olddata)>0){
            foreach ($olddata as $key => $value) {
                if($value['actualdate']!='' && !is_null($value['actualdate']) ) {
                    $actualdate = $actualdate.$value['actualdate'].',';
                    $actualdays = $actualdays+1;
                }
                if($value['presetdate']!='' && !is_null($value['presetdate']) ) { 
                    $presetdate = $presetdate.$value['presetdate'].',';
                    $day = $day+1;
                }
            }
            $presetdate = ($presetdate=='')? $presetdate :substr($presetdate,0,-1);
            $actualdate = ($actualdate=='')? $actualdate :substr($actualdate,0,-1);
        }
        $newsurvey = array( 'presetdate' =>$presetdate,'day' =>$day, 'actualdate' =>$actualdate,'actualdays' =>$actualdays);
        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $newsurvey['sponsor'] = $queryData['sponsor'];
        }

        if ( isset($queryData['phone1']) && $queryData['phone1'] ) {
            $newsurvey['phone1'] = $queryData['phone1'];
        }

        if ( isset($queryData['phone2']) && $queryData['phone2'] ) {
            $newsurvey['phone2'] = $queryData['phone2'];
        }

        if ( isset($queryData['mail']) && $queryData['mail'] ) {
            $newsurvey['mail'] = $queryData['mail'];
        }

        if ( isset($queryData['fax']) && $queryData['fax'] ) {
            $newsurvey['fax'] = $queryData['fax'];
        }

        $updata = itineracy_survey::where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->where('city',$queryData['city'])
                ->update($newsurvey);
        if($updata){
            return true;
        }else{
            return false;
        }
    }
}
