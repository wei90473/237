<?php
namespace App\Repositories;

use App\Models\M22tb;
use DB;


class PasswordMaintenanceUserRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPasswordMaintenanceUserList($queryData = [])
    {
        $query = M22tb::select(
            'm22tb.status as m22status',
            'm17tb.status as m17status',
            'm22tb.pswerrcnt',
            'm22tb.userorg as enrollorg',
            'm17tb.enrollname',
            'm22tb.userid',
            DB::raw('RTRIM(m22tb.selfid) AS account'),
            DB::raw('RTRIM(m22tb.lname) + RTRIM(m22tb.fname) as username'),
            DB::raw('case when m22tb.usertype1=\'Y\' then \'＊\' else \'\' end as usertype1'),
            DB::raw('case when m22tb.usertype2=\'Y\' then \'＊\' else \'\' end as usertype2'),
            DB::raw('case when m22tb.usertype3=\'Y\' then \'＊\' else \'\' end as usertype3')

        );

        $query->join('m17tb', 'm17tb.enrollorg', '=', 'm22tb.userorg', 'left');

        // 身分證字號
        if ( isset($queryData['userid']) && $queryData['userid'] ) {

            $query->where('m22tb.userid', 'LIKE', '%'.$queryData['userid'].'%');
        }

        // 使用狀況
        if ( isset($queryData['status']) && $queryData['status'] ) {

            $query->where('m22tb.status', $queryData['status']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
