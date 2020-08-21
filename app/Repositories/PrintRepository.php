<?php
namespace App\Repositories;

use App\Models\T08tb;


class PrintRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPrintList($queryData = [])
    {
        $query = T08tb::select('t08tb.id', 't08tb.hire', 't08tb.course', 't08tb.cname', 't08tb.dept', 't08tb.position', 't08tb.liaison', 't08tb.offtela1', 't08tb.offtelb1', 't08tb.offfaxa', 't08tb.offfaxb', 't08tb.term', 't08tb.course');

        // $query->join('t06tb', function($join)
        // {
        //     $join->on('t06tb.class', '=', 't08tb.class');
        //     $join->on('t06tb.term', '=', 't08tb.term');
        //     $join->on('t06tb.course', '=', 't08tb.course');
        // });

        // // 排序
        // if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

        //     if (in_array($queryData['_sort_field'], [])) {

        //         $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
        //     }
        // } else {
        //     // 預設排序
        //     $query->orderBy('id', 'desc');
        // }

        // // 班別
        // if ( isset($queryData['class']) && $queryData['class'] ) {
        //     $query->where('t08tb.class', $queryData['class']);
        // }

        // // 期別
        // if ( isset($queryData['term']) && $queryData['term'] ) {

        //     // $queryData['term'] = str_pad($queryData['term'] ,2,'0',STR_PAD_LEFT);

        //     $query->where('t08tb.term', $queryData['term']);
        // }

        // // 遴聘與否
        // if ( isset($queryData['hire']) && $queryData['hire'] ) {

        //     $query->where('t08tb.hire', $queryData['hire']);
        // }
        
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
