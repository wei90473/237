<?php
namespace App\Repositories;

use App\Models\Edu_loansroom;
use App\Repositories\Repository;
use DB;

class EduloansroomRepository extends Repository
{
    public function __construct(Edu_loansroom $eduLoansroom)
    {
        $this->model = $eduLoansroom;
    }  

    public function getLoansRoom($applyno,$croomclsno){
        $model = $this->model->selectRaw('edu_loansroom.*
                                 ,edu_classroomcls.croomclsfullname,edu_classroomcls.croomclsname
                                 ,edu_floor.floorname
                                 ,edu_classcode.name AS sexname')
                            ->leftJoin('edu_classroomcls', 'edu_loansroom.croomclsno', '=', 'edu_classroomcls.croomclsno')
                            ->leftJoin('edu_floor', function($join){
                                    $join->on('edu_loansroom.croomclsno', '=', 'edu_floor.croomclsno')
                                         ->on('edu_loansroom.floorno', '=', 'edu_floor.floorno');
                              })
                            ->leftJoin('edu_classcode', function($join){
                                    $join->on('edu_loansroom.sex', '=', 'edu_classcode.code')
                                         ->on('edu_classcode.class', '=', DB::raw('15'));
                              })
                            ->orderByRaw('edu_loansroom.applyno,edu_loansroom.croomclsno,edu_loansroom.bedroom,edu_loansroom.bedno desc');

        $model->where('edu_loansroom.applyno', '=' , $applyno);
        $model->where('edu_loansroom.croomclsno', '=' , $croomclsno);
        $query = $model->get();

        return $query;
    }

}