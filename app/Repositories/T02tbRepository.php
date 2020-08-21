<?php
namespace App\Repositories;

use App\Models\T02tb;
use DB;
use App\Repositories\Repository;

class T02tbRepository extends Repository
{
    public function __construct(T02tb $t02tb)
    {
        $this->model = $t02tb;
    }  

    public function getTotalByClasses($classes)
    {
        $model = $this->model->selectRaw("class, sum(quota) total");
        $model->whereIn('class', $classes);
        $model->groupBy('class');
        return $model->get();
    }
}