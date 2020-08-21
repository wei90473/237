<?php
namespace App\Repositories;

use DB;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T13tb;
use App\Models\M02tb;

class StayStaticsAfterRegRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getStayStaticsAfterReg($queryData = [])
    {

        $query = T04tb::select('edu_floor.floorname','t13tb.floorno');

        $query->join('t13tb', function($join)
        {
            $join->on('t13tb.class', '=', 't04tb.class')
            ->on('t13tb.term', '=', 't04tb.term');
        });

        $query->join('edu_floor', function($join)
        {
            $join->on('edu_floor.floorno', '=', 't13tb.floorno');
        });

        $query->where('t04tb.sdate', '>=', $queryData['sdate']);
        $query->where('t04tb.edate', '<=', $queryData['edate']);
        $query->where('t13tb.dorm', '=', 'Y');
        $query->where('t13tb.floorno', '!=', '');

        $query->orderBy('t13tb.floorno', 'asc');

        $query->groupBy('edu_floor.floorname', 't13tb.floorno');

        $data = $query->get()->toArray();

        foreach($data as & $row){

            $query = T13tb::select(DB::raw("count(1) as dorm_count, t04tb.sdate, t04tb.edate, t13tb.class, t13tb.term, t01tb.name"));

            $query->join('t04tb', function($join)
            {
                $join->on('t04tb.class', '=', 't13tb.class')
                ->on('t04tb.term', '=', 't13tb.term');
            });

            $query->join('t01tb', function($join)
            {
                $join->on('t01tb.class', '=', 't04tb.class');
            });

            $query->where('t04tb.sdate', '>=', $queryData['sdate']);
            $query->where('t04tb.edate', '<=', $queryData['edate']);
            $query->where('t13tb.dorm', '=', 'Y');
            $query->where('t13tb.status', '=', '1');
            $query->where('t13tb.floorno', $row['floorno']);
            $query->groupBy('t13tb.class', 't13tb.term');

            $T13tb = $query->get()->toArray();

            if(!empty($T13tb)){
                foreach($T13tb as $T13tb_row){
                    $row_data = $T13tb_row;
                    $startdate=strtotime($row_data['sdate']+19110000);
                    $enddate=strtotime($row_data['edate']+19110000);
                    $row_data['day'] = ($enddate-$startdate)/3600/24;
                    $row_data['total'] = $row_data['day'] * $row_data['dorm_count'];
                    $row['class_data'][] = $row_data;
                }
            }

        }
        // dd($data);

        return $data;
    }

}
