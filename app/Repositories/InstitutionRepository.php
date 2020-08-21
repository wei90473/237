<?php
namespace App\Repositories;

use App\Models\M13tb;


class InstitutionRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getInstitutionList($queryData = [])
    {
        $query = M13tb::select('organ', 'lname', 'type', 'division', 'sponsor1', 'telnoa1', 'telnob1', 'telnoc1', 'faxnoa1', 'faxnob1', 'address');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['organ', 'lname', 'type'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('organ', 'desc');
        }

        // 關鍵字
        if ( isset($queryData['organ']) && $queryData['organ']) {

            $query->where('organ', 'LIKE', '%'.$queryData['organ'].'%');
        }

        $data = $query->paginate($queryData['_paginate_qty']);

        return $data;
    }
}
