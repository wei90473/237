<?php
namespace App\Repositories;

use App\Models\SponsorAgent;
use DB;
use App\Repositories\Repository;

class SponsorAgentRepository extends Repository
{
    public function __construct(SponsorAgent $sponsor_agent)
    {
        $this->model = $sponsor_agent;
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