<?php
namespace App\Repositories;

use App\Models\T53tb;
use Auth;
use DB;


class TrainingSurveyRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTrainingSurveyList($queryData = [])
    {
        $query = T53tb::select('id', 'class', 'term', 'times', 'copy');

        $query->where('times', '');

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
        SELECT  X.class, RTRIM(X.name) AS name 
        FROM ( SELECT A.class, A.name, 0 AS sort 
        FROM t01tb A 
        INNER JOIN t04tb B ON A.class=B.class
        INNER JOIN m09tb C  ON B.sponsor=C.userid
        WHERE UPPER(B.sponsor)='".$uesr."'
        AND A.type<>'13' 
        GROUP BY A.class,A.name 
        UNION ALL
        SELECT class,name,1 AS sort FROM t01tb  WHERE type<>'13' ) X 
        ORDER BY X.sort ASC,X.class DESC";

        return DB::select($sql);
    }

    /**
     * 取得未選取的課程
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourseNotSelectList($class, $term)
    {
        $sql = "
                  
        SELECT IFNULL(t05.name,'') as unitname,
        IFNULL(t06.date,'') as date,
        IFNULL(t06.name,'') as coursename,
        IFNULL(m01.cname,'') as cname,
        IFNULL(t06.unit,'') as unit,
        IFNULL(t06.course,'') as course
        FROM t04tb t04
        INNER JOIN t06tb t06 on t06.class=t04.class
        and t06.term=t04.term
        LEFT OUTER JOIN t09tb t09 on t09.class=t06.class
        and t09.term=t06.term
        and t09.course=t06.course
        and t09.type='1'
        LEFT OUTER JOIN t05tb t05 ON t05.class=t06.class
        AND t05.term=t06.term 
        AND t05.unit=t06.unit
        LEFT OUTER JOIN m01tb m01 on m01.idno=t09.idno
        WHERE t04.class =(Left('".$class."', 6))
        AND t04.term='".$term."'
            AND  t06.course not in
            (SELECT course FROM t58tb
            WHERE class=(Left('".$class."', 6))
            AND term='".$term."' )
        
        ORDER BY t06.unit";

        return DB::select($sql);
    }

    /**
     * 取得已選取的課程
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourseSelectList($class, $term)
    {
        $sql = "
          SELECT  IFNULL(t05.name,'') as unitname,
          IFNULL(t06.date,'') as classdate,
          IFNULL(t06.name,'') as classname,
          IFNULL(m01.cname,'') as cname,
          IFNULL(t06.unit,'') as unit,
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
                                      and t09.type='1'
          LEFT OUTER JOIN m01tb m01 on m01.idno=t09.idno
          WHERE t58.class=(Left('".$class."', 6))
            AND t58.term='".$term."' 
          ORDER BY t58.sequence";

        return DB::select($sql);
    }

    /**
     * 取得所有課程(新增用)
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourseAllList($class, $term)
    {
        $course = "
        SELECT IFNULL(t05.name,'') as unitname,
        IFNULL(t06.date,'') as date,
        IFNULL(t06.name,'') as coursename,
        IFNULL(m01.cname,'') as cname,
        IFNULL(t06.unit,'') as unit,
        IFNULL(t06.course,'') as course
        FROM t04tb t04
        INNER JOIN t06tb t06 on t06.class=t04.class
        and t06.term=t04.term
        LEFT OUTER JOIN t09tb t09 on t09.class=t06.class
        and t09.term=t06.term
        and t09.course=t06.course
        and t09.type='1'
        LEFT OUTER JOIN t05tb t05 ON t05.class=t06.class
        AND t05.term=t06.term 
        AND t05.unit=t06.unit
        LEFT OUTER JOIN m01tb m01 on m01.idno=t09.idno
        WHERE t04.class =(Left('".$class."', 6))
        AND t04.term='".$term."'
        ORDER BY t06.unit";

        return DB::select($course);
    }
}
