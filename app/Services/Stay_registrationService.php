<?php
namespace App\Services;

use App\Repositories\Stay_registrationRepository;


class Stay_registrationService
{
    /**
     * Stay_registrationService constructor.
     * @param Stay_registrationRepository $stay_registrationRpository
     */
    public function __construct(Stay_registrationRepository $stay_registrationRpository)
    {
        $this->stay_registrationRpository = $stay_registrationRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getStay_registration($queryData = [])
    {
        return $this->stay_registrationRpository->getStay_registration($queryData);
    }

}
