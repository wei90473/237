<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Services\User_groupService;

class BusinessStatisticsController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('business_statistics', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin/business_statistics/list');
    }

    /*
    公務統計報表CSDIR1130
    參考Tables:
    使用範本:D13.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //訓練期間, 民國年月日起迄範圍
        $sdatetw = $request->input('sdatetw');
        $edatetw = $request->input('edatetw');
        $sdate = str_replace('/','',$sdatetw);
        $edate = str_replace('/','',$edatetw);

        $checkrank = $request->input('checkrank'); //學員官等
        $checkage = $request->input('checkage');   //年齡
        $checkedu = $request->input('checkedu');   //性別及學歷
        $checknum = $request->input('checknum');   //開班數及受訓人次

        //學員官等
        if($checkrank=='1'){
            //1:學員官等 //訓練類別
            $sql1="SELECT RTRIM(S.name),
                            COUNT(T.idno),
                            SUM(CASE WHEN T.rank = '15'                THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '10' AND '14' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '06' AND '09' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '01' AND '05' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '16' AND '20' THEN 1 ELSE 0 END)
                    FROM (
                                    SELECT  A.class,
                                                    A.term,
                                                    A.quota,
                                                    (CASE
                                                                WHEN B.type = '13' THEN '25' ELSE B.type END
                                                    ) AS type,
                                                    B.process,
                                                    (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                                    B.trainday,
                                                    (CASE WHEN B.trainday <= 0.5                        THEN '1'
                                                                WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                                                WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                                                WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                                                WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                                                WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                                                WHEN B.trainday >= 20.0                       THEN '7'
                                                    END ) AS period_type,
                                                    B.classhr,
                                                    D.idno,
                                                    (CASE
                                                        WHEN E.sex IN ('M','F') THEN E.sex
                                                        WHEN E.sex= 'm' THEN 'M'
                                                        WHEN E.sex= 'f' THEN 'F'
                                                        WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                                        WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                                        ELSE 'F'
                                                    END) AS SEX,
                                                    D.age,
                                                    (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                                    D.ecode
                                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                            INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                            INNER JOIN m02tb E ON D.idno = E.idno
                                    WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                    AND D.status = '1'
                                    AND B.process IN ('1','3','4')
                                    AND B.cntflag = '1'
                                    ORDER BY A.class, A.term, B.type
                                ) T LEFT JOIN s01tb S ON T.type = S.code AND S.type = 'K'
                    GROUP BY RTRIM(S.name)
                    ORDER BY S.code
                        ";
            $reportlist1 = DB::select($sql1);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist1) != 0) {
                $arraykeys1=array_keys((array)$reportlist1[0]);
            }
            $reportlist1 = json_decode(json_encode($reportlist1), true);

            //開班類別
            $sql1A="SELECT RTRIM(S.name),
                            COUNT(T.idno),
                            SUM(CASE WHEN T.rank = '15'                THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '10' AND '14' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '06' AND '09' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '01' AND '05' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '16' AND '20' THEN 1 ELSE 0 END)
                    FROM   (SELECT '1' AS code,'自辦' AS name FROM dual
                            UNION ALL
                            SELECT '3' AS code,'合辦' AS name FROM dual
                            UNION ALL
                            SELECT '4' AS code,'接受委訓' AS name FROM dual
                            ) S LEFT JOIN
                            (SELECT A.class,
                                    A.term,
                                    A.quota,
                                    (CASE WHEN B.type = '13' THEN '25' ELSE B.type END
                                    ) AS type,
                                    B.process,
                                    (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                    B.trainday,
                                    (CASE   WHEN B.trainday <= 0.5                        THEN '1'
                                            WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                            WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                            WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                            WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                            WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                            WHEN B.trainday >= 20.0                       THEN '7'
                                    END ) AS period_type,
                                    B.classhr,
                                    D.idno,
                                    (CASE
                                        WHEN E.sex IN ('M','F') THEN E.sex
                                        WHEN E.sex= 'm' THEN 'M'
                                        WHEN E.sex= 'f' THEN 'F'
                                        WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                        WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                        ELSE 'F'
                                    END) AS SEX,
                                    D.age,
                                    (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                    D.ecode
                                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                 INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                 INNER JOIN m02tb E ON D.idno = E.idno
                                    WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                    AND D.status = '1'
                                    AND B.process IN ('1','3','4')
                                    AND B.cntflag = '1'
                                    ORDER BY A.class, A.term, B.type
                                ) T ON T.process = S.code
                    GROUP BY RTRIM(S.name)
                    ORDER BY S.code
                        ";
            $reportlist1A = DB::select($sql1A);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist1A) != 0) {
                $arraykeys1A=array_keys((array)$reportlist1A[0]);
            }
            $reportlist1A = json_decode(json_encode($reportlist1A), true);

            //訓練期程
            $sql1B="SELECT RTRIM(S.name),
                            COUNT(T.idno),
                            SUM(CASE WHEN T.rank = '15'                THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '10' AND '14' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '06' AND '09' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '01' AND '05' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN T.rank BETWEEN '16' AND '20' THEN 1 ELSE 0 END)
                    FROM   (SELECT '1' AS code,'0.5天' AS name FROM dual
                            UNION ALL
                            SELECT '2' AS code,'1天' AS name FROM dual
                            UNION ALL
                            SELECT '3' AS code,'2天' AS name FROM dual
                            UNION ALL
                            SELECT '4' AS code,'3天' AS name FROM dual
                            UNION ALL
                            SELECT '5' AS code,'逾3天至5天' AS name FROM dual
                            UNION ALL
                            SELECT '6' AS code,'逾5天至20天' AS name FROM dual
                            UNION ALL
                            SELECT '7' AS code,'逾20天' AS name FROM dual
                            ) S LEFT JOIN
                            (SELECT A.class,
                                    A.term,
                                    A.quota,
                                    (CASE WHEN B.type = '13' THEN '25' ELSE B.type END
                                    ) AS type,
                                    B.process,
                                    (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                    B.trainday,
                                    (CASE   WHEN B.trainday <= 0.5                        THEN '1'
                                            WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                            WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                            WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                            WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                            WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                            WHEN B.trainday >= 20.0                       THEN '7'
                                    END ) AS period_type,
                                    B.classhr,
                                    D.idno,
                                    (CASE
                                        WHEN E.sex IN ('M','F') THEN E.sex
                                        WHEN E.sex= 'm' THEN 'M'
                                        WHEN E.sex= 'f' THEN 'F'
                                        WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                        WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                        ELSE 'F'
                                    END) AS SEX,
                                    D.age,
                                    (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                    D.ecode
                                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                 INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                 INNER JOIN m02tb E ON D.idno = E.idno
                                    WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                    AND D.status = '1'
                                    AND B.process IN ('1','3','4')
                                    AND B.cntflag = '1'
                                    ORDER BY A.class, A.term, B.type
                                ) T ON T.period_type = S.code
                    GROUP BY RTRIM(S.name)
                    ORDER BY S.code
                        ";
            $reportlist1B = DB::select($sql1B);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist1B) != 0) {
                $arraykeys1B=array_keys((array)$reportlist1B[0]);
            }
            $reportlist1B = json_decode(json_encode($reportlist1B), true);

        }

        //年齡
        if($checkage=='1'){
            //2:年齡
            $sql2="SELECT RTRIM(S.name),
                            COUNT(T.idno),
                            SUM(CASE WHEN age <= 24             THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 25 AND 29 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 30 AND 34 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 35 AND 39 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 40 AND 44 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 45 AND 49 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age >= 50             THEN 1 ELSE 0 END)
                    FROM (
                                    SELECT  A.class,
                                                    A.term,
                                                    A.quota,
                                                    (CASE
                                                                WHEN B.type = '13' THEN '25' ELSE B.type END
                                                    ) AS type,
                                                    B.process,
                                                    (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                                    B.trainday,
                                                    (CASE WHEN B.trainday <= 0.5                        THEN '1'
                                                                WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                                                WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                                                WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                                                WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                                                WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                                                WHEN B.trainday >= 20.0                       THEN '7'
                                                    END ) AS period_type,
                                                    B.classhr,
                                                    D.idno,
                                                    (CASE
                                                        WHEN E.sex IN ('M','F') THEN E.sex
                                                        WHEN E.sex= 'm' THEN 'M'
                                                        WHEN E.sex= 'f' THEN 'F'
                                                        WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                                        WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                                        ELSE 'F'
                                                    END) AS SEX,
                                                    D.age,
                                                    (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                                    D.ecode
                                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                            INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                            INNER JOIN m02tb E ON D.idno = E.idno
                                    WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                    AND D.status = '1'
                                    AND B.process IN ('1','3','4')
                                    AND B.cntflag = '1'
                                    ORDER BY A.class, A.term, B.type
                                ) T LEFT JOIN s01tb S ON T.type = S.code AND S.type = 'K'
                    GROUP BY RTRIM(S.name)
                    ORDER BY S.code
                        ";
            $reportlist2 = DB::select($sql2);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist2) != 0) {
                $arraykeys2=array_keys((array)$reportlist2[0]);
            }
            $reportlist2 = json_decode(json_encode($reportlist2), true);

            //開班類別
            $sql2A="SELECT RTRIM(S.name),
                            COUNT(T.idno),
                            SUM(CASE WHEN age <= 24             THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 25 AND 29 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 30 AND 34 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 35 AND 39 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 40 AND 44 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 45 AND 49 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age >= 50             THEN 1 ELSE 0 END)
                    FROM   (SELECT '1' AS code,'自辦' AS name FROM dual
                            UNION ALL
                            SELECT '3' AS code,'合辦' AS name FROM dual
                            UNION ALL
                            SELECT '4' AS code,'接受委訓' AS name FROM dual
                            ) S LEFT JOIN
                            (SELECT A.class,
                                    A.term,
                                    A.quota,
                                    (CASE WHEN B.type = '13' THEN '25' ELSE B.type END
                                    ) AS type,
                                    B.process,
                                    (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                    B.trainday,
                                    (CASE   WHEN B.trainday <= 0.5                        THEN '1'
                                            WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                            WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                            WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                            WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                            WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                            WHEN B.trainday >= 20.0                       THEN '7'
                                    END ) AS period_type,
                                    B.classhr,
                                    D.idno,
                                    (CASE
                                        WHEN E.sex IN ('M','F') THEN E.sex
                                        WHEN E.sex= 'm' THEN 'M'
                                        WHEN E.sex= 'f' THEN 'F'
                                        WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                        WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                        ELSE 'F'
                                    END) AS SEX,
                                    D.age,
                                    (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                    D.ecode
                                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                INNER JOIN m02tb E ON D.idno = E.idno
                                    WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                    AND D.status = '1'
                                    AND B.process IN ('1','3','4')
                                    AND B.cntflag = '1'
                                    ORDER BY A.class, A.term, B.type
                                ) T ON T.process = S.code
                    GROUP BY RTRIM(S.name)
                    ORDER BY S.code
                        ";
            $reportlist2A = DB::select($sql2A);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist2A) != 0) {
                $arraykeys2A=array_keys((array)$reportlist2A[0]);
            }
            $reportlist2A = json_decode(json_encode($reportlist2A), true);

            //訓練期程
            $sql2B="SELECT RTRIM(S.name),
                            COUNT(T.idno),
                            SUM(CASE WHEN age <= 24             THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 25 AND 29 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 30 AND 34 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 35 AND 39 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 40 AND 44 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age BETWEEN 45 AND 49 THEN 1 ELSE 0 END),
                            SUM(CASE WHEN age >= 50             THEN 1 ELSE 0 END)
                    FROM   (SELECT '1' AS code,'0.5天' AS name FROM dual
                            UNION ALL
                            SELECT '2' AS code,'1天' AS name FROM dual
                            UNION ALL
                            SELECT '3' AS code,'2天' AS name FROM dual
                            UNION ALL
                            SELECT '4' AS code,'3天' AS name FROM dual
                            UNION ALL
                            SELECT '5' AS code,'逾3天至5天' AS name FROM dual
                            UNION ALL
                            SELECT '6' AS code,'逾5天至20天' AS name FROM dual
                            UNION ALL
                            SELECT '7' AS code,'逾20天' AS name FROM dual
                            ) S LEFT JOIN
                            (SELECT A.class,
                                    A.term,
                                    A.quota,
                                    (CASE WHEN B.type = '13' THEN '25' ELSE B.type END
                                    ) AS type,
                                    B.process,
                                    (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                    B.trainday,
                                    (CASE   WHEN B.trainday <= 0.5                        THEN '1'
                                            WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                            WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                            WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                            WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                            WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                            WHEN B.trainday >= 20.0                       THEN '7'
                                    END ) AS period_type,
                                    B.classhr,
                                    D.idno,
                                    (CASE
                                        WHEN E.sex IN ('M','F') THEN E.sex
                                        WHEN E.sex= 'm' THEN 'M'
                                        WHEN E.sex= 'f' THEN 'F'
                                        WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                        WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                        ELSE 'F'
                                    END) AS SEX,
                                    D.age,
                                    (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                    D.ecode
                                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                INNER JOIN m02tb E ON D.idno = E.idno
                                    WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                    AND D.status = '1'
                                    AND B.process IN ('1','3','4')
                                    AND B.cntflag = '1'
                                    ORDER BY A.class, A.term, B.type
                                ) T ON T.period_type = S.code
                    GROUP BY RTRIM(S.name)
                    ORDER BY S.code
                        ";
            $reportlist2B = DB::select($sql2B);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist2B) != 0) {
                $arraykeys2B=array_keys((array)$reportlist2B[0]);
            }
            $reportlist2B = json_decode(json_encode($reportlist2B), true);
        }

        //性別及學歷
        if($checkedu=='1'){
            //3:性別及學歷
            $sql3="SELECT RTRIM(S.name),
                            COUNT(T.idno),
                            SUM(CASE WHEN sex   = 'M' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN sex   = 'F' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '1' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '2' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '3' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '4' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '5' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '6' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '7' THEN 1 ELSE 0 END)
                    FROM (
                                    SELECT  A.class,
                                                    A.term,
                                                    A.quota,
                                                    (CASE
                                                                WHEN B.type = '13' THEN '25' ELSE B.type END
                                                    ) AS type,
                                                    B.process,
                                                    (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                                    B.trainday,
                                                    (CASE WHEN B.trainday <= 0.5                        THEN '1'
                                                                WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                                                WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                                                WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                                                WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                                                WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                                                WHEN B.trainday >= 20.0                       THEN '7'
                                                    END ) AS period_type,
                                                    B.classhr,
                                                    D.idno,
                                                    (CASE
                                                        WHEN E.sex IN ('M','F') THEN E.sex
                                                        WHEN E.sex= 'm' THEN 'M'
                                                        WHEN E.sex= 'f' THEN 'F'
                                                        WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                                        WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                                        ELSE 'F'
                                                    END) AS SEX,
                                                    D.age,
                                                    (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                                    D.ecode
                                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                            INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                            INNER JOIN m02tb E ON D.idno = E.idno
                                    WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                    AND D.status = '1'
                                    AND B.process IN ('1','3','4')
                                    AND B.cntflag = '1'
                                    ORDER BY A.class, A.term, B.type
                                ) T LEFT JOIN s01tb S ON T.type = S.code AND S.type = 'K'
                    GROUP BY RTRIM(S.name)
                    ORDER BY S.code
                        ";
            $reportlist3 = DB::select($sql3);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist3) != 0) {
                $arraykeys3=array_keys((array)$reportlist3[0]);
            }
            $reportlist3 = json_decode(json_encode($reportlist3), true);

            //開班類別
            $sql3A="SELECT RTRIM(S.name),
                            COUNT(T.idno),
                            SUM(CASE WHEN sex   = 'M' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN sex   = 'F' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '1' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '2' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '3' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '4' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '5' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '6' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '7' THEN 1 ELSE 0 END)
                    FROM   (SELECT '1' AS code,'自辦' AS name FROM dual
                            UNION ALL
                            SELECT '3' AS code,'合辦' AS name FROM dual
                            UNION ALL
                            SELECT '4' AS code,'接受委訓' AS name FROM dual
                            ) S LEFT JOIN
                            (SELECT A.class,
                                    A.term,
                                    A.quota,
                                    (CASE WHEN B.type = '13' THEN '25' ELSE B.type END
                                    ) AS type,
                                    B.process,
                                    (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                    B.trainday,
                                    (CASE   WHEN B.trainday <= 0.5                        THEN '1'
                                            WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                            WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                            WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                            WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                            WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                            WHEN B.trainday >= 20.0                       THEN '7'
                                    END ) AS period_type,
                                    B.classhr,
                                    D.idno,
                                    (CASE
                                        WHEN E.sex IN ('M','F') THEN E.sex
                                        WHEN E.sex= 'm' THEN 'M'
                                        WHEN E.sex= 'f' THEN 'F'
                                        WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                        WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                        ELSE 'F'
                                    END) AS SEX,
                                    D.age,
                                    (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                    D.ecode
                                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                INNER JOIN m02tb E ON D.idno = E.idno
                                    WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                    AND D.status = '1'
                                    AND B.process IN ('1','3','4')
                                    AND B.cntflag = '1'
                                    ORDER BY A.class, A.term, B.type
                                ) T ON T.process = S.code
                    GROUP BY RTRIM(S.name)
                    ORDER BY S.code
                        ";
            $reportlist3A = DB::select($sql3A);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist3A) != 0) {
                $arraykeys3A=array_keys((array)$reportlist3A[0]);
            }
            $reportlist3A = json_decode(json_encode($reportlist3A), true);

            //訓練期程
            $sql3B="SELECT RTRIM(S.name),
                            COUNT(T.idno),
                            SUM(CASE WHEN sex   = 'M' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN sex   = 'F' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '1' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '2' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '3' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '4' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '5' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '6' THEN 1 ELSE 0 END),
                            SUM(CASE WHEN ecode = '7' THEN 1 ELSE 0 END)
                    FROM   (SELECT '1' AS code,'0.5天' AS name FROM dual
                            UNION ALL
                            SELECT '2' AS code,'1天' AS name FROM dual
                            UNION ALL
                            SELECT '3' AS code,'2天' AS name FROM dual
                            UNION ALL
                            SELECT '4' AS code,'3天' AS name FROM dual
                            UNION ALL
                            SELECT '5' AS code,'逾3天至5天' AS name FROM dual
                            UNION ALL
                            SELECT '6' AS code,'逾5天至20天' AS name FROM dual
                            UNION ALL
                            SELECT '7' AS code,'逾20天' AS name FROM dual
                            ) S LEFT JOIN
                            (SELECT A.class,
                                    A.term,
                                    A.quota,
                                    (CASE WHEN B.type = '13' THEN '25' ELSE B.type END
                                    ) AS type,
                                    B.process,
                                    (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                    B.trainday,
                                    (CASE   WHEN B.trainday <= 0.5                        THEN '1'
                                            WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                            WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                            WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                            WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                            WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                            WHEN B.trainday >= 20.0                       THEN '7'
                                    END ) AS period_type,
                                    B.classhr,
                                    D.idno,
                                    (CASE
                                        WHEN E.sex IN ('M','F') THEN E.sex
                                        WHEN E.sex= 'm' THEN 'M'
                                        WHEN E.sex= 'f' THEN 'F'
                                        WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                        WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                        ELSE 'F'
                                    END) AS SEX,
                                    D.age,
                                    (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                    D.ecode
                                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                INNER JOIN m02tb E ON D.idno = E.idno
                                    WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                    AND D.status = '1'
                                    AND B.process IN ('1','3','4')
                                    AND B.cntflag = '1'
                                    ORDER BY A.class, A.term, B.type
                                ) T ON T.period_type = S.code
                    GROUP BY RTRIM(S.name)
                    ORDER BY S.code
                        ";
            $reportlist3B = DB::select($sql3B);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist3B) != 0) {
                $arraykeys3B=array_keys((array)$reportlist3B[0]);
            }
            $reportlist3B = json_decode(json_encode($reportlist3B), true);
        }

        //開班數及受訓人次
        if($checknum=='1'){
            //4:開班數及受訓人次
            $sql4="SELECT RTRIM(name),
                            COUNT(class),
                            SUM(quota),
                            SUM(cnt_idno),
                            SUM(cnt_idno*trainday),
                            SUM(cnt_idno*classhr)
                    FROM (
                            SELECT  T.class, T.term, S.code, S.name,
                                    IFNULL(quota,0) AS quota,
                                    IFNULL(COUNT(idno),0) AS cnt_idno,
                                    IFNULL(trainday,0) AS trainday,
                                    IFNULL(classhr,0) AS classhr
                            FROM (
                                    SELECT  A.class,
                                                    A.term,
                                                    A.quota,
                                                    (CASE
                                                                WHEN B.type = '13' THEN '25' ELSE B.type END
                                                    ) AS type,
                                                    B.process,
                                                    (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                                    B.trainday,
                                                    (CASE WHEN B.trainday <= 0.5                        THEN '1'
                                                                WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                                                WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                                                WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                                                WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                                                WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                                                WHEN B.trainday >= 20.0                       THEN '7'
                                                    END ) AS period_type,
                                                    B.classhr,
                                                    D.idno,
                                                    (CASE
                                                        WHEN E.sex IN ('M','F') THEN E.sex
                                                        WHEN E.sex= 'm' THEN 'M'
                                                        WHEN E.sex= 'f' THEN 'F'
                                                        WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                                        WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                                        ELSE 'F'
                                                    END) AS SEX,
                                                    D.age,
                                                    (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                                    D.ecode
                                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                            INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                            INNER JOIN m02tb E ON D.idno = E.idno
                                    WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                    AND D.status = '1'
                                    AND B.process IN ('1','3','4')
                                    AND B.cntflag = '1'
                                    ORDER BY A.class, A.term, B.type
                                ) T LEFT JOIN s01tb S ON T.type = S.code AND S.type = 'K'
                                GROUP BY S.code, S.name, quota, trainday, classhr, T.class, T.term
                            ) TT
                    GROUP BY RTRIM(name)
                    ORDER BY code
                        ";
            $reportlist4 = DB::select($sql4);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist4) != 0) {
                $arraykeys4=array_keys((array)$reportlist4[0]);
            }
            $reportlist4 = json_decode(json_encode($reportlist4), true);

            //開班類別
            $sql4A="SELECT RTRIM(name),
                            COUNT(class),
                            SUM(quota),
                            SUM(cnt_idno),
                            SUM(cnt_idno*trainday),
                            SUM(cnt_idno*classhr)
                    FROM (
                            SELECT  T.class, T.term, S.code, S.name,
                                    IFNULL(quota,0) AS quota,
                                    IFNULL(COUNT(idno),0) AS cnt_idno,
                                    IFNULL(trainday,0) AS trainday,
                                    IFNULL(classhr,0) AS classhr
                            FROM   (SELECT '1' AS code,'自辦' AS name FROM dual
                                    UNION ALL
                                    SELECT '3' AS code,'合辦' AS name FROM dual
                                    UNION ALL
                                    SELECT '4' AS code,'接受委訓' AS name FROM dual
                                    ) S LEFT JOIN
                                    (SELECT A.class,
                                            A.term,
                                            A.quota,
                                            (CASE WHEN B.type = '13' THEN '25' ELSE B.type END
                                            ) AS type,
                                            B.process,
                                            (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                            B.trainday,
                                            (CASE   WHEN B.trainday <= 0.5                        THEN '1'
                                                    WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                                    WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                                    WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                                    WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                                    WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                                    WHEN B.trainday >= 20.0                       THEN '7'
                                            END ) AS period_type,
                                            B.classhr,
                                            D.idno,
                                            (CASE
                                                WHEN E.sex IN ('M','F') THEN E.sex
                                                WHEN E.sex= 'm' THEN 'M'
                                                WHEN E.sex= 'f' THEN 'F'
                                                WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                                WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                                ELSE 'F'
                                            END) AS SEX,
                                            D.age,
                                            (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                            D.ecode
                                            FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                        INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                        INNER JOIN m02tb E ON D.idno = E.idno
                                            WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                            AND D.status = '1'
                                            AND B.process IN ('1','3','4')
                                            AND B.cntflag = '1'
                                            ORDER BY A.class, A.term, B.type
                                        ) T ON T.process = S.code
                                    GROUP BY S.code, S.name, quota, trainday, classhr, T.class, T.term
                            ) TT
                        GROUP BY RTRIM(name)
                        ORDER BY code
                        ";
            $reportlist4A = DB::select($sql4A);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist4A) != 0) {
                $arraykeys4A=array_keys((array)$reportlist4A[0]);
            }
            $reportlist4A = json_decode(json_encode($reportlist4A), true);

            //訓練期程
            $sql4B="SELECT RTRIM(name),
                            COUNT(class),
                            SUM(quota),
                            SUM(cnt_idno),
                            SUM(cnt_idno*trainday),
                            SUM(cnt_idno*classhr)
                    FROM (
                            SELECT  T.class, T.term, S.code, S.name,
                                    IFNULL(quota,0) AS quota,
                                    IFNULL(COUNT(idno),0) AS cnt_idno,
                                    IFNULL(trainday,0) AS trainday,
                                    IFNULL(classhr,0) AS classhr
                            FROM   (SELECT '1' AS code,'0.5天' AS name FROM dual
                                    UNION ALL
                                    SELECT '2' AS code,'1天' AS name FROM dual
                                    UNION ALL
                                    SELECT '3' AS code,'2天' AS name FROM dual
                                    UNION ALL
                                    SELECT '4' AS code,'3天' AS name FROM dual
                                    UNION ALL
                                    SELECT '5' AS code,'逾3天至5天' AS name FROM dual
                                    UNION ALL
                                    SELECT '6' AS code,'逾5天至20天' AS name FROM dual
                                    UNION ALL
                                    SELECT '7' AS code,'逾20天' AS name FROM dual
                                    ) S LEFT JOIN
                                    (SELECT A.class,
                                            A.term,
                                            A.quota,
                                            (CASE WHEN B.type = '13' THEN '25' ELSE B.type END
                                            ) AS type,
                                            B.process,
                                            (CASE WHEN B.process IN ('1','3') THEN 1 WHEN B.process = '4' THEN 2 END) AS process_type,
                                            B.trainday,
                                            (CASE   WHEN B.trainday <= 0.5                        THEN '1'
                                                    WHEN B.trainday  > 0.5 AND B.trainday <= 1.0  THEN '2'
                                                    WHEN B.trainday  > 1.0 AND B.trainday <= 2.0  THEN '3'
                                                    WHEN B.trainday  > 2.0 AND B.trainday <= 3.0  THEN '4'
                                                    WHEN B.trainday  > 3.0 AND B.trainday <= 5.0  THEN '5'
                                                    WHEN B.trainday  > 5.0 AND B.trainday <= 20.0 THEN '6'
                                                    WHEN B.trainday >= 20.0                       THEN '7'
                                            END ) AS period_type,
                                            B.classhr,
                                            D.idno,
                                            (CASE
                                                WHEN E.sex IN ('M','F') THEN E.sex
                                                WHEN E.sex= 'm' THEN 'M'
                                                WHEN E.sex= 'f' THEN 'F'
                                                WHEN SUBSTRING(D.idno,2,1) = '1' THEN 'M'
                                                WHEN SUBSTRING(D.idno,2,1) = '2' THEN 'F'
                                                ELSE 'F'
                                            END) AS SEX,
                                            D.age,
                                            (CASE WHEN NOT D.rank BETWEEN '01' AND '20' THEN '20' ELSE D.rank END) AS rank,
                                            D.ecode
                                            FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                                        INNER JOIN t13tb D ON A.class = D.class AND A.term = D.term
                                                        INNER JOIN m02tb E ON D.idno = E.idno
                                            WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
                                            AND D.status = '1'
                                            AND B.process IN ('1','3','4')
                                            AND B.cntflag = '1'
                                            ORDER BY A.class, A.term, B.type
                                        ) T ON T.period_type = S.code
                                    GROUP BY S.code, S.name, quota, trainday, classhr, T.class, T.term
                                ) TT
                        GROUP BY RTRIM(name)
                        ORDER BY code
                        ";
            $reportlist4B = DB::select($sql4B);
            //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
            //取出全部項目
            if(sizeof($reportlist4B) != 0) {
                $arraykeys4B=array_keys((array)$reportlist4B[0]);
            }
            $reportlist4B = json_decode(json_encode($reportlist4B), true);
        }

        // 檔案名稱
        $fileName = 'D13';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

        if($checkrank=='1'){
            $objActSheet = $objPHPExcel->getSheet(0);
            //A4:108年12月01日至109年01月31日
            $objActSheet->setCellValue('A4', substr($sdate,0,3).'年'.substr($sdate,3,2).'月'.substr($sdate,5,2).'日至'.substr($edate,0,3).'年'.substr($edate,3,2).'月'.substr($edate,5,2).'日');
            //中華民國 109 年 1 月 15 日 編製
            $objActSheet->setCellValue('G20', '中華民國 '.(date('Y')-'1911').'年'.date('m').'月'.date('d').'日 編製');
            if(sizeof($reportlist1) != 0) {
                //新增、減少【訓練類別】
                if(sizeof($reportlist1)==1){
                    $objActSheet->removeRow(7,2);
                }elseif(sizeof($reportlist1)==1){
                    $objActSheet->removeRow(7);
                }elseif(sizeof($reportlist1)>3){
                    $objActSheet->insertNewRowBefore(9,sizeof($reportlist1)-3);
                }
                $objActSheet->setCellValue('A7', '訓練類別');

                //第六列 總計
                for ($t=0; $t < sizeof($arraykeys1); $t++) {
                    if($t>=1){
                        $NameFromNumber=$this->getNameFromNumber($t+2); //B
                        //=SUM(C7:C10)
                        $objActSheet->setCellValue($NameFromNumber.'6', '=SUM('.$NameFromNumber.'7:'.$NameFromNumber.(7+sizeof($reportlist1)-1).')');
                    }
                }

                //訓練類別
                for ($j=0; $j < sizeof($reportlist1); $j++) {
                    for ($i=0; $i < sizeof($arraykeys1); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist1[$j][$arraykeys1[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+7), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+7), $reportlist1[$j][$arraykeys1[$i]]);
                        }
                    }
                }
            }

            //開班類別
            if(sizeof($reportlist1A) != 0) {
                for ($j=0; $j < sizeof($reportlist1A); $j++) {
                    for ($i=0; $i < sizeof($arraykeys1A); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist1A[$j][$arraykeys1A[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist1)), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist1)), $reportlist1A[$j][$arraykeys1A[$i]]);
                        }
                    }
                }
            }

            //訓練期程
            if(sizeof($reportlist1B) != 0) {
                for ($j=0; $j < sizeof($reportlist1B); $j++) {
                    for ($i=0; $i < sizeof($arraykeys1B); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist1B[$j][$arraykeys1B[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist1)+3), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist1)+3), $reportlist1B[$j][$arraykeys1B[$i]]);
                        }
                    }
                }
            }
        }

        if($checkage=='1'){
            $objActSheet = $objPHPExcel->getSheet(1);
            //A4:108年12月01日至109年01月31日
            $objActSheet->setCellValue('A4', substr($sdate,0,3).'年'.substr($sdate,3,2).'月'.substr($sdate,5,2).'日至'.substr($edate,0,3).'年'.substr($edate,3,2).'月'.substr($edate,5,2).'日');
            $objActSheet->setCellValue('H20', '中華民國 '.(date('Y')-'1911').' 年 '.date('m').' 月 '.date('d').' 日 編製');
            if(sizeof($reportlist2) != 0) {
                //新增、減少【訓練類別】
                if(sizeof($reportlist2)==1){
                    $objActSheet->removeRow(7,2);
                }elseif(sizeof($reportlist2)==1){
                    $objActSheet->removeRow(7);
                }elseif(sizeof($reportlist2)>3){
                    $objActSheet->insertNewRowBefore(9,sizeof($reportlist2)-3);
                }
                $objActSheet->setCellValue('A7', '訓練類別');

                //第六列 總計
                for ($t=0; $t < sizeof($arraykeys2); $t++) {
                    if($t>=1){
                        $NameFromNumber=$this->getNameFromNumber($t+2); //B
                        //=SUM(C7:C10)
                        $objActSheet->setCellValue($NameFromNumber.'6', '=SUM('.$NameFromNumber.'7:'.$NameFromNumber.(7+sizeof($reportlist2)-1).')');
                    }
                }

                //訓練類別
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    for ($i=0; $i < sizeof($arraykeys2); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist2[$j][$arraykeys2[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+7), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+7), $reportlist2[$j][$arraykeys2[$i]]);
                        }
                    }
                }
            }

            //開班類別
            if(sizeof($reportlist2A) != 0) {
                for ($j=0; $j < sizeof($reportlist2A); $j++) {
                    for ($i=0; $i < sizeof($arraykeys2A); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist2A[$j][$arraykeys2A[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist2)), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist2)), $reportlist2A[$j][$arraykeys2A[$i]]);
                        }
                    }
                }
            }

            //訓練期程
            if(sizeof($reportlist2B) != 0) {
                for ($j=0; $j < sizeof($reportlist2B); $j++) {
                    for ($i=0; $i < sizeof($arraykeys2B); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist2B[$j][$arraykeys2B[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist2)+3), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist2)+3), $reportlist2B[$j][$arraykeys2B[$i]]);
                        }
                    }
                }
            }
        }

        if($checkedu=='1'){
            $objActSheet = $objPHPExcel->getSheet(2);
            //A4:108年12月01日至109年01月31日
            $objActSheet->setCellValue('A4', substr($sdate,0,3).'年'.substr($sdate,3,2).'月'.substr($sdate,5,2).'日至'.substr($edate,0,3).'年'.substr($edate,3,2).'月'.substr($edate,5,2).'日');
            $objActSheet->setCellValue('H21', '中華民國 '.(date('Y')-'1911').' 年 '.date('m').' 月 '.date('d').' 日 編製');
            if(sizeof($reportlist3) != 0) {
                //新增、減少【訓練類別】
                if(sizeof($reportlist3)==1){
                    $objActSheet->removeRow(8,2);
                }elseif(sizeof($reportlist3)==1){
                    $objActSheet->removeRow(8);
                }elseif(sizeof($reportlist3)>3){
                    //$objActSheet->insertNewRowBefore(9,sizeof($reportlist3)-3);
                    $objActSheet->insertNewRowBefore(10,sizeof($reportlist3)-3);
                }
                $objActSheet->setCellValue('A8', '訓練類別');

                //第六列 總計
                for ($t=0; $t < sizeof($arraykeys3); $t++) {
                    if($t>=1){
                        $NameFromNumber=$this->getNameFromNumber($t+2); //B
                        //=SUM(C7:C10)
                        $objActSheet->setCellValue($NameFromNumber.'7', '=SUM('.$NameFromNumber.'8:'.$NameFromNumber.(8+sizeof($reportlist3)-1).')');
                    }
                }

                //訓練類別
                for ($j=0; $j < sizeof($reportlist3); $j++) {
                    for ($i=0; $i < sizeof($arraykeys3); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist3[$j][$arraykeys3[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+8), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+8), $reportlist3[$j][$arraykeys3[$i]]);
                        }
                    }
                }
            }

            //開班類別
            if(sizeof($reportlist3A) != 0) {
                for ($j=0; $j < sizeof($reportlist3A); $j++) {
                    for ($i=0; $i < sizeof($arraykeys3A); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist3A[$j][$arraykeys3A[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+8+sizeof($reportlist3)), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+8+sizeof($reportlist3)), $reportlist3A[$j][$arraykeys3A[$i]]);
                        }
                    }
                }
            }

            //訓練期程
            if(sizeof($reportlist3B) != 0) {
                for ($j=0; $j < sizeof($reportlist3B); $j++) {
                    for ($i=0; $i < sizeof($arraykeys3B); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist3B[$j][$arraykeys3B[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+8+sizeof($reportlist3)+3), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+8+sizeof($reportlist3)+3), $reportlist3B[$j][$arraykeys3B[$i]]);
                        }
                    }
                }
            }
        }

        if($checknum=='1'){
            $objActSheet = $objPHPExcel->getSheet(3);
            //A4:108年12月01日至109年01月31日
            $objActSheet->setCellValue('A4', substr($sdate,0,3).'年'.substr($sdate,3,2).'月'.substr($sdate,5,2).'日至'.substr($edate,0,3).'年'.substr($edate,3,2).'月'.substr($edate,5,2).'日');
            $objActSheet->setCellValue('F20', '中華民國 '.(date('Y')-'1911').' 年 '.date('m').' 月 '.date('d').' 日 編製');
            if(sizeof($reportlist4) != 0) {
                //新增、減少【訓練類別】
                if(sizeof($reportlist4)==1){
                    $objActSheet->removeRow(7,2);
                }elseif(sizeof($reportlist4)==1){
                    $objActSheet->removeRow(7);
                }elseif(sizeof($reportlist4)>3){
                    $objActSheet->insertNewRowBefore(9,sizeof($reportlist4)-3);
                }
                $objActSheet->setCellValue('A7', '訓練類別');

                //第六列 總計
                for ($t=0; $t < sizeof($arraykeys4); $t++) {
                    if($t>=1){
                        $NameFromNumber=$this->getNameFromNumber($t+2); //B
                        //=SUM(C7:C10)
                        $objActSheet->setCellValue($NameFromNumber.'6', '=SUM('.$NameFromNumber.'7:'.$NameFromNumber.(7+sizeof($reportlist4)-1).')');
                    }
                }

                //訓練類別
                for ($j=0; $j < sizeof($reportlist4); $j++) {
                    for ($i=0; $i < sizeof($arraykeys4); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist4[$j][$arraykeys4[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+7), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+7), $reportlist4[$j][$arraykeys4[$i]]);
                        }
                    }
                }
            }

            //開班類別
            if(sizeof($reportlist4A) != 0) {
                for ($j=0; $j < sizeof($reportlist4A); $j++) {
                    for ($i=0; $i < sizeof($arraykeys4A); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist4A[$j][$arraykeys4A[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist4)), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist4)), $reportlist4A[$j][$arraykeys4A[$i]]);
                        }
                    }
                }
            }

            //dd($reportlist4A);
            //訓練期程
            if(sizeof($reportlist4B) != 0) {
                for ($j=0; $j < sizeof($reportlist4B); $j++) {
                    for ($i=0; $i < sizeof($arraykeys4B); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($reportlist4B[$j][$arraykeys4B[$i]]=='0'){
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist4)+3), '- ');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+7+sizeof($reportlist4)+3), $reportlist4B[$j][$arraykeys4B[$i]]);
                        }
                    }
                }
            }
        }

        //學員官等
        if($checknum!='1'){
            $objActSheet = $objPHPExcel->removeSheetByIndex(3);
        }
        //年齡
        if($checkedu!='1'){
            $objActSheet = $objPHPExcel->removeSheetByIndex(2);
        }
        //性別及學歷
        if($checkage!='1'){
            $objActSheet = $objPHPExcel->removeSheetByIndex(1);
        }
        //開班數及受訓人次
        if($checkrank!='1'){
            $objActSheet = $objPHPExcel->removeSheetByIndex(0);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"公務統計報表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
        //export excel

    }
}
