<?php
namespace App\Repositories;

use App\Models\M22tb;
use App\Repositories\Repository;

class M22tbRepository extends Repository
{
    public function __construct(M22tb $m22tb)
    {
        $this->model = $m22tb;
    }  


}