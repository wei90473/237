<?php
namespace App\Repositories;

use App\Models\License_plate_setting;
use App\Models\Room;
use App\Models\Car_fare;

class Parameter_settingRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getParameter_setting1List($queryData = [])
    {
        $query = License_plate_setting::select('id', 'call', 'name', 'license_plate');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('call', 'asc');
        }

        // 班號
        if ( isset($queryData['call']) && $queryData['call'] ) {
            $query->where('call', 'LIKE', '%'.$queryData['call'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    public function getParameter_setting2List($queryData = [])
    {
        $query = Room::select('id', 'room_number');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('room_number', 'asc');
        }

        // 班號
        if ( isset($queryData['room_number']) && $queryData['room_number'] ) {
            $query->where('room_number', $queryData['room_number']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    public function getParameter_setting3List($queryData = [])
    {
        $query = Car_fare::select('id', 'county', 'area', 'fare');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('id', 'asc');
        }

        // 班號
        if ( isset($queryData['county']) && $queryData['county'] ) {
            $query->where('county', $queryData['county']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }


}
