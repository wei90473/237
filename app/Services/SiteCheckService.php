<?php
namespace App\Services;

use App\Repositories\SiteCheckRepository;


class SiteCheckService
{
    /**
     * SiteCheckService constructor.
     * @param SiteCheckRepository $siteCheckRpository
     */
    public function __construct(SiteCheckRepository $siteCheckRpository)
    {
        $this->siteCheckRpository = $siteCheckRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteCheckList($queryData = [])
    {
        return $this->siteCheckRpository->getSiteCheckList($queryData);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        return $this->siteCheckRpository->getClassList();
    }

    /**
     * 取得未選取的課程
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourseNotSelectList($class, $term)
    {
        return $this->siteCheckRpository->getCourseNotSelectList($class, $term);
    }

    /**
     * 取得已選取的課程
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourseSelectList($class, $term)
    {
        return $this->siteCheckRpository->getCourseSelectList($class, $term);
    }

    /**
     * 取得所有課程(新增用)
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourseAllList($class, $term)
    {
        return $this->siteCheckRpository->getCourseAllList($class, $term);
    }
}
