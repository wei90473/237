<?php
namespace App\Services;

use App\Repositories\SiteRepository;


class SiteService
{
    /**
     * SiteService constructor.
     * @param SiteRepository $siteRpository
     */
    public function __construct(SiteRepository $siteRpository)
    {
        $this->siteRpository = $siteRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteList($queryData = [])
    {
        return $this->siteRpository->getSiteList($queryData);
    }
}
