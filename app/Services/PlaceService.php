<?php
namespace App\Services;

use App\Repositories\PlaceRepository;
use App\Http\Controllers\Controller;

class PlaceService extends Controller
{
    /**
     * PlaceService constructor.
     * @param PlaceRepository $placeRepository
     */
    public function __construct(PlaceRepository $placeRepository)
    {
        $this->placeRepository = $placeRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPlaceList($queryData = [])
    {
        return $this->placeRepository->getPlaceList($queryData);
    }
}
