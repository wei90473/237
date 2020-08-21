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

    /**
     * 取得入口網站代碼
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getWebPortal($queryData = []){
        return $this->classesRpository->getWebPortal($queryData);
    }

    /**
     * 取得班期計畫表資料
     *
     * @param $class,$term 關鍵字
     * @return mixed
     */
    public function getClassPlanData($class,$term){
        return $this->classesRpository->getClassPlanData($class,$term);
    }

    public function getLecture($class,$term){
        return $this->classesRpository->getLecture($class,$term);
    }

    /**
     * 取得場地借用行事曆
     *
     * @param $yerly,$month 關鍵字
     * @return mixed
     */
    public function getSiteList($queryData = []){
        return $this->classesRpository->getSiteList($queryData);
    }

    //** 取得南投場地數據 **//
    public function getSiteData($queryData = [],$type = NULL){
        return $this->classesRpository->getSiteData($queryData,$type);
    }

    
}
