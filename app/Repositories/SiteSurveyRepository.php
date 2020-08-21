<?php
namespace App\Repositories;

use App\Models\T73tb;


class SiteSurveyRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteSurveyList($queryData = [])
    {
        $query = T73tb::select('id', 'year', 'year', 'times', 'serno');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['year', 'year', 'times'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('year', 'desc');
            $query->orderBy('times', 'desc');
            $query->orderBy('serno', 'asc');
        }

        // 年度
        if ( isset($queryData['year']) && $queryData['year'] ) {

            $queryData['year'] = str_pad($queryData['year'] ,3,'0',STR_PAD_LEFT);

            $query->where('year', 'like', '%'.$queryData['year'].'%');

        }

        // 第幾次調查
        if ( isset($queryData['times']) && $queryData['times'] ) {

            $query->where('times', $queryData['times']);

        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
