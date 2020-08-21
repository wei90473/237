<?php
namespace App\Repositories;

use App\Models\M21tb;
use DB;


class PasswordMaintenanceRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPasswordMaintenanceList($queryData = [])
    {
        $query = M21tb::select(
            'm21tb.status as m21status',
            'userpsw',
            'm21tb.pswerrcnt',
            'm21tb.enrollorg',
            'm21tb.userid',
            'm21tb.username',
            DB::raw('RTRIM(m21tb.selfid) AS account'),
            'm17tb.status as m17status',
            'm17tb.enrollname'
        );

        $query->join('m17tb', 'm17tb.enrollorg', '=', 'm21tb.enrollorg');

        // 機關代號
        if ( isset($queryData['enrollorg']) && $queryData['enrollorg'] ) {

            $query->where('m21tb.enrollorg', 'LIKE', '%'.$queryData['enrollorg'].'%');
        }

        // 身分證字號
        if ( isset($queryData['userid']) && $queryData['userid'] ) {

            $query->where('m21tb.userid', 'LIKE', '%'.$queryData['userid'].'%');
        }

        // 使用狀況
        if ( isset($queryData['status']) && $queryData['status'] ) {

            $query->where('m21tb.status', $queryData['status']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
