<?php
namespace App\Services;

use App\Repositories\StayStaticsAfterRegRepository;


class StayStaticsAfterRegService
{
    /**
     * StayStaticsAfterRegService constructor.
     * @param StayStaticsAfterRegRepository $stayStaticsAfterRegRpository
     */
    public function __construct(StayStaticsAfterRegRepository $stayStaticsAfterRegRpository)
    {
        $this->stayStaticsAfterRegRpository = $stayStaticsAfterRegRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getStayStaticsAfterReg($queryData = [])
    {
        return $this->stayStaticsAfterRegRpository->getStayStaticsAfterReg($queryData);
    }

}
