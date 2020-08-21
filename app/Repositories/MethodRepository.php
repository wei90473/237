<?php
namespace App\Repositories;

use App\Models\T13tb;
use App\Models\T01tb;
use App\Models\T49tb;
use App\Models\S06tb;
use Auth;
use DB;


class MethodRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getMethodList($queryData = [])
    {
        if ($queryData['class'] == '' || $queryData['term'] == '') {

            return array();
        }

        $sql = "
            SELECT A.id, C.idno, (CASE WHEN C.idno IS NOT NULL THEN 'Y' ELSE '' END) AS isseting,
            A.date,
            A.stime,
            A.etime,
             RTRIM(A.name) AS course_name, RTRIM(B.cname) AS teacher,A.class,A.term,A.course,B.idno,
            (CASE WHEN C.method1 = 'M01' THEN '1' WHEN C.method2 = 'M01' THEN '2' WHEN C.method3 = 'M01' THEN '3' ELSE '' END) AS M01,
            (CASE WHEN C.method1 = 'M02' THEN '1' WHEN C.method2 = 'M02' THEN '2' WHEN C.method3 = 'M02' THEN '3' ELSE '' END) AS M02,
            (CASE WHEN C.method1 = 'M03' THEN '1' WHEN C.method2 = 'M03' THEN '2' WHEN C.method3 = 'M03' THEN '3' ELSE '' END) AS M03,
            (CASE WHEN C.method1 = 'M04' THEN '1' WHEN C.method2 = 'M04' THEN '2' WHEN C.method3 = 'M04' THEN '3' ELSE '' END) AS M04,
            (CASE WHEN C.method1 = 'M05' THEN '1' WHEN C.method2 = 'M05' THEN '2' WHEN C.method3 = 'M05' THEN '3' ELSE '' END) AS M05,
            (CASE WHEN C.method1 = 'M06' THEN '1' WHEN C.method2 = 'M06' THEN '2' WHEN C.method3 = 'M06' THEN '3' ELSE '' END) AS M06,
            (CASE WHEN C.method1 = 'M07' THEN '1' WHEN C.method2 = 'M07' THEN '2' WHEN C.method3 = 'M07' THEN '3' ELSE '' END) AS M07,
            (CASE WHEN C.method1 = 'M08' THEN '1' WHEN C.method2 = 'M08' THEN '2' WHEN C.method3 = 'M08' THEN '3' ELSE '' END) AS M08,
            (CASE WHEN C.method1 = 'M09' THEN '1' WHEN C.method2 = 'M09' THEN '2' WHEN C.method3 = 'M09' THEN '3' ELSE '' END) AS M09,
            (CASE WHEN C.method1 = 'M10' THEN '1' WHEN C.method2 = 'M10' THEN '2' WHEN C.method3 = 'M10' THEN '3' ELSE '' END) AS M10,
            (CASE WHEN C.method1 = 'M11' THEN '1' WHEN C.method2 = 'M11' THEN '2' WHEN C.method3 = 'M11' THEN '3' ELSE '' END) AS M11,
            (CASE WHEN C.method1 = 'M12' THEN '1' WHEN C.method2 = 'M12' THEN '2' WHEN C.method3 = 'M12' THEN '3' ELSE '' END) AS M12,
            (CASE WHEN C.method1 = 'M13' THEN '1' WHEN C.method2 = 'M13' THEN '2' WHEN C.method3 = 'M13' THEN '3' ELSE '' END) AS M13,
            (CASE WHEN C.method1 = 'M14' THEN '1' WHEN C.method2 = 'M14' THEN '2' WHEN C.method3 = 'M14' THEN '3' ELSE '' END) AS M14,
            (CASE WHEN C.method1 = 'M15' THEN '1' WHEN C.method2 = 'M15' THEN '2' WHEN C.method3 = 'M15' THEN '3' ELSE '' END) AS M15,
            (CASE WHEN C.method1 = 'M16' THEN '1' WHEN C.method2 = 'M16' THEN '2' WHEN C.method3 = 'M16' THEN '3' ELSE '' END) AS M16,
            (CASE WHEN C.method1 = 'M17' THEN '1' WHEN C.method2 = 'M17' THEN '2' WHEN C.method3 = 'M17' THEN '3' ELSE '' END) AS M17,
            (CASE WHEN C.method1 = 'M18' THEN '1' WHEN C.method2 = 'M18' THEN '2' WHEN C.method3 = 'M18' THEN '3' ELSE '' END) AS M18,
            (CASE WHEN C.method1 = 'T01' THEN '1' WHEN C.method2 = 'T01' THEN '2' WHEN C.method3 = 'T01' THEN '3' ELSE '' END) AS T01,
            (CASE WHEN C.method1 = 'T02' THEN '1' WHEN C.method2 = 'T02' THEN '2' WHEN C.method3 = 'T02' THEN '3' ELSE '' END) AS T02,
            (CASE WHEN C.method1 = 'T03' THEN '1' WHEN C.method2 = 'T03' THEN '2' WHEN C.method3 = 'T03' THEN '3' ELSE '' END) AS T03,
            (CASE WHEN C.method1 = 'T04' THEN '1' WHEN C.method2 = 'T04' THEN '2' WHEN C.method3 = 'T04' THEN '3' ELSE '' END) AS T04,
            (CASE WHEN C.method1 = 'T05' THEN '1' WHEN C.method2 = 'T05' THEN '2' WHEN C.method3 = 'T05' THEN '3' ELSE '' END) AS T05,
            (CASE WHEN C.method1 = 'T06' THEN '1' WHEN C.method2 = 'T06' THEN '2' WHEN C.method3 = 'T06' THEN '3' ELSE '' END) AS T06,
            (CASE WHEN C.method1 = 'T07' THEN '1' WHEN C.method2 = 'T07' THEN '2' WHEN C.method3 = 'T07' THEN '3' ELSE '' END) AS T07,
            (CASE WHEN C.method1 = 'T08' THEN '1' WHEN C.method2 = 'T08' THEN '2' WHEN C.method3 = 'T08' THEN '3' ELSE '' END) AS T08,
            (CASE WHEN C.method1 = 'T09' THEN '1' WHEN C.method2 = 'T09' THEN '2' WHEN C.method3 = 'T09' THEN '3' ELSE '' END) AS T09,
            (CASE WHEN C.method1 = 'T10' THEN '1' WHEN C.method2 = 'T10' THEN '2' WHEN C.method3 = 'T10' THEN '3' ELSE '' END) AS T10,
            (CASE WHEN C.method1 = 'T11' THEN '1' WHEN C.method2 = 'T11' THEN '2' WHEN C.method3 = 'T11' THEN '3' ELSE '' END) AS T11,
             C.method1,C.method2,C.method3,other1,
            other2,other3,C.mark
             FROM t06tb A       /* 【t06tb 課程表資料檔】 */
            INNER JOIN t08tb B /* 【t08tb 擬聘講座資料檔】 */
            ON A.class = B.class AND A.term = B.term AND A.course = B.course
            LEFT JOIN t98tb C  /* 【t98tb 講座教學教法資料檔】 */
            ON A.class = C.class AND A.term = C.term AND A.course = C.course AND B.idno = C.idno
            WHERE B.hire = 'Y'AND A.class = '".$queryData['class']."' AND A.term = '".$queryData['term']."'
            ORDER BY A.date, A.stime, A.etime, A.course";

        return DB::select($sql);
    }

    public function getMethodRowData($id)
    {
        $sql = "
            SELECT A.id, C.idno, (CASE WHEN C.idno IS NOT NULL THEN 'Y' ELSE '' END) AS isseting,
              A.date,
            A.stime,
            A.etime,
             RTRIM(A.name) AS course_name, RTRIM(B.cname) AS teacher,A.class,A.term,A.course,B.idno,
            (CASE WHEN C.method1 = 'M01' THEN '1' WHEN C.method2 = 'M01' THEN '2' WHEN C.method3 = 'M01' THEN '3' ELSE '' END) AS M01,
            (CASE WHEN C.method1 = 'M02' THEN '1' WHEN C.method2 = 'M02' THEN '2' WHEN C.method3 = 'M02' THEN '3' ELSE '' END) AS M02,
            (CASE WHEN C.method1 = 'M03' THEN '1' WHEN C.method2 = 'M03' THEN '2' WHEN C.method3 = 'M03' THEN '3' ELSE '' END) AS M03,
            (CASE WHEN C.method1 = 'M04' THEN '1' WHEN C.method2 = 'M04' THEN '2' WHEN C.method3 = 'M04' THEN '3' ELSE '' END) AS M04,
            (CASE WHEN C.method1 = 'M05' THEN '1' WHEN C.method2 = 'M05' THEN '2' WHEN C.method3 = 'M05' THEN '3' ELSE '' END) AS M05,
            (CASE WHEN C.method1 = 'M06' THEN '1' WHEN C.method2 = 'M06' THEN '2' WHEN C.method3 = 'M06' THEN '3' ELSE '' END) AS M06,
            (CASE WHEN C.method1 = 'M07' THEN '1' WHEN C.method2 = 'M07' THEN '2' WHEN C.method3 = 'M07' THEN '3' ELSE '' END) AS M07,
            (CASE WHEN C.method1 = 'M08' THEN '1' WHEN C.method2 = 'M08' THEN '2' WHEN C.method3 = 'M08' THEN '3' ELSE '' END) AS M08,
            (CASE WHEN C.method1 = 'M09' THEN '1' WHEN C.method2 = 'M09' THEN '2' WHEN C.method3 = 'M09' THEN '3' ELSE '' END) AS M09,
            (CASE WHEN C.method1 = 'M10' THEN '1' WHEN C.method2 = 'M10' THEN '2' WHEN C.method3 = 'M10' THEN '3' ELSE '' END) AS M10,
            (CASE WHEN C.method1 = 'M11' THEN '1' WHEN C.method2 = 'M11' THEN '2' WHEN C.method3 = 'M11' THEN '3' ELSE '' END) AS M11,
            (CASE WHEN C.method1 = 'M12' THEN '1' WHEN C.method2 = 'M12' THEN '2' WHEN C.method3 = 'M12' THEN '3' ELSE '' END) AS M12,
            (CASE WHEN C.method1 = 'M13' THEN '1' WHEN C.method2 = 'M13' THEN '2' WHEN C.method3 = 'M13' THEN '3' ELSE '' END) AS M13,
            (CASE WHEN C.method1 = 'M14' THEN '1' WHEN C.method2 = 'M14' THEN '2' WHEN C.method3 = 'M14' THEN '3' ELSE '' END) AS M14,
            (CASE WHEN C.method1 = 'M15' THEN '1' WHEN C.method2 = 'M15' THEN '2' WHEN C.method3 = 'M15' THEN '3' ELSE '' END) AS M15,
            (CASE WHEN C.method1 = 'M16' THEN '1' WHEN C.method2 = 'M16' THEN '2' WHEN C.method3 = 'M16' THEN '3' ELSE '' END) AS M16,
            (CASE WHEN C.method1 = 'M17' THEN '1' WHEN C.method2 = 'M17' THEN '2' WHEN C.method3 = 'M17' THEN '3' ELSE '' END) AS M17,
            (CASE WHEN C.method1 = 'M18' THEN '1' WHEN C.method2 = 'M18' THEN '2' WHEN C.method3 = 'M18' THEN '3' ELSE '' END) AS M18,
            (CASE WHEN C.method1 = 'T01' THEN '1' WHEN C.method2 = 'T01' THEN '2' WHEN C.method3 = 'T01' THEN '3' ELSE '' END) AS T01,
            (CASE WHEN C.method1 = 'T02' THEN '1' WHEN C.method2 = 'T02' THEN '2' WHEN C.method3 = 'T02' THEN '3' ELSE '' END) AS T02,
            (CASE WHEN C.method1 = 'T03' THEN '1' WHEN C.method2 = 'T03' THEN '2' WHEN C.method3 = 'T03' THEN '3' ELSE '' END) AS T03,
            (CASE WHEN C.method1 = 'T04' THEN '1' WHEN C.method2 = 'T04' THEN '2' WHEN C.method3 = 'T04' THEN '3' ELSE '' END) AS T04,
            (CASE WHEN C.method1 = 'T05' THEN '1' WHEN C.method2 = 'T05' THEN '2' WHEN C.method3 = 'T05' THEN '3' ELSE '' END) AS T05,
            (CASE WHEN C.method1 = 'T06' THEN '1' WHEN C.method2 = 'T06' THEN '2' WHEN C.method3 = 'T06' THEN '3' ELSE '' END) AS T06,
            (CASE WHEN C.method1 = 'T07' THEN '1' WHEN C.method2 = 'T07' THEN '2' WHEN C.method3 = 'T07' THEN '3' ELSE '' END) AS T07,
            (CASE WHEN C.method1 = 'T08' THEN '1' WHEN C.method2 = 'T08' THEN '2' WHEN C.method3 = 'T08' THEN '3' ELSE '' END) AS T08,
            (CASE WHEN C.method1 = 'T09' THEN '1' WHEN C.method2 = 'T09' THEN '2' WHEN C.method3 = 'T09' THEN '3' ELSE '' END) AS T09,
            (CASE WHEN C.method1 = 'T10' THEN '1' WHEN C.method2 = 'T10' THEN '2' WHEN C.method3 = 'T10' THEN '3' ELSE '' END) AS T10,
            (CASE WHEN C.method1 = 'T11' THEN '1' WHEN C.method2 = 'T11' THEN '2' WHEN C.method3 = 'T11' THEN '3' ELSE '' END) AS T11,
            C.method1,C.method2,C.method3,other1,
            other2,other3,C.mark
             FROM t06tb A       /* 【t06tb 課程表資料檔】 */
            INNER JOIN t08tb B /* 【t08tb 擬聘講座資料檔】 */
            ON A.class = B.class AND A.term = B.term AND A.course = B.course
            LEFT JOIN t98tb C  /* 【t98tb 講座教學教法資料檔】 */
            ON A.class = C.class AND A.term = C.term AND A.course = C.course AND B.idno = C.idno
            WHERE B.hire = 'Y'
            AND A.id = '".$id."'
            ORDER BY A.date, A.stime, A.etime, A.course";

        return DB::select($sql);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList($queryData = [])
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;

        $query = T01tb::select('t01tb.class','t01tb.branch','t01tb.name','t01tb.branchname','t04tb.term','t01tb.process','t01tb.commission','t01tb.teaching','t04tb.sdate','t04tb.edate','t04tb.sponsor','m09tb.username');
        $query->join('t04tb', 't01tb.class', '=', 't04tb.class', 'INNER');
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {
            if (in_array($queryData['_sort_field'], ['class'])) {
                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        }else {
            // 預設排序
            $query->orderBy('class', 'desc')->orderBy('term');
        }
        //不要列洽借班期
        if (isset($queryData['type13'])){
            $query->where('t01tb.type', '<>','13');
        }

        if ( isset($queryData['teaching']) && $queryData['teaching'] ) {
            $query->where('t01tb.teaching', $queryData['teaching']);
        }
        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t01tb.class', 'like', '%'.$queryData['class'].'%');
        }
        // 班別名稱
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'like', '%'.$queryData['name'].'%');
        }
        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {
            $query->where('t04tb.term', 'like', '%'.$queryData['term']);
        }
        // 分班名稱**
        if ( isset($queryData['branchname']) && $queryData['branchname'] ) {
            $query->where('t01tb.branchname', 'like', '%'.$queryData['branchname'].'%');

        }
        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
            $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
            $query->where('t01tb.yerly', $queryData['yerly']);
        }
        // 辦班院區
        if ( isset($queryData['branch']) && $queryData['branch'] ) {

            $query->where('t01tb.branch', $queryData['branch']);
        }
        // 班別類型
        if ( isset($queryData['process']) && $queryData['process'] ) {
            $query->where('t01tb.process', 'LIKE', '%'.$queryData['process'].'%');
        }
        // 班務人員
        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $query->where('t04tb.sponsor', $queryData['sponsor']);
        }
        // 委訓機關
        if ( isset($queryData['commission']) && $queryData['commission'] ) {
            $query->where('t01tb.commission', $queryData['commission']);
        }
        // 訓練性質
        if ( isset($queryData['traintype']) && $queryData['traintype'] ) {
            $query->where('t01tb.traintype', $queryData['traintype']);
        }
        // 班別性質
        if ( isset($queryData['type']) && $queryData['type'] ) {
            $query->where('t01tb.type', $queryData['type']);
        }
        // 類別1
        if ( isset($queryData['categoryone']) && $queryData['categoryone'] ) {
            $query->where('t01tb.categoryone', $queryData['categoryone']);
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

        if ( isset($queryData['group']) && $queryData['group'] ) {
            $query->groupby($queryData['group']);
        }

        $query->leftjoin('m09tb','t04tb.sponsor','=','m09tb.userid');
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;

    }

    public function getClass($queryData = [])
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;

        $query = T01tb::select('t01tb.class','t01tb.branch','t01tb.name','t01tb.branchname','t04tb.term','t01tb.process','t01tb.commission','t01tb.teaching','t04tb.sdate','t04tb.edate','t04tb.sponsor','m09tb.username');
        $query->join('t04tb', 't01tb.class', '=', 't04tb.class', 'INNER');

        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t01tb.class', 'like', '%'.$queryData['class'].'%');
        }

        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {
            $query->where('t04tb.term', 'like', '%'.$queryData['term']);
        }

        $query->leftjoin('m09tb','t04tb.sponsor','=','m09tb.userid');
        $data = $query->first();

        return $data;

    }
    /**
     * 取得課程表
     *
     * @return mixed
     */
    public function getCurriculumList($class,$term,$filter=NUll){
        $sql = "SELECT B.id,A.course,A.`name`,A.`date`,A.stime,A.etime,B.cname,B.idno,C.method1,C.method2,C.method3,C.mark,D.type
        FROM t06tb as A  /* 【t06tb 課程表資料檔】 */
        INNER JOIN t08tb as B  /* 【t08tb 擬聘講座資料檔】 */
        ON  A.class=B.class AND A.term=B.term AND A.course = B.course AND A.class = '$class'  AND A.term = '$term'
        LEFT JOIN t09tb as D
        ON A.class = D.class AND A.term = D.term AND A.course = D.course AND B.idno = D.idno
        LEFT JOIN t98tb as C  /* 【t98tb 講座教學教法資料檔】 */
        ON A.class = C.class AND A.term = C.term AND A.course = C.course AND B.idno = C.idno
        where D.type = '1' ";
        if($filter=='2') {
            $sql .="AND C.method1 <> '' or C.method2 <> '' or C.method3 <> ''  ";
        }elseif($filter=='3'){
            $sql .="AND C.method1 = '' AND C.method2 = '' AND C.method3 = ''  ";
        }
        $sql .=" ORDER BY B.id ";
        return DB::select($sql);
    }

    public function getMaterialList($queryData = []){
        $query = T49tb::select('t49tb.serno','t49tb.material','t49tb.total','t49tb.paiddate','t49tb.kind','s06tb.accname');
        $query->join('s06tb', 't49tb.kind', '=', 's06tb.acccode', 'INNER');
        // 預設排序
        $query->orderBy('serno', 'desc');
        $yerly = substr($queryData['class'],0,3);
        $query->where('t49tb.class',$queryData['class'])->where('t49tb.term',$queryData['term']);
        $query->where('s06tb.yerly',$yerly);

        if ( isset($queryData['duedate']) && $queryData['duedate']!='' ) {
            $star = $queryData['duedate'].'00';
            $end = $queryData['duedate'].'32';
            $query->whereBetween('duedate',array($star,$end));
        }

        if ( isset($queryData['paiddate']) && $queryData['paiddate']!='' ) {
            $query->where('paiddate',$queryData['paiddate']);
        }

        if ( $queryData['ispaid']=='2' ) { //未支付
            $query->where('paiddate','');
        }elseif($queryData['ispaid']=='3'){ //已支付
            $query->where('paiddate','<>','');
        }
        $data = $query->get()->toarray();
        return $data;
    }

    public function getMaterialListNew($queryData = []){
        $query = T49tb::select(DB::raw('SUBSTR(t49tb.class, 1, 3) as year'),'t49tb.serno','t49tb.material','t49tb.total','t49tb.paiddate','t49tb.kind','t49tb.applicant','m09tb.username','t01tb.name','t49tb.term');
        $query->leftJoin('m09tb', function($join)
        {
            $join->on('t49tb.applicant', '=', 'm09tb.userid');
        });
        $query->leftJoin('t01tb', function($join)
        {
            $join->on('t49tb.class', '=', 't01tb.class');
        });
        // 預設排序
        $query->orderBy('serno', 'desc');
        // $yerly = substr($queryData['class'],0,3);
        // $query->where('t49tb.class',$queryData['class'])->where('t49tb.term',$queryData['term']);
        // $query->where('s06tb.yerly',$yerly);

        if ( isset($queryData['duedate']) && $queryData['duedate']!='' ) {
            $star = $queryData['duedate'].'00';
            $end = $queryData['duedate'].'32';
            $query->whereBetween('duedate',array($star,$end));
        }

        if ( isset($queryData['paiddate']) && $queryData['paiddate']!='' ) {
            $query->where('paiddate',$queryData['paiddate']);
        }

        if ( isset($queryData['ispaid']) && $queryData['ispaid']!='' ) {
            if ( $queryData['ispaid']=='2' ) { //未支付
                $query->where('paiddate','');
            }elseif($queryData['ispaid']=='3'){ //已支付
                $query->where('paiddate','<>','');
            }
        }
        // $data = $query->get()->toarray();
        $data = $query->paginate($queryData['_paginate_qty']);

        foreach($data as & $row){
            $S06tb = S06tb::select('accname')->where('acccode',$row->kind)->where('yerly',$row->year)->first();
            if(isset($S06tb->accname)){
                $row->accname = $S06tb->accname;
            }else{
                $row->accname = '';
            }
        }
        // dd($data);
        return $data;
    }

    // 教法運用統計圖表
    public function getTeachWayList($data=[]){
        $sql = "SELECT
        B.type, /* 班別性質 */
        D.class,
        RTRIM(B.name) AS classname,
        D.term,D.course,
        IFNULL(G.method1,'') AS method1,
        IFNULL(G.method2,'') AS method2,
        IFNULL(G.method3,'') AS method3
        FROM t04tb A
        INNER JOIN t01tb B
        ON A.class = B.class
        AND B.type <> '13'
        AND B.teaching = 'Y'";
        if(isset($data['type']) && $data['type']!='0') {
            $sql .=" AND B.type = ".$data['type'];
        }

        $sql.=" INNER JOIN t06tb D
        ON A.class = D.class
        AND A.term = D.term
        INNER JOIN t08tb E
        ON D.class = E.class
        AND D.term = E.term
        AND D.course = E.course
        AND E.hire = 'Y'
        INNER JOIN t98tb G
        ON D.class = G.class
        AND D.term = G.term
        AND D.course = G.course
        AND E.idno = G.idno
        WHERE A.edate BETWEEN '".$data['sdate']."' AND '".$data['edate']."' AND 1 = (CASE WHEN G.mark = '' THEN 1
          WHEN G.mark IS NULL THEN 1 END  )";
        return DB::select($sql);
    }
    // 班別性質教法運用滿意度統計表
    public function getSatisfactionList($data=[]){
        $sql = "SELECT
        xx.class,
        xx.term,
        xx.course,
        xx.type,
        xx.method1,
        xx.method2,
        xx.method3,
        (
            CASE
                WHEN SUM(ans1cnt)=0 THEN NULL
                ELSE SUM(ans1)/CAST(SUM(ans1cnt) AS float)
            END
        ) AS ans1avg  /* 教學技法 */
        FROM
        (
            SELECT
            A.class,   /* 班級編號 */
            A.term,    /* 期別 */
            A.course,  /* 課程編號 */
            A.idno,    /* 身分證字號  */
            BB.method1,
            BB.method2,
            BB.method3,
            BB.type,
            (
              CASE A.ans1
               WHEN 5 THEN 100
               WHEN 4 THEN 80
               WHEN 3 THEN 60
               WHEN 2 THEN 40
               WHEN 1 THEN 20
               ELSE 0
              END
             ) AS ans1,      /* 教學技法 */
            (CASE WHEN A.ans1>0 THEN 1 ELSE 0 END) AS ans1cnt /* 教學技法分母 */
            FROM t56tb A
            INNER JOIN
            (
                SELECT
                B.type, /* 班別性質 */
                D.class,
                RTRIM(B.name) AS classname,
                D.term,
                D.course,
                F.idno,
                IFNULL(G.method1,'') AS method1,
                IFNULL(G.method2,'') AS method2,
                IFNULL(G.method3,'') AS method3
                FROM t04tb A
                INNER JOIN t01tb B
                ON A.class = B.class
                AND B.type <> '13'
                AND B.teaching = 'Y'
                INNER JOIN s01tb C
                ON B.type = RTRIM(C.code)
                AND C.type = 'K'
                INNER JOIN t06tb D
                ON A.class = D.class
                AND A.term = D.term
                INNER JOIN t08tb E
                ON D.class = E.class
                AND D.term = E.term
                AND D.course = E.course
                AND E.hire = 'Y'
                INNER JOIN m01tb F
                ON E.idno = F.idno
                INNER JOIN t98tb G
                ON D.class = G.class
                AND D.term = G.term
                AND D.course = G.course
                AND F.idno = G.idno
                WHERE A.edate BETWEEN '".$data['sdate']."' AND '".$data['edate']."'
                AND 1 = (
                         CASE
                          WHEN G.mark = ''    THEN 1
                          WHEN G.mark IS NULL THEN 1
                         END
                        )
                ORDER BY
                B.type,        /* 班別性質 */
                D.class,       /* 班別代碼 */
                D.term,        /* 期別 */
                D.date,        /* 日期 */
                D.stime,       /* 開始時間 */
                D.etime,       /* 結束時間 */
                D.course       /* 課程代碼 */
            ) BB
            ON A.class = BB.class
            AND A.term = BB.term
            AND A.course = BB.course
            AND A.idno = BB.idno
        ) AS xx
        GROUP BY xx.class,xx.term,xx.course,xx.idno";
        return DB::select($sql);

    }
}
