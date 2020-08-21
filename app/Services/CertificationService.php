<?php
namespace App\Services;

use App\Repositories\CertificationRepository;


class CertificationService
{
    /**
     * CertificationService constructor.
     * @param CertificationRepository $certificationRepository
     */
    public function __construct(CertificationRepository $certificationRepository)
    {
        $this->certificationRepository = $certificationRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getCertificationList($queryData = [])
    {
        return $this->certificationRepository->getCertificationList($queryData);
    }

    public function getCertificationRowData($id)
    {
        return $this->certificationRepository->getCertificationRowData($id);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        return $this->certificationRepository->getClassList();
    }
}
