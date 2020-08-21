<?php
namespace App\Services;

use App\Repositories\WaitingRepository;


class WaitingService
{
    /**
     * WaitingService constructor.
     * @param WaitingRepository $waitingRpository
     */
    public function __construct(WaitingRepository $waitingRpository)
    {
        $this->waitingRpository = $waitingRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getWaitingList($queryData = [])
    {
        return $this->waitingRpository->getWaitingList($queryData);
    }

    public function getDetailList($queryData = [])
    {
        return $this->waitingRpository->getDetailList($queryData);
    }

    public function getClass($queryData = [])
    {
        return $this->waitingRpository->getClass($queryData);
    }

    public function getById($id=NULL)
    {
        return $this->waitingRpository->getById($id);
    }

    public function getHourById($id=NULL)
    {
        return $this->waitingRpository->getHourById($id);
    }

    public function getEditDelete($id=NULL)
    {
        return $this->waitingRpository->getEditDelete($id);
    }

    public function WhenMark($id=NULL)
    {
        return $this->waitingRpository->WhenMark($id);
    }

    public function notpay($id=NULL)
    {
        return $this->waitingRpository->notpay($id);
    }

    public function pay($id=NULL)
    {
        return $this->waitingRpository->pay($id);
    }

    public function MarkDelete($id=NULL)
    {
        return $this->waitingRpository->MarkDelete($id);
    }

    public function getSponsor()
    {
        return $this->waitingRpository->getSponsor();
    }

    public function getProfchk($class=NULL)
    {
        return $this->waitingRpository->getProfchk($class);
    }
}
