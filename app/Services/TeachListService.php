<?php
namespace App\Services;

use App\Repositories\TeachListRepository;


class TeachListService
{
    /**
     * TeachListService constructor.
     * @param teachListRepository $teachListRepository
     */
    public function __construct(teachListRepository $teachListRepository)
    {
        $this->teachListRepository = $teachListRepository;
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList($queryData = [])
    {
        return $this->teachListRepository->getClassList($queryData);
    }

}
