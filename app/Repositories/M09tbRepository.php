<?php
namespace App\Repositories;

use App\Models\M09tb;
use App\Repositories\Repository;

class M09tbRepository extends Repository
{
    public function __construct(M09tb $m09tb)
    {
        $this->model = $m09tb;
    }  

    public function get($queryData = null, $select = "*", $paginate = true)
    {
        $model = $this->model->select($select);
        $model->with(['sponsorAgents']);

        $fields = [
            'other_equal' => [
                'm09tb' => ['username']
            ]
        ];
        
        $this->queryField($model, $fields, $queryData);

        if ($paginate){
            return $model->paginate(10);
        }else{
            return $model->get();
        }
        
    }

    public function getSections()
    {
        $sections = $this->model->selectRaw("section")
                                ->groupBy("section")
                                ->get();
        return $sections;
    }
}