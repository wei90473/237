<?php
namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Signature;
use DB;

class SignatureRepository extends Repository
{
    public function __construct(Signature $signature)
    {
        $this->model = $signature;
    }

    public function get($queryData, $select = "*", $paginate = true)
    {
        $model = $this->model->select($select);

        $fields = [
            'other_like' => [
                'signatures' => ['name']
            ]
        ];

        $model = $this->queryField($model, $fields, $queryData);
        $model->orderBy('sort');
        return $model->paginate(10);
    }

    public function getFinallySort()
    {
        return $this->model->selectRaw('max(sort) as sort')
                           ->first();
    }
}
