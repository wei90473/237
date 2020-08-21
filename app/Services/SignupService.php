<?php
namespace App\Services;

use App\Repositories\SignupRepository;
use App\Repositories\T04tbRepository;
use App\Repositories\T01tbRepository;
use DB;


class SignupService
{
    /**
     * SignupService constructor.
     * @param SignupRepository $signupRepository
     */
    public function __construct(
        SignupRepository $signupRepository,
        T04tbRepository $t04tbRepository,
        T01tbRepository $t01tbRepository
        )
    {
        $this->signupRepository = $signupRepository;
        $this->t04tbRepository = $t04tbRepository;
        $this->t01tbRepository = $t01tbRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSignupList($queryData = [])
    {
        $data = $this->signupRepository->getSignupList($queryData);
        $data = collect($data);
        // dd($data);
        return [
            'data' => $data->toArray(),
            'sum' => [
                '年度分配人數' => $data->pluck('年度分配人數')->sum(),
                '線上分配人數' => $data->pluck('線上分配人數')->sum()
            ],
        ];
    }

    /**
     * 取得派訓日期
     *
     * @param $queryData
     * @return array
     */
    public function getDateData($queryData)
    {
        return $this->signupRepository->getDateData($queryData);
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList($queryData)
    {
        return $this->signupRepository->getClassList($queryData);
    }

    public function getT04tbs($queryData)
    {
        return $this->t04tbRepository->get($queryData);        
    }

    public function getOpenClassList($queryData)
    {
        return $this->t04tbRepository->getByQueryList($queryData);
    }

    public function getT04tb($t04tb_info)
    {
        return $this->t04tbRepository->find($t04tb_info);        
    }    

    /**
     * t51更新處理
     *
     * @param $class
     * @param $term
     * @param $sdate
     * @param $edate
     * @param $data
     */
    public function updateT51tb($class, $term, $sdate, $edate, $data)
    {
        // 建立暫存資料庫
        $dbName = $this->signupRepository->createDB();
        // 寫入暫存檔
        foreach ($data as $organ => $va) {
            $sql = "
                INSERT `".$dbName."`(organ,new_quota,pubsdate,pubedate)
                VALUES('".$organ."' , '".$va."','".$sdate."','".$edate."')";

            DB::select($sql);
        }

        // 寫入班別
        $sql = "UPDATE `".$dbName."` SET class='".$class."',term='".$term."'";

        DB::select($sql);

        $sql = "
            UPDATE `".$dbName."`
            INNER JOIN t51tb B ON `".$dbName."`.class=B.class AND `".$dbName."`.term=B.term AND `".$dbName."`.organ=B.organ
            SET `".$dbName."`.quota=B.quota, 
            `".$dbName."`.share=B.share";

        DB::select($sql);


        $sql = "
        UPDATE `".$dbName."` X SET allot=1 
         WHERE EXISTS( SELECT NULL FROM t51tb A
         INNER JOIN m17tb B  ON A.organ=B.enrollorg  WHERE A.class='".$class."' AND A.term='".$term."' AND B.grade<>'1'
        AND ( CASE  WHEN B.grade='2' THEN B.uporgan  WHEN B.grade='3' THEN  ( SELECT enrollorg FROM m17tb 
        WHERE enrollorg IN (SELECT uporgan FROM m17tb WHERE enrollorg=B.uporgan) )
        WHEN B.grade='4' THEN (SELECT uporgan FROM m17tb WHERE enrollorg IN (SELECT uporgan FROM m17tb WHERE enrollorg=B.uporgan))
         ELSE '' END)=X.organ)  ";

        DB::select($sql);

        $sql = "
            INSERT into t51tb(class,term,organ,quota,share,status,pubsdate,pubedate) 
            SELECT class, term, organ, new_quota, new_quota, 'Y' , pubsdate, pubedate FROM `".$dbName."` 
            WHERE new_quota>0 AND quota=0";

        DB::select($sql);


        // 下面開始會time out
        $sql = "
            DELETE A FROM t51tb A 
            INNER JOIN view_enrollorg B ON A.organ=B.enrollorg 
            INNER JOIN `".$dbName."` C ON B.grade1=C.organ AND C.new_quota=0  
            WHERE A.class='".$class."' AND A.term='".$term."'";

        DB::select($sql);

        $sql = "
            DELETE A FROM t27tb A
            INNER JOIN view_enrollorg B ON A.class='".$class."' AND A.term='".$term."'
            AND A.progress<>'0'/*只針對web報名*/
            AND A.loginid=B.enrollorg
            INNER JOIN `".$dbName."` C
            ON B.grade1=C.organ AND C.new_quota=0/*新的 線上分配人數為 0 時*/
            WHERE A.class='".$class."' AND A.term='".$term."'";

        DB::select($sql);

        $sql = "
            DELETE A 
            FROM t51tb A
            INNER JOIN `".$dbName."` B
            ON A.organ=B.organ AND A.class=B.class AND A.term=B.term
            AND B.new_quota=0";

        DB::select($sql);

        $sql = "
            DELETE A 
            FROM t27tb A
            INNER JOIN `".$dbName."` B
            ON A.loginid=B.organ AND A.class=B.class AND A.term=B.term
            AND B.new_quota=0
            AND A.progress<>'0'";

        DB::select($sql);

        $sql = "
            UPDATE t51tb A
            INNER JOIN `".$dbName."` B
            ON A.class=B.class AND A.term=B.term AND A.organ=B.organ
            SET
            A.quota=B.new_quota,
            A.share=B.share+(B.new_quota-B.quota)            
            WHERE B.new_quota>0 AND B.quota>0 AND B.new_quota>B.quota";

        DB::select($sql);

        $sql = "
            UPDATE t51tb A
            INNER JOIN `".$dbName."` B
            ON A.class=B.class AND A.term=B.term AND A.organ=B.organ
            SET
            A.quota=B.new_quota,
            A.share=B.share+(B.new_quota-B.quota)            
            WHERE B.new_quota>0 AND B.quota>0 AND B.new_quota<B.quota AND allot=0";

        DB::select($sql);

        $sql = "DROP TABLE `".$dbName."`";

        DB::select($sql);
    }

    public function getT01tb($class)
    {
        return $this->t01tbRepository->find($class);
    }

    public function updateT04tb($class_info, $data)
    {
        return $this->t04tbRepository->update($class_info, $data);
    }
}
