<?php
namespace App\Repositories;

use App\Models\T77tb;


class PollRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPollList($queryData = [])
    {
        $query = T77tb::select('serno', 'subject', 'sdate', 'edate');

        // 預設排序
        $query->orderBy('sdate', 'desc');

        // 關鍵字
        if ( isset($queryData['keyword']) && $queryData['keyword'] ) {

            $query->where('subject', 'LIKE', '%'.$queryData['keyword'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
