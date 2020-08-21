<?php
namespace App\Repositories;

use App\Models\T40tb;
use App\Repositories\Repository;
use DateTime;

use DB;

class T40tbRepository extends Repository{
    public function __construct(T40tb $t40tb)
    {
        $this->model = $t40tb;
    }     

}