<?php

namespace App\Repositories;

use App\Models\ApplyModifyLogForAmdin;
use DB;
use App\Repositories\Repository;

class ApplyModifyLogForAmdinRepository extends Repository
{
    public function __construct(ApplyModifyLogForAmdin $applyModifyLogForAmdin)
    {
        $this->model = $applyModifyLogForAmdin;
    }  


}