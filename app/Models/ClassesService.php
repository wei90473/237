<?php
namespace App\Services;

use App\Repositories\ClassesRepository;


class ClassesService
{
    /**
     * ClassesService constructor.
     * @param ClassesRepository $classesRpository
     */
    public function __construct(ClassesRepository $classesRpository)
    {
        $this->classesRpository = $classesRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassesList($queryData = [])
    {
        return $this->classesRpository->getClassesList($queryData);
    }

    /**
     * 取得年度資料
     *
     * @param $year 關鍵字
     * @return count
     */
    public function getClassesdata($year)
    {
        return $this->classesRpository->getClassesdata($year);
    }
}
