<?php
namespace App\Services;

use App\Repositories\NewsEnRepository;


class NewsEnService
{
    /**
     * NewsEnService constructor.
     * @param NewsEnRepository $newsEnRpository
     */
    public function __construct(NewsEnRepository $newsEnRpository)
    {
        $this->newsEnRpository = $newsEnRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getNewsEnList($queryData = [])
    {
        return $this->newsEnRpository->getNewsEnList($queryData);
    }
}
