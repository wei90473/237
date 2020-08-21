<?php
namespace App\Services;

use App\Repositories\BeddingLaundryStaticsRepository;


class BeddingLaundryStaticsService
{
    /**
     * LoanBeddingLaundryStaticsService constructor.
     * @param BeddingLaundryStaticsRepository $beddingLaundryStaticsRepository
     */
    public function __construct(BeddingLaundryStaticsRepository $beddingLaundryStaticsRepository)
    {
        $this->beddingLaundryStaticsRepository = $beddingLaundryStaticsRepository;
    }

    /**
     * 取得寢具洗滌數量統計表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getBeddingLaundryStatics($queryData = [])
    {
        return $this->beddingLaundryStaticsRepository->getBeddingLaundryStatics($queryData);
    }
}
