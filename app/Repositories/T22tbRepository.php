<?php 
namespace App\Repositories;

use App\Models\T22tb;
use App\Repositories\Repository;

class T22tbRepository extends Repository{
    public function __construct(T22tb $t22tb)
    {
        $this->model = $t22tb;
    }  

    public function checkReserved($class, $term, $date, $site, $range)
    {
        $t22tb = $this->model->where(function($query) use($class, $term){
                                $query->where("class", "<>", $class)
                                      ->orWhere("term", "<>", $term);
                             })
                             ->where("date", "=", $date)
                             ->where("site", "=", $site)
                             ->where(function($query) use($range){
                                 $query->where(function($query1) use($range){
                                     $query1->whereBetween("stime", $range);
                                 });
                                 $query->orwhere(function($query2) use($range){
                                     $query2->whereBetween("etime", $range);
                                 });
                             });
                             
        return $t22tb->get();
    }

    public function getByT04tb($t04tb_info)
    {
        return $this->model->where($t04tb_info)->get();
    }
}
