<?php
namespace App\Services;

use App\Repositories\TeachingMaterialRepository;


class TeachingMaterialService
{
    /**
     * TeachingMaterialService constructor.
     * @param TeachingMaterialRepository $teachingmaterialRpository
     */
    public function __construct(TeachingMaterialRepository $teachingmaterialRpository)
    {
        $this->teachingmaterialRpository = $teachingmaterialRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTeachingMaterialList($queryData = [])
    {
        return $this->teachingmaterialRpository->getTeachingMaterialList($queryData);
    }

    public function getDetailList($queryData = [])
    {
        return $this->teachingmaterialRpository->getDetailList($queryData);
    }

    public function getDelete($idno=null)
    {
        return $this->teachingmaterialRpository->getDelete($idno);
    }
}
