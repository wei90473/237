<?php
namespace App\Services;

use App\Repositories\PeriodsRepository;
use App\Models\T51tb;
use App\Repositories\T03tbRepository;
use App\Repositories\T04tbRepository;
use App\Repositories\T01tbRepository;
use App\Repositories\T51tbRepository;
use App\Repositories\T02tbRepository;
use App\Repositories\M17tbRepository;
use DB;

class PeriodsService
{
    /**
     * PeriodsService constructor.
     * @param PeriodsRepository $periodsRpository
     */
    public function __construct(
        PeriodsRepository $periodsRpository,
        T03tbRepository $t03tbRepository,
        T01tbRepository $t01tbRepository,
        T51tbRepository $t51tbRepository,
        T02tbRepository $t02tbRepository,
        M17tbRepository $m17tbRepository,
        T04tbRepository $t04tbRepository
    )
    {
        $this->periodsRpository = $periodsRpository;
        $this->t03tbRepository = $t03tbRepository;
        $this->t01tbRepository = $t01tbRepository;
        $this->t51tbRepository = $t51tbRepository;
        $this->t02tbRepository = $t02tbRepository;
        $this->m17tbRepository = $m17tbRepository;
        $this->t04tbRepository = $t04tbRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassList($queryData = [])
    {
        return $this->t01tbRepository->get($queryData, true, "*", ['t02tbs', 't03tbs']);
    }


    /**
     * 分配班期人數
     * @param int   class => 班號 
     * @param int   assign_num => 分配人數
     * @param int   start_term => 開始期別
     * @param int   end_term => 結束期別
     */
    public function assign($class, $assign_num, $start_term, $end_term)
    {
        if (empty($class) || !isset($assign_num) || empty($start_term) || empty($end_term)) return false;
        
        $t01tb = $this->periodsRpository->getAssignT01tb($class);

        if (empty($t01tb)) return false;

        $t02tbs = $t01tb->t02tbs()->with('m13tb')->get()->sortBy('m13tb.rank');
        
        $assigned = $t01tb->t03tbs->groupBy('term')->map(function($t03tb_group){
            return $t03tb_group->pluck('quota')->sum();
        });

        // 去除這次要分配的期別後剩餘的已分配人數
        $quota_without_now_assign = $assigned->map(function($sum, $term) use($start_term, $end_term){
            if ($start_term <= $term && $term <= $end_term) return 0;
            return $sum;
        })->sum();

        // 可分配人數總和
        $total_quota = $t02tbs->pluck('quota', 'organ')->sum();
        // 剩餘可分配人數 = 可分配人數總和 - 已分配人數(不包含此次要分配的期數)
        $total_quota -= $quota_without_now_assign;
        $total_quota = ($total_quota > $assign_num) ? $assign_num : $total_quota;

        // 開始分配
        $assign_result = $this->exec_assign($t01tb, $t02tbs, $total_quota, $start_term, $end_term);
        $t03tbs = $t01tb->t03tbs->groupBy('term')->map(function($t03tb_group){
            return $t03tb_group->keyBy('organ');
        });

        DB::beginTransaction();

        try {
        
            $this->t03tbRepository->clearOldAssignData($class, $start_term, $end_term);

            foreach ($assign_result as $organ => $assign){      
                foreach($assign as $term => $quota){
                    $term = str_pad($term, 2, '0', STR_PAD_LEFT);
                    $class = (string)$class;
                    $t03tb_info = compact(['class', 'term', 'organ']);
                    $t03tb = [
                        'quota' => $quota
                    ];
    
                    if (isset($t03tbs[$term][$organ])){
                        $this->t03tbRepository->update($t03tb_info, $t03tb);
                    }else{
                        $t03tb = array_merge($t03tb, $t03tb_info);
                        $this->t03tbRepository->insert($t03tb);
                    }   
                }
            }

            $this->t03tbRepository->delete(['quota' => 0, 'class' => $class]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
            var_dump($e->getMessage());
            die;
        }   
    }
    /*
        執行分配
    */
    public function exec_assign($t01tb, $t02tbs, $total_quota, $start_term, $end_term)
    {
        // 如果剩餘可分配人數低於 0 
        if ($total_quota < 0) return false;
        // 計算總共有幾期
        $term_count = $end_term - $start_term + 1;      
        // 如果輸入的期別有問題
        if ($term_count <= 0) return false;

        // 清除以前分配的紀錄

        // 取得各機關已分配人數
        $t03tbs = $t01tb->t03tbs->map(function($t03tb) use($start_term, $end_term){
            if ($start_term <= $t03tb->term && $t03tb->term <= $end_term) return null;
            return $t03tb;
        })->filter();

        $t03tbs = $t03tbs->groupBy('organ')->map(function($t03tb_group){
            return $t03tb_group->sum('quota');
        });

        $t02tbs = $t02tbs->pluck('quota', 'organ');

        // 多餘的人輪到哪一期
        $assign_result = [];
        
        foreach($t02tbs as $organ => $quota){
            // 減去已分配人數
            if (isset($t03tbs[$organ])){
                $quota = ($quota < $t03tbs[$organ]) ? 0 : $quota - $t03tbs[$organ];
            }

            $avg = floor($quota / $term_count);

            for($i = $start_term; $i <= $end_term; $i++){
                if ($total_quota - $avg >= 0){
                    $assign_result[$organ][$i] = $avg;
                    $total_quota -=  $avg;
                    $quota -= $avg;
                }else{
                    $assign_result[$organ][$i] = $total_quota;
                    $t02tb_quota = 0;      
                    $quota = 0;                      
                }
            }    
            $t02tbs[$organ] = $quota;
        }

        $mod_term = $start_term;
        foreach($t02tbs as $organ => $quota){
            while($quota > 0 && $total_quota > 0){
                if ($mod_term > $end_term) $mod_term = $start_term;

                if ($quota > 0 && $total_quota - 1 >= 0){
                    $assign_result[$organ][$mod_term]++;
                    $total_quota--;
                    $quota--;
                    $mod_term++;
                }
            }

        }

        return $assign_result;   
    }

    public function exec_online_update($classes)
    {
        $t04tbs = $this->t04tbRepository->_getByIn($classes);
        $t04tbs = $t04tbs->groupBy('class')->map(function($t04tb_group){
            return $t04tb_group->keyBy('term');
        });

        $t51tbs = $this->t51tbRepository->getByIn($classes);

        $t51tbs = $t51tbs->groupBy('class')->map(function($t51tb_group){
            return $t51tb_group->groupBy('term')->map(function($terms_group){
                return $terms_group->pluck('quota', 'organ');
            });
        });

        $t03tbs = $this->t03tbRepository->getByIn($classes);
        $t03tbs = $t03tbs->groupBy('class')->map(function($t03tb_group){
            return $t03tb_group->groupBy('term')->map(function($terms_group){
                return $terms_group->pluck('quota', 'organ');
            });
        });

        $grade1_m17tb = $this->m17tbRepository->getGrade1Organ()->pluck('enrollorg', 'organ');

        DB::beginTransaction();

        try {
            foreach($classes as $class => $terms){
                foreach ($terms as $term){
                    $this->t51tbRepository->update(compact(['class', 'term']), ['quota' => 0]);
                    if (isset($t03tbs[$class][$term])){
                        foreach ($t03tbs[$class][$term] as $organ => $quota){
                            $t51tb_info = compact(['class', 'term']);
                            if (isset($grade1_m17tb[$organ])){
                                $t51tb_info['organ'] = $grade1_m17tb[$organ];

                                if (isset($t51tbs[$class][$term][$t51tb_info['organ']])){
                                    $this->t51tbRepository->update($t51tb_info, ['quota' => $quota]);
                                }else{
                                    if (isset($t04tbs[$class][$term])){
                                        $t51tb_info['pubsdate'] = $t04tbs[$class][$term]->pubsdate;
                                        $t51tb_info['pubedate'] = $t04tbs[$class][$term]->pubedate;
                                    }                                    
                                    $t51tb_info['quota'] = $quota;
                                    $this->t51tbRepository->insert($t51tb_info);
                                }
                            }
                        }
                    }
                    $this->t03tbRepository->update(compact(['class', 'term']), ['is_online_update' => 1]);
                }
                
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            // return false;
            var_dump($e->getMessage());
            die;
        }    

    }
    /*
        放棄重分配
    */ 
    public function exec_remove($online_update_datas)
    {
        // foreach ($online_update_datas as $class => $terms)
        // {
        //     $tmp_assign_datas = $this->periodsRpository->getTmpAssign($class, $terms);
        //     $t51tb_exsit = $this->periodsRpository->getExsitT51tb($class, $terms);
            
        //     foreach ($tmp_assign_datas as $term => $tmp_assign_datas){
        //         foreach ($tmp_assign_datas as $organ => $tmp_assign_data){
        //             if (empty($t51tb_exsit[$term][$organ])){
        //                 $tmp_assign_data->delete();
        //             }else{
        //                 $tmp_assign_data->quota = $t51tb_exsit[$term][$organ]->quota;
        //                 $tmp_assign_data->online_update = 0;
        //                 $tmp_assign_data->save();
        //             }
        //         }
        //     }    
        // }
    }

    public function getT51tbByIn($conditions)
    {
        return $this->t51tbRepository->getByIn($conditions);
    }

    // public function getT01tbApplyMainOragn($t01tb)
    // {
    //     $enrollorgs = [];
    //     foreach($t01tb->t02tbs as $key => $t02tb){
    //         $min_grade_m17tb = $t02tb->min_grade_m17tb;
    //         if ($min_grade_m17tb){
    //             $m17tb_by_grade = $min_grade_m17tb->m17tb_by_grade;
    //             if ($m17tb_by_grade){
    //                 $enrollorgs[] = $m17tb_by_grade->enrollorg;
    //             }
    //         }
    //     }
    //     return $enrollorgs;
    // }

    public function storeT03tbs($t01tb, $quotas)
    {

        $t03tbs = $t01tb->t03tbs;

        $online_updated_t03tbs = $t03tbs->groupBy('term')->map(function($t03tb_group){
            $online_update = $t03tb_group->pluck('is_online_update', 'is_online_update');
            return isset($online_update[1]);
        });

        $t03tbs = $t03tbs->groupBy('term')->map(function($t03tb_group){
            return $t03tb_group->keyBy('organ');
        });        

        $t02tbs = $t01tb->t02tbs->pluck('quota', 'organ');

        DB::beginTransaction();

        try {
            foreach ($quotas as $organ => $terms){

                if (isset($t02tbs[$organ])){
                    $sum = array_sum($terms);

                    if ($sum > $t02tbs[$organ]){
                        return compact(['organ', 'sum']);
                    }

                    foreach($terms as $term => $quota){
                        $t03tb_info = compact(['term', 'organ']);
                        $t03tb_info['class'] = $t01tb->class;
                        $t03tb = compact(['quota']);

                        if (isset($t03tbs[$term][$organ]) && $online_updated_t03tbs[$term] == false){

                            if ($t03tbs[$term][$organ]->quota == $quota) continue;

                            $this->t03tbRepository->update($t03tb_info, $t03tb);
                        }else{
                            $t03tb = array_merge($t03tb, $t03tb_info);
                            $this->t03tbRepository->insert($t03tb);
                        }

                    }
                }

            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            
            var_dump($e->getMessage());
            return false;
            die;
        }    

    }
    
    public function getOnlineUpdateStatus($classes)
    {
        return $this->t03tbRepository->getOnlineUpdateStatus($classes);
    }

    public function checkAssignOtherOrgan($classes)
    {
        return $this->t51tbRepository->getAssignedOtherOrgan($classes);
    }

    public function checkOnlineUpdate($class, $start_term, $end_term)
    {
        $online_update = $this->t03tbRepository->getOnlineUpdate($class, $start_term, $end_term)->keyBy('term')->keys();
        if ($start_term <= $online_update->min() && $online_update->min() <= $end_term ){
            if ($start_term <= $online_update->max() && $online_update->max() <= $end_term ){
                return true;
            }
        }
        return false;
    }
}
