<?php
namespace App\Services;

use App\Repositories\LectureBedroomUsageRepository;


class LectureBedroomUsageService
{
    /**
     * LectureBedroomUsageService constructor.
     * @param LectureBedroomUsageRepository $lectureBedroomUsageSummaryRpository
     */
    public function __construct(LectureBedroomUsageRepository $lectureBedroomUsageSummaryRpository)
    {
        $this->lectureBedroomUsageSummaryRpository = $lectureBedroomUsageSummaryRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLectureBedroomUsage($queryData = [])
    {
        return $this->lectureBedroomUsageSummaryRpository->getLectureBedroomUsage($queryData);
    }

}
