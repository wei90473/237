<?php
namespace App\Services;

use App\Repositories\ItineracyRepository;


class ItineracyService
{
    /**
     * ItineracyService constructor.
     * @param ItineracyRepository $itineracyRpository
     */
    public function __construct(ItineracyRepository $itineracyRepository)
    {
        $this->itineracyRepository = $itineracyRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getItineracyList($queryData = [])
    {
        return $this->itineracyRepository->getItineracyList($queryData);
    }

    /**
     * 取得代號最大值
     *
     * @param $type 關鍵字
     * @return Max
     */
    public function getItineracyMax($type)
    {
        return $this->itineracyRepository->getItineracyMax($type);
    }
    //寫入代碼庫
    public function insertItineracy($queryData = [])
    {
        return $this->itineracyRepository->insertItineracy($queryData);
    }

    /**
     * 取得年度主題列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getAnnualList($queryData = [])
    {
        return $this->itineracyRepository->getAnnualList($queryData);
    }

    /**
     * 取得年度主題當年度最新期別
     *
     * @param $yerly 關鍵字
     * @return Max
     */
    public function getAnnualMax($yerly)
    {
        return $this->itineracyRepository->getAnnualMax($yerly);
    }
    /**
     * 取得該年度主題列表內容
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getAnnual($queryData = []){
        return $this->itineracyRepository->getAnnual($queryData);
    }
    /**
     * 取得該年度填報資料
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getList($queryData = []){
        return $this->itineracyRepository->getList($queryData);

    }
    /**
     * 取得該年度填報資料(縣市別)
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getScheduleList($queryData = []){
        return $this->itineracyRepository->getScheduleList($queryData);

    }
    /**
     * 取得日程表列印資料(縣市別)
     *
     * @param array $queryData 關鍵字
     * @return array
     */
    public function getSchedulePrintList($queryData = []){
        return $this->itineracyRepository->getSchedulePrintList($queryData);

    }
    public function updateSurvey($queryData = []){
        return $this->itineracyRepository->updateSurvey($queryData);
    }
}
