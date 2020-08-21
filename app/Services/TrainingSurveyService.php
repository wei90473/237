<?php
namespace App\Services;

use App\Repositories\TrainingSurveyRepository;


class TrainingSurveyService
{
    /**
     * TrainingSurveyService constructor.
     * @param TrainingSurveyRepository $trainingSurveyRpository
     */
    public function __construct(TrainingSurveyRepository $trainingSurveyRpository)
    {
        $this->trainingSurveyRpository = $trainingSurveyRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTrainingSurveyList($queryData = [])
    {
        return $this->trainingSurveyRpository->getTrainingSurveyList($queryData);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        return $this->trainingSurveyRpository->getClassList();
    }

    /**
     * 取得未選取的課程
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourseNotSelectList($class, $term)
    {
        return $this->trainingSurveyRpository->getCourseNotSelectList($class, $term);
    }

    /**
     * 取得已選取的課程
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourseSelectList($class, $term)
    {
        return $this->trainingSurveyRpository->getCourseSelectList($class, $term);
    }

    /**
     * 取得所有課程(新增用)
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourseAllList($class, $term)
    {
        return $this->trainingSurveyRpository->getCourseAllList($class, $term);
    }
}
