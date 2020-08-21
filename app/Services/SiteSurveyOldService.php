<?php
namespace App\Services;

use App\Repositories\SiteSurveyOldRepository;


class SiteSurveyOldService
{
    /**
     * SiteSurveyOldService constructor.
     * @param SiteSurveyOldRepository $siteSurveyOldRepository
     */
    public function __construct(SiteSurveyOldRepository $siteSurveyOldRepository)
    {
        $this->siteSurveyOldRepository = $siteSurveyOldRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteSurveyOldList($queryData = [])
    {
        return $this->siteSurveyOldRepository->getSiteSurveyOldList($queryData);
    }
}
