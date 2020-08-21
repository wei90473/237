<?php
namespace App\Repositories;

use App\Models\Edu_loanroom;
use App\Repositories\Repository;
use DB;

class EduloanroomRepository extends Repository
{
    public function __construct(Edu_loanroom $eduLoanroom)
    {
        $this->model = $eduLoanroom;
    }  

    public function getLoanRoom($applyno,$croomclsno){
        $model = $this->model->selectRaw('edu_loanroom.*
                                 ,edu_classroomcls.croomclsfullname,edu_classroomcls.croomclsname
                                 ,edu_classroom.roomname,edu_classroom.fullname,edu_classroom.num')
                            ->leftJoin('edu_classroomcls', 'edu_loanroom.croomclsno', '=', 'edu_classroomcls.croomclsno')
                            ->leftJoin('edu_classroom', 'edu_loanroom.classroomno', '=', 'edu_classroom.roomno')
                            ->orderByRaw('edu_loanroom.applyno,edu_loanroom.croomclsno,edu_loanroom.classroomno');

        $model->where('edu_loanroom.applyno', '=' , $applyno);
        $model->where('edu_loanroom.croomclsno', '=' , $croomclsno);
        $query = $model->get();

        return $query;
    }

}