<?php
namespace App\Services;

use App\Repositories\TrainingProcessRepository;


class TrainingProcessService
{
    /**
     * TrainingProcessService constructor.
     * @param TrainingProcessRepository $trainingProcessRepository
     */
    public function __construct(TrainingProcessRepository $trainingProcessRepository)
    {
        $this->trainingProcessRepository = $trainingProcessRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTrainingProcessList($queryData = [])
    {
        return $this->trainingProcessRepository->getTrainingProcessList($queryData);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        return $this->trainingProcessRepository->getClassList();
    }

    /**
     * 取得課程(新增時使用)
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourseForCreate($class, $term)
    {
        return $this->trainingProcessRepository->getCourseForCreate($class, $term);
    }

    /**
     * 取得課程
     *
     * @param $class
     * @param $term
     * @return mixed
     */
    public function getCourse($class, $term, $serno)
    {
        return $this->trainingProcessRepository->getCourse($class, $term, $serno);
    }
}
