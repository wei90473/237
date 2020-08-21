<?php
namespace App\Repositories;

use App\Models\T13tb;
use Auth;
use DB;


class CertificationRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getCertificationList($queryData = [])
    {
        if ($queryData['class'] == '' || $queryData['term'] == '') {

            return array();
        }

        $sql = "
             SELECT  A.class, A.term,  A.degree,  A.summary,  A.enroll,  A.validdate,A.county,A.site,A.sdate,
            A.edate,A.credit,A.unit,A.restriction,A.lodging,A.meal,A.upload2,A.grade,A.leave,A.file1,
            A.file2,A.file3,A.file4,A.file5,A.remark
            ,IFNULL(( SELECT category+ ''+RTRIM(name) FROM s03tb  WHERE category<>''  AND category=B.category),'') AS category,
             B.target,C.quota,CASE WHEN B.special='Y' THEN '其他' WHEN B.type='13' THEN CAST( C.fee AS char(10)) Else '0'  END As fee 
             FROM t47tb A 
             INNER JOIN t01tb B ON A.class = B.class 
             INNER JOIN t04tb C ON A.class = C.class AND A.term = C.term 
             WHERE A.class='".$queryData['class']."' AND  A.term= '".$queryData['term']."'";

        return DB::select($sql);
    }

    public function getCertificationRowData($id)
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
    public function getClassList()
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;

        $sql = "
            SELECT X.class,RTRIM(X.name) AS name
        FROM (SELECT A.class,A.name,0 AS sort
        FROM t01tb A 
        INNER JOIN t04tb B ON A.class=B.class
        INNER JOIN m09tb C ON B.sponsor=C.userid 
        WHERE A.type<>'13'AND UPPER(B.sponsor)=UCase('".$uesr."')
        GROUP BY A.class,A.name
        UNION ALL  
        SELECT class,name,1 AS sort
        FROM t01tb ) X 
         ORDER BY X.sort ASC,X.class DESC";

        return DB::select($sql);
    }
}
