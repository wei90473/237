<?php
namespace App\Repositories;

use App\Models\Edu_classcode;
use App\Repositories\Repository;

class EduclasscodeRepository extends Repository
{
    public function __construct(Edu_classcode $eduClasscode)
    {
        $this->model = $eduClasscode;
    }  

    public function getByClass($statusData)
    {
    	$model = $this->model->select("code","name");

    	$equal = ['class'];

    	foreach ($equal as $field){
            if (!empty($statusData[$field])){
                $model->where($field, '=', $statusData[$field]);

                if($statusData[$field] == 47){
                	$model->where('param1', '=', '1');
                } else if($statusData[$field] == 48){
					$model->where('param3', '=', '1');
                }
            }
        }

        return $model->get();
    }

}