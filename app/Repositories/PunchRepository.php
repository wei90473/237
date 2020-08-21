<?php
namespace App\Repositories;

use Auth;
use DB;


class PunchRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPunchList($queryData = [])
    {
        if ($queryData['class'] == '' || $queryData['term'] == '') {

            return array();
        }

        // 取得暫存表名稱
        $temporaryStorage = 'temporary_storage_'.time().rand(10000, 99999);

        $this->step1($temporaryStorage);

        $this->step2($temporaryStorage, $queryData);

        $this->step3($temporaryStorage);

        $result = $this->step4($temporaryStorage, $queryData);

        return $result;
    }

    /**
     * 第一步 :新增暫存檔
     *
     * @param $temporaryStorage
     */
    public function step1($temporaryStorage)
    {
        $sql = "CREATE TEMPORARY TABLE `".$temporaryStorage."`  (
                no varchar(3) ,
                cname nvarchar(26) ,
                dated varchar(7) ,
                data_a varchar(6) ,
                data_b varchar(6) ,
                data_c varchar(6) ,
                mark_a  varchar(1) ,
                mark_b  varchar(1)
            )";

        DB::select($sql);
    }

    /**
     * 第二步 :把資料寫入暫存檔
     *
     * @param $temporaryStorage
     * @param $queryData
     */
    public function step2($temporaryStorage, $queryData)
    {
        $sql = "
        insert into `".$temporaryStorage."`  (no,cname,dated,data_a,data_b,data_c )
        SELECT A.no,B.cname,ifnull(C.dated,'') as dated, 
        ifnull((select timed from t84tb where class=A.class and term=A.term and dated=C.dated and idno=A.idno and status='A' limit 1),'') as timeA,
        ifnull((select timed from t84tb where class=A.class and term=A.term and dated=C.dated and idno=A.idno and status='B' limit 1),'') as timeB, 
        ifnull((select timed from t84tb where class=A.class and term=A.term and dated=C.dated and idno=A.idno and status='C' limit 1),'') as timeC 
        FROM t13tb A 
        LEFT JOIN m02tb B ON A.idno=B.idno 
        LEFT JOIN ( select class,term,dated from t84tb group by class,term,dated) C  ON A.class=C.class AND A.term=C.term
        LEFT JOIN (select class,term,idno,dated  from t84tb   group by class,term,idno,dated ) D  ON A.class=D.class AND A.term=D.term  AND A.idno=D.idno 
         AND C.dated=D.dated 
        WHERE A.class='".$queryData['class']."' 
        and A.term='".$queryData['term']."'
        and A.no<>'' ";

        DB::select($sql);
    }

    /**
     * 第三步 :更新暫存檔
     *
     * @param $temporaryStorage
     * @param $queryData
     */
    public function step3($temporaryStorage)
    {
        $sql = "
        UPDATE `".$temporaryStorage."` SET 
        mark_a=CASE WHEN left(data_a,4) >'0930' THEN 'Y' ELSE '' END ,
        mark_b=CASE WHEN left(data_b,4) > '1400' THEN 'Y' ELSE '' END ";

        DB::select($sql);
    }

    /**
     * 第四步 :從暫存檔查詢資料
     *
     * @param $temporaryStorage
     * @param $queryData
     * @return mixed
     */
    public function step4($temporaryStorage, $queryData)
    {
        $queryData['no'] = str_pad($queryData['no'] ,3,'0',STR_PAD_LEFT);

        $sql = "SELECT no ,cname as name,
            CASE WHEN dated='' THEN '' ELSE LEFT(dated,3)+'/'+SUBSTRING(dated,4,2)+'/'+RIGHT(dated,2)  END as 'swipe_date',
            CASE WHEN data_a='' THEN '' ELSE left(data_a,2)+':'+substring(rtrim(data_a),3,2) END as 'morning',
            CASE WHEN data_b='' THEN '' ELSE left(data_b,2)+':'+substring(rtrim(data_b),3,2) END as 'afternoon',
            CASE WHEN data_c='' THEN '' ELSE left(data_c,2)+':'+substring(rtrim(data_c),3,2) END as 'swipe_return',
            mark_a,mark_b
            from `".$temporaryStorage."`
             WHERE 1=1
            AND ".$queryData['no']."=CASE WHEN ".$queryData['no']."='' THEN '' ELSE no END";

        // 搜尋區間
        if ($queryData['date']) {

            $queryData['date'] = array_diff(explode(' - ', $queryData['date']), array(null, 'null', '', ' '));

            $sql .= " AND  dated between '".$queryData['date'][0]."' and '".$queryData['date'][1]."' ";
        }

        // 排序
        $sql .= " ORDER BY dated, no";

        return DB::select($sql);
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

        $sql = "

        SELECT X.class,RTRIM(X.name) AS name
        FROM (SELECT A.class,A.name,0 AS sort
        FROM t01tb A 
        INNER JOIN t04tb B ON A.class=B.class
        INNER JOIN m09tb C ON B.sponsor=C.userid 
        WHERE A.type<>'13'AND UPPER(B.sponsor)=UCase('".$uesr."')
        GROUP BY A.class,A.name
        UNION ALL  
        SELECT class,name,1 AS sort
        FROM t01tb ) X 
        ORDER BY X.sort ASC,X.class DESC";

        return DB::select($sql);
    }
}
