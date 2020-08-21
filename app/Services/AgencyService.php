<?php
namespace App\Services;

use App\Repositories\AgencyRepository;
use App\Http\Controllers\Controller;

class AgencyService extends Controller
{
    /**
     * AgencyService constructor.
     * @param AgencyRepository $agencyRepository
     */
    public function __construct(AgencyRepository $agencyRepository)
    {
        $this->agencyRepository = $agencyRepository;
    }

    /**
     * 取得查詢列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getAgencyList($queryData = [])
    {
        return $this->agencyRepository->getAgencyList($queryData);
    }

    /**
     * 取得機構詳細資料
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getAgencyData($queryData = [])
    {
        return $this->agencyRepository->getAgencyData($queryData);
    }

}
