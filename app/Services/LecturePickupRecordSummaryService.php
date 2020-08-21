<?php
namespace App\Services;

use App\Repositories\LecturePickupRecordSummaryRepository;


class LecturePickupRecordSummaryService
{
    /**
     * LecturePickupRecordSummaryService constructor.
     * @param LecturePickupRecordSummaryRepository $lecturePickupRecordSummaryRpository
     */
    public function __construct(LecturePickupRecordSummaryRepository $lecturePickupRecordSummaryRpository)
    {
        $this->lecturePickupRecordSummaryRpository = $lecturePickupRecordSummaryRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLecturePickupRecordSummary($queryData = [])
    {
        return $this->lecturePickupRecordSummaryRpository->getLecturePickupRecordSummary($queryData);
    }

}
