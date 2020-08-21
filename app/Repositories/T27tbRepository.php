<?php 
namespace App\Repositories;

use App\Models\T27tb;
use App\Repositories\Repository;
use DB;

class T27tbRepository extends Repository{
    public function __construct(T27tb $t27tb)
    {
        $this->model = $t27tb;
    }  
    public function get($queryData, $paginate = true)
    {
        $t27tb = $this->model->select(["t27tb.*"])
                             ->join('m17tb', 'm17tb.enrollorg', '=', 't27tb.enrollid', 'LEFT')
                             ->join('m13tb', 'm13tb.organ', '=', 't27tb.organ', 'LEFT');
        
        if (!empty($queryData['class'])){
            $t27tb->where('t27tb.class', '=', $queryData['class']);
        }
        
        if (!empty($queryData['term'])){
            $t27tb->where('t27tb.term', '=', $queryData['term']);
        }

        // 身分證號
        if (!empty($queryData['idno'])){
            $t27tb->where('t27tb.idno', '=', $queryData['idno']);
        }
        
        // 姓名
        if (!empty($queryData['name'])){
            $t27tb->where(DB::raw('concat(t27tb.lname, t27tb.fname)'), '=', $queryData['name']);
        }

        // 機關代碼
        if (!empty($queryData['organ'])){
            $t27tb->where('t27tb.organ', 'LIKE', "%{$queryData['idno']}%");
        }

        // 官職等
        if (!empty($queryData['rank'])){
            $t27tb->where('t27tb.rank', '=', $queryData['rank']);
        }
        // 選項
        if (!empty($queryData['prove'])){
            $t27tb->where('t27tb.prove', '=', $queryData['prove']);
        }
        // 職稱
        if (!empty($queryData['position'])){
            $t27tb->where('t27tb.position', 'LIKE', "%{$queryData['position']}%");
        }
        // E-mail
        if (!empty($queryData['email'])){
            $t27tb->where('t27tb.email', 'LIKE', "%{$queryData['email']}%");
        }


        if (!empty($queryData['enrollorg'])){
            $t27tb->where('m17tb.enrollorg', 'LIKE', "%{$queryData['enrollorg']}%");
        }
        // 機關名稱
        if (!empty($queryData['enrollname'])){
            $t27tb->where('m17tb.enrollname', 'LIKE', "%{$queryData['enrollname']}%");
        }

        if (!empty($queryData['organ_name'])){
            $t27tb->where('m13tb.lname', 'LIKE', "%{$queryData['organ_name']}%");
        }


        if ($paginate){
            return $t27tb->paginate(10);
        }else{
            return $t27tb->get();
        }
        
    }
    /*
        取得兩個班重複人員
    */
    // public function getRepeatCount($class_info)
    // {
    //     $t27tb = $this->model->select(DB::raw('idno, count(*) `count`'))
    //                          ->join('t13tb', 't13tb.class', '=', $class_info['copy_purpose']['class'])
    //                          ->where('t13tb', '=', $class_info['copy_purpose']['class'])
    //                          ->orWhere($class_info['copyed'])
    //                          ->groupBy('idno');

    //     return DB::select("SELECT count(*) `count` FROM ({$t27tb->toSql()}) t27tb WHERE t27tb.`count` > 1", $t27tb->getBindings())[0]->count;

    //     // SELECT idno 
    //     // FROM (
    //     //     SELECT idno, count(*) `count`
    //     //     FROM t27tb 
    //     //     WHERE (class = '108901' AND term = '04') OR (class = '108901' AND term = '01')
    //     //     GROUP BY idno
    //     // ) A 
    //     // WHERE A.`count` > 1        
    // }
    
    public function getRepeatIdno($class_info)
    {
        $repeat_idnos = $this->model->select(DB::raw('idno, count(*) `count`'))
                                    ->where($class_info['copy_purpose'])
                                    ->orWhere($class_info['copyed'])
                                    ->groupBy('idno');    
        $repeat_idnos = DB::select("SELECT idno 
                                    FROM ({$repeat_idnos->toSql()}) t2
                                    WHERE t2.`count` > 1", $repeat_idnos->getBindings());
        return collect($repeat_idnos)->pluck('idno')->toArray();                                         
    }

    public function deleteWithClassIdno($class_info, $repeat_idnos)
    {
        return $this->model->where($class_info)
                           ->whereIn('idno', $repeat_idnos)
                           ->delete(); 
    }

    public function getCopyData($copy_info, $repeat_indos)
    {
        $t27tbs = $this->model->where($copy_info['copyed']);
            
        if ($copy_info['over_data'] == 2 && $copy_info['copy_mode'] == 'insert'){
            // 不覆蓋則排除重複的人
            if (!empty($repeat_indos)){
                $t27tbs->whereNotIn('idno', $repeat_indos);
            }
        }

        return $t27tbs->get();    
    }

    public function review($review_info, $idnos, $prove)
    {
        return $this->model->where($review_info)
                           ->whereIn('idno', $idnos)
                           ->where('prove', '<>', 'S')
                           ->update(['prove' => $prove]);
    }

    public function getByIdnos($t04tb_info, $idnos)
    {
        return $this->model->where($t04tb_info)
                           ->whereIn('idno', $idnos)
                           ->get();
    }

}
 