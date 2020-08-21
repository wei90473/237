<?php
namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\T03tb;
use DB;

class T03tbRepository extends Repository
{
    public function __construct(T03tb $t03tb)
    {
        $this->model = $t03tb;
    }  

    public function get($queryData = null, $paginate = true)
    {
        return $this->model->get();
    }

    // public function find($class, $term, $organ, $select = "*")
    // {
    //     $t03tb = $this->model->select($select)
    //                          ->where("class", "=", $class)
    //                          ->where("term", "=", $term)
    //                          ->where("organ", "=", $organ)
    //                          ->first();
    //     return $t03tb;
    // }

    public function getSumQuota($class, $term)
    {
        $t03tb = $this->model->selectRaw("class, term, sum(quota) sum_quota")
                             ->where("class", "=", $class)
                             ->where("term", "=", $term)
                             ->groupBy(["class", "term"])
                             ->first();

        if (!empty($t03tb)){
            return $t03tb->sum_quota;
        }else{
            return false;
        }
    }

    public function getByT04tb($t04tb_info)
    {
        $t03tb = $this->model->where($t04tb_info)
                             ->get();
        return $t03tb;
    }

    public function clearOldAssignData($class, $start_term, $end_term){
        return $this->model->where('class', '=', $class)
                           ->where('term', '>=', $start_term)
                           ->where('term', '<=', $end_term)
                           ->update(['quota' => 0]);
    }

    public function getOnlineUpdateStatus($classes)
    {
        return $this->model->whereIn('class', $classes)
                           ->get();
    }

    public function getByIn($classes)
    {
        $model = $this->model->select("*");
        foreach ($classes as $class => $terms){
            foreach ($terms as $term){
                $model->orWhere(function($query) use ($class, $term){
                    $query->where('class', '=', $class)
                          ->where('term', '=',  $term);
                });
            }
        }

        return $model->get();
    }

    public function getOnlineUpdate($class, $start_term, $end_term)
    {
        return $this->model->selectRaw("class, term, count(*) data_num")
                           ->where('class', '=', $class)
                           ->where('is_online_update', '=', 1)
                           ->groupBy('class', 'term')
                           ->get();
    }

    public function getByYerlyCount($yerly, $is_type13 = false)
    {
        $model = $this->model->join('t01tb', 't01tb.class', '=', 't03tb.class')
                             ->where('t03tb.class', 'LIKE', "{$yerly}%");

        if ($is_type13 == false){
            $model->where('t01tb.type', '<>', 13);
        }

        return $model->count();                  
    }

    public function getByT04tbs($t04tb_keys)
    {
        $model = $this->model->select(["*"]);

        foreach ($t04tb_keys as $class => $terms){
            foreach ($terms as $term){
                $model->orWhere(function($query) use($class, $term){
                    $query->where(compact(['class', 'term']));
                });
            }
        }

        return $model->get();
    }

    /*
        訓練排程批次新增
    */
    public function getScheduleBatchInsertData($yerly, $is_type13 = false)
    {
        $query = $this->model->selectRaw('t03tb.class, t03tb.term, SUM(t03tb.quota) as quota')
                             ->join('t01tb', 't01tb.class', '=', 't03tb.class')
                             ->where('t01tb.yerly', '=', $yerly)
                             ->groupBy('t03tb.class', 't03tb.term');
        if ($is_type13){
            $query->where('t01tb.type', '<>', 13);
        }

        return $query->get();
    }
}