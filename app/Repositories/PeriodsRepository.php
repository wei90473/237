<?php
namespace App\Repositories;

use App\Models\T01tb;
use App\Models\TmpAssignResult;
use App\Models\T51tb;
use App\Models\T69tb;
use DB;

class PeriodsRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassList($queryData = [])
    {
        $query = T01tb::select('class', 'name', 'yerly', 'times', 'quotatot')
                        ->with(['t03tbs', 't02tbs']);
                    //   ->with('tmp_assign_sum_quota', 't51tb_assigned_terms', 't69tb_sum_checkcnt', 'tmp_assigns');

        // 年度 
        if (!empty($queryData['yerly'])){
            $query->where('yerly', '=', $queryData['yerly']);
        }else{
            // $query->where('yerly', '!=', '');
        }

        // 辦班院區
        if (!empty($queryData['branch'])){
            $query->where('branch', '=', $queryData['branch']);
        }

        // 班號
        if (!empty($queryData['class'])) {
            $query->where('class', 'like', '%'.$queryData['class'].'%');
        }

       // 班別名稱
        if (!empty($queryData['classes_name'])) {
            $query->where('name', 'like', '%'.$queryData['classes_name'].'%');
        }
        
        if (!empty($queryData['times'])){
            $queryData['times'] = str_pad((string)$queryData['times'], 2, '0', STR_PAD_LEFT);
            $query->where('times', '=', $queryData['times']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);
        
        return $data;
    }

    public function getAssignT01tb($class)
    {
        return T01tb::select('class', 'name', 'yerly', 'times', 'quotatot', 'period')->find($class);
                    // ->with('t03tbs', 't69tb_sum_checkcnt', 't69tb', 't51tb_assigned_terms', 't02tbs')
    }

    public function storeTmpAssign($tmp_assign_result)
    {
        $organ = $tmp_assign_result["organ"];
        $class = $tmp_assign_result["class"];

        $tmp_assigns = [];
        foreach ($tmp_assign_result["result"] as $term => $quota){
            $tmp_assign = new TmpAssignResult();
            $tmp_assign->organ = $organ;
            $tmp_assign->class = $class;
            $tmp_assign->term = str_pad((string)$term, 2, '0', STR_PAD_LEFT);
            $tmp_assign->quota = $quota;
            $tmp_assign->online_update = 0;
            $tmp_assign->save();
            $tmp_assigns[] = $tmp_assign;
        }

        return $tmp_assign;
    }

    public function clearHistroy($class, $start_term, $end_term)
    {
        $terms = [];
        for($term = $start_term; $term <= $end_term; $term++){
            $terms[] = str_pad((string)$term, 2, '0', STR_PAD_LEFT);
        }

        return TmpAssignResult::where("class", "=", $class)
                              ->whereIn("term", $terms)
                              ->delete();
    }

    public function getTmpAssign($class, $terms, $online_update = null)
    {
        $tmp_assigns = TmpAssignResult::select(["id", "class", "term", "organ", "quota", "online_update"])
                                      ->where('class', '=', $class)
                                      ->whereIn('term', $terms);

        if ($online_update !== null){
            $tmp_assigns->where('online_update', $online_update);
        }        
        
        $tmp_assigns = $tmp_assigns->get();

        $tmp_assign_datas = [];
        
        foreach ($tmp_assigns as $tmp_assign){
            $tmp_assign_datas[$tmp_assign->term][$tmp_assign->organ] = $tmp_assign;
        }

        return $tmp_assign_datas;
    }

    public function getExsitT51tb($class, $terms){
        $assigns = t51tb::where('class', '=', $class)
                        ->whereIn('term', $terms)
                        ->get();
        $assign_datas = [];
        
        foreach ($assigns as $assign){
            $assign_datas[$assign->term][$assign->organ] = $assign;
        }

        return $assign_datas;
    }

    public function getSumTmpAssignQuota($class, $start_term, $end_term)
    {
        $terms = [];
        for($term = $start_term; $term <= $end_term; $term++){
            $terms[] = str_pad((string)$term, 2, '0', STR_PAD_LEFT);
        }

        return TmpAssignResult::select([DB::raw('SUM(quota) sum_quota')])
                              ->whereIn('term', $terms)
                              ->where('class', $class)
                              ->first()->sum_quota;
    }

    public function getAssignedQuota($class)
    {
        $tmp_assign_results = TmpAssignResult::select([DB::raw('class, organ, SUM(quota) sum_quota')])
                                            ->where("class", "=", $class)
                                            ->groupBy(["class", "organ"])
                                            ->get();
        $quotas = [];
        foreach ($tmp_assign_results as $result){
            $quotas[$result->organ] = (int)$result->sum_quota;
        }

        return $quotas;
    }

    public function getT69tbByOrgan($class, $enrollorgs)
    {
        $t69tbs = T69tb::whereIn("organ", $enrollorgs)
                        ->where("class", "=", $class);
        return $t69tbs->get();
    }

}
