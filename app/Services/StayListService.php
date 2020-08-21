<?php
namespace App\Services;

use App\Repositories\StayListRepository;


class StayListService
{
    /**
     * StayListService constructor.
     * @param StayListRepository $stayListRepository
     */
    public function __construct(StayListRepository $stayListRepository)
    {
        $this->stayListRepository = $stayListRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getStayListList($queryData = [])
    {
        return $this->stayListRepository->getStayListList($queryData);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        return $this->stayListRepository->getClassList();
    }
}
