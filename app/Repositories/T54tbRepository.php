<?php 
namespace App\Repositories;

use App\Models\T54tb;
use App\Repositories\Repository;

class T54tbRepository extends Repository{
    public function __construct(T54tb $t54tb)
    {
        $this->model = $t54tb;
    }  


}
