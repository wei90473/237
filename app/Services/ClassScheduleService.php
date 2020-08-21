<?php
namespace App\Services;

use App\Repositories\ClassScheduleRepository;


class ClassScheduleService
{
    /**
     * ClassScheduleService constructor.
     * @param ClassScheduleRepository $classScheduleRpository
     */
    public function __construct(ClassScheduleRepository $classScheduleRpository)
    {
        $this->classScheduleRpository = $classScheduleRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassScheduleList($queryData = [])
    {
        return $this->classScheduleRpository->getClassScheduleList($queryData);
    }
    /**
     * 取得課程表處理列表
     *
     * @return mixed
     */
    public function getScheduleList($class,$term)
    {
        return $this->classScheduleRpository->getScheduleList($class,$term);
    }
    /**
     * 取得課程表教室名稱
     *
     * @return mixed
     */
    public function getsitename($site=NULL,$branch='1')    
    {
        return $this->classScheduleRpository->getsitename($site,$branch);
    }
    /**
     * 取得課程
     *
     * @return array
     */ 
    public function getcourse($class,$term,$course)
    {
        return $this->classScheduleRpository->getcourse($class,$term,$course);
    }
}
