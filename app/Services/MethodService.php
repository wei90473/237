<?php
namespace App\Services;

use App\Repositories\MethodRepository;


class MethodService
{
    /**
     * MethodService constructor.
     * @param MethodRepository $methodRepository
     */
    public function __construct(MethodRepository $methodRepository)
    {
        $this->methodRepository = $methodRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getMethodList($queryData = [])
    {
        return $this->methodRepository->getMethodList($queryData);
    }

    public function getMethodRowData($id)
    {
        return $this->methodRepository->getMethodRowData($id);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList($queryData = [])
    {
        return $this->methodRepository->getClassList($queryData);
    }

    public function getClass($queryData = [])
    {
        return $this->methodRepository->getClass($queryData);
    }

    /**
     * 取得課程表
     *
     * @return mixed
     */
    public function getCurriculumList($class,$term,$filter=NUll){
        return $this->methodRepository->getCurriculumList($class,$term,$filter);
    }

    /**
     * 取得教材印製統計處理列表
     *
     * @return mixed
     */
    public function getMaterialList($queryData = []){
        return $this->methodRepository->getMaterialList($queryData);
    }

    public function getMaterialListNew($queryData = []){
        return $this->methodRepository->getMaterialListNew($queryData);
    }

    public function getTeachWayList($queryData=[]){
        return $this->methodRepository->getTeachWayList($queryData);
    }
    public function getSatisfactionList($queryData=[]){
        return $this->methodRepository->getSatisfactionList($queryData);
    }
}
