<?php
namespace App\Services;

use App\Repositories\ClassesRequirementsRepository;
use DB;
use App\Helpers\SystemParam;

class ClassesRequirementsService
{
    /**
     * WaitingService constructor.
     * @param ClassesRequirementsRepository $siteScheduleRpository
     */
    public function __construct(
        ClassesRequirementsRepository $ClassesRequirementsRpository    ){
        $this->ClassesRequirementsRpository = $ClassesRequirementsRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassesRequirementsList($queryData = [])
    {
        return $this->ClassesRequirementsRpository->getClassesRequirementsList($queryData);
    }

    // 更新單價
    public function updataunitprice($queryData=[]){
        return $this->ClassesRequirementsRpository->updataunitprice($queryData);
    }

    // 編輯頁
    public function getEditList($queryData = []){
        return $this->ClassesRequirementsRpository->getEditList($queryData);
    }
    
     // 計算出膳宿數量
    public function getLiveList($queryData = [] ){
        return $this->ClassesRequirementsRpository->getLiveList($queryData);
    }
    // 伙食費核銷總表
    public function getFoodExpenseList($queryData = [] ){
        return $this->ClassesRequirementsRpository->getFoodExpenseList($queryData);
    }
}