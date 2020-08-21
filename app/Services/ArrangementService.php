<?php
namespace App\Services;

use App\Repositories\ArrangementRepository;
use App\Repositories\T04tbRepository;
use App\Repositories\T05tbRepository;
use App\Repositories\T06tbRepository;
use App\Repositories\T01tbRepository;
use App\Repositories\T08tbRepository;
use App\Repositories\T09tbRepository;
use App\Repositories\T56tbRepository;
use App\Repositories\T54tbRepository;

use DB;
use App\Helpers\SystemParam;

class ArrangementService
{
    /**
     * WaitingService constructor.
     * @param ArrangementRepository $siteScheduleRpository
     */
    public function __construct(
        ArrangementRepository $ArrangementRpository,
        T04tbRepository $t04tbRepository,
        T06tbRepository $t06tbRepository,
        T01tbRepository $t01tbRepository,
        T05tbRepository $t05tbRepository,
        T08tbRepository $t08tbRepository,
        T09tbRepository $t09tbRepository,
        T56tbRepository $t56tbRepository,
        T54tbRepository $t54tbRepository
    )
    {
        $this->ArrangementRpository = $ArrangementRpository;
        $this->t04tbRepository = $t04tbRepository;
        $this->t06tbRepository = $t06tbRepository;
        $this->t01tbRepository = $t01tbRepository;
        $this->t05tbRepository = $t05tbRepository;
        $this->t08tbRepository = $t08tbRepository;
        $this->t09tbRepository = $t09tbRepository;
        $this->t56tbRepository = $t56tbRepository;
        $this->t54tbRepository = $t54tbRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getArrangementList($queryData = [])
    {
        return $this->ArrangementRpository->getArrangementList($queryData);
    }

    public function test()
    {
        return $this->ArrangementRpository->test();
    }

    public function getOpenClassList($queryData)
    {
        return $this->t04tbRepository->getByQueryList($queryData);
    }

    public function getT04tb($class_info)
    {
        $t04tb = $this->t04tbRepository->find($class_info);
        return $t04tb;
    }

    public function getT06tb($course_info)
    {
        $t06tb = $this->t06tbRepository->find($course_info);
        return $t06tb;
    }

    public function checkIsPayed($t06tbKey)
    {
        return $this->t09tbRepository->getIsPayed($t06tbKey) > 0;
    }

    public function storeT06tb($t06tb, $action, $course_info = null)
    {   
        $t06tb['is_must_read'] = (empty($t06tb['is_must_read'])) ? 0 : 1;
        if($action == "insert"){
            $course_info = [
                "class" => $t06tb['class'],
                "term" => $t06tb['term']
            ];
            $course = $this->t06tbRepository->getLastCourse($course_info);
            $new_course = str_pad((int)$course+1, 2, '0', STR_PAD_LEFT);
            $t06tb['course'] = $new_course;
            $t06tb['hour'] = (empty($t06tb['hour']))? 0 : $t06tb['hour'];
            return $this->t06tbRepository->insert($t06tb);
        }elseif($action == "update"){
            return $this->t06tbRepository->update($course_info, $t06tb);
        }
    }

    public function uploadSchedule($class, $content)
    {
        return $this->t01tbRepository->update($class, ['planmk' => $content]);
    }
    
    public function getT01tb($class)
    {
        return $this->t01tbRepository->find($class);
    }

    public function getHoursInfo($class_info)
    {
        return $this->t06tbRepository->getHoursInfo($class_info);
    }

    public function copyT06tb($copy_info, $copySchedule = false)
    {
        $count = $this->t06tbRepository->getByClassNoDefault($copy_info['copy_purpose']);
        if ($count > 0){
            return ['status' => 1, 'message' => 'exsit', 'exsit' => 't06tb'];
        }
        
        $count = $this->t05tbRepository->getData($copy_info['copy_purpose'], '*', [], 'count');
        if ($count > 0){
            return ['status' => 1, 'message' => 'exsit', 'exsit' => 't05tb'];
        }

        $count = $this->t08tbRepository->getData($copy_info['copy_purpose'], '*', [], 'count');
        if ($count > 0){
            return ['status' => 1, 'message' => 'exsit', 'exsit' => 't08tb'];
        }
        // 課程表資料檔
        $copyedT06tbs = $this->t06tbRepository->getData($copy_info['copyed']);
        // 課程配當單元資料檔
        $copyedT05tbs = $this->t05tbRepository->getData($copy_info['copyed']);
        // 擬聘講座資料檔
        $copyedT08tbs = $this->t08tbRepository->getData($copy_info['copyed']);

        $copyedT06tbs->map(function($t06tb) use ($copy_info){
            $t06tb->class = $copy_info['copy_purpose']['class'];
            $t06tb->term = $copy_info['copy_purpose']['term'];
            $t06tb->date = null;
            $t06tb->site = null;        
            $t06tb->branch = null; 
            $t06tb->location = null;
            return $t06tb;
        });

        $copyedT05tbs->map(function($t05tb) use ($copy_info){
            $t05tb->class = $copy_info['copy_purpose']['class'];
            $t05tb->term = $copy_info['copy_purpose']['term'];
            return $t05tb;
        });

        $copyedT08tbs->map(function($t08tb) use ($copy_info){
            $t08tb->class = $copy_info['copy_purpose']['class'];
            $t08tb->term = $copy_info['copy_purpose']['term'];
            $t08tb->hire = 'N';
            return $t08tb;
        });

        // dd($copyedT06tbs, $copyedT05tbs, $copyedT08tbs);

        DB::beginTransaction();
            
        try {
            $this->t06tbRepository->delete($copy_info['copy_purpose']);

            foreach ($copyedT06tbs->toArray() as $t06tb){
                $this->t06tbRepository->insert($t06tb);
            }
            
            foreach ($copyedT05tbs->toArray() as $t05tb){
                $this->t05tbRepository->insert($t05tb);
            }
            
            if ($copySchedule){
                foreach ($copyedT08tbs->toArray() as $t08tb){
                    $this->t08tbRepository->insert($t08tb);
                }
            }

            // DB::rollback();
            DB::commit();
            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => 'fail'];
        }
        
    }

    public function deleteT06tb($course_info)
    {
        DB::beginTransaction();
        
        try {
            // 刪除【t06tb 課程表資料】 資料
            $this->t06tbRepository->delete($course_info);

            // 刪除【t08tb 擬聘講座資料】資料
            $this->t08tbRepository->delete($course_info);

            // 刪除【t09tb 講座聘任資料】資料
            $this->t09tbRepository->delete($course_info);

            // 刪除【t54tb 成效問卷題目檔二】
            $this->t54tbRepository->delete($course_info);

            // 刪除【t56tb 成效問卷資料檔二】資料
            $this->t56tbRepository->delete($course_info);

            // 刪除【t98tb 講座教學教法資料檔】資料
            $this->t06tbRepository->delete($course_info);                                    

            // 重新計算補充保費(同一班期同一講師同一日 累計 鐘點費、稿酬、講演費)
            $this->updateInsurerate($course_info);

            // DB::rollback();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
        // return $this->t06tbRepository->delete($course_info);
    }

    public function updateInsurerate($course_info){
        $class_info = [
            'class' => $course_info['class'],
            'term' => $course_info['term']
        ];
        $t04tb = $this->t04tbRepository->find($class_info);
        $insurerate = SystemParam::get()->insurerate;
        $insurerate_info = $this->t09tbRepository->getComputeInsurerateInfo($class_info);

        $total = [];

        foreach($insurerate_info as $t09tb){
            if (empty($total[$t09tb->idno])){
                $total[$t09tb->idno]['lectamt_acc'] = 0;
                $total[$t09tb->idno]['noteamt_acc'] = 0;
                $total[$t09tb->idno]['speakamt_acc'] = 0; 
            } 
            $total[$t09tb->idno]['lectamt_acc'] += $t09tb->lectamt;
            $total[$t09tb->idno]['noteamt_acc'] += $t09tb->noteamt;
            $total[$t09tb->idno]['speakamt_acc'] += $t09tb->speakamt;
        }
        $insureamt1_new = [];
        foreach($insurerate_info as $t09tb){

            $insureamt1_new = 0;
            $insureamt2_new = 0;

            if ($t09tb->insuremk1 == 'Y'){
                if ($t09tb->t06tb_date < 1030901 && ($total[$t09tb->idno]['lectamt_acc'] + $total[$t09tb->idno]['speakamt_acc']) > 5000){
                    $insureamt1_new = round(($t09tb->lectamt + $t09tb->speakamt) * $insurerate, 0);
                }else if (($t09tb->t06tb_date >= 1030901 && $t09tb->t06tb_date <= 1040630) && ($total[$t09tb->idno]['lectamt_acc'] + $total[$t09tb->idno]['speakamt_acc']) > 19273){
                    $insureamt1_new = round(($t09tb->lectamt + $t09tb->speakamt) * $insurerate, 0);
                }else if ($t09tb->t06tb_date >= 1040701 && ($total[$t09tb->idno]['lectamt_acc'] + $total[$t09tb->idno]['speakamt_acc']) >= 20008){
                    $insureamt1_new = round(($t09tb->lectamt + $t09tb->speakamt) * $insurerate, 0);
                }
            }

            if ($t09tb->insuremk2 == 'Y'){
                if ($t09tb->t06tb_date < 1050101 && $total[$t09tb->idno]['lectamt_acc'] > 5000){
                    $insureamt2_new = round($t09tb->noteamt * $insurerate, 0);
                }

                if ($t09tb->t06tb_date >= 1050101 && $total[$t09tb->idno]['noteamt_acc'] >= 20000){
                    $insureamt2_new = round($t09tb->noteamt * $insurerate, 0);
                }

            }

            // 補充保費 未改變資料 停止
            if (($t09tb->insureamt1 == $insureamt1_new) && $t09tb->insureamt2 == $insureamt2_new) {
                continue;
            }

            $t09tb->insureamt1 = $insureamt1_new;
            $t09tb->insureamt2 = $insureamt2_new;
            $t09tb->insuretot = $insureamt1_new + $insureamt2_new;
            $t09tb->netpay = $t09tb->teachtot - $t09tb->deductamt - $t09tb->insuretot;
            $t09tb->totalpay = $t09tb->netpay + $t09tb->tratot;
            
            $t09tb->save();
        }

    }
}