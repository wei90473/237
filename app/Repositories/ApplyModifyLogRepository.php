<?php

namespace App\Repositories;

use App\Models\ApplyModifyLog;
use DB;
use App\Repositories\Repository;

class ApplyModifyLogRepository extends Repository
{
    public function __construct(ApplyModifyLog $applyModifyLog)
    {
        $this->model = $applyModifyLog;
    }  

    public function getNotReviewByid($ids)
    {
        return $this->model->whereIn('id', $ids)->where('status', '<>', 'Y')->get();
    }
}