<?php
namespace App\Services;

use App\Repositories\HolidayRepository;
use App\Http\Controllers\Controller;

class HolidayService extends Controller
{
    /**
     * HolidayService constructor.
     * @param HolidayRepository $holidayRepository
     */
    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getHolidayList($queryData = [])
    {
        return $this->holidayRepository->getHolidayList($queryData);
    }
}
