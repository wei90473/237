<?php
namespace App\Repositories;

use App\Models\M12tb;


class HolidayRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getHolidayList($queryData = [])
    {
        $query = M12tb::select('holiday', 'date');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['holiday', 'date'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('date', 'desc');
        }

        // 關鍵字
        if ( isset($queryData['keyword']) && $queryData['keyword']) {

            $query->where('holiday', 'LIKE', '%'.$queryData['keyword'].'%');
        }

        // 年度
        if ( isset($queryData['year']) && $queryData['year']) {

            $queryData['year'] = str_pad($queryData['year'] ,3,'0',STR_PAD_LEFT);

            $query->where('date', 'LIKE', $queryData['year'].'%');
        }

        $data = $query->paginate($queryData['_paginate_qty']);

        return $data;
    }
}
