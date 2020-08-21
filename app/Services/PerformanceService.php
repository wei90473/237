<?php
namespace App\Services;

use App\Repositories\PerformanceRepository;


class PerformanceService
{
    /**
     * PerformanceService constructor.
     * @param PerformanceRepository $PerformanceRpository
     */
    public function __construct(PerformanceRepository $PerformanceRpository)
    {
        $this->PerformanceRpository = $PerformanceRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPerformanceList($queryData = [])
    {
        return $this->PerformanceRpository->getPerformanceList($queryData);
    }
    /**
     * 取得課程表處理列表
     *
     * @return mixed
     */
    public function getScheduleList($class,$term)
    {
        return $this->PerformanceRpository->getScheduleList($class,$term);
    }
    /**
     * 取得課程表教室名稱
     *
     * @return mixed
     */
    public function getsitename($site=NULL)
    {
        return $this->PerformanceRpository->getsitename($site);
    }
    /**
     * 取得課程
     *
     * @return array
     */ 
    public function getcourse($class,$term,$course)
    {
        return $this->PerformanceRpository->getcourse($class,$term,$course);
    }
}
