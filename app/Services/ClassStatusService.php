<?php
namespace App\Services;

use App\Repositories\ClassStatusRepository;


class ClassStatusService
{
    /**
     * ClassStatusService constructor.
     * @param ClassStatusRepository $classStatusRpository
     */
    public function __construct(ClassStatusRepository $classStatusRpository)
    {
        $this->classStatusRpository = $classStatusRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassStatus($queryData = [])
    {
        return $this->classStatusRpository->getClassStatus($queryData);
    }

}
