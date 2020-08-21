<?php
namespace App\Repositories;

use App\Models\T38tb;


class SessionRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSessionList($queryData = [])
    {
        $query = T38tb::select('meet', 'serno', 'name', 'cnt', 'sdate', 'edate');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['meet', 'meet'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('meet');
            $query->orderBy('serno');
        }

        // 會議代號
        if ( isset($queryData['meet']) && $queryData['meet'] ) {

            $query->where('meet', 'LIKE', '%'.$queryData['meet'].'%');
        }

        // 會議名稱
        if ( isset($queryData['name']) && $queryData['name'] ) {

            $query->where('name', 'like', '%'.$queryData['name'].'%');
        }

        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {

            $query->where('sdate', 'like',  $queryData['yerly'].'%');
        }

        // 開始時間
        if ( isset($queryData['sdate']) && $queryData['sdate'] ) {
            //$queryData['sdate'] =  ltrim( date("Ymd",strtotime("-1911 year",strtotime( $queryData['sdate']))),"0");
            $query->where('sdate','>', $queryData['sdate']);
        }

        // 結束時間
        if ( isset($queryData['edate']) && $queryData['edate'] ) {
           // $queryData['edate'] =  ltrim( date("Ymd",strtotime("-1911 year",strtotime( $queryData['edate']))),"0");
            $query->where('edate','<', $queryData['edate']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
