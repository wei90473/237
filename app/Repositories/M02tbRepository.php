<?php
namespace App\Repositories;

use App\Models\M02tb;
use App\Repositories\Repository;

class M02tbRepository extends Repository
{
    public function __construct(M02tb $m02tb)
    {
        $this->model = $m02tb;
    }  

    public function get($queryData, $paginate = true, $with = [])
    {
        $model = $this->model->select("*");
        $model->with($with);
        $like = ['position', 'email', 'enrollid'];

        foreach ($like as $field){
            if (!empty($queryData[$field])){
                $model->where($field, 'LIKE', '%'.$queryData[$field].'%');
            }
        }

        $equal = ['idno', 'cname', 'identity', 'rank'];

        foreach ($equal as $field){
            if (!empty($queryData[$field])){
                $model->where($field, '=', $queryData[$field]);
            }
        }

        $YN = ['chief', 'personnel', 'aborigine', 'handicap'];

        foreach ($YN as $field){
            if (!empty($queryData[$field])){
                $model->where($field, '=', 'Y');
            }
        }

        if (isset($queryData['special_situation'])){
            if ($queryData['special_situation'] == "Y"){
                $model->where(function ($query){
                    $query->whereNotNull('special_situation')
                          ->where('special_situation', '<>', '');
                });
            }
        }

        $model->orderBy('idno');
        return $model->paginate(10);
    }

    public function getByIdnos($idnos)
    {
        return $this->model->whereIn('idno', $idnos)
                           ->get();
    }
}