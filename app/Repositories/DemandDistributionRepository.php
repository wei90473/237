<?php
namespace App\Repositories;

use App\Models\T01tb;
use App\Models\T02tb;
use App\Models\M13tb;
use DB;

class DemandDistributionRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassList($queryData = [])
    {   

        //class 班號, name 班別名稱, yerly 年度, times 梯次,
        $query = T01tb::select('class', 'name', 'yerly', 'times', 'quotatot','rank');

                
                // ,'m13tb.lname AS lname')->leftJoin('t02tb', 't01tb.class', '=', 't02tb.class')->leftJoin('m13tb', 't02tb.organ', '=', 'm13tb.organ')->get();

        // SELECT t01tb.class, t01tb.name, t01tb.yerly, t01tb.times, m13tb.organ, m13tb.lname
        // FROM t01tb
        // LEFT JOIN t02tb
        // ON t01tb.class = t02tb.class
        // LEFT JOIN m13tb
        // ON t02tb.organ = m13tb.organ


        // demand_distribution need @param
        // year = 年度, term = 期別, organ = 機關名稱, class = 班別名稱, _sort_field = 排序欄位
        // _sort_mode = 排序方向, _paginate_qty = 每頁幾筆

        // 班別名稱
        if ( isset($queryData['classes_name']) && $queryData['classes_name'] &&  $queryData['classes_name'] !== "empty" ) {

            $query->where('class', 'like', '%'.$queryData['classes_name'].'%')
            ->orwhere('name', 'like', '%'.$queryData['classes_name'].'%');
       
        
        }

        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] &&  $queryData['yerly'] !== "empty" ) {

            $query->where('yerly', $queryData['yerly'] );
        }

        // 第幾次(期別)
        if ( isset($queryData['times']) && $queryData['times'] &&  $queryData['times'] !== "empty"  ) {

            $query->where('times', $queryData['times']);
        }
        // 預設排序
        $query->orderBy(DB::raw('rank,class'));

        // // 機關名稱
        // if ( isset($queryData['organ']) && $queryData['organ']) {
        //     $query->where('lname', $queryData['organ']);
        // }
            
        //paginate 分頁處理
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    /**
     * 取得該年度該院區的調查次數
     *
     * @param array $queryData 年度跟院區
     * @return mixed
     */
    public function getTimesByYearBranchList($queryData = [])
    {  

        $data = DB::select('SELECT DISTINCT times FROM t01tb WHERE yerly = \''.$queryData['yerly'].'\' and branch = \''.$queryData['branch'].'\' ORDER BY times');
        return $data;
    }
}
