<?php
namespace App\Services;

use App\Repositories\LoanBeddingLaundryStaticsRepository;


class LoanBeddingLaundryStaticsService
{
    /**
     * LoanBeddingLaundryStaticsService constructor.
     * @param LoanBeddingLaundryStaticsRepository $loanBeddingLaundryStaticsRpository
     */
    public function __construct(LoanBeddingLaundryStaticsRepository $loanBeddingLaundryStaticsRpository)
    {
        $this->loanBeddingLaundryStaticsRpository = $loanBeddingLaundryStaticsRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLoanBeddingLaundryStatics($queryData = [])
    {
        return $this->loanBeddingLaundryStaticsRpository->getLoanBeddingLaundryStatics($queryData);
    }

    /**
     * 取得寢具洗滌數量統計表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getBeddingLaundryStatics($queryData = [])
    {
        return $this->loanBeddingLaundryStaticsRpository->getBeddingLaundryStatics($queryData);
    }
}
