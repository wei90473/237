<?php
namespace App\Repositories;

use App\Models\method;


class TeachingmethodRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getList($queryData = [])
    {
        $query = method::select('id','method' ,'name' ,'mode','yerly','modifytime' );

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['id'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('id', 'desc');
        }

        // 編號
        if ( isset($queryData['id']) && $queryData['id'] ) {
 
            $query->where('id', 'like', '%'.$queryData['id'].'%');
        }
        // 教學教法名稱
        if ( isset($queryData['name']) && $queryData['name'] ) {

            $query->where('name', 'like', '%'.$queryData['name'].'%');
        }

        // 狀態
        if ( isset($queryData['mode']) && $queryData['mode'] ) {

            $query->where('mode', $queryData['mode']);
        }

        // 創建年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {

            $query->where('yerly', $queryData['yerly']);
        }
        $data = $query->paginate(10);

        return $data;
    }
}
