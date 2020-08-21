<?php
namespace App\Services;

use App\Repositories\RecommendRepository;


class RecommendService
{
    /**
     * RecommendService constructor.
     * @param RecommendRepository $recommendRpository
     */
    public function __construct(RecommendRepository $recommendRpository)
    {
        $this->recommendRpository = $recommendRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getRecommendList($queryData = [])
    {
        return $this->recommendRpository->getRecommendList($queryData);
    }
}
