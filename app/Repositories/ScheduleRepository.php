<?php
namespace App\Repositories;

use App\Models\T01tb;
use App\Models\T03tb;
use App\Models\T04tb;

class ScheduleRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassList($queryData = [])
    {
        $query = T04tb::select('t04tb.class', 't01tb.name','t04tb.term','t04tb.quota','t04tb.site','t04tb.sdate','t04tb.edate');
        $query->join('t01tb', 't01tb.class', '=', 't04tb.class');
        $query->where('t04tb.class', '!=', '');
       
        // 年度
         if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
 
            $queryData['yerly'] = str_pad($queryData['yerly'] ,3,'0',STR_PAD_LEFT);
            $query->where('t04tb.class', 'LIKE', $queryData['yerly'].'%');
        }

      
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
}
