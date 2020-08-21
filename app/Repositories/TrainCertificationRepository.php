<?php
namespace App\Repositories;

use App\Models\T13tb;
use Auth;
use DB;


class TrainCertificationRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTrainCertificationList($queryData = [])
    {
        if ($queryData['class'] == '' || $queryData['term'] == '') {

            return array();
        }

        $sql = "
            Select t13tb.no as no,ifnull(m02tb.cname,'') as cname,ifnull(t15tb.totscr,0) as totscr,t13tb.pass as pass
            from t13tb
            LEFT OUTER JOIN m02tb ON t13tb.idno = m02tb.idno
            LEFT OUTER JOIN t15tb ON t13tb.idno = t15tb.idno
            and t15tb.class =(Left(Trim('".$queryData['class']."'), 6))
            and t15tb.term=(Trim('".$queryData['term']."'))
            where t13tb.class =(Left(Trim('".$queryData['class']."'), 6))
            and t13tb.term=(Trim('".$queryData['term']."'))
            and t13tb.status='1'
            ORDER BY t13tb.no";

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
