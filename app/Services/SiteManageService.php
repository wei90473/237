<?php
namespace App\Services;

use App\Repositories\SiteManageRepository;


class SiteManageService
{
    /**
     * WaitingService constructor.
     * @param SiteManageRepository $siteManageRpository
     */
    public function __construct(SiteManageRepository $siteManageRpository)
    {
        $this->siteManageRpository = $siteManageRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteManageList($queryData = [])
    {
        return $this->siteManageRpository->getSiteManageList($queryData);
    }
    /**
     * 取得洽借場地班期年度資料
     *
     * @param $year 關鍵字
     * @return array
     */
    public function getClassesdata($year){
        return $this->siteManageRpository->getClassesdata($year);
    }
}
