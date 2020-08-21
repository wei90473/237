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

class TrainingResult9395Controller extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('training_result_93_95', $user_group_auth)){
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
        //return view('admin/training_result_93_95/list');
        $classArr = $this->getclass();
        $result = '';
        return view('admin/training_result_93_95/list', compact('classArr', 'result'));
    }

    // 搜尋下拉『班別』
    public function getclass() {
            $sql = "SELECT DISTINCT t53tb.class, t01tb.name
                      FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                     WHERE t53tb.times<>'' AND SUBSTRING(t53tb.class,1,3) BETWEEN '093' AND '095'
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
    訓練成效評估結果統計圖表(93) CSDIR5021
    參考Tables:
    使用範本:L16.xlsx
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
        //取得 固定題目統計
        $sql="SELECT
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=5 THEN 1 ELSE NULL  END))
                        ELSE 0
                        END),
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=4 THEN 1 ELSE NULL  END))
                        ELSE 0
                        END),
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=3 THEN 1 ELSE NULL  END))
                        ELSE 0
                        END),
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=2 THEN 1 ELSE NULL  END))
                        ELSE 0
                        END),
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=1 THEN 1 ELSE NULL  END))
                        ELSE 0
                        END),
                        (CASE
                        WHEN A.key1='q11' THEN SUM((CASE WHEN q11 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                        WHEN A.key1='q12' THEN SUM((CASE WHEN q12 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                        WHEN A.key1='q13' THEN SUM((CASE WHEN q13 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                        WHEN A.key1='q21' THEN SUM((CASE WHEN q21 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                        WHEN A.key1='q22' THEN SUM((CASE WHEN q22 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                        WHEN A.key1='q41' THEN SUM((CASE WHEN q41 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                        WHEN A.key1='q42' THEN SUM((CASE WHEN q42 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                        WHEN A.key1='q43' THEN SUM((CASE WHEN q43 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                        ELSE 0
                        END)
                        FROM
                        (
                        SELECT  'q11' AS key1, 'Q11.我認為本次研習所訂目標符合開辦本次研習之需求' AS caption
                        UNION ALL
                        SELECT  'q12' AS key1, 'Q12.我認為本次研習內容的課程安排符合研習所訂目標' AS caption
                        UNION ALL
                        SELECT  'q13' AS key1, 'Q13.我認為本次研習內容與政府機關業務具有相關性，或有助於個人公務之處理' AS caption
                        UNION ALL
                        SELECT  'q21' AS key1, 'Q21.我對本班輔導人員的學習輔導與其他相關服務感到滿意' AS caption
                        UNION ALL
                        SELECT  'q22' AS key1, 'Q22.本次研習的教材及相關資料很適當' AS caption
                        UNION ALL
                        SELECT  'q41' AS key1, 'Q41.教學環境與設備' AS caption
                        UNION ALL
                        SELECT  'q42' AS key1, 'Q42.餐飲' AS caption
                        UNION ALL
                        SELECT  'q43' AS key1, 'Q43.住宿' AS caption
                        ) A
                        INNER JOIN t72tb t72tb ON 1 = 1
                    WHERE t72tb.class= '".$class."'
                    AND t72tb.term= '".$term."'
                    AND t72tb.times= IFNULL('".$times."',t72tb.times)
                    GROUP BY A.caption, A.key1
                    ORDER BY A.caption
        ";

        $reportlist = DB::select($sql);
        $dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        //取得 TITLE
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
                    WHERE t53tb.times<>'' AND SUBSTRING(t53tb.class,1,3) between '093' AND '095'
                    AND t53tb.class = '".$class."'
                    AND t53tb.term= '".$term."'
                    AND t53tb.times= IFNULL('".$times."',t53tb.times)
                    ORDER BY t53tb.class DESC

        ";
        $reportlistTitle = DB::select($sqlTitle);
        $dataArrTitle=json_decode(json_encode(DB::select($sqlTitle)), true);

        //取得 訓期, Begin Date & End Date
        $sqlDate="SELECT sdate,edate ,
                            CONCAT('訓期：',
                                                    SUBSTRING(sdate,1,3),'/', SUBSTRING(sdate,4,2), '/', SUBSTRING(sdate,6,2),'~',
                                                    SUBSTRING(edate,1,3),'/', SUBSTRING(edate,4,2), '/', SUBSTRING(edate,6,2)
                                            ) AS sdate_edate
                    FROM t04tb
                    WHERE class='".$class."'
                    AND term= '".$term."'
        ";
        $reportlistDate = DB::select($sqlDate);
        $dataArrDate=json_decode(json_encode(DB::select($sqlDate)), true);

        //取得 A14:REMARK 1
        /*
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
                                FROM t72tb
                               WHERE class='".$class."'
                                AND term= '".$term."'
                                AND times= IFNULL('".$times."',times)
                                AND times IN (SELECT IFNULL(MAX(times),0)
                                                FROM t72tb
                                               WHERE class= '".$class."'
                                                AND term= '".$term."'
                                              )
                            ) B ON 1 = 1
            ";
        */
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
                                FROM t72tb
                            WHERE class='".$class."'
                                AND term= '".$term."'
                                AND times= IFNULL('".$times."',times)
                            ) B ON 1 = 1
                            ";
        $reportlistRemark1 = DB::select($sqlRemark1);
        $dataArrRemark1=json_decode(json_encode(DB::select($sqlRemark1)), true);

        //REMARK2:「講座授課」面向之滿意度（平均）
        $sqlRemarkAVG="SELECT ROUND(SUM((T.avg_ans1+ T.avg_ans2 + T.avg_ans3)/3) / COUNT(*),2) AS AVG
						    FROM (
                                 SELECT  CONCAT(RTRIM(IFNULL(D.cname,A.idno)),'(', RTRIM(IFNULL(C.name,A.course)), ')') AS class_name,
                                         IFNULL(AVG(CASE WHEN B.ans1=0 THEN NULL ELSE B.ans1*20.00 END),0) AS avg_ans1,
                                         IFNULL(AVG(CASE WHEN B.ans2=0 THEN NULL ELSE B.ans2*20.00 END),0) AS avg_ans2,
                                         IFNULL(AVG(CASE WHEN B.ans3=0 THEN NULL ELSE B.ans3*20.00 END),0) AS avg_ans3
                                  FROM t54tb A LEFT JOIN t56tb B ON A.class=B.class AND A.term=B.term AND A.times=B.times
                                                                 AND A.course=B.course AND A.idno=B.idno
                                               LEFT JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                               LEFT JOIN m01tb D ON A.idno=D.idno
                                               LEFT JOIN t09tb ON A.class = t09tb.class AND A.term = t09tb.term AND A.course = t09tb.course
                                 WHERE A.idno<>''
                                   AND A.class= '".$class."'
                                   AND A.term= '".$term."'
                                   AND A.times= IFNULL('".$times."',A.times)
								   AND t09tb.okrate IS NOT NULL
                                 GROUP BY A.sequence, A.class, A.term, A.course, C.name, A.idno, D.cname
                                    ) T
        ";
        $reportlistAVG= DB::select($sqlRemarkAVG);
        $dataArrAVG=json_decode(json_encode(DB::select($sqlRemarkAVG)), true);

        //A15~A16:REMARK2
        $sqlRemark2="SELECT CONCAT('另「講座授課」面向之滿意度（平均為%AVG%）統計結果詳次表；「整體評價」之平均為', worper,'。') AS REMARK2A,
                            CONCAT(' 本次研習學員在5個面向之滿意度總平均為', totper,'分。') AS REMARK2B
                        FROM t57tb
                        WHERE class='".$class."'
                        AND term= '".$term."'
                        AND times= IFNULL('".$times."',times)
        ";
        $reportlistRemark2 = DB::select($sqlRemark2);
        $dataArrRemark2=json_decode(json_encode(DB::select($sqlRemark2)), true);

        //REMARK3:兩個標準差範圍
        $sqlRemark2STDDEV="SELECT ROUND(AVG((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3)),2) +
                                    (ROUND(STDDEV_SAMP((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3)),2) * 2) AS sngMax,
                                    ROUND(AVG((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3)),2) -
                                    (ROUND(STDDEV_SAMP((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3)),2) * 2) AS sngMin
                            FROM (
                                    SELECT
                                    (CASE
                                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=5 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=5 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=5 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=5 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=5 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=5 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=5 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=5 THEN 1 ELSE NULL  END))
                                    ELSE 0
                                    END) AS D3,
                                    (CASE
                                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=4 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=4 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=4 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=4 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=4 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=4 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=4 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=4 THEN 1 ELSE NULL  END))
                                    ELSE 0
                                    END) AS F3,
                                    (CASE
                                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=3 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=3 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=3 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=3 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=3 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=3 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=3 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=3 THEN 1 ELSE NULL  END))
                                    ELSE 0
                                    END) AS H3,
                                    (CASE
                                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=2 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=2 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=2 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=2 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=2 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=2 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=2 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=2 THEN 1 ELSE NULL  END))
                                    ELSE 0
                                    END) AS J3,
                                    (CASE
                                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=1 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=1 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=1 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=1 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=1 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=1 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=1 THEN 1 ELSE NULL  END))
                                    WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=1 THEN 1 ELSE NULL  END))
                                    ELSE 0
                                    END) AS L3
                                    FROM
                                    (
                                    SELECT  'q11' AS key1 FROM DUAL
                                    UNION ALL
                                    SELECT  'q12' AS key1 FROM DUAL
                                    UNION ALL
                                    SELECT  'q13' AS key1 FROM DUAL
                                    UNION ALL
                                    SELECT  'q21' AS key1 FROM DUAL
                                    UNION ALL
                                    SELECT  'q22' AS key1 FROM DUAL
                                    UNION ALL
                                    SELECT  'q41' AS key1 FROM DUAL
                                    UNION ALL
                                    SELECT  'q42' AS key1 FROM DUAL
                                    UNION ALL
                                    SELECT  'q43' AS key1 FROM DUAL
                                    ) A
                                    INNER JOIN t72tb t72tb ON 1 = 1
                                WHERE t72tb.class= '".$class."'
                                AND t72tb.term= '".$term."'
                                AND t72tb.times=  IFNULL('".$times."',times)
                                GROUP BY A.key1
                                    ) T

        ";
        $reportlist2STDDEV = DB::select($sqlRemark2STDDEV);
        $dataArr2STDDEV=json_decode(json_encode(DB::select($sqlRemark2STDDEV)), true);

        //A18:檢核REMARK3:N欄 平均數
        $sqlRemark3N="SELECT ROUND((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3),2) AS N
                        FROM (
                                SELECT
                                (CASE
                                WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=5 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=5 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=5 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=5 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=5 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=5 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=5 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=5 THEN 1 ELSE NULL  END))
                                ELSE 0
                                END) AS D3,
                                (CASE
                                WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=4 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=4 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=4 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=4 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=4 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=4 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=4 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=4 THEN 1 ELSE NULL  END))
                                ELSE 0
                                END) AS F3,
                                (CASE
                                WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=3 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=3 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=3 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=3 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=3 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=3 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=3 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=3 THEN 1 ELSE NULL  END))
                                ELSE 0
                                END) AS H3,
                                (CASE
                                WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=2 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=2 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=2 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=2 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=2 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=2 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=2 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=2 THEN 1 ELSE NULL  END))
                                ELSE 0
                                END) AS J3,
                                (CASE
                                WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=1 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=1 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=1 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=1 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=1 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q41' THEN COUNT((CASE WHEN q41=1 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q42' THEN COUNT((CASE WHEN q42=1 THEN 1 ELSE NULL  END))
                                WHEN A.key1='q43' THEN COUNT((CASE WHEN q43=1 THEN 1 ELSE NULL  END))
                                ELSE 0
                                END) AS L3
                  FROM
                  (
                  SELECT  'q11' AS key1 FROM DUAL
                  UNION ALL
                  SELECT  'q12' AS key1 FROM DUAL
                  UNION ALL
                  SELECT  'q13' AS key1 FROM DUAL
                  UNION ALL
                  SELECT  'q21' AS key1 FROM DUAL
                  UNION ALL
                  SELECT  'q22' AS key1 FROM DUAL
                  UNION ALL
                  SELECT  'q41' AS key1 FROM DUAL
                  UNION ALL
                  SELECT  'q42' AS key1 FROM DUAL
                  UNION ALL
                  SELECT  'q43' AS key1 FROM DUAL
                  ) A
                  INNER JOIN t72tb t72tb ON 1 = 1
              WHERE t72tb.class= '".$class."'
              AND t72tb.term= '".$term."'
              AND t72tb.times= IFNULL('".$times."',times)
              GROUP BY A.key1
                  ) T
        ";
        $reportlistRemark3N = DB::select($sqlRemark3N);
        $dataArrRemark3N=json_decode(json_encode(DB::select($sqlRemark3N)), true);
        //取出全部項目
        if(sizeof($reportlistRemark3N) != 0) {
            $arraykeysRemark3N=array_keys((array)$reportlistRemark3N[0]);
        }

        //A19~A20:REMARK4
        $sqlRemark4="SELECT CONCAT('4.本次學員參與研習原因經統計包括：「因工作需要經機關指派參加」者', T.reason_1,'人（',ROUND(T.reason_1/reason*100,2), '％），',
                                    '「因工作需要自願參加」者', T.reason_2,'人（',ROUND(T.reason_2/reason*100,2), '％），') AS REMARK4A,
                            CONCAT(' 「與工作無關但自願參加」者', T.reason_3,'人（',ROUND(T.reason_3/reason*100,2), '％），',
                                    '「與工作無關經機關指派參加」者', T.reason_4,'人（',ROUND(T.reason_4/reason*100,2), '％），') AS REMARK4B
                            FROM (SELECT SUM(CASE reason WHEN '1' THEN 1 ELSE 0 END) AS reason_1,
                                    SUM(CASE reason WHEN '2' THEN 1 ELSE 0 END) AS reason_2,
                                    SUM(CASE reason WHEN '3' THEN 1 ELSE 0 END) AS reason_3,
                                    SUM(CASE reason WHEN '4' THEN 1 ELSE 0 END) AS reason_4,
                                    SUM(CASE reason WHEN '' THEN 0 ELSE 1 END) AS reason
                            FROM t72tb
                            WHERE class= '".$class."'
                            AND term='".$term."'
                            AND times= IFNULL('".$times."',times)
                            GROUP BY class,term,times
                            ) T
        ";
        $reportlistRemark4 = DB::select($sqlRemark4);
        $dataArrRemark4=json_decode(json_encode(DB::select($sqlRemark4)), true);

        //講座統計
        $sql1="SELECT   T.class_name,
                        T.avg_ans1,
                        T.avg_ans2,
                        T.avg_ans3,
                        ((T.avg_ans1+T.avg_ans2+T.avg_ans3)/3) AVG123
                FROM (
                            SELECT  CONCAT(RTRIM(IFNULL(D.cname,A.idno)),'(', RTRIM(IFNULL(C.name,A.course)), ')') AS class_name,
                                            IFNULL(AVG(CASE WHEN B.ans1=0 THEN NULL ELSE B.ans1*20.00 END),0) AS avg_ans1,
                                            IFNULL(AVG(CASE WHEN B.ans2=0 THEN NULL ELSE B.ans2*20.00 END),0) AS avg_ans2,
                                            IFNULL(AVG(CASE WHEN B.ans3=0 THEN NULL ELSE B.ans3*20.00 END),0) AS avg_ans3
                            FROM t54tb A LEFT JOIN t56tb B ON A.class=B.class AND A.term=B.term AND A.times=B.times
                                                                                            AND A.course=B.course AND A.idno=B.idno
                                                        LEFT JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                                        LEFT JOIN m01tb D ON A.idno=D.idno
                                                        LEFT JOIN t09tb ON A.class = t09tb.class AND A.term = t09tb.term AND A.course = t09tb.course
                            WHERE A.idno<>''
                                AND A.class= '".$class."'
                                AND A.term= '".$term."'
                                AND A.times= IFNULL('".$times."',A.times)
                                AND t09tb.okrate IS NOT NULL
                            GROUP BY A.sequence, A.class, A.term, A.course, C.name, A.idno, D.cname
                    ) T
        ";
        $reportlist1 = DB::select($sql1);
        $dataArr1=json_decode(json_encode(DB::select($sql1)), true);
        //取出全部項目
        if(sizeof($reportlist1) != 0) {
            $arraykeys1=array_keys((array)$reportlist1[0]);
        }

        //講座統計
        //REMARK3:兩個標準差範圍
        $sqlRemark2STDDEV1="SELECT ROUND(AVG((T.avg_ans1+T.avg_ans2+T.avg_ans3)/3) + (ROUND(STDDEV_SAMP((T.avg_ans1+T.avg_ans2+T.avg_ans3)/3),2) * 2),2) AS sngMax,
                                   ROUND(AVG((T.avg_ans1+T.avg_ans2+T.avg_ans3)/3) - (ROUND(STDDEV_SAMP((T.avg_ans1+T.avg_ans2+T.avg_ans3)/3),2) * 2),2) AS sngMin
                            FROM (SELECT CONCAT(RTRIM(IFNULL(D.cname,A.idno)),'(', RTRIM(IFNULL(C.name,A.course)), ')') AS class_name,
                                                    IFNULL(AVG(CASE WHEN B.ans1=0 THEN NULL ELSE B.ans1*20.00 END),0) AS avg_ans1,
                                                    IFNULL(AVG(CASE WHEN B.ans2=0 THEN NULL ELSE B.ans2*20.00 END),0) AS avg_ans2,
                                                    IFNULL(AVG(CASE WHEN B.ans3=0 THEN NULL ELSE B.ans3*20.00 END),0) AS avg_ans3
                                    FROM t54tb A LEFT JOIN t56tb B ON A.class=B.class AND A.term=B.term AND A.times=B.times
                                                                AND A.course=B.course AND A.idno=B.idno
                                                LEFT JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                                LEFT JOIN m01tb D ON A.idno=D.idno
                                                LEFT JOIN t09tb ON A.class = t09tb.class AND A.term = t09tb.term AND A.course = t09tb.course
                                    WHERE A.idno<>''
                                    AND A.class= '".$class."'
                                    AND A.term= '".$term."'
                                    AND A.times= IFNULL('".$times."',A.times)
                                    AND t09tb.okrate IS NOT NULL
                                  GROUP BY A.sequence, A.class, A.term, A.course, C.name, A.idno, D.cname
                                ) T
        ";
        $reportlist2STDDEV1 = DB::select($sqlRemark2STDDEV1);
        $dataArr2STDDEV1=json_decode(json_encode(DB::select($sqlRemark2STDDEV1)), true);

        //講座統計
        //檢核REMARK3:F欄 平均數
        $sqlRemark3N1="SELECT ROUND((T.avg_ans1+T.avg_ans2+T.avg_ans3)/3,2)	AS F
                        FROM (SELECT CONCAT(RTRIM(IFNULL(D.cname,A.idno)),'(', RTRIM(IFNULL(C.name,A.course)), ')') AS class_name,
                                                IFNULL(AVG(CASE WHEN B.ans1=0 THEN NULL ELSE B.ans1*20.00 END),0) AS avg_ans1,
                                                IFNULL(AVG(CASE WHEN B.ans2=0 THEN NULL ELSE B.ans2*20.00 END),0) AS avg_ans2,
                                                IFNULL(AVG(CASE WHEN B.ans3=0 THEN NULL ELSE B.ans3*20.00 END),0) AS avg_ans3
                                FROM t54tb A LEFT JOIN t56tb B ON A.class=B.class AND A.term=B.term AND A.times=B.times
                                                            AND A.course=B.course AND A.idno=B.idno
                                            LEFT JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                            LEFT JOIN m01tb D ON A.idno=D.idno
                                            LEFT JOIN t09tb ON A.class = t09tb.class AND A.term = t09tb.term AND A.course = t09tb.course
                               WHERE A.idno<>''
                                AND A.class= '".$class."'
                                AND A.term= '".$term."'
                                AND A.times= IFNULL('".$times."',A.times)
                                AND t09tb.okrate IS NOT NULL
                            GROUP BY A.sequence, A.class, A.term, A.course, C.name, A.idno, D.cname
                            ) T
        ";
        $reportlistRemark3N1 = DB::select($sqlRemark3N1);
        $dataArrRemark3N1=json_decode(json_encode(DB::select($sqlRemark3N1)), true);
        //取出全部項目
        if(sizeof($reportlistRemark3N1) != 0) {
            $arraykeysRemark3N1=array_keys((array)$reportlistRemark3N1[0]);
        }


        //問答題, 測試的資料有處理過故先照改寫
        /*
        strSQL = strSQL & " q1note,  "      '研習規劃補充說明
        strSQL = strSQL & " addcourse,  " '研習規劃增加課程
        strSQL = strSQL & " delcourse,  " '研習規劃刪除課程
        strSQL = strSQL & " q2note,  " '學習輔導補充說明
        strSQL = strSQL & " q3note,  " '講座補充說明
        strSQL = strSQL & " addprof, " '推薦講座及課程
        strSQL = strSQL & " q4note,  " '行政服務補充說明
        strSQL = strSQL & " q5note  " '整體評價其他建議
        'col index
        '0  q1note 研習規劃補充說明
        '1  addcourse 研習規劃增加課程
        '2  delcourse 研習規劃刪除課程
        '3  q2note 學習輔導補充說明
        '4  q3note 講座補充說明
        '5  addprof 推薦講座及課程
        '6  q4note 行政服務補充說明
        '7  q5note 整體評價其他建議
        */
        $sql2="SELECT q1note,
                        addcourse,
                        delcourse,
                        q2note,
                        q3note,
                        addprof,
                        q4note,
                        q5note
                FROM t72tb
                WHERE (addcourse <> '' or delcourse <> '' or q2note <> '' or q3note <> '' or addprof <> '' or q4note <> '' or q5note <> '')
                AND class= '".$class."'
                AND term= '".$term."'
                AND times= IFNULL('".$times."',times)
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
                            FROM t75tb
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
            $fileName = 'L16';
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
                $objActSheet->setCellValue('M1', '訓期：');
            }else{
                $objActSheet->setCellValue('A1', $dataArrTitle[0]['TITLE']);
                $objActSheet->setCellValue('M1', str_replace('~0','~',str_replace('：0','：',$dataArrDate[0]['sdate_edate'])));
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
                $objActSheet->getCell('A14')->setValue($objRichText);
            }

            if(empty($dataArrRemark2)){
                $objActSheet->setCellValue('A16', '2.本表所列同意程度係學員在「研習規劃」、「學習輔導」、「行政服務」等3個面向、8題次反應之平均，結果相當於0.00；另「講座授課」');
                $objActSheet->setCellValue('A17', '  面向之滿意度（平均為82.89）統計結果詳次表；「整體評價」之平均為0.00。本次研習學員在5個面向之滿意度總平均為0.00分。');
            }else{
                //$objActSheet->setCellValue('A16', str_replace('%AVG%',$dataArrAVG[0]['AVG'],$dataArrRemark2[0]['REMARK2A']));
                //$objActSheet->setCellValue('A17', $dataArrRemark2[0]['REMARK2B']);
                $objRichText2 = new PHPExcel_RichText();
                $objRichText2->createText(str_replace('%AVG%',$dataArrAVG[0]['AVG'],$dataArrRemark2[0]['REMARK2A']));
                $objFont = $objRichText2->createTextRun($dataArrRemark2[0]['REMARK2B']);
                $objFont->getFont()->setBold(true);
                $objFont->getFont()->setUnderline(true);
                $objFont->getFont()->setName("標楷體");
                $objFont->getFont()->setSize("12");
                $objActSheet->getCell('A16')->setValue($objRichText2);
            }

            if(empty($dataArrRemark4)){
                $objActSheet->setCellValue('A18', '4.本次學員參與研習原因經統計包括：「因工作需要經機關指派參加」者0人（0.00％），「因工作需要自願參加」者0人（0.00％），「');
                $objActSheet->setCellValue('A19', '  與工作無關但自願參加」者0人（0.00％），「與工作無關經機關指派參加」者0人（0.00％）。');
            }else{
                $objActSheet->setCellValue('A18', $dataArrRemark4[0]['REMARK4A']);
                $objActSheet->setCellValue('A19', $dataArrRemark4[0]['REMARK4B']);
            }

            $lineName = 'C';
            if(sizeof($reportlist) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    if($i==0){
                        $lineName = 'D';
                    } elseif($i==1){
                        $lineName = 'F';
                    } elseif($i==2){
                        $lineName = 'H';
                    } elseif($i==3){
                        $lineName = 'J';
                    } elseif($i==4){
                        $lineName = 'L';
                    } elseif($i==5){
                        $lineName = 'P';
                    }else {
                        //$NameFromNumber=$this->getNameFromNumber($i+2); //B
                        $lineName = 'C';
                    }
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlist); $j++) {
                        //3開始
                        $objActSheet->setCellValue($lineName.($j+3), $reportlist[$j][$arraykeys[$i]]);
                    }
                }
            }


            //各問項題目平均數與標準差的比較
            //$dataArr2STDDEV[0]['sngMax'] ,
            //$dataArr2STDDEV[0]['sngMin'] ,
            $reportlistRemark3N = json_decode(json_encode($reportlistRemark3N), true);
            //dd((double)$dataArr2STDDEV[0]['sngMax']);
            //dd($dataArr2STDDEV[0]['sngMin']);
            //dd($reportlistRemark3N);
            $REMARK3 = '';
            $strMax='';
            $strMin='';
            if(sizeof($reportlistRemark3N) != 0) {
                for ($i=0; $i < sizeof($arraykeysRemark3N); $i++) {
                    for ($j=0; $j < sizeof($reportlistRemark3N); $j++) {
                       //$reportlistRemark3N[$j][$dataArrRemark3N[$i]] ,
                       //'高於兩個標準差範圍
                       if($dataArr2STDDEV[0]['sngMax'] < $reportlistRemark3N[$j][$arraykeysRemark3N[$i]]){
                          if($strMax==''){
                            $strMax= ($j+1);
                          } else{
                            $strMax= $strMax.'、'.($j+1);
                          }
                       }

                       //'低於兩個標準差範圍
                        if($dataArr2STDDEV[0]['sngMin'] > $reportlistRemark3N[$j][$arraykeysRemark3N[$i]]){
                            if($strMin==''){
                                $strMin= ($j+1);
                            }else{
                                $strMin= $strMin.'、'.($j+1);
                            }
                        }
                    }
                }
            }
            if($strMax=='' && $strMin==''){
                $REMARK3 = '3.本次問卷統計結果各題滿意度平均數均無偏高或偏低之情形。';
            }
            if($strMax!=''){
                $REMARK3 = '3.本次問卷統計結果第'.($strMax).'題滿意度平均數有偏高情形（高於兩個標準差範圍）';
            }
            if($strMin!=''){
                $REMARK3 = $REMARK3.'3.本次問卷統計結果第'.($strMin).'題滿意度平均數有偏低情形（低於兩個標準差範圍）';
            }
            $objActSheet->setCellValue('A17', $REMARK3);

            //固定題目統計圖表
            //指定sheet
            $objActSheet = $objPHPExcel->getSheet(1);
            if(empty($dataArrTitle)){

            }else{
                $title = new PHPExcel_Chart_Title(str_replace('訓練成效評估結果統計表','訓練成效評估結果統計圖',$dataArrTitle[0]['TITLE']));
            }
            $X_title = new PHPExcel_Chart_Title('各題次問項內容');
            $Y_title = new PHPExcel_Chart_Title('百分位數');
            $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', '固定題目統計!$C$2', NULL, 1));
            $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', '固定題目統計!$C$3:$C$10', NULL, 100));
            $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', '固定題目統計!$O$3:$O$10', NULL, 100));
            //顯示數值
            $layout=new PHPExcel_Chart_Layout();
            $layout->setShowVal(true);
            //設定最大值,這是1.81版才有的功能，若是1.80則無此功能
            $axis=new PHPExcel_Chart_Axis();
            $axis->setAxisOptionsProperties("nextTo", null, null, null, null, null,0, 100);
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
            $reportlist1 = json_decode(json_encode($reportlist1), true);
            //dd($reportlist2);
            if(empty($dataArrTitle)){
                $objActSheet->setCellValue('A1', '訓練成效評估結果統計表（講座滿意度）');
                $objActSheet->setCellValue('E1', '訓期：');
            }else{
                $objActSheet->setCellValue('A1', $dataArrTitle[0]['TITLE']);
                $objActSheet->setCellValue('E1', str_replace('~0','~',str_replace('：0','：',$dataArrDate[0]['sdate_edate'])));
            }

            if(sizeof($reportlist1) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys1); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+2); //B
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlist1); $j++) {
                        if($i==0){
                            $objActSheet->setCellValue('A'.($j+3), ($j+1));
                        }
                        //3開始
                        $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist1[$j][$arraykeys1[$i]]);
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
                $objActSheet->getStyle('A3:F'.($j+2))->applyFromArray($styleArray);

                //VERTICAL_CENTER 垂直置中
                //HORIZONTAL_CENTER 水平置中
                $styleArrayCenter = array(
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                        )
                );
                $objActSheet->setCellValue('B'.($j+3), '加　　總　　平　　均');
                $objActSheet->getStyle('B'.($j+3))->applyFromArray($styleArrayCenter);

                //=IF(ISERROR(AVERAGE(F3:F9)),0,AVERAGE(F3:F9))
                $objActSheet->setCellValue('F'.($j+3), '=IF(ISERROR(AVERAGE(F3:F'.($j+2).')),0,AVERAGE(F3:F'.($j+2).'))');
                //=IF(ISERROR(STDEV(F3:F9)),0,STDEV(F3:F9))
                $objActSheet->setCellValue('F'.($j+4), '=IF(ISERROR(STDEV(F3:F'.($j+2).')),0,STDEV(F3:F'.($j+2).'))');

                //remark1
                $objRichText3 = new PHPExcel_RichText();
                if(empty($dataArrRemark1)){

                }else{
                    $objRichText3->createText($dataArrRemark1[0]['REMARK1A']);
                    $objFont = $objRichText3->createTextRun($dataArrRemark1[0]['REMARK1B']);
                    $objFont->getFont()->setBold(true);
                    $objFont->getFont()->setUnderline(true);
                    $objFont->getFont()->setName("標楷體");
                    $objFont->getFont()->setSize("12");
                    $objActSheet->getCell('A'.($j+4+2))->setValue($objRichText3);
                    $objActSheet->mergeCells('A'.($j+4+2).':F'.($j+4+2));
                    $styleArrayLeft = array(
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                        )
                    );
                    $objActSheet->getStyle('A'.($j+4+2))->applyFromArray($styleArrayLeft);
                }

                if(empty($dataArrAVG)){

                }else{
                    $objActSheet->setCellValue('A'.($j+4+2+1), '2.本次問卷調查結果之平均數轉化為百分位數（以100分為滿分），相當於'.$dataArrAVG[0]['AVG'].'分。');
                    $objActSheet->mergeCells('A'.($j+4+2+1).':F'.($j+4+2+1));
                    $objActSheet->getStyle('A'.($j+4+2+1))->applyFromArray($styleArrayLeft);
                }

                ////講座統計
                //各問項題目平均數與標準差的比較
                $reportlistRemark3N1 = json_decode(json_encode($reportlistRemark3N1), true);
                $REMARK3 = '';
                $strMax='';
                $strMin='';
                if(sizeof($reportlistRemark3N1) != 0) {
                    for ($i=0; $i < sizeof($arraykeysRemark3N1); $i++) {
                        for ($j=0; $j < sizeof($reportlistRemark3N1); $j++) {
                        //'高於兩個標準差範圍
                        if($dataArr2STDDEV1[0]['sngMax'] < $reportlistRemark3N1[$j][$arraykeysRemark3N1[$i]]){
                            if($strMax==''){
                                $strMax= ($j+1);
                            } else{
                                $strMax= $strMax.'、'.($j+1);
                            }
                        }
                        //'低於兩個標準差範圍
                            if($dataArr2STDDEV1[0]['sngMin'] > $reportlistRemark3N1[$j][$arraykeysRemark3N1[$i]]){
                                if($strMin==''){
                                    $strMin= ($j+1);
                                }else{
                                    $strMin= $strMin.'、'.($j+1);
                                }
                            }
                        }
                    }
                }
                if($strMax=='' && $strMin==''){
                    $REMARK3 = '3.本次講座個別之授課滿意度平均數均無偏高或偏低之情形。';
                }
                if($strMax!=''){
                    $REMARK3 = '3.本次問卷統計結果，計有'.($strMax).'等講座授課滿意度平均數有偏高情形（高於兩個標準差範圍）';
                }
                if($strMin!=''){
                    $REMARK3 = $REMARK3.'3.本次問卷統計結果，計有'.($strMin).'等講座授課滿意度平均數有偏低情形（低於兩個標準差範圍）';
                }
                //$objActSheet->setCellValue('A'.($j+4+2+1+1), '3.本次講座個別之授課滿意度平均數均無偏高或偏低之情形。');
                $objActSheet->setCellValue('A'.($j+4+2+1+1), $REMARK3);
                $objActSheet->mergeCells('A'.($j+4+2+1+1).':F'.($j+4+2+1+1));
                $objActSheet->getStyle('A'.($j+4+2+1+1))->applyFromArray($styleArrayLeft);
                //      strMax = "本次問卷統計結果，計有" & strMax & "等講座授課滿意度平均數有偏高情形（高於兩個標準差範圍）"
                //      strMin = "本次問卷統計結果，計有" & strMin & "等講座授課滿意度平均數有偏低情形（低於兩個標準差範圍）"
                //3.本次講座個別之授課滿意度平均數均無偏高或偏低之情形。



            }


            //講座統計圖表
            //指定sheet
            $objActSheet = $objPHPExcel->getSheet(3);
            /*
            $styleArrayC = array(
                'font'  => array(
                     'bold'  => true,
                     'size'  => 18,
                     'name'  => '標楷體'
                 ));

            $titleC=str_replace('訓練成效評估結果統計表','訓練成效評估結果統計圖（講座滿意度）',$dataArrTitle[0]['TITLE']);
            getStyle()->applyFromArray($styleArrayC);
            */
            //$title = new PHPExcel_Chart_Title(str_replace('訓練成效評估結果統計表','訓練成效評估結果統計圖（講座滿意度）',$dataArrTitle[0]['TITLE']));
            if(empty($dataArrTitle)){

            }else{
                $title = new PHPExcel_Chart_Title(str_replace('訓練成效評估結果統計表','訓練成效評估結果統計圖（講座滿意度）',$dataArrTitle[0]['TITLE']));
            }

            $X_title = new PHPExcel_Chart_Title('各題次問項內容');
            $Y_title = new PHPExcel_Chart_Title('百分位數');
            $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', '講座統計!$B$2', NULL, 1));
            if(sizeof($reportlist1)>0){
                $dsvnum = sizeof($reportlist1) - 1;
            }
            $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', '講座統計!$B$3:$B$'.($dsvnum+3), NULL, 100));
            $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', '講座統計!$F$3:$F$'.($dsvnum+3), NULL, 100));
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
            /*
                'col index
                '0  q1note 研習規劃補充說明
                '1  addcourse 研習規劃增加課程
                '2  delcourse 研習規劃刪除課程
                '3  q2note 學習輔導補充說明
                '4  q3note 講座補充說明
                '5  addprof 推薦講座及課程
                '6  q4note 行政服務補充說明
                '7  q5note 整體評價其他建議
            */
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
                    //資料by班別迴圈
                    $k=1;
                    //$linenum=1;
                    $comments='';
                    for ($j=0; $j < sizeof($reportlist2); $j++) {
                        if($reportlist2[$j][$arraykeys2[$i]]<>''){
                            $comments = $comments.'    '.($k).'.'.$reportlist2[$j][$arraykeys2[$i]]."\n";
                            $k++;
                            //$linenum++;
                        }
                        if(($j+1)==sizeof($reportlist2)){
                            //*3固定間隔三行
                            $objActSheet->setCellValue('A'.(($i+1)*3), $comments);
                            //$objActSheet->getRowDimension(($i+1)*3)->setRowHeight($k*16);
                            $objActSheet->getRowDimension(($i+1)*3)->setRowHeight(-1);
                            $objActSheet->getStyle('A'.(($i+1)*3))->getAlignment()->setWrapText(true);
                        }
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
            //dd($reportlist3);


            $objActSheet = $objPHPExcel->getSheet(0);

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"3",$request->input('doctype'),"訓練成效評估結果統計圖表(93~95)");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 
    }
}
