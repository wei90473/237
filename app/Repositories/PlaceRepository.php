<?php
namespace App\Repositories;

use App\Models\M14tb;


class PlaceRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPlaceList($queryData = [])
    {
        $query = M14tb::select('*');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['site'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('site', 'desc');
        }
        // 場地編號

        if ( isset($queryData['site']) && $queryData['site']) {

            $query->where('site',$queryData['site']);
        }
        // 院區
        if ( isset($queryData['branch']) && $queryData['branch']) {

            $query->where('branch',$queryData['branch']);
        }

        // 場地類型

        if ( isset($queryData['type']) && $queryData['type']) {

            $query->where('type',$queryData['type']);
        }
        // 場地名稱

        if ( isset($queryData['name']) && $queryData['name']) {

            $query->where('name','like','%'.$queryData['name'].'%');
        }
        // 關鍵字
        // if ( isset($queryData['keyword']) && $queryData['keyword']) {

        //     $query->where(function ($query) use ($queryData) {
        //         $query->where('site', 'like', '%'.$queryData['keyword'].'%')
        //             ->orwhere('name', 'like', '%'.$queryData['keyword'].'%');
        //     });
        // }
        // 關鍵字
        

        $data = $query->paginate($queryData['_paginate_qty']);

        return $data;
    }
}
