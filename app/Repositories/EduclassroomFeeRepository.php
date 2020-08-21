<?php

namespace App\Repositories;

use App\Models\Edu_clsroomfee;
use DB;
use App\Repositories\Repository;

class EduclassroomFeeRepository extends Repository
{
    public function __construct(Edu_clsroomfee $eduClsroomfee)
    {
        $this->model = $eduClsroomfee;
    } 

    public function getFeeByDay($croomclsno)
    {
        return $this->model->where('clsroomno', '=', $croomclsno)
                           ->where('feetype', '<>', 4)
                           ->get();
    }

    public function getFeeByTime($croomclsno)
    {
        return$this->model->where('clsroomno', '=', $croomclsno)
                          ->where('feetype', '=', 4)
                          ->get();
    }    
}