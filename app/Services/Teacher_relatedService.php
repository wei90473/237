<?php
namespace App\Services;

use App\Repositories\Teacher_relatedRepository;


class Teacher_relatedService
{
    /**
     * Teacher_relatedService constructor.
     * @param Teacher_relatedRepository $teacher_relatedRpository
     */
    public function __construct(Teacher_relatedRepository $teacher_relatedRpository)
    {
        $this->teacher_relatedRpository = $teacher_relatedRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTeacher_relatedList($queryData = [])
    {
        return $this->teacher_relatedRpository->getTeacher_relatedList($queryData);
    }

    public function getDetailList($queryData = [])
    {
        return $this->teacher_relatedRpository->getDetailList($queryData);
    }

    public function getRoomDate($queryData = [])
    {
        return $this->teacher_relatedRpository->getRoomDate($queryData);
    }

    public function getCarDate($queryData = [])
    {
        return $this->teacher_relatedRpository->getCarDate($queryData);
    }

    public function getFoodDate($queryData = [])
    {
        return $this->teacher_relatedRpository->getFoodDate($queryData);
    }

    public function getClass($queryData = [])
    {
        return $this->teacher_relatedRpository->getClass($queryData);
    }

    public function getIdno($queryData = [])
    {
        return $this->teacher_relatedRpository->getIdno($queryData);
    }

    public function getClassWeek($queryData = [])
    {
        return $this->teacher_relatedRpository->getClassWeek($queryData);
    }

    public function getTeacherByWeek($queryData = [])
    {
        return $this->teacher_relatedRpository->getTeacherByWeek($queryData);
    }

    public function change_room($queryData = [])
    {
        return $this->teacher_relatedRpository->change_room($queryData);
    }

    public function getSponsor()
    {
        return $this->teacher_relatedRpository->getSponsor();
    }

    public function getTeachDate($queryData = [])
    {
        return $this->teacher_relatedRpository->getTeachDate($queryData);
    }

    public function getSpecialNeed($queryData = [])
    {
        return $this->teacher_relatedRpository->getSpecialNeed($queryData);
    }

    public function getClassName($queryData = [])
    {
        return $this->teacher_relatedRpository->getClassName($queryData);
    }
}
