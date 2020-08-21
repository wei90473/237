<?php
namespace App\Repositories;

use App\Models\Class_group;


class ClassGroupRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 
     * @return mixed
     */
    public function getGroupList($queryData = [])
    {
        $query = Class_group::select('id','groupid','class_group', 'class', 'name');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['id'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('id', 'desc');
        }

        // 群組名稱
        if ( isset($queryData['class_group']) && $queryData['class_group']) {

            $query->where('class_group', 'LIKE', '%'.$queryData['class_group'].'%');
        }
        // 班別
        if ( isset($queryData['class']) && $queryData['class']) {

            $query->where('class', 'LIKE', '%'.$queryData['class'].'%');
        }
        // 班別名稱
        if ( isset($queryData['name']) && $queryData['name']) {

            $query->where('name', 'LIKE', '%'.$queryData['name'].'%');
        }
        
        $query->groupby('class_group');

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
