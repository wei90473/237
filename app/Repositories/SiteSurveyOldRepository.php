<?php
namespace App\Repositories;

use App\Models\T73tb;
use DB;


class SiteSurveyOldRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteSurveyOldList($queryData = [])
    {
        if ($queryData['year'] == '' || $queryData['times'] == '') {

            return array();
        }

        $queryData['year'] = str_pad($queryData['year'] ,3,'0',STR_PAD_LEFT);

        $sql = "
            SELECT  id, year ,  times , serno , q1, q2, q3,  q4,  q5,  q6,  q7,  q8, q9,  q10,  
            (CASE  WHEN q1=0 THEN '' ELSE CAST(q1 AS char(4)) END)AS  q1, 
             (CASE  WHEN q2=0 THEN '' ELSE CAST(q2 AS char(4)) END)AS q2, 
            (CASE  WHEN q3=0 THEN '' ELSE CAST(q3 AS char(4)) END)AS  q3, 
            (CASE  WHEN q4=0 THEN '' ELSE CAST(q4 AS char(4)) END)AS  q4,
            (CASE  WHEN q5=0 THEN '' ELSE CAST(q5 AS char(4)) END)AS  q5, 
            (CASE  WHEN q6=0 THEN '' ELSE CAST(q6 AS char(4)) END)AS  q6,
            (CASE  WHEN q7=0 THEN '' ELSE CAST(q7 AS char(4)) END)AS  q7,
             (CASE  WHEN q8=0 THEN '' ELSE CAST(q8 AS char(4)) END)AS q8, 
            (CASE  WHEN q9=0 THEN '' ELSE CAST(q9 AS char(4)) END)AS  q9, 
             (CASE  WHEN q10=0 THEN '' ELSE CAST(q10 AS char(4)) END)AS q10, 
             dept ,extdept,site1,site2,site3,site4,applycnt,apply, extapply, duty,comment 
            FROM t73tb 
            WHERE year='".$queryData['year']."' AND  times='".$queryData['times']."'  
            ORDER BY serno";

        return DB::select($sql);
    }
}
