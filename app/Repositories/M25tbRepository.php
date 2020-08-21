<?php
namespace App\Repositories;

use App\Models\M25tb;

class M25tbRepository
{
    public function __construct(M25tb $m25tb)
    {
        $this->model = $m25tb;
    }  

    public function get($queryData = null, $paginate = true)
    {
        $model = $this->model->select("*");
        if ($paginate) return $model->paginate(15);
        return $this->model->get();
    }

    public function find($site)
    {
        return $this->model->find($site);
    }
}