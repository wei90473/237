<?php
namespace App\Repositories;

use DB;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T13tb;
use App\Models\M02tb;

class Stay_registrationRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getStay_registration($queryData = [])
    {

        $query = T04tb::select('t01tb.remark','t01tb.name','t01tb.branchname','t01tb.trainday','t04tb.class','t04tb.term','t04tb.sdate','t04tb.edate','m09tb.username');

        $query->join('m09tb', function($join)
        {
            $join->on('m09tb.userid', '=', 't04tb.sponsor');
        });

        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't04tb.class');
        });

        $query->join('t13tb', function($join)
        {
            $join->on('t13tb.class', '=', 't04tb.class')
            ->on('t13tb.term', '=', 't04tb.term');
        });

        $query->where('t04tb.sdate', '>=', $queryData['sdate']);
        $query->where('t04tb.edate', '<=', $queryData['edate']);
        $query->where('t13tb.dorm', '=', 'Y');
        $query->where('t13tb.status', '!=', '3');

        $query->orderBy('t04tb.class', 'asc');
        $query->orderBy('t04tb.term', 'asc');

        $query->groupBy('t04tb.class', 't04tb.term');

        $data = $query->get()->toArray();

        foreach($data as & $row){

            $row["all_count"] = '0';
            $row["M"] = '0';
            $row["F"] = '0';
            $row["dorm_M"] = '0';
            $row["dorm_F"] = '0';
            $query = T13tb::select(DB::raw("count(1) all_count , sum(Case when m02tb.sex='M' then 1 else 0 End ) M, sum(Case when m02tb.sex='F' then 1 else 0 End ) F, sum(Case when t13tb.dorm='Y' and m02tb.sex='M' then 1  else 0 End ) as dorm_M, sum(Case when t13tb.dorm='Y' and m02tb.sex='F' then 1  else 0 End ) as dorm_F"));

            $query->join('m02tb', function($join)
            {
                $join->on('m02tb.idno', '=', 't13tb.idno');
            });
            $query->where('t13tb.class', $row['class']);
            $query->where('t13tb.term', $row['term']);
            $query->where('t13tb.status', '!=', '3');

            $T13tb = $query->get()->toArray();

            if(!empty($T13tb)){
                $row["all_count"] = $T13tb[0]['all_count'];
                $row["M"] = $T13tb[0]['M'];
                $row["F"] = $T13tb[0]['F'];
                $row["dorm_M"] = $T13tb[0]['dorm_M'];
                $row["dorm_F"] = $T13tb[0]['dorm_F'];
            }

        }

        // dd($data);

        return $data;
    }

}
