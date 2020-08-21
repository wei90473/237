<?php
namespace App\Services;

use App\Repositories\LectureReceptionListRepository;


class LectureReceptionListService
{
    /**
     * LectureReceptionListService constructor.
     * @param LectureReceptionListRepository $lectureReceptionListRpository
     */
    public function __construct(LectureReceptionListRepository $lectureReceptionListRpository)
    {
        $this->lectureReceptionListRpository = $lectureReceptionListRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLectureReceptionList1($queryData = [])
    {
        return $this->lectureReceptionListRpository->getLectureReceptionList1($queryData);
    }

    public function getLectureReceptionList2($queryData = [])
    {
        return $this->lectureReceptionListRpository->getLectureReceptionList2($queryData);
    }

}
