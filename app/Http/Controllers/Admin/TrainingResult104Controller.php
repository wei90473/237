<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPEXcel_RichText;
use PHPExcel_Chart;
use PHPExcel_Chart_Title;
use PHPExcel_Chart_Layout;
use PHPExcel_Chart_Axis;

class TrainingResult104Controller extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('training_result_104', $user_group_auth)){
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
        //return view('admin/training_result_104/list');
        $classArr = $this->getclass();
        $result = '';
        return view('admin/training_result_104/list', compact('classArr', 'result'));
    }

    // 搜尋下拉『班別』
    public function getclass() {
            $sql = "SELECT DISTINCT t53tb.class, t01tb.name
                      FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                     WHERE t53tb.times<>'' AND SUBSTRING(t53tb.class,1,3) = '104'
                     ORDER BY t53tb.class DESC  ";
            $classArr = DB::select($sql);
            return $classArr;
    }

    // 搜尋下拉『期別』
    public function getTermByClass(Request $request)
    {
      $RptBasic = new \App\Rptlib\RptBasic;
      return $RptBasic->getTermByClass($request->input('class'));
    }

    // 搜尋下拉『第幾次調查』
    public function getTimeByClass(Request $request)
    {
      $RptBasic = new \App\Rptlib\RptBasic;
      return $RptBasic->getTimeByClass($request->input('class'), $request->input('term'));
    }

    /*
    訓練成效評估結果統計圖表(104) CSDIR5024
    參考Tables:
    使用範本:L9.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //班別
        $class = $request->input('class');
        //期別
        $term = $request->input('term');
        //第幾次調查
        $times= $request->input('times');

    //固定題目統計
    $sql="SELECT
                    (CASE
                    WHEN A.key1='q1' THEN COUNT((CASE WHEN q1=5 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q2' THEN COUNT((CASE WHEN q2=5 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q3' THEN COUNT((CASE WHEN q3=5 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q4' THEN COUNT((CASE WHEN q4=5 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q5' THEN COUNT((CASE WHEN q5=5 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q6' THEN COUNT((CASE WHEN q6=5 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q7' THEN COUNT((CASE WHEN q7=5 THEN 1 ELSE NULL  END))
                    ELSE 0
                    END),
                    (CASE
                    WHEN A.key1='q1' THEN COUNT((CASE WHEN q1=4 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q2' THEN COUNT((CASE WHEN q2=4 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q3' THEN COUNT((CASE WHEN q3=4 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q4' THEN COUNT((CASE WHEN q4=4 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q5' THEN COUNT((CASE WHEN q5=4 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q6' THEN COUNT((CASE WHEN q6=4 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q7' THEN COUNT((CASE WHEN q7=4 THEN 1 ELSE NULL  END))
                    ELSE 0
                    END),
                    (CASE
                    WHEN A.key1='q1' THEN COUNT((CASE WHEN q1=3 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q2' THEN COUNT((CASE WHEN q2=3 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q3' THEN COUNT((CASE WHEN q3=3 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q4' THEN COUNT((CASE WHEN q4=3 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q5' THEN COUNT((CASE WHEN q5=3 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q6' THEN COUNT((CASE WHEN q6=3 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q7' THEN COUNT((CASE WHEN q7=3 THEN 1 ELSE NULL  END))
                    ELSE 0
                    END),
                    (CASE
                    WHEN A.key1='q1' THEN COUNT((CASE WHEN q1=2 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q2' THEN COUNT((CASE WHEN q2=2 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q3' THEN COUNT((CASE WHEN q3=2 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q4' THEN COUNT((CASE WHEN q4=2 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q5' THEN COUNT((CASE WHEN q5=2 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q6' THEN COUNT((CASE WHEN q6=2 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q7' THEN COUNT((CASE WHEN q7=2 THEN 1 ELSE NULL  END))
                    ELSE 0
                    END),
                    (CASE
                    WHEN A.key1='q1' THEN COUNT((CASE WHEN q1=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q2' THEN COUNT((CASE WHEN q2=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q3' THEN COUNT((CASE WHEN q3=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q4' THEN COUNT((CASE WHEN q4=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q5' THEN COUNT((CASE WHEN q5=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q6' THEN COUNT((CASE WHEN q6=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q7' THEN COUNT((CASE WHEN q7=1 THEN 1 ELSE NULL  END))
                    ELSE 0
                    END),
                    (CASE
                    WHEN A.key1='q1' THEN SUM((CASE WHEN q1 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q2' THEN SUM((CASE WHEN q2 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q3' THEN SUM((CASE WHEN q3 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q4' THEN SUM((CASE WHEN q4 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q5' THEN SUM((CASE WHEN q5 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q6' THEN SUM((CASE WHEN q6 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q7' THEN SUM((CASE WHEN q7 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    ELSE 0
                    END)
                    FROM
                    (
                        SELECT  'q1' AS key1, 'Q1.我認為本次研習所訂您對本次研習活動整體安排是否滿意研習目標符合組織或個人之需求' AS caption
                        UNION ALL
                        SELECT  'q2' AS key1, 'Q2.您對本次研習課程是否滿意' AS caption
                        UNION ALL
                        SELECT  'q3' AS key1, 'Q3.您認為本次研習對您公務執行有無幫助' AS caption
                        UNION ALL
                        SELECT  'q4' AS key1, 'Q4.您對本次研習的教學場地是否滿意' AS caption
                        UNION ALL
                        SELECT  'q5' AS key1, 'Q5.您對班務人員的服務是否滿意' AS caption
                        UNION ALL
                        SELECT  'q6' AS key1, 'Q6.您對伙食是否滿意' AS caption
                        UNION ALL
                        SELECT  'q7' AS key1, 'Q7.您對住宿環境是否滿意' AS caption
                    ) A
                    INNER JOIN t91tb t91tb ON 1 = 1
                WHERE t91tb.class= '".$class."'
                AND t91tb.term= '".$term."'
                AND t91tb.times= IFNULL('".$times."',t91tb.times)
                GROUP BY A.caption, A.key1
                ORDER BY A.caption
                    ";


        $reportlist = DB::select($sql);
        $dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

    //TITLE
    $sqlTitle="SELECT DISTINCT t53tb.class, t01tb.name, t53tb.term, t53tb.times,
                    CONCAT(t01tb.name, '第',
                    CASE t53tb.term WHEN '01' THEN '1'
                                    WHEN '02' THEN '2'
                                    WHEN '03' THEN '3'
                                    WHEN '04' THEN '4'
                                    WHEN '05' THEN '5'
                                    WHEN '06' THEN '6'
                                    WHEN '07' THEN '7'
                                    WHEN '08' THEN '8'
                                    WHEN '09' THEN '9'
                                    ELSE t53tb.term END
                    , '期訓練成效評估結果統計表' ,
                                                            '(' ,
                                                            CASE t53tb.times WHEN 1 THEN '一' WHEN 2 THEN '二' WHEN 3 THEN '三' WHEN 4 THEN '四'  WHEN 5 THEN '五'
                                                                            WHEN 6 THEN '六' WHEN 7 THEN '七' WHEN 8 THEN '八' WHEN 9 THEN '九'  WHEN 10 THEN '十'
                                                                            ELSE t53tb.times END
                                                            ,')')  AS TITLE
                FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                WHERE t53tb.times<>''
                AND t53tb.class = '".$class."'
                AND t53tb.term= '".$term."'
                AND t53tb.times= IFNULL('".$times."',t53tb.times)
                ORDER BY t53tb.class DESC

        ";
    $reportlistTitle = DB::select($sqlTitle);
    $dataArrTitle=json_decode(json_encode(DB::select($sqlTitle)), true);

    //A14:REMARK 1
    //不需在額外判斷最大次數
    $sqlRemark1="SELECT CONCAT('1.本次調查問卷計發出', A.lngCopy_Count, '份，共回收', B.lngBack_Count, '份，') AS REMARK1A,
        CONCAT('回收率',
                CASE WHEN A.lngCopy_Count = 0 THEN
                        '0%。'
                    ELSE
        CONCAT(FORMAT( B.lngBack_Count / A.lngCopy_Count * (100), 2), '%。')
                    END ) AS REMARK1B
        FROM (
        SELECT copy AS lngCopy_Count
        FROM t53tb
        WHERE class= '".$class."'
            AND term= '".$term."'
            AND times= IFNULL('".$times."',times)
        ) A LEFT JOIN
        ( SELECT COUNT(*) AS lngBack_Count
            FROM t91tb
            WHERE class='".$class."'
            AND term= '".$term."'
            AND times= IFNULL('".$times."',times)
        ) B ON 1 = 1
        ";
    $reportlistRemark1 = DB::select($sqlRemark1);
    $dataArrRemark1=json_decode(json_encode(DB::select($sqlRemark1)), true);


    //A15~A16:REMARK2
    $sqlRemark2="SELECT CONCAT('2.本次研習學員滿意度總平均為', totper, '分') AS REMARK2A,
                        CONCAT('，其中「研習規劃」滿意度平均為', conper,
                               '、「對公務執行有助益」者', offper,
                               '、「學習輔導」滿意度平均為', worper) AS REMARK2B,
                        CONCAT('、「講座授課」滿意度平均為', teaper,
                                '，「講座授課」統計結果詳附表。') AS REMARK2C
                    FROM t57tb
                    WHERE class='".$class."'
                    AND term= '".$term."'
                    AND times= IFNULL('".$times."',times)
    ";
    $reportlistRemark2 = DB::select($sqlRemark2);
    $dataArrRemark2=json_decode(json_encode(DB::select($sqlRemark2)), true);

    //講座統計
    //講者
    $sql1Name="SELECT D.cname, D.name,
                      CASE C.anstype WHEN 'ans1' THEN '教學方法'
                                     WHEN 'ans2' THEN '教學內容'
                      END asntype,
                      C.ansname,
                      0 AS P_COUNT,
                      0 AS COL1, 0 AS COL2, 0 AS COL3, 0 AS COL4
                FROM (SELECT anstype, ans, ansname
                            FROM (
                            SELECT 'ans1' AS anstype
                            UNION ALL
                            SELECT 'ans2' AS anstype
                            ) A
                            CROSS JOIN (
                            SELECT '5' AS ans,'非常滿意' AS ansname
                            UNION ALL
                            SELECT '4','滿意'
                            UNION ALL
                            SELECT '3','普通'
                            UNION ALL
                            SELECT '2','不滿意'
                            UNION ALL
                            SELECT '1','非常不滿意'
                            ) B
                        ORDER BY 1,2 DESC
                    ) C
                        CROSS JOIN (SELECT m01tb.cname, t06tb.name, t54tb.class, t54tb.term, t54tb.course, t54tb.times
                                    FROM t54tb  LEFT JOIN m01tb ON t54tb.idno=m01tb.idno
                                                LEFT JOIN t06tb ON t54tb.class = t06tb.class
                                                                AND t54tb.term = t06tb.term
                                                                AND t54tb.course = t06tb.course
                                    WHERE t54tb.class= '".$class."'
                                    AND t54tb.term= '".$term."'
                                    AND t54tb.times= IFNULL('".$times."',t54tb.times)
                                    ) D
                ORDER BY D.course, C.anstype, C.ans DESC
        ";
    $reportlist1Name = DB::select($sql1Name);
    $dataArr1Name=json_decode(json_encode(DB::select($sql1Name)), true);
    //取出全部項目
    if(sizeof($reportlist1Name) != 0) {
        $arraykeys1Name=array_keys((array)$reportlist1Name[0]);
    }

    //講座統計
    $sql1="SELECT count(CASE WHEN ans1=5 THEN 1 ELSE NULL END ) AS ans1q5,
                  count(CASE WHEN ans1=4 THEN 1 ELSE NULL END ) AS ans1q4,
                  count(CASE WHEN ans1=3 THEN 1 ELSE NULL END ) AS ans1q3,
                  count(CASE WHEN ans1=2 THEN 1 ELSE NULL END ) AS ans1q2,
                  count(CASE WHEN ans1=1 THEN 1 ELSE NULL END ) AS ans1q1,
                  count(CASE WHEN ans2=5 THEN 1 ELSE NULL END ) AS ans2q5,
                    count(CASE WHEN ans2=4 THEN 1 ELSE NULL END ) AS ans2q4,
                    count(CASE WHEN ans2=3 THEN 1 ELSE NULL END ) AS ans2q3,
                    count(CASE WHEN ans2=2 THEN 1 ELSE NULL END ) AS ans2q2,
                    count(CASE WHEN ans2=1 THEN 1 ELSE NULL END ) AS ans2q1
            FROM t56tb
            WHERE t56tb.class= '".$class."'
            AND t56tb.term= '".$term."'
            AND t56tb.times= IFNULL('".$times."',t56tb.times)
            GROUP BY t56tb.class, t56tb.term, t56tb.times, t56tb.course
            ORDER BY t56tb.course
        ";
    $reportlist1 = DB::select($sql1);
    $dataArr1=json_decode(json_encode(DB::select($sql1)), true);
    //取出全部項目
    if(sizeof($reportlist1) != 0) {
        $arraykeys1=array_keys((array)$reportlist1[0]);
    }

    //問答題
    $sql2="	 SELECT  t91tb.note
               FROM  t91tb
              WHERE class= '".$class."'
                AND term= '".$term."'
                AND times= IFNULL('".$times."',times)
                AND note <> '' AND note IS NOT NULL
              ORDER BY serno
            ";
    $reportlist2 = DB::select($sql2);
    $dataArr2=json_decode(json_encode(DB::select($sql2)), true);
    //取出全部項目
    if(sizeof($reportlist2) != 0) {
    $arraykeys2=array_keys((array)$reportlist2[0]);
    }


    //基本資料統計
    $sql3="SELECT   CONCAT('男（',IFNULL(SUM(sex_1),'0'),'及', IFNULL(ROUND(SUM(sex_1/(sex_1+sex_2) * 100),2),'0') ,'%）'),
                    CONCAT('女（',IFNULL(SUM(sex_2),'0'),'及', IFNULL(ROUND(SUM(sex_2/(sex_1+sex_2) * 100),2),'0') ,'%）'),
                    CONCAT('24歲以下（',IFNULL(SUM(age_1),'0'),'及', IFNULL(ROUND(SUM(age_1/(age_1+age_2+age_3+age_4+age_5) * 100),2),'0') ,'%）'),
                    CONCAT('25～29歲（',IFNULL(SUM(age_2),'0'),'及', IFNULL(ROUND(SUM(age_2/(age_1+age_2+age_3+age_4+age_5) * 100),2),'0') ,'%）'),
                    CONCAT('30～39歲（',IFNULL(SUM(age_3),'0'),'及', IFNULL(ROUND(SUM(age_3/(age_1+age_2+age_3+age_4+age_5) * 100),2),'0') ,'%）'),
                    CONCAT('40～49歲（',IFNULL(SUM(age_4),'0'),'及', IFNULL(ROUND(SUM(age_4/(age_1+age_2+age_3+age_4+age_5) * 100),2),'0') ,'%）'),
                    CONCAT('50歲以上（',IFNULL(SUM(age_5),'0'),'及', IFNULL(ROUND(SUM(age_5/(age_1+age_2+age_3+age_4+age_5) * 100),2),'0') ,'%）'),
                    CONCAT('高中以下（',IFNULL(SUM(ecode_1),'0'),'及', IFNULL(ROUND(SUM(ecode_1/(ecode_1+ecode_2+ecode_3+ecode_4+ecode_5) * 100),2),'0') ,'%）'),
                    CONCAT('專科（',IFNULL(SUM(ecode_2),'0'),'及', IFNULL(ROUND(SUM(ecode_2/(ecode_1+ecode_2+ecode_3+ecode_4+ecode_5) * 100),2),'0') ,'%）'),
                    CONCAT('大學（',IFNULL(SUM(ecode_3),'0'),'及', IFNULL(ROUND(SUM(ecode_3/(ecode_1+ecode_2+ecode_3+ecode_4+ecode_5) * 100),2),'0') ,'%）'),
                    CONCAT('碩士（',IFNULL(SUM(ecode_4),'0'),'及', IFNULL(ROUND(SUM(ecode_4/(ecode_1+ecode_2+ecode_3+ecode_4+ecode_5) * 100),2),'0') ,'%）'),
                    CONCAT('博士（',IFNULL(SUM(ecode_5),'0'),'及', IFNULL(ROUND(SUM(ecode_5/(ecode_1+ecode_2+ecode_3+ecode_4+ecode_5) * 100),2),'0') ,'%）'),
                    CONCAT('行政機關（',IFNULL(SUM(dept_1),'0'),'及', IFNULL(ROUND(SUM(dept_1/(dept_1+dept_2+dept_3+dept_4) * 100),2),'0') ,'%）'),
                    CONCAT('事業機構（',IFNULL(SUM(dept_2),'0'),'及', IFNULL(ROUND(SUM(dept_2/(dept_1+dept_2+dept_3+dept_4) * 100),2),'0') ,'%）'),
                    CONCAT('學校（',IFNULL(SUM(dept_3),'0'),'及', IFNULL(ROUND(SUM(dept_3/(dept_1+dept_2+dept_3+dept_4) * 100),2),'0') ,'%）'),
                    CONCAT('其他（',IFNULL(SUM(dept_4),'0'),'及', IFNULL(ROUND(SUM(dept_4/(dept_1+dept_2+dept_3+dept_4) * 100),2),'0') ,'%）'),
                    CONCAT('簡任（',IFNULL(SUM(rank_1),'0'),'及', IFNULL(ROUND(SUM(rank_1/(rank_1+rank_2+rank_3+rank_4) * 100),2),'0') ,'%）'),
                    CONCAT('薦任（',IFNULL(SUM(rank_2),'0'),'及', IFNULL(ROUND(SUM(rank_2/(rank_1+rank_2+rank_3+rank_4) * 100),2),'0') ,'%）'),
                    CONCAT('委任（',IFNULL(SUM(rank_3),'0'),'及', IFNULL(ROUND(SUM(rank_3/(rank_1+rank_2+rank_3+rank_4) * 100),2),'0') ,'%）'),
                    CONCAT('其他（',IFNULL(SUM(rank_4),'0'),'及', IFNULL(ROUND(SUM(rank_4/(rank_1+rank_2+rank_3+rank_4) * 100),2),'0') ,'%）'),
                    CONCAT('主管（',IFNULL(SUM(duty_1),'0'),'及', IFNULL(ROUND(SUM(duty_1/(duty_1+duty_2) * 100),2),'0') ,'%）'),
                    CONCAT('非主管（',IFNULL(SUM(duty_2),'0'),'及', IFNULL(ROUND(SUM(duty_2/(duty_1+duty_2) * 100),2),'0') ,'%）'),
                    CONCAT('1年未滿（',IFNULL(SUM(dutytime_1),'0'),'及', IFNULL(ROUND(SUM(dutytime_1/(dutytime_1+dutytime_2+dutytime_3+dutytime_4) * 100),2),'0') ,'%）'),
                    CONCAT('1年以上～5年未滿（',IFNULL(SUM(dutytime_2),'0'),'及', IFNULL(ROUND(SUM(dutytime_2/(dutytime_1+dutytime_2+dutytime_3+dutytime_4) * 100),2),'0') ,'%）'),
                    CONCAT('5年以上～10年未滿（',IFNULL(SUM(dutytime_3),'0'),'及', IFNULL(ROUND(SUM(dutytime_3/(dutytime_1+dutytime_2+dutytime_3+dutytime_4) * 100),2),'0') ,'%）'),
                    CONCAT('10年以上（',IFNULL(SUM(dutytime_4),'0'),'及', IFNULL(ROUND(SUM(dutytime_4/(dutytime_1+dutytime_2+dutytime_3+dutytime_4) * 100),2),'0') ,'%）'),
                    CONCAT('5年未滿（',IFNULL(SUM(officertime_1),'0'),'及', IFNULL(ROUND(SUM(officertime_1/(officertime_1+officertime_2+officertime_3+officertime_4+officertime_5) * 100),2),'0') ,'%）'),
                    CONCAT('5年以上～10年未滿（',IFNULL(SUM(officertime_2),'0'),'及', IFNULL(ROUND(SUM(officertime_2/(officertime_1+officertime_2+officertime_3+officertime_4+officertime_5) * 100),2),'0') ,'%）'),
                    CONCAT('10年以上～15年未滿（',IFNULL(SUM(officertime_3),'0'),'及', IFNULL(ROUND(SUM(officertime_3/(officertime_1+officertime_2+officertime_3+officertime_4+officertime_5) * 100),2),'0') ,'%）'),
                    CONCAT('15年以上（',IFNULL(SUM(officertime_4),'0'),'及', IFNULL(ROUND(SUM(officertime_4/(officertime_1+officertime_2+officertime_3+officertime_4+officertime_5) * 100),2),'0') ,'%）'),
                    CONCAT('其他（',IFNULL(SUM(officertime_5),'0'),'及', IFNULL(ROUND(SUM(officertime_5/(officertime_1+officertime_2+officertime_3+officertime_4+officertime_5) * 100),2),'0') ,'%）')

            FROM (
                    SELECT  1 AS key1,
                            COUNT(CASE WHEN sex='1' THEN 1 ELSE NULL END) AS sex_1,
                            COUNT(CASE WHEN sex='2' THEN 1 ELSE NULL END) AS sex_2,
                            COUNT(CASE WHEN age='1' THEN 1 ELSE NULL END) AS age_1,
                            COUNT(CASE WHEN age='2' THEN 1 ELSE NULL END) AS age_2,
                            COUNT(CASE WHEN age='3' THEN 1 ELSE NULL END) AS age_3,
                            COUNT(CASE WHEN age='4' THEN 1 ELSE NULL END) AS age_4,
                            COUNT(CASE WHEN age='5' THEN 1 ELSE NULL END) AS age_5,
                            COUNT(CASE WHEN ecode='1' THEN 1 ELSE NULL END) AS ecode_1,
                            COUNT(CASE WHEN ecode='2' THEN 1 ELSE NULL END) AS ecode_2,
                            COUNT(CASE WHEN ecode='3' THEN 1 ELSE NULL END) AS ecode_3,
                            COUNT(CASE WHEN ecode='4' THEN 1 ELSE NULL END) AS ecode_4,
                            COUNT(CASE WHEN ecode='5' THEN 1 ELSE NULL END) AS ecode_5,
                            COUNT(CASE WHEN dept='1' THEN 1 ELSE NULL END) AS dept_1,
                            COUNT(CASE WHEN dept='2' THEN 1 ELSE NULL END) AS dept_2,
                            COUNT(CASE WHEN dept='3' THEN 1 ELSE NULL END) AS dept_3,
                            COUNT(CASE WHEN dept='4' THEN 1 ELSE NULL END) AS dept_4,
                            COUNT(CASE WHEN rank='1' THEN 1 ELSE NULL END) AS rank_1,
                            COUNT(CASE WHEN rank='2' THEN 1 ELSE NULL END) AS rank_2,
                            COUNT(CASE WHEN rank='3' THEN 1 ELSE NULL END) AS rank_3,
                            COUNT(CASE WHEN rank='4' THEN 1 ELSE NULL END) AS rank_4,
                            COUNT(CASE WHEN duty='1' THEN 1 ELSE NULL END) AS duty_1,
                            COUNT(CASE WHEN duty='2' THEN 1 ELSE NULL END) AS duty_2,
                            COUNT(CASE WHEN dutytime='1' THEN 1 ELSE NULL END) AS dutytime_1,
                            COUNT(CASE WHEN dutytime='2' THEN 1 ELSE NULL END) AS dutytime_2,
                            COUNT(CASE WHEN dutytime='3' THEN 1 ELSE NULL END) AS dutytime_3,
                            COUNT(CASE WHEN dutytime='4' THEN 1 ELSE NULL END) AS dutytime_4,
                            COUNT(CASE WHEN officertime='1' THEN 1 ELSE NULL END) AS officertime_1,
                            COUNT(CASE WHEN officertime='2' THEN 1 ELSE NULL END) AS officertime_2,
                            COUNT(CASE WHEN officertime='3' THEN 1 ELSE NULL END) AS officertime_3,
                            COUNT(CASE WHEN officertime='4' THEN 1 ELSE NULL END) AS officertime_4,
                            COUNT(CASE WHEN officertime='5' THEN 1 ELSE NULL END) AS officertime_5
                    FROM t91tb
                    WHERE class = '".$class."'
                    AND term = '".$term."'
                    AND times= IFNULL('".$times."',times)
                    GROUP BY class,term,times
                    UNION ALL
                    SELECT 1 AS key1,
                            0 AS sex_1,0 AS sex_2,0 AS age_1,0 AS age_2,0 AS age_3,0 AS age_4,0 AS age_5,
                            0 AS ecode_1,0 AS ecode_2,0 AS ecode_3, 0 AS ecode_4, 0 AS ecode_5,
                            0 AS dept_1,0 AS dept_2,
                                0 AS dept_3,
                            0 AS dept_4,
                            0 AS rank_1,
                            0 AS rank_2,
                            0 AS rank_3,
                            0 AS rank_4,
                            0 AS duty_1,
                            0 AS duty_2,
                            0 AS dutytime_1,
                            0 AS dutytime_2,
                            0 AS dutytime_3,
                            0 AS dutytime_4,
                            0 AS officertime_1,
                            0 AS officertime_2,
                            0 AS officertime_3,
                            0 AS officertime_4,
                            0 AS officertime_5
                    FROM DUAL
                ) T
                GROUP BY T.key1
        ";
    $reportlist3 = DB::select($sql3);
    $dataArr3=json_decode(json_encode(DB::select($sql3)), true);
    //取出全部項目
    if(sizeof($reportlist3) != 0) {
    $arraykeys3=array_keys((array)$reportlist3[0]);
    }


    // 檔案名稱
    $fileName = 'L9';
    //範本位置
    $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
    //讀取excel

    //$objPHPExcel = PHPExcel_IOFactory::load($filePath);
    $excelReader = PHPExcel_IOFactory::createReaderForFile($filePath);
    $excelReader->setReadDataOnly(false);
    $excelReader->setIncludeCharts(true);
    $objPHPExcel = $excelReader->load($filePath);

    //固定題目統計
    //指定sheet
    $objActSheet = $objPHPExcel->getActiveSheet();
    $objActSheet = $objPHPExcel->getSheet(0);
    $reportlist = json_decode(json_encode($reportlist), true);
    //dd($reportlist);
    if(empty($dataArrTitle)){
        $objActSheet->setCellValue('A1', '訓練成效評估結果統計表');
    }else{
        $objActSheet->setCellValue('A1', $dataArrTitle[0]['TITLE']);
    }

    if(empty($dataArrRemark1)){
        $objActSheet->setCellValue('A15', '1.本次調查問卷計發出0份，共回收0份，回收率0.00%。');
    }else{
        //針對文字設定粗體與底線
        //$objActSheet->setCellValue('A14', $dataArrRemark1[0]['REMARK1A'].$objRichText);
        $objRichText = new PHPExcel_RichText();
        $objRichText->createText($dataArrRemark1[0]['REMARK1A']);
        $objFont = $objRichText->createTextRun($dataArrRemark1[0]['REMARK1B']);
        $objFont->getFont()->setBold(true);
        $objFont->getFont()->setUnderline(true);
        $objFont->getFont()->setName("標楷體");
        $objFont->getFont()->setSize("12");
        $objActSheet->getCell('A12')->setValue($objRichText);
    }

    //$objActSheet->setCellValue('A16', str_replace('%AVG%',$dataArrAVG[0]['AVG'],$dataArrRemark2[0]['REMARK2A']));
    //$objActSheet->setCellValue('A17', $dataArrRemark2[0]['REMARK2B']);
    if(empty($dataArrRemark2)){
        $objActSheet->setCellValue('A13', '2.本次研習學員滿意度總平均為0.00分，其中「研習規劃」滿意度平均為0.00、「對公務執行有助益」者0.00、「學習輔導」滿意度平均為0.00');
        $objActSheet->setCellValue('A14', '、「講座授課」滿意度平均為0.00，「講座授課」統計結果詳附表。');
    }else{
        $objRichText2 = new PHPExcel_RichText();
        $objRichText2->createText($dataArrRemark2[0]['REMARK2A']);
        $objFont = $objRichText2->createTextRun($dataArrRemark2[0]['REMARK2B']);
        $objFont->getFont()->setBold(false);
        $objFont->getFont()->setUnderline(false);
        $objFont->getFont()->setName("標楷體");
        $objFont->getFont()->setSize("12");
        $objActSheet->getCell('A13')->setValue($objRichText2);
        $objActSheet->setCellValue('A14', $dataArrRemark2[0]['REMARK2C']);
    }

    $lineName = 'C';
    if(sizeof($reportlist) != 0) {
        //項目數量迴圈
        for ($i=0; $i < sizeof($arraykeys); $i++) {
        //excel 欄位 1 == A, etc
            if($i==0){
                $lineName = 'C';
            } elseif($i==1){
                $lineName = 'E';
            } elseif($i==2){
                $lineName = 'G';
            } elseif($i==3){
                $lineName = 'I';
            } elseif($i==4){
                $lineName = 'K';
            } elseif($i==5){
                $lineName = 'M';
            }else {
                //$NameFromNumber=$this->getNameFromNumber($i+2); //B
                $lineName = 'B';
            }
            //資料by班別迴圈
            for ($j=0; $j < sizeof($reportlist); $j++) {
                //4開始
                $objActSheet->setCellValue($lineName.($j+4), $reportlist[$j][$arraykeys[$i]]);
            }
        }
    }


    //固定題目統計圖表
    //指定sheet
    $objActSheet = $objPHPExcel->getSheet(1);
    if(empty($dataArrTitle)){

    }else{
        $title = new PHPExcel_Chart_Title(str_replace('訓練成效評估結果統計表','訓練成效評估結果統計圖',$dataArrTitle[0]['TITLE']));
    }
    $X_title = new PHPExcel_Chart_Title('各題次問項內容');
    $Y_title = new PHPExcel_Chart_Title('百分位數');
    $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', '固定題目統計!$B$3', NULL, 1));
    $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', '固定題目統計!$B$4:$B$10', NULL, 100));
    $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', '固定題目統計!$O$4:$O$10', NULL, 100));
    //顯示數值
    $layout=new PHPExcel_Chart_Layout();
    $layout->setShowVal(true);
    //設定最大值,這是1.81版才有的功能，若是1.80則無此功能
    $axis=new PHPExcel_Chart_Axis();
    $axis->setAxisOptionsProperties("nextTo", null, null, null, null, null,0, 1);
    //長條圖
    $ds = new \PHPExcel_Chart_DataSeries(\PHPExcel_Chart_DataSeries::TYPE_BARCHART, \PHPExcel_Chart_DataSeries::GROUPING_STANDARD, range(0, count($dsv) - 1), $dsl, $xal, $dsv);
    $pa = new \PHPExcel_Chart_PlotArea($layout, array($ds));
    $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, $layout, false);
    $chart1 = new PHPExcel_Chart('Chart1', $title, $legend, $pa, true,0,$X_title,$Y_title, $axis);
    $chart1->setTopLeftPosition('A1');
    $chart1->setBottomRightPosition('Q33');
    $objActSheet->addChart($chart1);


    //講座統計
    //指定sheet
    $objActSheet = $objPHPExcel->getSheet(2);
    $reportlist1Name = json_decode(json_encode($reportlist1Name), true);
    $reportlist1 = json_decode(json_encode($reportlist1), true);
    //dd($reportlist2);
    if(empty($dataArrTitle)){
        $objActSheet->setCellValue('A1', '訓練成效評估結果統計表（講座滿意度）');
    }else{
        $objActSheet->setCellValue('A1', $dataArrTitle[0]['TITLE']);
    }

    if(sizeof($reportlist1Name) != 0) {
        $k=0;
        $l=0;
        for ($j=0; $j < sizeof($reportlist1Name); $j++) {
            for ($i=0; $i < sizeof($arraykeys1Name); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                $objActSheet->setCellValue($NameFromNumber.($j+4), $reportlist1Name[$j][$arraykeys1Name[$i]]);
            }
            // 帶入公式 =E4/SUM(E4:E8)*100
            $objActSheet->setCellValue('F'.($j+4), '=E'.($j+4).'/SUM(E'.(($k*5)+4).':E'.(($k*5)+8).')*100');
            if((($j+1)%5)==0){
                $k++;
            }
            if((($j+1)%10)==0){
                //合併, 帶入公式
                $objActSheet->mergeCells('A'.(($l*10)+4).':A'.(($l*10)+13));
                $objActSheet->mergeCells('B'.(($l*10)+4).':B'.(($l*10)+13));
                $objActSheet->mergeCells('C'.(($l*10)+4).':C'.(($l*10)+8));
                $objActSheet->mergeCells('C'.(($l*10)+4+5).':C'.(($l*10)+8+5));
                $objActSheet->mergeCells('G'.(($l*10)+4).':G'.(($l*10)+8));
                //帶入公式=SUM(E4:E8)
                $objActSheet->setCellValue('G'.(($l*10)+4), '=SUM(E'.(($l*10)+4).':E'.(($l*10)+8).')');
                $objActSheet->mergeCells('G'.(($l*10)+4+5).':G'.(($l*10)+8+5));
                $objActSheet->setCellValue('G'.(($l*10)+4+5), '=SUM(E'.(($l*10)+4+5).':E'.(($l*10)+8+5).')');

                //特別注意此功能大部份同CSDIR5023(102年度)僅在範本N10.xlsx滿意度算法不同, 與在下列講座滿意度算法不同
                //此有包含等距的權重
                //帶入公式=SUM(E4*100+E5*80+E6*60+E7*40+E8*20)/(SUM(E4:E8)*100)*100
                      //帶入公式=F4+F5
                $objActSheet->mergeCells('H'.(($l*10)+4).':H'.(($l*10)+8));
                //$objActSheet->setCellValue('H'.(($l*10)+4), '=F'.(($l*10)+4).'+F'.(($l*10)+5));
                $objActSheet->mergeCells('H'.(($l*10)+4+5).':H'.(($l*10)+8+5));
                //$objActSheet->setCellValue('H'.(($l*10)+4+5), '=F'.(($l*10)+4+5).'+F'.(($l*10)+5+5));
                $objActSheet->setCellValue('H'.(($l*10)+4), '=SUM(E'.(($l*10)+4).'*100+E'.(($l*10)+5).'*80+E'.(($l*10)+6).'*60+E'.(($l*10)+7).'*40+E'.(($l*10)+8).'*20)/(SUM(E'.(($l*10)+4).':E'.(($l*10)+8).')*100)*100');
                $objActSheet->setCellValue('H'.(($l*10)+4+5), '=SUM(E'.(($l*10)+4+5).'*100+E'.(($l*10)+5+5).'*80+E'.(($l*10)+6+5).'*60+E'.(($l*10)+7+5).'*40+E'.(($l*10)+8+5).'*20)/(SUM(E'.(($l*10)+4+5).':E'.(($l*10)+8+5).')*100)*100');

                $objActSheet->mergeCells('I'.(($l*10)+4).':I'.(($l*10)+13));
                //帶入公式=(H4+H9)/2
                $objActSheet->setCellValue('I'.(($l*10)+4), '=(H'.(($l*10)+4).'+H'.(($l*10)+9).')/2');
                //$objActSheet->mergeCells('J'.(($l*10)+4).':J'.(($l*10)+13));

                //圖表使用
                //帶入 講者(課程)
                $objActSheet->setCellValue('J'.($l+4), $reportlist1Name[(($l*10)+4)][$arraykeys1Name[0]].'('.$reportlist1Name[(($l*10)+4)][$arraykeys1Name[1]].')');
                //帶入公式=I4
                $objActSheet->setCellValue('K'.($l+4), '=I'.(($l*10)+4));

                $l++;
            }
        }
        //框線
        $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
        );
        $objActSheet->getStyle('A4:I'.($j+3))->applyFromArray($styleArray);

        //=AVERAGE(I4:I43)
        if($l>0){
            $objActSheet->setCellValue('I2', '=AVERAGE(I4:I'.((($l-1)*10)+13).')');
        } else {
            $objActSheet->setCellValue('I2', '=AVERAGE(I4:I13)');
        }

    }


    //人數
    if(sizeof($reportlist1) != 0) {
        for ($j=0; $j < sizeof($reportlist1); $j++) {
            for ($i=0; $i < sizeof($arraykeys1); $i++) {
                $objActSheet->setCellValue('E'.($j*10+4+$i), $reportlist1[$j][$arraykeys1[$i]]);
            }
        }
    }


    //講座統計圖表
    //指定sheet
    $objActSheet = $objPHPExcel->getSheet(3);
    if(empty($dataArrTitle)){

    }else{
        $title = new PHPExcel_Chart_Title(str_replace('訓練成效評估結果統計表','訓練成效評估結果統計圖（講座滿意度）',$dataArrTitle[0]['TITLE']));
    }
    $X_title = new PHPExcel_Chart_Title('各題次問項內容');
    $Y_title = new PHPExcel_Chart_Title('百分位數');
    $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', '講座統計!$B$3', NULL, 1));
    $dsvnum = 0;
    if(sizeof($reportlist1Name)>0){
        $dsvnum = ((sizeof($reportlist1Name) / 10) - 1) ;
    }
    $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', '講座統計!$J$4:$J$'.($dsvnum+4), NULL, 100));
    $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', '講座統計!$K$4:$K$'.($dsvnum+4), NULL, 100));
    //顯示數值
    $layout=new PHPExcel_Chart_Layout();
    $layout->setShowVal(true);
    $layout->setShowPercent(TRUE);  // Initializing the data labels with Percentages
    //設定最大值,這是1.81版才有的功能，若是1.80則無此功能
    $axis=new PHPExcel_Chart_Axis();
    $axis->setAxisOptionsProperties("nextTo", null, null, null, null, null,0, 100);
    //長條圖
    $ds = new \PHPExcel_Chart_DataSeries(\PHPExcel_Chart_DataSeries::TYPE_BARCHART, \PHPExcel_Chart_DataSeries::GROUPING_STANDARD, range(0, count($dsv) - 1), $dsl, $xal, $dsv);
    $pa = new \PHPExcel_Chart_PlotArea($layout, array($ds));
    $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, $layout, false);
    $chart2 = new PHPExcel_Chart('Chart2', $title, $legend, $pa, true,0,$X_title,$Y_title, $axis);
    $chart2->setTopLeftPosition('A1');
    $chart2->setBottomRightPosition('S33');
    $objActSheet->addChart($chart2);


    //問答題
    //指定sheet
    $objActSheet = $objPHPExcel->getSheet(4);
    $reportlist2 = json_decode(json_encode($reportlist2), true);
    //dd($reportlist2);
    if(empty($dataArrTitle)){
        $objActSheet->setCellValue('A1', '訓練成效評估結果統計表');
    }else{
        $objActSheet->setCellValue('A1', $dataArrTitle[0]['TITLE']);
    }
    if(sizeof($reportlist2) != 0) {
        //項目數量迴圈
        for ($i=0; $i < sizeof($arraykeys2); $i++) {
            //excel 欄位 1 == A, etc
            $NameFromNumber=$this->getNameFromNumber($i+1); //A
            //資料by班別迴圈
            for ($j=0; $j < sizeof($reportlist2); $j++) {
                //4開始
                $objActSheet->setCellValue($NameFromNumber.($j+4), ($j+1).'.'.$reportlist2[$j][$arraykeys2[$i]]);
            }
        }
    }


    //基本資料統計
    //指定sheet
    $objActSheet = $objPHPExcel->getSheet(5);
    //dd($reportlist3);
    $reportlist3 = json_decode(json_encode($reportlist3), true);
    //dd($reportlist3);
    if(sizeof($reportlist3) != 0) {
        //資料by班別迴圈
        for ($j=0; $j < sizeof($reportlist3); $j++) {
        //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys3); $i++) {
                //3開始
                if($i>=26){
                    $objActSheet->setCellValue('B'.($i+3+7), $reportlist3[$j][$arraykeys3[$i]]);
                }elseif($i>=22){
                    $objActSheet->setCellValue('B'.($i+3+6), $reportlist3[$j][$arraykeys3[$i]]);
                }elseif($i>=20){
                    $objActSheet->setCellValue('B'.($i+3+5), $reportlist3[$j][$arraykeys3[$i]]);
                }elseif($i>=16){
                    $objActSheet->setCellValue('B'.($i+3+4), $reportlist3[$j][$arraykeys3[$i]]);
                }elseif($i>=12){
                    $objActSheet->setCellValue('B'.($i+3+3), $reportlist3[$j][$arraykeys3[$i]]);
                }elseif($i>=7){
                    $objActSheet->setCellValue('B'.($i+3+2), $reportlist3[$j][$arraykeys3[$i]]);
                }elseif($i>=2){
                    $objActSheet->setCellValue('B'.($i+3+1), $reportlist3[$j][$arraykeys3[$i]]);
                }else{
                    $objActSheet->setCellValue('B'.($i+3), $reportlist3[$j][$arraykeys3[$i]]);
                }
            }
        }
    }

    $objActSheet = $objPHPExcel->getSheet(0);

    $RptBasic = new \App\Rptlib\RptBasic();
    $RptBasic->exportfile($objPHPExcel,"3",$request->input('doctype'),"訓練成效評估結果統計圖表(104)");
    //$obj: entity of file
    //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
    //$doctype:1.ooxml 2.odf
    //$filename:filename 

    }
}
