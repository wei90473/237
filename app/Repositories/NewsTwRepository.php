<?php
namespace App\Repositories;

use App\Models\T28tb;


class NewsTwRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getNewsTwList($queryData = [])
    {
        $query = T28tb::select('*');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['sdate', 'edate'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('sdate', 'desc');
        }

        // 關鍵字
        if ( isset($queryData['keyword']) && $queryData['keyword'] ) {

            $query->where('title', 'LIKE', '%'.$queryData['keyword'].'%');
        }

        // 分類
        if ( isset($queryData['type']) && $queryData['type'] ) {

            $query->where('type', $queryData['type']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
