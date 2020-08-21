<?php
namespace App\Repositories;

use App\Models\Users;
use App\Models\M22tb;
use DB;


class UsersRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getUsersList($queryData = [])
    {
        $query = M22tb::select('userid', 'lname', 'fname', 'offtela1', 'offtelb1', 'offtelc1', 'mobiltel', 'email');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['userid'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('userid', 'desc');
        }

        // 身分證字號
        if ( isset($queryData['userid']) && $queryData['userid'] ) {

            $query->where('userid', 'LIKE', '%'.$queryData['userid'].'%');
        }

        // 姓名
        if ( isset($queryData['name']) && $queryData['name'] ) {

            $query->where(DB::raw('CONCAT(`lname`, `fname`)'), 'LIKE', '%'.$queryData['name'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
