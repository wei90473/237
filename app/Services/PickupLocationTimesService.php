<?php
namespace App\Services;

use App\Repositories\PickupLocationTimesRepository;


class PickupLocationTimesService
{
    /**
     * PickupLocationTimesService constructor.
     * @param PickupLocationTimesRepository $pickupLocationTimesRpository
     */
    public function __construct(PickupLocationTimesRepository $pickupLocationTimesRpository)
    {
        $this->pickupLocationTimesRpository = $pickupLocationTimesRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPickupLocationTimes($queryData = [])
    {
        return $this->pickupLocationTimesRpository->getPickupLocationTimes($queryData);
    }

}
