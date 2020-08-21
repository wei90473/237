<?php
namespace App\Services;

use App\Repositories\PunchRepository;
use App\Repositories\T04tbRepository;
use App\Repositories\T84tbRepository;

class PunchService
{
    /**
     * PunchService constructor.
     * @param PunchRepository $punchRepository
     */
    public function __construct(
        T04tbRepository $t04tbRepository,
        T84tbRepository $t84tbRepository
    )
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->t84tbRepository = $t84tbRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    // public function getPunchList($queryData = [])
    // {
    //     return $this->punchRepository->getPunchList($queryData);
    // }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getOpenClassList($queryData)
    {
        return $this->t04tbRepository->getByQueryList($queryData);
    }

    public function getT04tb($t04tb_info)
    {
        return $this->t04tbRepository->find($t04tb_info);
    }    

    public function getT84tbs($t04tb_info, $queryData)
    {
        $queryData = array_merge($t04tb_info, $queryData);
        return $this->t84tbRepository->get($queryData, false);
    }
}
