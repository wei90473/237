<?php
namespace App\Repositories;

use App\Models\T01tb;


class SiteRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteList($queryData = [])
    {
        $query = T01tb::select('t01tb.class', 'name', 'publish', 'publish3', 'pubsdate', 'pubedate', 'term');

        $query->join('t04tb', 't04tb.class', '=', 't01tb.class');

        $query->where('t01tb.type', 13);

        $query->orderBy('t01tb.class');

        // 關鍵字
        if ( isset($queryData['keyword']) && $queryData['keyword'] ) {

            $query->where('t01tb.name', 'LIKE', '%'.$queryData['keyword'].'%');
        }

        // 年度
        if ( isset($queryData['year']) && $queryData['year'] ) {

            $queryData['year'] = str_pad($queryData['year'] ,3,'0',STR_PAD_LEFT);

            $query->where('t01tb.class', 'LIKE', $queryData['year'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
