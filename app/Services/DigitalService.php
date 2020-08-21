<?php
namespace App\Services;

use App\Repositories\DigitalRepository;
use App\Repositories\T04tbRepository;
use App\Repositories\T13tbRepository;
use App\Repositories\ElearnClassRepository;
use App\Repositories\ElearnHistoryRepository;
use DB;

class DigitalService
{
    /**
     * DigitalService constructor.
     * @param DigitalRepository $digitalRepository
     */
    public function __construct(
        DigitalRepository $digitalRepository,
        T04tbRepository $t04tbRepository,
        ElearnClassRepository $elearnClassRepository,
        ElearnHistoryRepository $elearnHistoryRepository,
        T13tbRepository $t13tbRepository
    )
    {
        $this->digitalRepository = $digitalRepository;
        $this->t04tbRepository = $t04tbRepository;
        $this->elearnClassRepository = $elearnClassRepository; 
        $this->elearnHistoryRepository = $elearnHistoryRepository;
        $this->t13tbRepository = $t13tbRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getDigitalList($queryData = [])
    {
        return $this->digitalRepository->getDigitalList($queryData);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        return $this->digitalRepository->getClassList();
    }

    public function getOpenClassList($queryData)
    {
        return $this->t04tbRepository->getByQueryList($queryData);
    }

    public function getT04tb($t04tb_info)
    {
        return $this->t04tbRepository->find($t04tb_info);
    }    

    public function storeClassSetting($t04tb_info, $elearn_classes, $action)
    {
        if ($action == "insert"){
            foreach($elearn_classes as $elearn_class){
                $elearn_class['class'] = $t04tb_info['class'];
                $elearn_class['term'] = $t04tb_info['term'];
                $this->elearnClassRepository->insert($elearn_class);
            }
        }else if ($action == "update"){
            foreach($elearn_classes as $id => $elearn_class){
                $this->elearnClassRepository->update(['id' => $id], $elearn_class);
            }
        }
    }

    public function getElearnHistorys($t04tb)
    {
        $elearn_class_ids = $t04tb->elearn_classes->pluck('id');
        $elearn_historys_data = $this->elearnHistoryRepository->getByElearnIds($elearn_class_ids);
        $elearn_historys = [];
        foreach($elearn_historys_data as $elearn_history){
            $elearn_historys[$elearn_history->elearn_class_id][$elearn_history->idno] = $elearn_history->status;
        }
        return $elearn_historys;
    }

    public function getIdnoByNo($t04tb_info, $nos){
        $t13tbs = $this->t13tbRepository->getByNo($t04tb_info, $nos);
        return $t13tbs->pluck(['idno'])->toArray();
    }


    public function getByIdnos($t04tb_info, $nos){
        $t13tbs = $this->t13tbRepository->getByIdnos($t04tb_info, $nos);
        return $t13tbs->pluck(['idno'])->toArray();
    }
    

    public function storeElearnHistorys($t04tb, $elearn_historys_status)
    {
        $t13tbs = $t04tb->t13tbs()->where('status', '=', 1)->get();    
        $elearn_historys = $this->getElearnHistorys($t04tb);
        DB::beginTransaction();
        try {
            foreach ($elearn_historys_status as $elearn_id => $elearn_history_status){
             
                foreach ($elearn_history_status as $no => $status){
                    $nos[] = (string)$no;
                }
                $updateY = $nos;
                $updateN = $t13tbs->pluck('idno')->toArray();
                foreach($updateN as $idno){
                     $elearn_history = [
                        'elearn_class_id' => $elearn_id,
                        'idno' => $idno
                    ];
                    $elearn_history_nopass = [                        
                        'elearn_class_id' => $elearn_id,
                        'idno' => $idno,
                        'status' =>'N'
                    ];
                    $elearn_info = $this->elearnHistoryRepository->find($elearn_history);  
           
                    if(null === $elearn_info){        
                        $this->elearnHistoryRepository->insert($elearn_history);
                    }else{
                        $this->elearnHistoryRepository->update($elearn_history,$elearn_history_nopass);
                    }
                }                     
                // $this->elearnHistoryRepository->batchNoPass($elearn_id, $updateN);
                $this->elearnHistoryRepository->batchPass($elearn_id, $nos);

                // $idnos = $this->getByIdnos(['class' => $t04tb->class, 'term' => $t04tb->term], $nos);
                // $updateN = array_diff($t13tbs->pluck('idno')->toArray(), $idnos);
                // $inserts = $idnos;
                // if (!empty($elearn_historys[$elearn_id])){
                //     $inserts = array_diff($idnos, array_keys($elearn_historys[$elearn_id]));               
                // }
                // foreach($inserts as $idno){
                //     $elearn_history = [
                //         'elearn_class_id' => $elearn_id,
                //         'idno' => $idno,
                //         'status' => 'Y'
                //     ];
                //     $this->elearnHistoryRepository->insert($elearn_history);
                // }   
                // $this->elearnHistoryRepository->batchNoPass($elearn_id, $updateN);
                // $this->elearnHistoryRepository->batchPass($elearn_id, array_diff($idnos, $inserts));
            }

            DB::commit();
            
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;            
            return false;

        } 


    }
    public function updateElearnHistorysByEplus($elearn_id,$elearn_historys_status)
    {    
        DB::beginTransaction();
        try {
            $elearn_history = [
                'elearn_class_id' => $elearn_id,
                'idno' =>$elearn_historys_status->personal_id
                ];
                $elearn_history_pass = [                        
                    'elearn_class_id' => $elearn_id,
                    'idno' =>$elearn_historys_status->personal_id,
                    'status' =>'Y'
                ];
                $elearn_info = $this->elearnHistoryRepository->find($elearn_history);  

                if(null === $elearn_info){        
                    $this->elearnHistoryRepository->insert($elearn_history_pass);
                }else{
                    $this->elearnHistoryRepository->update($elearn_history,$elearn_history_pass);
                }
                                
            // $this->elearnHistoryRepository->batchNoPass($elearn_historys_status->course_code, $updateN);
            // $this->elearnHistoryRepository->batchPass($elearn_historys_status->course_code, $nos);
            

            DB::commit();
            
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;            
            return false;

        } 


    }


}
