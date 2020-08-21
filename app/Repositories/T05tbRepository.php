<?php
namespace App\Repositories;

use App\Models\T05tb;
use App\Repositories\Repository;
use DateTime;

class T05tbRepository extends Repository
{
    public function __construct(T05tb $t05tb)
    {
        $this->model = $t05tb;
    }  
    
    public function getLastUnit($class_info)
    {
        return $this->model->where($class_info)->max('unit');
    }
}