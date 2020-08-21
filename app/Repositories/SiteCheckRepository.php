<?php
namespace App\Repositories;

use App\Models\T38tb;
use Auth;
use DB;


class SiteCheckRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteCheckList($queryData = [])
    {
        $query = T38tb::select(
            'id',
            'prove',
            DB::raw('CONCAT(meet,serno) as no'),
            DB::raw( "CASE WHEN prove='W' THEN '審核中' WHEN prove='Y' THEN '同意' WHEN prove='R' THEN '退回' END AS status"),
            'name as unit',
            'activity As name'
        );

        $query->where(DB::raw('LEFT(meet,1)'), 'I');

        $query->whereBetween('applydate', array($queryData['start_date'], $queryData['end_date']));

        // 一定要有搜尋
        if ( ! $queryData['start_date'] ||  ! $queryData['end_date']) {

            $query->where('id', 'N');
        }

        if ($queryData['status'] == '2') {
            // 已審核
            $query->where(function ($query) {
                $query->where('prove', 'Y')
                    ->orwhere('prove', 'R');
            });
        } else {
            // 未審核
            $query->where('prove', 'W');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
