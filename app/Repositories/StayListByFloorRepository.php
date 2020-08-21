<?php
namespace App\Repositories;

use DB;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T13tb;
use App\Models\M02tb;

class StayListByFloorRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getStayListByFloor($queryData = [])
    {
        $startdate=strtotime(($queryData['sdate']+19110000));
        $enddate=strtotime(($queryData['edate']+19110000));
        $days=round(($enddate-$startdate)/3600/24) ;
        // dd(($queryData['sdate']+19110000));

        $data = array();

        for($i=0;$i<=$days;$i++){
            $day = date('Ymd',strtotime('+'.$i.' day', strtotime(($queryData['sdate']+19110000))));
            $day = $day-19110000;

            $query = T13tb::select('m02tb.cname','t01tb.name','edu_floor.floorname', 'edu_bed.roomname', 't13tb.bedno','t04tb.term','t04tb.sdate','t04tb.edate');
            $query->join('edu_floor', function($join)
            {
                $join->on('edu_floor.floorno', '=', 't13tb.floorno');
            });

            $query->join('edu_bed', function($join)
            {
                $join->on('edu_bed.bedno', '=', 't13tb.bedno');
            });

            $query->join('m02tb', function($join)
            {
                $join->on('m02tb.idno', '=', 't13tb.idno');
            });

            $query->join('t01tb', function($join)
            {
                $join->on('t01tb.class', '=', 't13tb.class');
            });

            $query->join('t04tb', function($join)
            {
                $join->on('t04tb.class', '=', 't13tb.class')
                ->on('t04tb.term', '=', 't13tb.term');
            });

            $query->where('t04tb.sdate', '<=', $day);
            $query->where('t04tb.edate', '>=', $day);
            // $query->whereBetween($day ,'t04tb.sdate','t04tb.edate');
            $query->where('t13tb.dorm', '=', 'Y');
            $query->where('t13tb.floorno', $queryData['floorno']);
            $query->where('t13tb.bedno', '!=', '');
            $query->orderBy('t13tb.bedno', 'asc');
            $day_data = $query->get()->toArray();

            if(!empty($day_data)){
                $data[$day] = $day_data;
            }

        }
        // dd(count($data));
        // dd($data);

        return $data;
    }

}
