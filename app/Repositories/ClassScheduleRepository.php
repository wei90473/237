<?php
namespace App\Repositories;

use App\Models\T06tb;
use App\Models\M14tb;
use App\Models\Edu_classroom;
use Auth;
use DB;


class ClassScheduleRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassScheduleList($queryData = [])
    {
        $query = T06tb::select('class', 'term', 'name', 'date', 'stime', 'etime', 'hour');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['class', 'name', 'date', 'stime', 'etime'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('date', 'desc');
            $query->orderBy('stime');
        }

        // 班別
        if ( isset($queryData['class']) && $queryData['class'] ) {

            $query->where('class', $queryData['class']);
        }

        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {

            $query->where('term', $queryData['term']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    public function getcourse($class=NULL,$term=NULL,$course=NULL){
        if(is_null($class)||is_null($term)||is_null($course)) return false;

        $query = T06tb::select('class', 'term','course', 'name', 'date', 'stime', 'etime', 'hour');    
        // 課程
        $data =$query->where('class', $class)->where('term', $term)->where('course', $course)->get()->first();
        return $data;    
    }

    public function getScheduleList($class=NULL,$term=NULL){
        if(is_null($class)||is_null($term)) return false;

        // $query = T06tb::select('t06tb.course','t06tb.name','t06tb.date','t06tb.stime','t06tb.etime','t06tb.hour','t08tb.cname','m14tb.name as sitename','m25tb.name as sitename2');
        // $query->JOIN('t08tb',function($join){
        //     $join->on('t06tb.class','=','t08tb.class')->where('t06tb.term','t08tb.term')->where('t06tb.course','t08tb.course')->where('t06tb.class',$class)->where('t06tb.term',$term);   
        // },null,null,null,null,'inner');  //laravel join的問題...

        // $query->leftJoin('t04tb',function($join){
        //     $join->on('t06tb.class','=','t04tb.class')->where('t06tb.term','t04tb.term');
        // },null,'left');
        // $query->leftJoin('m14tb','t04tb.site','=','m14tb.site');
        // $query->leftJoin('m25tb','t04tb.site','=','m25tb.site');
        // $data = $query->get();
        // return $data;

        $sql = "select `t06tb`.`course`, `t06tb`.`name`, `t06tb`.`date`, `t06tb`.`stime`, `t06tb`.`etime`, `t06tb`.`hour`, `t08tb`.`cname`, `m14tb`.`name` as `sitename`, `m25tb`.`name` as `sitename2` 
                from `t06tb` join `t08tb` on `t06tb`.`class` = `t08tb`.`class` and `t06tb`.`term` = t08tb.term and `t06tb`.`course` = t08tb.course and `t06tb`.`class` = $class and `t06tb`.`term` = $term 
                left join `t04tb` on `t06tb`.`class` = `t04tb`.`class` and `t06tb`.`term` = t04tb.term 
                left join `m14tb` on `t04tb`.`site` = `m14tb`.`site` 
                left join `m25tb` on `t04tb`.`site` = `m25tb`.`site`
                GROUP BY course";
        return DB::select($sql);
    }
    //取得教室名稱
    public function getsitename($site=NULL,$branch='1')    {
        if(is_null($site)) return false;
        
        if($branch =='1'){
            $data = M14tb::select('name','site')->where('site',$site);
        }else{
            $data = Edu_classroom::select(DB::raw('roomno as site,roomname as name'))->where('roomno',$site);
        }
        return $data->first();   
    }
}
