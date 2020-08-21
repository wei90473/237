<?php
namespace App\Repositories;

use App\Models\S01tb;


class SystemCodeRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSystemCodeList($queryData = [])
    {
        $query = S01tb::select('*');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['type'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        }

        // 關鍵字
        if ( isset($queryData['keyword']) && $queryData['keyword'] ) {

            $query->where(function ($query) use ($queryData) {
                $query->where('code', 'like', '%'.$queryData['keyword'].'%')
                    ->orwhere('name', 'like', '%'.$queryData['keyword'].'%');
            });
        }

        // 分類
        if ( isset($queryData['type']) && $queryData['type'] ) {

            $query->where('type', $queryData['type']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
