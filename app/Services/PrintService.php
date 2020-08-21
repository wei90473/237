<?php
namespace App\Services;

use App\Repositories\PrintRepository;


class PrintService
{
    /**
     * WaitingService constructor.
     * @param PrintRepository $siteScheduleRpository
     */
    public function __construct(PrintRepository $printRpository)
    {
        $this->printRpository = $printRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPrintList($queryData = [])
    {
        return $this->printRpository->getPrintList($queryData);
    }
}