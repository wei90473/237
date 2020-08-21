<?php
namespace App\Services;

use App\Repositories\TrainCertificationRepository;


class TrainCertificationService
{
    /**
     * TrainCertificationService constructor.
     * @param TrainCertificationRepository $trainCertificationRepository
     */
    public function __construct(TrainCertificationRepository $trainCertificationRepository)
    {
        $this->trainCertificationRepository = $trainCertificationRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTrainCertificationList($queryData = [])
    {
        return $this->trainCertificationRepository->getTrainCertificationList($queryData);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        return $this->trainCertificationRepository->getClassList();
    }
}
