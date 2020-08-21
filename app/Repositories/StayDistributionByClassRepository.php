<?php
namespace App\Repositories;

use DB;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T13tb;
use App\Models\M02tb;

class StayDistributionByClassRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getStayDistributionByClass($queryData = [])
    {


        $data = array();

        $query = T04tb::select('t01tb.name','t04tb.class','t04tb.term');

        $query->join('t13tb', function($join)
        {
            $join->on('t13tb.class', '=', 't04tb.class')
            ->on('t13tb.term', '=', 't04tb.term');
        });

        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't04tb.class');
        });

        $query->where('t04tb.sdate', $queryData['sdate']);
        $query->where('t13tb.dorm', '=', 'Y');
        $query->where('t13tb.status', '!=', '3');
        $query->where('t13tb.bedno', '!=', '');

        $query->orderBy('t04tb.class', 'asc');
        $query->orderBy('t04tb.term', 'asc');
        $query->groupBy('t04tb.class', 't04tb.term');
        $class_data = $query->get()->toArray();

        foreach($class_data as & $row){

            $query = T13tb::select('t13tb.no','m02tb.cname','edu_floor.floorname', 'edu_bed.roomname', 't13tb.bedno');
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

            $query->where('m02tb.sex', '=', 'M');
            $query->where('t13tb.class', $row['class']);
            $query->where('t13tb.term', $row['term']);
            $query->where('t13tb.dorm', '=', 'Y');
            $query->where('t13tb.status', '!=', '3');
            $query->where('t13tb.bedno', '!=', '');
            $query->orderBy('t13tb.no', 'asc');
            $M_data = $query->get()->toArray();

            $row['M_data'] = $M_data;

            $query = T13tb::select('t13tb.no','m02tb.cname','edu_floor.floorname', 'edu_bed.roomname', 't13tb.bedno');
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

            $query->where('m02tb.sex', '=', 'F');
            $query->where('t13tb.class', $row['class']);
            $query->where('t13tb.term', $row['term']);
            $query->where('t13tb.dorm', '=', 'Y');
            $query->where('t13tb.bedno', '!=', '');
            $query->orderBy('t13tb.no', 'asc');
            $F_data = $query->get()->toArray();

            $row['F_data'] = $F_data;

        }
        // dd($class_data);

        return $class_data;
    }

}
