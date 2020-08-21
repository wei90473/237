<?php
namespace App\Services;

use App\Repositories\NewsTwRepository;


class NewsTwService
{
    /**
     * NewsTwService constructor.
     * @param NewsTwRepository $newsTwRpository
     */
    public function __construct(NewsTwRepository $newsTwRpository)
    {
        $this->newsTwRpository = $newsTwRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getNewsTwList($queryData = [])
    {
        return $this->newsTwRpository->getNewsTwList($queryData);
    }
}
