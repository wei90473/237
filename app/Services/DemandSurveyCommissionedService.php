<?php
namespace App\Services;

use App\Repositories\DemandSurveyCommissionedRepository;
use App\Models\DemandDistribution;


class DemandSurveyCommissionedService
{
    /**
     * DemandSurveyCommissionedService constructor.
     * @param DemandSurveyCommissionedRepository $demandSurveyCommissioneRpository
     */
    public function __construct(DemandSurveyCommissionedRepository $demandSurveyCommissioneRpository)
    {
        $this->demandSurveyCommissioneRpository = $demandSurveyCommissioneRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getDemandSurveyList($queryData = [])
    {
        return $this->demandSurveyCommissioneRpository->getDemandSurveyList($queryData);
    }

    /**
     * 取得專班列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getDemandSurveyPreList($queryData = [])
    {
        return $this->demandSurveyCommissioneRpository->getDemandSurveyPreList($queryData);
    }

    /**
     * 取得審核中與審核通過的專班列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getDemandSurveyAuditList($queryData = [])
    {
        return $this->demandSurveyCommissioneRpository->getDemandSurveyAuditList($queryData);
    }

}
