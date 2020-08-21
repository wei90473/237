<?php
namespace App\Services;

use App\Repositories\LectureRepository;


class LectureService
{
    /**
     * LectureService constructor.
     * @param LectureRepository $lectureRpository
     */
    public function __construct(LectureRepository $lectureRpository)
    {
        $this->lectureRpository = $lectureRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLectureList($queryData = [])
    {
        return $this->lectureRpository->getLectureList($queryData);
    }

    public function getDelete($idno=null)
    {
        return $this->lectureRpository->getDelete($idno);
    }
}
