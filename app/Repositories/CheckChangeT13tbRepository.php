<?php
namespace App\Repositories;

use App\Models\CheckChangeT13tb;
use App\Repositories\Repository;
use DB;

class CheckChangeT13tbRepository extends Repository
{   
    public function __construct(CheckChangeT13tb $checkChangeT13tb)
    {
        $this->model = $checkChangeT13tb;
    }  

}
