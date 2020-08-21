<?php

namespace App\Repositories;

use App\Models\OnlineApplyOrgan;
use DB;
use App\Repositories\Repository;

class OnlineApplyOrganRepository extends Repository
{
    public function __construct(OnlineApplyOrgan $onlineApplyOrgan)
    {
        $this->model = $onlineApplyOrgan;
    }  


}