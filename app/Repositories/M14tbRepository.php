<?php
namespace App\Repositories;

use App\Models\M14tb;
use DB;
use App\Repositories\Repository;

class M14tbRepository extends Repository
{
    public function __construct(M14tb $m14tb)
    {
        $this->model = $m14tb;
    }  

    public function get($queryData = null, $paginate = true)
    {
        $model = $this->model->select("*");

        if (!empty($queryData['enrollorg'])) $model->where('enrollorg', '=', '%'.$queryData['enrollorg'].'%');

        if (!empty($queryData['enrollname'])) $model->where('enrollname', 'LIKE', '%'.$queryData['enrollname'].'%');

        if ($paginate) return $model->paginate(15);
        return $model->get();
    }

    public function getClassRoom($branch)
    {
        $m14tb = $this->model->where("branch", "=", $branch);
        if ($branch == 1){
            return $m14tb->whereIn("type", [1, 2])
                            ->whereNotIn(DB::raw('LEFT(site,1)'), ['1', '2'])
                            ->get();
        }else{
            return $m14tb->get();            
        }
    }

}