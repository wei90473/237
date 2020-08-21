<?php
namespace App\Repositories;

use App\Models\T01tb;


class TrainRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTrainList($queryData = [])
    {
        $query = T01tb::select('publish', 'class', 'name');

        // 排序
        $query->orderBy('class', 'desc');

        // 年度
        if ( isset($queryData['year']) && $queryData['year'] ) {

            $queryData['year'] = str_pad($queryData['year'] ,3,'0',STR_PAD_LEFT);

            $query->where('class', 'LIKE', $queryData['year'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
