<?php
namespace App\Repositories;

use App\Models\T13tb;
use Auth;
use DB;


class DigitalRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getDigitalList($queryData = [])
    {
        if ($queryData['class'] == '' || $queryData['term'] == '') {

            return array();
        }

        $sql = "
            SELECT A.no,RTRIM(B.cname) AS cname, A.elearning
            FROM t13tb A 
            INNER JOIN m02tb B ON A.idno=B.idno 
            WHERE class='".$queryData['class']."' AND term='".$queryData['term']."'  AND A.status='1' ORDER BY A.no";

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
