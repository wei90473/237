<?php
namespace App\Services;

use App\Repositories\TrainRepository;


class TrainService
{
    /**
     * TrainService constructor.
     * @param TrainRepository $trainRpository
     */
    public function __construct(TrainRepository $trainRpository)
    {
        $this->trainRpository = $trainRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTrainList($queryData = [])
    {
        return $this->trainRpository->getTrainList($queryData);
    }
}
