<?php
namespace App\Repositories;

use App\Models\Elearn_class;
use App\Repositories\Repository;
use DateTime;

class ElearnClassRepository extends Repository
{
    public function __construct(Elearn_class $elearn_class)
    {
        $this->model = $elearn_class;
    }  

}