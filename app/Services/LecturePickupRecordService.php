<?php
namespace App\Services;

use App\Repositories\LecturePickupRecordRepository;


class LecturePickupRecordService
{
    /**
     * LecturePickupRecordService constructor.
     * @param LecturePickupRecordRepository $lecturePickupRecordRpository
     */
    public function __construct(LecturePickupRecordRepository $lecturePickupRecordRpository)
    {
        $this->lecturePickupRecordRpository = $lecturePickupRecordRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLecturePickupRecord($queryData = [])
    {
        return $this->lecturePickupRecordRpository->getLecturePickupRecord($queryData);
    }

    public function getLecturePickupRecord2($queryData = [])
    {
        return $this->lecturePickupRecordRpository->getLecturePickupRecord2($queryData);
    }

}
