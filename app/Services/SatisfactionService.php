<?php
namespace App\Services;

use App\Repositories\SatisfactionRepository;


class SatisfactionService
{
    /**
     * SatisfactionService constructor.
     * @param SatisfactionRepository $satisfactionRpository
     */
    public function __construct(SatisfactionRepository $satisfactionRpository)
    {
        $this->satisfactionRpository = $satisfactionRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSatisfactionList($queryData = [])
    {
        return $this->satisfactionRpository->getSatisfactionList($queryData);
    }

    public function getSatisfaction($queryData = [])
    {
        return $this->satisfactionRpository->getSatisfaction($queryData);
    }

    public function getSatisfaction2($queryData = [])
    {
        return $this->satisfactionRpository->getSatisfaction2($queryData);
    }

    public function getExport($queryData = [])
    {
        return $this->satisfactionRpository->getExport($queryData);
    }

}
