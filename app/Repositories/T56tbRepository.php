<?php 
namespace App\Repositories;

use App\Models\T56tb;
use App\Repositories\Repository;

class T56tbRepository extends Repository{
    public function __construct(T56tb $t56tb)
    {
        $this->model = $t56tb;
    }  


}
