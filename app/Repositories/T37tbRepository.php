<?php
namespace App\Repositories;

use App\Models\T37tb;
use App\Repositories\Repository;
use DateTime;
use App\Helpers\TrainSchedule;
use DB;

class T37tbRepository extends Repository{
    public function __construct(T37tb $t37tb)
    {
        $this->model = $t37tb;
    }     

    public function clearHistory($class, $term, $site)
    {
        return $this->model->where("class", "=", $class)
                        ->where("term", "=", $term)
                        ->where("site", "=", $site)
                        ->where(function ($query){
                            $query->where("type", "=", 2)
                                  ->orWhere(function($query1){
                                        $today = new DateTime();
                                        $query1->where("type", "=", 1)
                                               ->where("request", "<", (string)($today->format("Y")-1911).$today->format("md"));
                                  });
                        })
                        ->delete();
    }
}