<?php
namespace App\Services;

use App\Repositories\EmployRepository;


class EmployService
{
    /**
     * EmployService constructor.
     * @param EmployRepository $employRpository
     */
    public function __construct(EmployRepository $employRpository)
    {
        $this->employRpository = $employRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getEmployList($queryData = [])
    {
        return $this->employRpository->getEmployList($queryData);
    }

    public function getSponsor()
    {
        return $this->employRpository->getSponsor();
    }

    public function getDetailList($queryData = [])
    {
        return $this->employRpository->getDetailList($queryData);
    }

    public function getClass($queryData = [])
    {
        return $this->employRpository->getClass($queryData);
    }

    public function getEditDelete($id=NULL)
    {
        return $this->employRpository->getEditDelete($id);
    }

    public function getTeacher($queryData = [])
    {
        return $this->employRpository->getTeacher($queryData);
    }
}
