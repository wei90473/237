<?php
namespace App\Services;

use App\Repositories\DemandDistributionRepository;


class DemandDistributionService
{
    /**
     * DemandDistributionService constructor.
     * @param DemandDistributionRepository $demandDistributionRpository
     */
    public function __construct(DemandDistributionRepository $demandDistributionRpository)
    {
        $this->demandDistributionRpository = $demandDistributionRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassList($queryData = [])
    {
        return $this->demandDistributionRpository->getClassList($queryData);
    }


    /**
     * 取得該年度該院區的調查次數
     *
     * @param array $queryData 年度院區
     * @return mixed
     */
    public function getTimesByYearBranchList($queryData = [])
    {
        return $this->demandDistributionRpository->getTimesByYearBranchList($queryData);
    }

}
