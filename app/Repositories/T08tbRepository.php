<?php
namespace App\Repositories;

use App\Models\T08tb;
use App\Repositories\Repository;
use DateTime;

class T08tbRepository extends Repository
{
    public function __construct(T08tb $t08tb)
    {
        $this->model = $t08tb;
    }  
    
}