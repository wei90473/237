<?php
namespace App\Repositories;

use App\Models\T13tb;
use Auth;
use DB;


class SignupRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSignupList($queryData = [])
    {
        if ($queryData['class'] == '' || $queryData['term'] == '') {

            return array();
        }

        $sdate = $this->getSdate($queryData['class'], $queryData['term']);

        $sql = "SELECT B.organ_10 AS 機關代碼, A.sname AS 機關名稱,IFNULL(C.quota,0) AS 年度分配人數,IFNULL(D.quota,0) AS 線上分配人數
                FROM m13tb A /* 【m13tb 機關基本資料檔】 */
                LEFT JOIN (
                    SELECT X.organ AS organ_7, MIN(X.enrollorg) AS organ_10 
                    FROM m17tb X /* 【m17tb 薦送機關基本資料檔】 */ 
                    INNER JOIN ( 
                        SELECT organ,MIN(grade) AS grade   
                        FROM m17tb /* 【m17tb 薦送機關基本資料檔】 */ 
                        GROUP BY organ) Y ON X.organ=Y.organ  AND X.grade=Y.grade 
                    GROUP BY X.organ
                ) B ON A.organ=B.organ_7  
                LEFT JOIN t03tb C  /* 【t03tb 各期參訓單位報名檔】 */ 
                    ON A.organ=C.organ 
                    AND C.class='".$queryData['class']."' 
                    AND C.term='".$queryData['term']."' 
                LEFT JOIN t51tb D /* 【t51tb 薦送報名分配檔】 */ 
                    ON B.organ_10=D.organ  
                    AND D.class='".$queryData['class']."'  
                    AND D.term='".$queryData['term']."' 
                WHERE A.kind='Y' /* 統計使用狀態  Y:統計 N:不統計 */ 
                    AND 1 = (
                        CASE WHEN C.quota > 0  THEN 1  
                             WHEN D.quota > 0  THEN 1 
                             WHEN A.effdate <= '".$sdate."' AND A.expdate >= '".$sdate."' THEN 1 
                             WHEN A.effdate <= '".$sdate."' AND A.expdate = '' THEN 1  
                        END )  
                ORDER BY A.rank ";
        
        return DB::select($sql);
    }

    /**
     * 取得派訓日期
     *
     * @param $queryData
     * @return array
     */
    public function getDateData($queryData)
    {
        if ($queryData['class'] == '' || $queryData['term'] == '') {

            return array();
        }

        $sql = "
             SELECT 
             RTRIM(A.sdate) AS sdate,        /* 開課日期 */ 
            RTRIM(A.edate) AS edate,        /* 結束日期 */ 
            RTRIM(A.pubsdate) AS pubsdate,  /* 薦送報名開始日期 */ 
            RTRIM(A.pubedate) AS pubedate,  /* 薦送報名結束日期 */ 
            RTRIM(A.notice) AS notice,      /* 是否聯合派訓 */
            RTRIM(A.pubdatemk) AS pubdatemk,/* 薦送報名日期註記 */ 
            RTRIM(B.orgchk) AS orgchk,      /* 機關 */
            RTRIM(B.traintype) AS traintype, /* 訓練性質 */
            A.apply_code,
            A.apply_password
            FROM t04tb A
            INNER JOIN t01tb B ON A.class = B.class
             WHERE A.class = '".$queryData['class']."'
            AND A.term = '".$queryData['term']."'
        ";

        $result = DB::select($sql);

        return (isset($result[0]))? $result[0] : array();
    }

    /**
     * 取得日期
     *
     * @param $class
     * @param $term
     * @return false|string
     */
    public function getSdate($class, $term)
    {
        $sql = "
            SELECT sdate 
            FROM t04tb 
            WHERE class = '".$class."' 
            AND term = '".$term."' ";

        $result = DB::select($sql);

        return ($result[0]->sdate)? $result[0]->sdate : date('Ymd');

    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;

        $sql = "SELECT class, name  FROM t01tb  WHERE type <> '13'  GROUP BY class,name   ORDER BY class DESC";

        return DB::select($sql);
    }

    /**
     * 建立暫存檔
     *
     * @return string
     */
    public function createDB()
    {
        // 取得暫存表名稱
        $temporaryStorage = 'temporary_storage_'.time().rand(10000, 99999);

        $sql = "
            CREATE TABLE `".$temporaryStorage."`
            (
                class     varchar(20)    NOT NULL DEFAULT '',/*班號*/
                term      varchar(5)    NOT NULL DEFAULT '',/*期別*/
                organ     varchar(20)    NOT NULL DEFAULT '',/*機關代碼*/
                new_quota int NOT NULL DEFAULT 0,/*新的 線上分配人數(總分配人數)*/
                quota     int NOT NULL DEFAULT 0,/*線上分配人數(總分配人數)*/
                share     int NOT NULL DEFAULT 0,/*分配人數*/
                allot     int NOT NULL DEFAULT 0, /*是否已分配給下層 0：未分配;1：已分配*/
                pubsdate  char(7), /*是否已分配給下層 0：未分配;1：已分配*/
                pubedate  char(7) /*是否已分配給下層 0：未分配;1：已分配*/
            )";

        DB::select($sql);

        return $temporaryStorage;
    }
}
