<?php
namespace App\Services;

use App\Repositories\StayDistributionByClassRepository;


class StayDistributionByClassService
{
    /**
     * StayDistributionByClassService constructor.
     * @param StayDistributionByClassRepository $stayDistributionByClassRpository
     */
    public function __construct(StayDistributionByClassRepository $stayDistributionByClassRpository)
    {
        $this->stayDistributionByClassRpository = $stayDistributionByClassRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getStayDistributionByClass($queryData = [])
    {
        return $this->stayDistributionByClassRpository->getStayDistributionByClass($queryData);
    }

}
