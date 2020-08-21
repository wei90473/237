<?php
namespace App\Repositories;

use App\Models\T46tb;
use Auth;
use DB;

class ClassMaterialRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassMaterialList($queryData = [])
    {
        if ($queryData['class'] == '' || $queryData['term'] == '') {

            return array();
        }

        $sql = "
         SELECT A.class,A.term,A.course,A.idno,B.date
         ,(CASE WHEN B.date<>'' THEN CAST(CAST(SUBSTRING(B.date,1,3) AS signed) AS char(4))+'/'+ 
         SUBSTRING(B.date,4,2)+'/'+ SUBSTRING(B.date,6,2)  ELSE '' END) AS 上課日期, 
        B.name AS course_name,A.cname AS teacher FROM t08tb A 
        INNER JOIN t06tb B ON A.class=B.class AND A.term=B.term
         AND A.course=B.course WHERE A.hire='Y' 
        AND A.class='".$queryData['class']."' AND A.term='".$queryData['term']."'
         ORDER BY B.date, B.stime";


        return DB::select($sql);
    }

    /**
     * 取得已選取的教材
     *
     * @param $queryData
     * @return mixed
     */
    public function selectMaterial($queryData) {

        if ($queryData['class'] == '' || $queryData['term'] == '') {

            return T46tb::where(DB::raw('0'))->paginate(10);
        }

        $sql = "
            SELECT A.class,A.term,A.course, A.idno, A.handoutno,B.handout
            FROM t10tb A 
            LEFT JOIN m08tb B ON A.handoutno=B.serno 
            WHERE A.handoutno<>-1
            AND A.class='".$queryData['class']."' AND A.term='".$queryData['term']."'";

        return DB::select($sql);
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
            SELECT X.class,RTRIM(X.name) AS name
            FROM (SELECT A.class,A.name,0 AS sort FROM t01tb A 
            INNER JOIN t04tb B ON A.class=B.class 
            INNER JOIN m09tb C ON B.sponsor=C.userid  
            WHERE A.type<>'13' AND UPPER(B.sponsor)=':user'
            GROUP BY A.class,A.name
            UNION ALL  
            SELECT class,name, 1 AS sort FROM t01tb ) X
            ORDER BY X.sort ASC,X.class DESC ";

        return DB::select($sql, ['user' => $uesr]);
    }
}
