<?php
namespace App\Repositories;

use App\Models\Grade_sub_option;
use App\Repositories\Repository;

class GradeSubOptionRepository extends Repository
{   
    public function __construct(Grade_sub_option $grade_sub_option)
    {
        $this->model = $grade_sub_option;
    }  
}
