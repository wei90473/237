<?php
namespace App\Services;

use App\Repositories\SiteScheduleRepository;


class SiteScheduleService
{
    /**
     * WaitingService constructor.
     * @param SiteScheduleRepository $siteScheduleRpository
     */
    public function __construct(SiteScheduleRepository $siteScheduleRpository)
    {
        $this->siteScheduleRpository = $siteScheduleRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteScheduleList($queryData = [])
    {
        return $this->siteScheduleRpository->getSiteScheduleList($queryData);
    }

    /**
     * 取得不重複班級列表
     *
     * @param array $queryData 關鍵字
     * @return array
     */
    public function getclasslist($queryData = []){
        return $this->siteScheduleRpository->getclasslist($queryData);
    }
    
    /**
     * 取得行事曆列表
     *
     * @param array $class,$term 關鍵字
     * @return mixed
     */
    public function getcalendarlist($class,$term){
        return $this->siteScheduleRpository->getcalendarlist($class,$term);
    }
}
