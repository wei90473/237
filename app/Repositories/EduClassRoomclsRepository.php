<?php

namespace App\Repositories;

use App\Models\Edu_classroomcls;
use DB;
use App\Repositories\Repository;

class EduClassRoomclsRepository extends Repository
{
    public function __construct(Edu_classroomcls $eduClassroomcls)
    {
        $this->model = $eduClassroomcls;
    }  

    public function getList($queryData, $paginate = true, $select = "*", $with = [])
    {
    	$model = $this->model->select($select)->with($with);

    	if (isset($queryData['croomclsno'])){
    		$model->where('croomclsno', 'LIKE', "%{$queryData['croomclsno']}%");
    	}

    	if (isset($queryData['croomclsname'])){
    		$model->where('croomclsname', 'LIKE', "%{$queryData['croomclsname']}%");
    	}

    	if ($paginate){
            $_paginate_qty = isset($queryData['_paginate_qty']) ? $queryData['_paginate_qty'] : 10;
    		return $model->paginate($_paginate_qty);
    	}else{
    		return $model->get();
    	}

    }

}