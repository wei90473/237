<?php
namespace App\Repositories;

use App\Models\T68tb;
use App\Models\T01tb;
use App\Models\M17tb;
use App\Helpers\ModifyLog;
use DB;
class DemandSurveyRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getDemandSurveyList($queryData = [])
    {
        $query = T68tb::select('t68tb.id', 't68tb.yerly', 't68tb.times', 't68tb.purpose','t68tb.sdate','t68tb.edate','t68tb.branch');
        
        // id
        if ( isset($queryData['id']) && $queryData['id'] ) {
            $query->where('t68tb.id', $queryData['id']);
        }
        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {

            $queryData['yerly'] = str_pad($queryData['yerly'] ,3,'0',STR_PAD_LEFT);

            $query->where('t68tb.yerly', $queryData['yerly']);
        }
        // 辦班院區
        if ( isset($queryData['branch']) && $queryData['branch'] ) {

            $query->where('t68tb.branch', $queryData['branch']);
        }
        // 第幾次
        if ( isset($queryData['times']) && $queryData['times'] ) {

            $query->where('t68tb.times', 'like', '%'.$queryData['times']);
        }
        
        // 需求調查名稱
        if ( isset($queryData['purpose']) && $queryData['purpose'] ) {

            $query->where('t68tb.purpose', 'like', '%'.$queryData['purpose'].'%');
        }
        $query->groupBy('id');
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['demand_survey_id', 'year', 'times', 'purpose'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('id', 'desc');
        }
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }
    /**
     * 取得未被用過的班別
     *
     * @param $classesId
     * @return mixed
     */
    public function getDemandSurveyClasses($yerly, $times)
    {
        $query = T01tb::select('t01tb.class', 't01tb.name', 't01tb.yerly', 't01tb.times','t80tb.prove');
        $query->where('t01tb.yerly', $yerly);
        $query->where('t01tb.times', $times);
        $query->join('t80tb', 't01tb.class', '=', 't80tb.class', 'inner');/* 【t80tb 需求填報名冊資料檔 */
        $query->groupBy('class');
        return $query->get()->toArray();
    }
    // 取得左下清單資訊
    public function getTitleMsg($yerly, $times,$enrollorg){
        $query = T68tb::select('t68tb.sdate','t68tb.edate','t79tb.type',DB::raw("(CASE WHEN t70tb.status='1' THEN ' (已彙報)' WHEN t70tb.status='2' THEN ' (已凍結)' ELSE ''  END  ) AS report"));
        $query->where('t68tb.yerly',$yerly)->where('t68tb.times','like','%'.$times.'%');
        $query->leftJoin('t79tb','t68tb.yerly','=',DB::raw("`t79tb`.`yerly` and `t68tb`.`times` = `t79tb`.`times` and `t79tb`.`organ` = '".$enrollorg."'"));
        $query->leftJoin('t70tb','t68tb.yerly','=',DB::raw("`t70tb`.`yerly` and `t68tb`.`times` = `t70tb`.`times` and `t70tb`.`organ` = '".$enrollorg."'"));
        return $query->get()->toArray();
    }

    /**
     * 取得行政機關列表
     *
     * @param $yerly,$times
     * @return array
     */
    public function getAdministrationList($yerly, $times,$grade=1,$uporgan=NULL){
        $uporgansql = is_null($uporgan)?'':" AND A.uporgan = '".$uporgan."'";
        $sql = " SELECT RTRIM(A.enrollorg) AS enrollorg,RTRIM(A.enrollname) AS enrollname, 
            (CASE WHEN IFNULL(C.yerly,0)<>0 THEN '(已彙報)' ELSE '' END ) AS report 
            FROM m17tb A  /* 【m17tb 薦送機關基本資料檔 */
            INNER JOIN m13tb B/* 【m13tb 機關基本資料檔】 */
            ON A.organ = B.organ 
            LEFT JOIN ( SELECT yerly,times,organ FROM t70tb  /* 【t70tb 填報凍結機關資料檔】 */
            WHERE yerly = ".$yerly." AND times= ".$times." ) C  ON A.enrollorg=C.organ 
            WHERE A.grade='".$grade."'".$uporgansql." AND ( A.enrollorg LIKE '3%' OR A.enrollorg LIKE 'A%' OR A.enrollorg='604000000A'  
                ) AND 1 = ( CASE WHEN LEFT(B.effdate,3) <= ".$yerly." AND LEFT(B.expdate,3) >= ".$yerly." THEN 1 
            WHEN LEFT(B.effdate,3) <= ".$yerly." AND B.expdate = '' THEN 1 
            WHEN C.yerly IS NOT NULL  THEN 1 END ) 
            ORDER BY B.rank";
        return  DB::select($sql);
    }
    /**
     * 取得訓練機構列表
     *
     * @param $yerly,$times
     * @return array
     */
    public function getTrainingInstitutionList($yerly, $times){
        $sql ="SELECT   X.agency,RTRIM(X.enrollorg) AS enrollorg,RTRIM(X.NAME) AS enrollname,
            (CASE WHEN Y.status='1' THEN ' (已彙報)' WHEN Y.status='2' THEN ' (已凍結)' ELSE ''  END  ) AS report
            FROM m07tb X  /* 【m07tb 訓練機構資料檔 */
            LEFT JOIN  (SELECT yerly,times,organ,status FROM t70tb /* 【t70tb 填報凍結機關資料檔】 */
            WHERE yerly='".$yerly."'
            AND times='".$times."'  GROUP BY organ) Y 
            ON  X.enrollorg=Y.organ ORDER BY X.agency";
        return  DB::select($sql);    
    }
    /**
     * 取得該單位的填表狀況
     *
     * @param $enrollorg
     * @return array
     */
    public function getDemandSurveyData($yerly, $times,$enrollorg,$type=NULL){
        $typesql = is_null($type)? '':' AND X.type = '.$type; 
        $sql = "select X.class,X.`name`,Y.applycnt,Y.checkcnt,X.rank FROM t01tb as X
        LEFT JOIN 
          ( SELECT A.enrollorg,A.enrollname,B.class,B.applycnt,B.checkcnt,B.organ,C.`status` 
          FROM m17tb as A 
          INNER JOIN t69tb as B 
          ON A.enrollorg=B.organ 
          LEFT JOIN t70tb as C 
          ON  C.yerly='".$yerly."' AND  C.times='".$times."' AND  A.enrollorg=C.organ 
            WHERE ( A.enrollorg LIKE '3%' OR A.enrollorg LIKE 'A%' OR A.uporgan='604000000A' )
              AND ( A.uporgan='".$enrollorg."' OR A.enrollorg='".$enrollorg."') 
            ) as Y 
             ON X.class=Y.class 
             WHERE X.yerly='".$yerly."' AND X.times='".$times.$typesql."' GROUP BY X.class,X.`name`,X.rank";
           
        return  DB::select($sql);
    }
    /**
     * 取得列印機關建議班別
     *
     * @param $yerly,$times
     * @return array
     */
    public function getprintdata($yerly, $times,$organ=NULL,$force='N'){
        if(!is_null($organ)){
            $organsql = "INNER JOIN 
            ( SELECT enrollorg, enrollname FROM m17tb
                WHERE enrollorg = '".$organ."' AND (EXISTS(
                    SELECT * FROM t70tb WHERE yerly='".$yerly."' AND times='".$times."' AND organ=m17tb.enrollorg AND status='1'
                ) )
                UNION ALL
                SELECT enrollorg, enrollname FROM m17tb 
                WHERE uporgan='".$organ."' AND( EXISTS (
                    SELECT * FROM t70tb WHERE yerly='".$yerly."' AND times='".$times."' AND organ=m17tb.uporgan AND status='1' 
                ) ) AND EXISTS (
                    SELECT * FROM t70tb WHERE yerly='".$yerly."' AND times='".$times."' AND organ=m17tb.enrollorg AND status='1' 
                )
                UNION ALL
                SELECT A.enrollorg, A.enrollname FROM m17tb A
                INNER JOIN 
                (
                    SELECT enrollorg, enrollname FROM m17tb 
                    WHERE uporgan='".$organ."' AND ( EXISTS (
                         SELECT * FROM t70tb WHERE yerly='".$yerly."' AND times='".$times."' AND organ=m17tb.uporgan AND status='1' 
                    ) ) AND EXISTS (
                        SELECT * FROM t70tb WHERE yerly='".$yerly."' AND times='".$times."' AND organ=m17tb.enrollorg AND status='1' 
                    )
                ) B ON  A.uporgan=B.enrollorg AND EXISTS (
                        SELECT * FROM t70tb WHERE yerly='".$yerly."' AND times='".$times."' AND organ=A.enrollorg AND status='1' 
                )
                UNION ALL
                SELECT D.enrollorg,D.enrollname FROM m17tb D
                INNER JOIN 
                (
                    SELECT A.enrollorg, A.enrollname FROM m17tb A
                    INNER JOIN 
                    (
                        SELECT enrollorg    FROM m17tb 
                        WHERE uporgan='".$organ."' AND ( EXISTS (
                            SELECT * FROM t70tb WHERE   yerly='".$yerly."' AND times='".$times."' AND organ=m17tb.uporgan AND status='1' 
                        ) )AND EXISTS (
                            SELECT * FROM t70tb WHERE yerly='".$yerly."' AND times='".$times."' AND organ=m17tb.enrollorg AND status='1' 
                        )
                    ) B ON A.uporgan=B.enrollorg AND EXISTS (
                        SELECT * FROM t70tb WHERE  yerly='".$yerly."' AND times='".$times."' AND organ=A.enrollorg AND status='1' 
                    )
                ) C ON D.uporgan=C.enrollorg
                UNION ALL
                SELECT agency,name FROM m07tb WHERE agency='".$organ."'
            ) Y ON X.organ=Y.enrollorg ORDER BY X.organ";
        }else{
            $organsql = '';
        }
        $sql ="SELECT X.organ,IFNULL(X.enrollname,X.`name`) AS 'name', X.content,
        (CASE WHEN X.organ=X.modorgan THEN '' ELSE X.modorgan END) AS 'modorgan',
        (CASE WHEN X.organ=X.modorgan THEN '' ELSE (SELECT enrollname FROM m17tb WHERE enrollorg=X.modorgan) END) AS 'modname' 
        FROM 
        (   SELECT A.organ,A.content,A.modorgan,C.enrollname,D.`name`
            FROM t71tb A 
            INNER JOIN t70tb B ON A.yerly=B.yerly AND A.times=B.times AND A.organ=B.organ
            LEFT  JOIN m17tb C ON A.organ=C.enrollorg
            LEFT  JOIN m07tb D ON A.organ=D.agency
            WHERE A.yerly='".$yerly."' AND A.times='".$times."'   AND B.status='1'
            UNION ALL
            SELECT A.organ,A.content,A.modorgan,C.enrollname,D.`name`
            FROM t71tb A 
            LEFT JOIN m17tb C ON A.organ=C.enrollorg
            LEFT JOIN m07tb D ON A.organ=D.agency
            WHERE A.yerly='".$yerly."' AND A.times='".$times."'    
            AND NOT EXISTS(
               SELECT *  FROM t70tb WHERE  yerly=A.yerly AND times=A.times AND organ=A.organ AND status='1'
            )
        ) X ".$organsql;
        return  DB::select($sql);
    }
    //取得參訓機關的資格限制 return class array
    public function ufn_csditrain_orgchk($organ,$yerly,$times,$type=NULL,$userid=NULL){
        $data =  DB::select("SELECT (CASE WHEN B.type='1' THEN '1' WHEN B.type IN('3','4') THEN '2' ELSE '' END) as type,A.organ
                FROM m17tb as A 
                INNER JOIN m13tb as B
                on A.organ = B.organ WHERE A.enrollorg = '".$organ."' ");
        $type = $data[0]->type; //1:中央 2:地方 @orgchk
        $masterorgan = $data[0]->organ; //主管機關代碼 @chforg
        $data =  DB::select("SELECT enrollorg FROM m17tb WHERE organ = '".$masterorgan."' AND grade = '1'  "); //轉成10碼
        $masterorgan = $data[0]->enrollorg; //@chforg
        $data =  DB::select("SELECT A.class FROM t01tb as A 
                LEFT JOIN t82tb as B
                on A.orgchk = '3' AND A.class=B.class AND B.organ = '".$masterorgan."'
                LEFT JOIN s01tb as C
                on C.type = 'K' AND A.type = C.`code` where A.yerly = '".$yerly."' AND A.times = '".$times."' AND 1 = 
                (CASE WHEN A.orgchk='0' THEN 1 
                    WHEN A.orgchk='".$type."' THEN 1 
                    WHEN A.orgchk='3' AND B.organ = '".$masterorgan."' THEN 1 ELSE 0 END
                )
                ORDER BY A.type,A.rank,A.class");
        return $data;
    }
    //查詢該機關所屬機關 &審核層級 return data array 
    public function ufn_sub_organ($organ){
        $data = M17tb::select('enrollorg','grade','uporgan')->where('enrollorg',$organ)->first();
        $range = $data['grade'];
        for($i=1;$i<$range;$i++){
            $enrollorg = $data['uporgan'];
            $data = M17tb::select('enrollorg','uporgan')->where('enrollorg',$enrollorg)->first();
        }
        $data['grade'] = $range;
        return $data;
    }
    //更新審核層級
    public function updataprogress($class,$enrollorg,$grade){
        $sql= "SELECT * FROM t80tb WHERE progress = '".$grade."' AND loginorg = '".$enrollorg."' AND prove = 'Y' AND class in(".$class.")";
        $olddata = DB::select($sql);
        $sql= "update t80tb SET progress = '".$grade."' where loginorg = '".$enrollorg."' AND prove = 'Y' AND class in(".$class.")";
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        $data = DB::update($sql);
        $sqllog = DB::getQueryLog();
        $sql= "SELECT * FROM t80tb WHERE progress = '".$grade."' AND loginorg = '".$enrollorg."' AND prove = 'Y' AND class in(".$class.")";
        $nowdata = DB::select($sql);
        createModifyLog('U','t80tb',$olddata,$nowdata,end($sqllog));
        if($data) {
            return TRUE;
        }else{
            return FALSE;
        }
    }
}
