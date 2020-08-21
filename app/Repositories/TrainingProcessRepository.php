<?php
namespace App\Repositories;

use App\Models\T59tb;
use DB;
use Auth;

class TrainingProcessRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTrainingProcessList($queryData = [])
    {
        $query = T59tb::select(
            'id',
            'class',
            'term',
            'serno',
            'comment',
            'addcourse',
            'delcourse',
            DB::raw('(CASE  WHEN wholeval=0 THEN \'\' ELSE CAST(wholeval AS char(4)) END) AS  wholeval'),
            DB::raw('(CASE  WHEN willing=0 THEN \'\' ELSE CAST(willing AS char(4)) END) AS  willing')
        );

        // 預設排序
        $query->orderBy('class', 'desc');

        // 一定要有搜尋
        if ($queryData['class'] && $queryData['term']) {

            $query->where('class', $queryData['class']);

            $query->where('term', $queryData['term']);

        } else {
            $query->where('class', 'N');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

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

        $sql = "
        SELECT A.class, A.name, 0 AS sort 
        FROM t01tb A 
        INNER JOIN t04tb B ON A.class=B.class
        INNER JOIN m09tb C  ON B.sponsor=C.userid
        WHERE UPPER(B.sponsor)='".$uesr."'
        AND A.type<>'13' 
        GROUP BY A.class,A.name";

        return DB::select($sql);
    }

    public function getCourseForCreate($class, $term)
    {
        $sql = '
          SELECT  IFNULL(t05.name,\'\') as unitname,
          IFNULL(t06.date,\'\') as classdate,
          IFNULL(t06.name,\'\') as classname,
          IFNULL(m01.cname,\'\') as cname,
          IFNULL(t06.unit,\'\') as unit,
          t58.course
          FROM t58tb t58
          LEFT OUTER JOIN t06tb t06 on t06.class=t58.class
                                   AND t06.term=t58.term
                                   AND t06.course=t58.course
          LEFT OUTER JOIN t05tb t05 ON t05.class=t06.class
                                   AND t05.term=t06.term 
                                   AND t05.unit=t06.unit
          LEFT OUTER JOIN t09tb t09 on t09.class=t06.class
                                      and t09.term=t06.term
                                      and t09.course=t06.course
                                      and t09.type=\'1\'
          LEFT OUTER JOIN m01tb m01 on m01.idno=t09.idno
          WHERE t58.class=(Left(:class, 6))
            AND t58.term=:term 
          ORDER BY t58.sequence';

        return DB::select($sql, ['class' => $class, 'term' => $term]);
    }

    /**
     * 取得課程
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourse($class, $term, $serno)
    {
        $sql = "
        SELECT B.id, A.sequence AS no,C.name AS coursename,(CASE IfNULL(B.ans,0) WHEN 0  THEN '' ELSE CAST(B.ans AS char(4)) END) AS score,
        A.class,A.term, A.course, IfNULL(B.serno,'')AS serno, IfNULL(B.ans,0) AS ans
        FROM t58tb A
        LEFT JOIN t60tb B
        ON A.class=B.class  AND A.term=B.term AND A.course=B.course
        INNER JOIN t06tb C  ON A.class=C.class AND A.term=C.term AND A.course=C.course
        WHERE A.class=:class
        AND  A.term=:term
        AND  B.serno=:serno
        ORDER BY A.sequence";

        return DB::select($sql, ['class' => $class, 'term' => $term, 'serno' => $serno]);
    }
}
