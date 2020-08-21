<?php
namespace App\Repositories;

use App\Models\M21tb;
use App\Repositories\Repository;

class M21tbRepository extends Repository
{
    public function __construct(M21tb $m21tb)
    {
        $this->model = $m21tb;
    }  


}