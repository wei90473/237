<?php 
namespace App\Repositories;

use App\Models\T23tb;
use App\Repositories\Repository;
use DB;

class T23tbRepository extends Repository{
    public function __construct(T23tb $t23tb)
    {
        $this->model = $t23tb;
    }  

    public function getConclusionInfo($t04tb_info)
    {
        return  $this->model->select(
                                DB::raw("	
                                          sum(sincnt) sincnt, 
                                          sum(donecnt + dtwocnt) donecnt, 
                                          sum(meacnt) meacnt, 
                                          sum(luncnt) luncnt, 
                                          sum(dincnt) dincnt
                                        ")
                            )
                      ->where($t04tb_info)
                      ->where('type', '=', '3')
                      ->first();
                    
    }
}
