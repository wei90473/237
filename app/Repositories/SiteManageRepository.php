<?php
namespace App\Repositories;

use App\Models\T01tb;


class SiteManageRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteManageList($queryData = [])
    {
        $query = T01tb::select('class','name','style','branch','kind','quota','trainhour','period','branchcode');
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('class', 'desc');
        }
        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {

            $query->where('class','like', $queryData['yerly'].'%');
        }
        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {

            $query->where('class', 'like', '%'.$queryData['class'].'%');
        }
        // 班別名稱
        if ( isset($queryData['name']) && $queryData['name'] ) {

            $query->where('name', 'like', '%'.$queryData['name'].'%');
        }
        // 辦班院區
        if ( isset($queryData['branch']) && $queryData['branch'] ) {

            $query->where('branch', $queryData['branch']);
        }
        // 課程分類
        if ( isset($queryData['classtype']) && $queryData['classtype'] ) {

            $query->where('classtype', $queryData['classtype']);
        }else{
            $query->where('classtype','<>','');
        }
        
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
    public function getClassesdata($year){
        if(is_null($year)) return false;

        $query = T01tb::where('class','like', $year.'%')->where('classtype','<>','')->get()->toArray();
        return $query;
    }
}
