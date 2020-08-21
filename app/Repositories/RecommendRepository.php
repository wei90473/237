<?php
namespace App\Repositories;

use App\Models\M17tb;


class RecommendRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getRecommendList($queryData = [])
    {
        $query = M17tb::select('m17tb.enrollorg', 'm17tb.enrollname', 'm17tb.status', 'm13tb.lname');

        $query->join('m13tb', 'm13tb.organ', '=', 'm17tb.organ', 'left');

        // 預設排序
        $query->orderBy('m17tb.enrollorg', 'desc');

        // 薦送機關代碼
        if ( isset($queryData['enrollorg']) && $queryData['enrollorg'] ) {

            $query->where('m17tb.enrollorg', 'like', '%'.$queryData['enrollorg'].'%');
        }

        // 主管機關代碼
        if ( isset($queryData['organ']) && $queryData['organ'] ) {

            $query->where('m17tb.organ', 'like', '%'.$queryData['organ'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
