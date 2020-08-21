<?php
namespace App\Repositories;

use App\Models\T46tb;
use Auth;
use DB;

class StayListRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getStayListList($queryData = [])
    {
        if ($queryData['class'] == '' || $queryData['term'] == '' || $queryData['type'] == '') {

            return T46tb::where(DB::raw('0'))->paginate(10);
        }

        $query = T46tb::select('serno', 'date', 'cname', 'type');

        $query->orderBy('date');

        // 班別
        $query->where('class', $queryData['class']);

        // 期別
        $query->where('term', $queryData['term']);

        // 選項(全部、早餐、住宿)
        $query->where('type', $queryData['type']);

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
            SELECT X.class,RTRIM(X.name) AS name
            FROM (SELECT A.class, A.name,0 AS sort FROM t01tb A 
            INNER JOIN t04tb B ON A.class=B.class
            INNER JOIN m09tb C ON B.sponsor=C.userid  WHERE A.type<>'13' 
            AND UPPER(B.sponsor)=':user' GROUP BY A.class,A.name 
            UNION ALL
            SELECT class,   name, 1 AS sort FROM t01tb) X 
            ORDER BY X.sort ASC,X.class DESC ";

        return DB::select($sql, ['user' => $uesr]);
    }
}
