<?php

namespace App\Repositories;

use App\Models\M17tb;
use DB;
use App\Repositories\Repository;

class M17tbRepository extends Repository
{
    public function __construct(M17tb $m17tb)
    {
        $this->model = $m17tb;
    }  
    /*
        取得主管機關
    */
    public function getCompetentAuthoritys()
    {
        $min_grade = "SELECT organ, min(grade) grade 
                      FROM m17tb 
                      GROUP BY organ";

        $organ = $this->model->selectRaw("m17tb.enrollorg, m17tb.enrollname, CONCAT(m17tb.enrollorg, ' ', m17tb.enrollname) enroll_full_name, m17tb.organ")
                             ->join(DB::raw("({$min_grade}) main"),
                                    function($join){
                                        $join->on('main.organ', '=', 'm17tb.organ')
                                             ->on('main.grade', '=', 'm17tb.grade');
                                    })
                             ->get();                                                           
        return $organ;
    }


    public function get($queryData, $paginate = true, $select = "*")
    {
        $model = $this->model->select($select['m13tb']);
        if (!empty($queryData['enrollorg'])){
            $model->where('enrollorg', 'LIKE', "%{$queryData['enrollorg']}%");
        }
        if (!empty($queryData['enrollname'])){
            $model->where('enrollname', 'LIKE', "%{$queryData['enrollname']}%");
        }

        if ($paginate){
            $paginate_qty = (isset($queryData['_paginate_qty']) && $queryData['_paginate_qty']) ? $queryData['_paginate_qty'] : 10;
            $data = $model->paginate($paginate_qty);
        }else{
            $data = $model->get();
        }
        return $data;

    }

    public function getByEnrollorgs($enrollorgs)
    {
        return $this->model->whereIn('enrollorg', $enrollorgs)
                           ->get();
    }

    public function getGrade1Organ()
    {
        return $this->model->where("grade", '=', 1)->get();
    }
}