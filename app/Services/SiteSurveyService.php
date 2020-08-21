<?php
namespace App\Services;

use App\Repositories\SiteSurveyRepository;


class SiteSurveyService
{
    /**
     * SiteSurveyService constructor.
     * @param SiteSurveyRepository $siteSurveyRepository
     */
    public function __construct(SiteSurveyRepository $siteSurveyRepository)
    {
        $this->siteSurveyRepository = $siteSurveyRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSiteSurveyList($queryData = [])
    {
        return $this->siteSurveyRepository->getSiteSurveyList($queryData);
    }
}
