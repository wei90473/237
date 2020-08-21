<?php
namespace App\Services;

use App\Repositories\StayListByFloorRepository;


class StayListByFloorService
{
    /**
     * StayListByFloorService constructor.
     * @param StayListByFloorRepository $stayListByFloorRpository
     */
    public function __construct(StayListByFloorRepository $stayListByFloorRpository)
    {
        $this->stayListByFloorRpository = $stayListByFloorRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getStayListByFloor($queryData = [])
    {
        return $this->stayListByFloorRpository->getStayListByFloor($queryData);
    }

}
