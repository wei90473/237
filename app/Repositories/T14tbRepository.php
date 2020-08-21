<?php
namespace App\Repositories;

use App\Models\T14tb;
use App\Repositories\Repository;
use DateTime;
use DB;

class T14tbRepository extends Repository
{
    public function __construct(T14tb $t14tb)
    {
        $this->model = $t14tb;
    }  

    public function getT04tbMaxSerno($t04tb_info, $idnos)
    {
        return $this->model->selectRaw("idno, max(serno) max_serno")
                           ->where($t04tb_info)
                           ->whereIn('idno', $idnos)
                           ->groupBy(['idno'])
                           ->get();
                           
    }
}