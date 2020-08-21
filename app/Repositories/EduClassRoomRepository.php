<?php

namespace App\Repositories;

use App\Models\Edu_classroom;
use DB;
use App\Repositories\Repository;

class EduClassRoomRepository extends Repository
{
    public function __construct(Edu_classroom $eduClassroom)
    {
        $this->model = $eduClassroom;
    }  

    public function getList($queryData, $paginate = true, $select = "*", $with = [])
    {
    	$model = $this->model->select($select)->with($with);

    	if (isset($queryData['roomno'])){
    		$model->where('roomno', 'LIKE', "%{$queryData['roomno']}%");
    	}

    	if (isset($queryData['roomname'])){
    		$model->where('roomname', 'LIKE', "%{$queryData['roomname']}%");
    	}

    	if ($paginate){
            $_paginate_qty = isset($queryData['_paginate_qty']) ? $queryData['_paginate_qty'] : 10;
    		return $model->paginate($_paginate_qty);
    	}else{
    		return $model->get();
    	}

    }

}