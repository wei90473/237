<?php
namespace App\Services;

use App\Repositories\DemandSurveyRepository;
use App\Models\DemandDistribution;


class DemandSurveyService
{
    /**
     * DemandSurveyService constructor.
     * @param DemandSurveyRepository $demandSurveyRpository
     */
    public function __construct(DemandSurveyRepository $demandSurveyRpository)
    {
        $this->demandSurveyRpository = $demandSurveyRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getDemandSurveyList($queryData = [])
    {
        return $this->demandSurveyRpository->getDemandSurveyList($queryData);
    }

    /**
     * 更新需求調查時,更新需求分配
     *
     * @param $newData
     * @param $oldData
     */
    public function updateDemandDistribution($newData, $oldData)
    {
        // 迴圈跑每一個班級
        foreach($newData as $va){

            // 確認是否存在
            if ( ! DemandDistribution::where('demand_survey_id', $oldData->demand_survey_id)->where('classes_id', $va)->first()) {
                // 不存在時新增
                DemandDistribution::create([
                    'demand_survey_id' => $oldData->demand_survey_id,
                    'classes_id' => $va,
                ]);
            }
        }

        // 將新資料沒有的刪除
        $data = DemandDistribution::where('demand_survey_id', $oldData->demand_survey_id)->whereNotIn('classes_id', $newData)->get();

        foreach($data as $va){
            $va->delete();
        }
    }
    /**
     * 取得未被用過的班別
     *
     * @param $classesId
     * @return mixed
     */
    public function getDemandSurveyClasses($yerly, $times){
        return $this->demandSurveyRpository->getDemandSurveyClasses($yerly, $times);
    }
    /**
     * 取得行政機關列表
     *
     * @param $yerly,$times
     * @return array
     */
    public function getAdministrationList($yerly, $times,$grade=1,$uporgan=NULL){
        return $this->demandSurveyRpository->getAdministrationList($yerly, $times,$grade,$uporgan);
    }
    /**
     * 取得訓練機構列表
     *
     * @param $yerly,$times
     * @return array
     */
    public function getTrainingInstitutionList($yerly, $times){
        return $this->demandSurveyRpository->getTrainingInstitutionList($yerly, $times);
    }
    /**
     * 取得該單位的填表狀況
     *
     * @param $enrollorg
     * @return array
     */
    public function getDemandSurveyData($yerly, $times,$enrollorg,$type=NULL){
        return $this->demandSurveyRpository->getDemandSurveyData($yerly,$times,$enrollorg,$type);
    }
    // 取得左下清單資訊
    public function getTitleMsg($yerly, $times,$enrollorg){
        return $this->demandSurveyRpository->getTitleMsg($yerly, $times,$enrollorg);
    }
    /**
     * 取得列印機關建議班別
     *
     * @param $yerly,$times
     * @return array
     */
    public function getprintdata($yerly, $times,$organ=NULL,$force='N'){
        return $this->demandSurveyRpository->getprintdata($yerly,$times,$organ,$force);
    }
    //取得參訓機關的資格限制 return class array
    public function ufn_csditrain_orgchk($organ,$yerly,$times,$type=NULL,$userid=NULL){
        return $this->demandSurveyRpository->ufn_csditrain_orgchk($organ,$yerly,$times,$type,$userid);
    }
    //查詢該機關所屬機關 return enrollorg 
    public function ufn_sub_organ($organ){
        return $this->demandSurveyRpository->ufn_sub_organ($organ);
    }
    //更新審核層級
    public function updataprogress($class,$enrollorg,$grade){
        return $this->demandSurveyRpository->updataprogress($class,$enrollorg,$grade);
    }
}
