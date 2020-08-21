<?php
namespace App\Repositories;

use App\Models\SpecialClassFee;
use DB;
use App\Repositories\Repository;

class SpecialClassFeeRepository extends Repository
{
    public function __construct(SpecialClassFee $special_class_fee)
    {
        $this->model = $special_class_fee;
    }  

    public function get($queryData = null, $paginate = true, $select = "*")
    {
        $model = $this->model->select($select);

        $fields = [
            'other_equal' => [
                'm09tb' => ['username']
            ]
        ];
        
        $this->queryField($model, $fields, $queryData);
        return $model->paginate(10);
    }

}