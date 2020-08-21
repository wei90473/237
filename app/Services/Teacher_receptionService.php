<?php
namespace App\Services;

use App\Repositories\Teacher_receptionRepository;


class Teacher_receptionService
{
    /**
     * Teacher_receptionService constructor.
     * @param Teacher_receptionRepository $teacher_receptionRpository
     */
    public function __construct(Teacher_receptionRepository $teacher_receptionRpository)
    {
        $this->teacher_receptionRpository = $teacher_receptionRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTeacher_receptionList($queryData = [])
    {
        return $this->teacher_receptionRpository->getTeacher_receptionList($queryData);
    }

    public function getDetailList($queryData = [])
    {
        return $this->teacher_receptionRpository->getDetailList($queryData);
    }

    public function getFoodList($queryData = [])
    {
        return $this->teacher_receptionRpository->getFoodList($queryData);
    }

    public function getRoomList($queryData = [])
    {
        return $this->teacher_receptionRpository->getRoomList($queryData);
    }

    public function getCarList($queryData = [])
    {
        return $this->teacher_receptionRpository->getCarList($queryData);
    }

    public function getRoomDate($queryData = [])
    {
        return $this->teacher_receptionRpository->getRoomDate($queryData);
    }

    public function getCarDate($queryData = [])
    {
        return $this->teacher_receptionRpository->getCarDate($queryData);
    }

    public function getFoodDate($queryData = [])
    {
        return $this->teacher_receptionRpository->getFoodDate($queryData);
    }

    public function getClass($queryData = [])
    {
        return $this->teacher_receptionRpository->getClass($queryData);
    }

    public function getIdno($queryData = [])
    {
        return $this->teacher_receptionRpository->getIdno($queryData);
    }

    public function getClassWeek($queryData = [])
    {
        return $this->teacher_receptionRpository->getClassWeek($queryData);
    }

    public function getTeacherByWeek($queryData = [])
    {
        return $this->teacher_receptionRpository->getTeacherByWeek($queryData);
    }

    public function change_room($queryData = [])
    {
        return $this->teacher_receptionRpository->change_room($queryData);
    }

    public function getSponsor()
    {
        return $this->teacher_receptionRpository->getSponsor();
    }

    public function getTeachDate($queryData = [])
    {
        return $this->teacher_receptionRpository->getTeachDate($queryData);
    }

    public function getRoom()
    {
        return $this->teacher_receptionRpository->getRoom();
    }

    public function getLicenseData()
    {
        return $this->teacher_receptionRpository->getLicenseData();
    }
}
