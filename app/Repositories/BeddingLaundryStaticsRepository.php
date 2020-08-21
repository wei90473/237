<?php
namespace App\Repositories;

use DB;
use App\Models\Edu_unitset;
use App\Models\T04tb;
use App\Models\T13tb;

class BeddingLaundryStaticsRepository
{
    /**
     * 取得寢具洗滌數量統計表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getBeddingLaundryStatics($queryData=[])
    {
        $query = Edu_unitset::select('washingfare')->first()->toArray(); // 洗滌費用
        $washingfare = (int) floor($query['washingfare']);

        $query = T04tb::select('t04tb.class','t04tb.term','t01tb.name','t01tb.process','t04tb.sdate','t04tb.edate','t04tb.section');

        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't04tb.class')->on('t01tb.branch','=',DB::raw("'2'"));
        });
        $query->where('t04tb.sdate', '>=', $queryData['sdate']);
        $query->where('t04tb.edate', '<=', $queryData['edate']);
        $data = $query->get()->toArray();
        // 計算人數
        foreach ($data as $key => $value) {
            $query = T13tb::where('dorm','Y');
            $query->where('class',$value['class']);
            $query->where('term',$value['term']);
            $query->where('status',1);
            $count = $query->count();
            $data[$key]['count'] = $count;
            $data[$key]['washingfare'] = $count*$washingfare;
        }
        return $data;
    }
}
