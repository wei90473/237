<?php
namespace App\Services;

use App\Repositories\EffectivenessSurveyRepository;


class EffectivenessSurveyService
{
    /**
     * EffectivenessSurveyService constructor.
     * @param EffectivenessSurveyRepository $effectivenessSurveyRpository
     */
    public function __construct(EffectivenessSurveyRepository $effectivenessSurveyRpository)
    {
        $this->effectivenessSurveyRpository = $effectivenessSurveyRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getEffectivenessSurveyList($queryData = [],$mode=null)
    {
        return $this->effectivenessSurveyRpository->getEffectivenessSurveyList($queryData,$mode);
    }

    //取得T53tb
    public function getT53tb($data=[],$class_info=[])
    {
        return $this->effectivenessSurveyRpository->getT53tb($data,$class_info);
    }
    public function getClassTimes($class_info)
    {
        return $this->effectivenessSurveyRpository->getClassTimes($class_info);
    }
    public function getAns($class_info)
    {
        return $this->effectivenessSurveyRpository->getAns($class_info);
    }

}
