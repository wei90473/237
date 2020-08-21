<?php
namespace App\Repositories;

use App\Models\Restructuring;
use DB;
use App\Repositories\Repository;

class RestructuringRepository extends Repository
{
    public function __construct(Restructuring $restructuring)
    {
        $this->model = $restructuring;
    }  

    public function get($queryData, $paginate = true)
    {
        $model = $this->model->selectRaw("distinct restructuring.id")
                             ->join('restructuring_detail', 'restructuring_detail.restructuring_id', '=', 'restructuring.id')
                             ->join('m17tb', 'm17tb.enrollorg', '=', 'restructuring_detail.enrollorg');

        $fields = [
            'likes' => [
                'restructuring_detail' => ['enrollorg'],
                'm17tb' => ['enrollname']
            ]
        ];

        $model = $this->queryForList($model, $fields, $queryData);
        $ids = $model->get();

        return $this->model->whereIn('id', $ids)->with(['before', 'after'])->paginate(10);
    }
}