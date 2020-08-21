<?php
namespace App\Repositories;

use App\Models\T36tb;
use App\Repositories\Repository;
use DB;
use DateTime;

class T36tbRepository extends Repository{
    public function __construct(T36tb $t36tb)
    {
        $this->model = $t36tb;
    }     

    public function checkExsit($class, $term)
    {
        $t36tb = $this->model->where("class", "=", $class)
                             ->where("term", "=", $term)
                             ->get();
        return !($t36tb->isEmpty());
    }

    public function getByT04tb($t04tb_info)
    {
        $t36tbs = $this->model->where($t04tb_info)->get();
        return $t36tbs;
    }

    public function deleteByDate($t04tb_info, $dates)
    {
        return $this->model->where($t04tb_info)
                           ->whereIn('date', $dates)
                           ->delete();
    }

    public function getForDetail($queryData){

        $model = $this->model->select(['t36tb.*', 't01tb.name as t01tb_name', 't04tb.quota as t04tb_quota'])
                             ->join("t04tb", function($join){
                                $join->on('t04tb.class', '=', 't36tb.class')
                                     ->on('t04tb.term', '=', 't36tb.term');
                             })
                             ->join('t01tb', 't01tb.class', '=', 't04tb.class')
                             ->where("t36tb.class", "LIKE", "{$queryData['yerly']}%");

        if ($queryData['s_month']){
            $queryData['s_month'] = $queryData['yerly'].str_pad($queryData['s_month'], 2, '0', STR_PAD_LEFT)."01";
            $model->where("t04tb.sdate",">=", $queryData['s_month']);
        }

        if ($queryData['e_month']){
            
            $yerly = $queryData['yerly'] + 1911;
            $queryData['e_month'] = (int)$queryData['e_month'] + 1;

            if ($queryData['e_month'] > 12){
                $queryData['e_month'] = 1;
                $yerly++;
            }

            $end = new DateTime($yerly.str_pad($queryData['e_month'], 2, '0', STR_PAD_LEFT)."01");
            $end->modify("-1 day");
            $queryData['e_month'] = ((int)$end->format("Y")-1911).$end->format("md");
            $model->where("t04tb.sdate","<=", $queryData['e_month']);
        }
        return $model->get();        
    }
}