<?php
namespace App\Services;

use App\Repositories\Class_processRepository;

class Class_processService
{

    public function __construct(Class_processRepository $class_processRepository)
    {
        $this->class_processRepository = $class_processRepository;
    }

    public function getList($queryData = [])
    {
        return $this->class_processRepository->getList($queryData);
    }

    public function getSub($id)
    {
        return $this->class_processRepository->getSub($id);
    }

}
