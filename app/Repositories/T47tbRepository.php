<?php 
namespace App\Repositories;

use App\Models\T47tb;
use App\Repositories\Repository;

class T47tbRepository extends Repository{
    public function __construct(T47tb $t47tb)
    {
        $this->model = $t47tb;
    }  


}
