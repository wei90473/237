<?php
namespace App\Services;

use App\Repositories\Transfer_processingRepository;


class Transfer_processingService
{
    /**
     * Transfer_processingService constructor.
     * @param Transfer_processingRepository $transfer_processingRpository
     */
    public function __construct(Transfer_processingRepository $transfer_processingRpository)
    {
        $this->transfer_processingRpository = $transfer_processingRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTransfer_processingList($queryData = [])
    {
        return $this->transfer_processingRpository->getTransfer_processingList($queryData);
    }

    public function getT04tbKind($queryData = [])
    {
        return $this->transfer_processingRpository->getT04tbKind($queryData);
    }

    public function checkIdno($queryData = [])
    {
        return $this->transfer_processingRpository->checkIdno($queryData);
    }

    public function doTransfer($queryData = [])
    {
        return $this->transfer_processingRpository->doTransfer($queryData);
    }

    public function doCancel($queryData = [])
    {
        return $this->transfer_processingRpository->doCancel($queryData);
    }

    public function getFile($queryData = [])
    {
        return $this->transfer_processingRpository->getFile($queryData);
    }

    public function TransferExists($queryData = [])
    {
        return $this->transfer_processingRpository->TransferExists($queryData);
    }

    public function getSponsor()
    {
        return $this->transfer_processingRpository->getSponsor();
    }
}
