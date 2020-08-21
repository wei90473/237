<?php
namespace App\Repositories;

use App\Models\s06tb;
use App\Models\T33tb;
use App\Models\T34tb;


class FundingRepository
{   

    /**
     * 取得年分
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function gets06tbList($queryData = [])
    {
        $query = s06tb::select('*');

        $query->orderBy('yerly');


        // 關鍵字
        if ( isset($queryData['year']) && $queryData['year'] ) {

            $query->where('subjectid', 'LIKE', '%'.$queryData['year'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getT33tbList($queryData = [])
    {
        $query = T33tb::select('*');

        $query->orderBy('subjectid');
        $query->orderBy('date');

        // 關鍵字
        if ( isset($queryData['keyword']) && $queryData['keyword'] ) {

            $query->where('subjectid', 'LIKE', '%'.$queryData['keyword'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getT34tbList($queryData = [])
    {
        $query = T34tb::select('*');

        $query->orderBy('subjectid');
        $query->orderBy('date');

        // 關鍵字
        if ( isset($queryData['keyword']) && $queryData['keyword'] ) {

            $query->where('subjectid', 'LIKE', '%'.$queryData['keyword'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
