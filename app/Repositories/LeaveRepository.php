<?php
namespace App\Repositories;

use App\Models\T14tb;
use Auth;
use DB;


class LeaveRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLeaveList($queryData)
    {
        if ($queryData['class'] == '' || $queryData['term'] == '') {

            return array();
        }

        $sql = "
            SELECT t13tb.no AS no, rtrim(m02tb.cname) AS cname, t14tb.*, t13tb.dept 
            FROM t13tb
            JOIN t14tb ON t13tb.idno = t14tb.idno and t13tb.class = t14tb.class and t13tb.term = t14tb.term
            JOIN m02tb ON t14tb.idno = m02tb.idno
            WHERE t13tb.class = ? AND t13tb.term = ?
            ORDER BY t13tb.no,t14tb.sdate";

        return DB::select($sql, [$queryData['class'], $queryData['term']]);
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
            SELECT
                X.class,
                X.type,
                RTRIM(X. name) AS name
            FROM
                (
                    SELECT
                        A.class,
                        A.type,
                        A.name,
                        0 AS sort
                    FROM
                        t01tb A
                    INNER JOIN t04tb B ON A.class = B.class
                    INNER JOIN m09tb C ON B.sponsor = C.userid
                    WHERE
                        A.type <> '13'
                    AND UPPER(B.sponsor) = ':user'
                        GROUP BY
                            A.class,
                            A.type,
                            A.name
                        UNION ALL
                            SELECT
                                class,
                                type,
                                NAME,
                                1 AS sort
                            FROM
                                t01tb
                    ) X
                    ORDER BY
                        X.sort ASC,
                        X.class DESC";

        return DB::select($sql, ['user' => $uesr]);
    }
}
