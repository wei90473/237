<?php
namespace App\Repositories;

use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T36tb;
use DB ;
class SiteScheduleRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteScheduleList($queryData = [])
    {
        $query = T04tb::select('t04tb.class','t01tb.branch','t01tb.name','t04tb.term','t04tb.sdate','t04tb.edate','t04tb.site_branch','t04tb.sponsor','t04tb.quota','t04tb.site','t04tb.time','t04tb.lineup','t04tb.force','t04tb.fee','t04tb.section','t01tb.style','t01tb.day','t01tb.time1','t01tb.time2','t01tb.time3','t01tb.time4','t01tb.time5','t01tb.time6','t01tb.time7','t01tb.holiday','m09tb.username');
        $query->join('t01tb', 't01tb.class', '=', 't04tb.class', 'INNER');
        $query->leftjoin('m09tb','t04tb.sponsor','=','m09tb.userid');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {
            if (in_array($queryData['_sort_field'], ['class'])) {
                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        }else {
            // 預設排序
            $query->orderBy('class', 'desc');
        }
        // 月份
        if ( isset($queryData['month']) && $queryData['month'] ) {

            $query->where('t04tb.sdate', '<=', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).$queryData['month'].'99');
            $query->where('t04tb.edate', '>=', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).$queryData['month'].'00');
        }
        if ( isset($queryData['smonth']) && $queryData['smonth'] ) {
            $query->where('t04tb.sdate', '>=', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).$queryData['smonth'].'00');
        }
        if ( isset($queryData['emonth']) && $queryData['emonth'] ) {
            $query->where('t04tb.edate', '<=', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).$queryData['emonth'].'99');
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
        // 辦班院區
        if ( isset($queryData['branch']) && $queryData['branch'] ) {
            $query->where('t01tb.branch', 'LIKE', '%'.$queryData['branch'].'%');
        }
        // 訓練性質
        if ( isset($queryData['traintype']) && $queryData['traintype'] ) {
            $query->where('t01tb.traintype', 'LIKE', '%'.$queryData['traintype'].'%');
        }
        // 班務人員
        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $query->where('t04tb.sponsor', '=', $queryData['sponsor']);
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
        }
        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {
            $query->where('t04tb.term', $queryData['term']);
        }

        $query->where('t01tb.type','13');//只包括 游於藝班級

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    /**
     * 取得不重複班級列表
     *
     * @param array $queryData 關鍵字
     * @return array
     */
    public function getclasslist($queryData = []){
        $query = T04tb::select('t04tb.class','t01tb.branch','t01tb.name','t04tb.term');
        $query->join('t01tb', 't01tb.class', '=', 't04tb.class', 'INNER');
        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {

          //  $query->where('t01tb.class', 'like', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).'%');
        }
        // 月份
        if ( isset($queryData['month']) && $queryData['month'] ) {

            $query->where('t04tb.sdate', '<=', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).$queryData['month'].'99');
            $query->where('t04tb.edate', '>=', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).$queryData['month'].'00');
        }
        if ( isset($queryData['smonth']) && $queryData['smonth'] ) {
            $query->where('t04tb.sdate', '>=', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).$queryData['smonth'].'00');
        }
        if ( isset($queryData['emonth']) && $queryData['emonth'] ) {
            $query->where('t04tb.edate', '<=', str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).$queryData['emonth'].'99');
        }
        
        $query->where('t01tb.type','13');//只包括 游於藝班級
        $query->orderBy('class', 'desc');
        $data = $query;
        
        return $data;
    }
    /**
     * 取得行事曆列表
     *
     * @param array $class,$term 關鍵字
     * @return mixed
     */
    public function getcalendarlist($class,$term){
        $sql = "SELECT DISTINCT t04tb.class, t04tb.term, IFNULL(t36tb.date,'') AS `date`, t04tb.sdate, t04tb.edate, t04tb.quota, t04tb.sponsor, t04tb.lineup,  t04tb.fee, t36tb.site  FROM t04tb LEFT JOIN t36tb ON t04tb.class = t36tb.class AND t04tb.term = t36tb.term WHERE t04tb.class= '".$class."' AND t04tb.term= '".$term."' ORDER BY date ";
        // $query = T04tb::select('t04tb.class, t04tb.term, IFNULL(t36tb.date,'') AS date, t04tb.sdate, t04tb.edate, t04tb.quota, t04tb.sponsor, t04tb.lineup,  t04tb.fee');
        // $query->join('t36tb', 't36tb.class', '=', 't04tb.class', 'left');
        // $query->where('t01tb.type','13');//只包括 游於藝班級

        return DB::select($sql);
    }
}
