<?php
namespace App\Services;

use App\Repositories\RecommendUserRepository;
use App\Http\Controllers\Controller;

class RecommendUserService extends Controller
{
    /**
     * RecommendUserService constructor.
     * @param RecommendUserRepository $recommendUserRepository
     */
    public function __construct(RecommendUserRepository $recommendUserRepository)
    {
        $this->recommendUserRepository = $recommendUserRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getRecommendUserList($queryData = [])
    {
        return $this->recommendUserRepository->getRecommendUserList($queryData);
    }
}
