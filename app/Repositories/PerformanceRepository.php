<?php
namespace App\Repositories;

use App\Models\T13tb;
use App\Models\T04tb;
use Auth;
use DB;


class PerformanceRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPerformanceList($queryData = [])
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;


        $query = T04tb::select( 't01tb.class','t01tb.branch','t01tb.name','t01tb.branchname','t04tb.term','t01tb.process','t01tb.commission','t04tb.sdate','t04tb.edate','t04tb.sponsor','m09tb.username',DB::raw('IFNULL(t01tb.trainday,0) as trainday'),DB::raw('IFNULL(t01tb.trainhour,0) as trainhour'), 't04tb.regcnt', 't04tb.passcnt', 't04tb.endcnt');
        $query->join('t01tb', 't04tb.class', '=', 't01tb.class', 'LEFT OUTER');
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {
            if (in_array($queryData['_sort_field'], ['class'])) {
                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        }else {
            // 預設排序
            $query->orderBy('class')->orderBy('term');
        }
        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
            $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
            $query->where('t01tb.yerly', $queryData['yerly']);
        }
        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t01tb.class', 'LIKE', '%'.$queryData['class'].'%');
        }
        // 班別
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'LIKE', '%'.$queryData['name'].'%');
        }
        // 分班名稱
        if ( isset($queryData['branchname']) && $queryData['branchname'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['branchname'].'%');
        }
        // 辦班院區
        if ( isset($queryData['branch']) && $queryData['branch'] ) {
            $query->where('t01tb.branch', 'LIKE', '%'.$queryData['branch'].'%');
        }
        // 班別類型
        if ( isset($queryData['process']) && $queryData['process'] ) {
            $query->where('t01tb.process', 'LIKE', '%'.$queryData['process'].'%');
        }
        // 訓練性質
        if ( isset($queryData['traintype']) && $queryData['traintype'] ) {
            $query->where('t01tb.traintype', 'LIKE', '%'.$queryData['traintype'].'%');
        }
        // 類別1
        if ( isset($queryData['categoryone']) && $queryData['categoryone'] ) {
            $query->where('t01tb.categoryone', $queryData['categoryone']);
        }
        // 班務人員
        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $query->where('t04tb.sponsor', '=', $queryData['sponsor']);
        }
        // 班別性質
        if ( isset($queryData['type']) && $queryData['type'] ) {
            $query->where('t01tb.type', $queryData['type']);
        }
        // 上課地點
        if ( isset($queryData['sitebranch']) && $queryData['sitebranch'] ) {
            $query->where('t04tb.site_branch', $queryData['sitebranch']);
        }
        // 開訓日期
        if ( isset($queryData['sdate']) && $queryData['sdate'] ) {
            $queryData['sdate'] = str_pad($queryData['sdate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '>=', $queryData['sdate']);
        }
        if ( isset($queryData['edate']) && $queryData['edate'] ) {
            $queryData['edate'] = str_pad($queryData['edate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '<=', $queryData['edate']);
        }
        // 結訓日期
        if ( isset($queryData['sdate2']) && $queryData['sdate2'] ) {
            $queryData['sdate2'] = str_pad($queryData['sdate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '>=', $queryData['sdate2']);
        }
        if ( isset($queryData['edate2']) && $queryData['edate2'] ) {
            $queryData['edate2'] = str_pad($queryData['edate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '<=', $queryData['edate2']);
        }
        // 在訓日期
        if(isset($queryData['sdate3']) && $queryData['sdate3'] && isset($queryData['edate3']) && $queryData['edate3'] ){
            $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);
            $queryData['edate3'] = str_pad($queryData['edate3'],7,'0',STR_PAD_LEFT);

            $query->leftJoin('t06tb', function($join)
            {
                $join->on('t04tb.class', '=', 't06tb.class')
                ->on('t04tb.term', '=', 't06tb.term');
            });
            $query->where('t06tb.date', '>=', $queryData['sdate3']);
            $query->where('t06tb.date', '<=', $queryData['edate3']);
            $query->distinct();
            $query->groupBy('t04tb.class', 't04tb.term');
        }else{
            if ( isset($queryData['sdate3']) && $queryData['sdate3'] ) {
                $query->leftJoin('t06tb', function($join)
                {
                    $join->on('t04tb.class', '=', 't06tb.class')
                    ->on('t04tb.term', '=', 't06tb.term');
                });
                $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);
                $query->where('t06tb.date', '>=', $queryData['sdate3']);
                $query->distinct();
            }
            if ( isset($queryData['edate3']) && $queryData['edate3'] ) {
                $query->leftJoin('t06tb', function($join)
                {
                    $join->on('t04tb.class', '=', 't06tb.class')
                    ->on('t04tb.term', '=', 't06tb.term');
                });
                $queryData['edate3'] = str_pad($queryData['edate3'],7,'0',STR_PAD_LEFT);
                $query->where('t06tb.date', '<=', $queryData['edate3']);
                $query->distinct();
            }
            $query->groupBy('t04tb.class', 't04tb.term');
        }

        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {
            $query->where('t04tb.term', $queryData['term']);
        }
        // 委訓機關
        if ( isset($queryData['commission']) && $queryData['commission'] ) {
            $query->where('t01tb.commission', $queryData['commission']);
        }
        $query->where('t01tb.cntflag','2');
        $query->leftjoin('m09tb','t04tb.sponsor','=','m09tb.userid');
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);


        // var_dump($data);exit();
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
    public function getsitename($site=NULL)    {
        if(is_null($site)) return false;


        $query = M14tb::select('name','site');
        $query2 = M25tb::select('name','site')->where('site',$site);
        $data = $query->union($query2)->where('site',$site)->first(); //主教室
        return $data;
    }
}
