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
use PHPExcel_Style_Alignment;

class TrainingResult9092Controller extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('training_result_90_92', $user_group_auth)){
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
        return view('admin/training_result_90_92/list');
    }

    // 搜尋下拉『班別』
    public function getclass(Request $request) {
        /* '問卷版本 新:0 舊:1 */
        $ratioInfo = $request->input('info');
        if($ratioInfo=="0") {
            $sql = "SELECT DISTINCT t53tb.class, t01tb.name
                      FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                     WHERE t53tb.times<>''
                     ORDER BY t53tb.class DESC  ";
        }
        else{
            $sql = "SELECT DISTINCT t16tb.class, t01tb.name
                      FROM t16tb INNER JOIN t01tb ON t16tb.class = t01tb.class
                     ORDER BY t16tb.class DESC";
        }
        $classArr = DB::select($sql);
        return $classArr;
    }

    // 搜尋下拉『期別』
    public function getTermByClass(Request $request)
    {
      /* '問卷版本 新:0 舊:1  		 */
      $ratioInfo = $request->input('info');
      $class = $request->input('class');
      if($ratioInfo=="0") {
        $RptBasic = new \App\Rptlib\RptBasic;
        return $RptBasic->getTermByClass($request->input('class'));
      }
      else{
        $sql = "SELECT DISTINCT term FROM t16tb
                WHERE class = '".$class."'
                 ORDER By 1";
        $classArr = DB::select($sql);
        return $classArr;
      }

    }

    // 搜尋下拉『第幾次調查』
    public function getTimeByClass(Request $request)
    {
      /* '問卷版本 新:0 舊:1  		 */
      $ratioInfo = $request->input('info');
      $class = $request->input('class');
      $term = $request->input('term');
      if($ratioInfo=="0") {
        $RptBasic = new \App\Rptlib\RptBasic;
        return $RptBasic->getTimeByClass($request->input('class'), $request->input('term'));
      }
      else{
        $sql = "SELECT DISTINCT times FROM t16tb
                 WHERE class = '".$class."'
                   AND term = '".$term."'
                 ORDER By 1";
        $classArr = DB::select($sql);
        return $classArr;
      }
    }

    /*
    訓後成效評估結果統計圖表 CSDIR5070
    參考Tables:
    使用範本:L19A.xlsx(新版), L19B.xlsx (舊版)
    'History:
    '2003/04/28
    '若題目數被【統計圖表最大題目數】整除
    '統計圖表會多出一個
    '2003/04/04
    '只顯示有問卷題目之班期
    '統計圖表標題，標示(一)、(二)..
    '2003/04/01 Update
    '統計圖表最大題目數
    '需更新CSDIR5070.xlt
    '2002/12/25 Update
    '修正bug
    '2002/12/24 Update
    'New Vsersion 2002/12/24 start 2002/12/24 end
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
        //'版本 新:0 舊:1
        $ratioInfo = $request->input('info');

        //取得訓期, Begin Date & End Date
        $sqlDate="SELECT sdate,edate ,
                        CONCAT('訓期：',
                                                SUBSTRING(sdate,1,3),'/', SUBSTRING(sdate,4,2), '/', SUBSTRING(sdate,6,2),'~',
                                                SUBSTRING(edate,1,3),'/', SUBSTRING(edate,4,2), '/', SUBSTRING(edate,6,2)
                                        ) AS sdate_edate,
                        CONCAT('日期：',
                                                SUBSTRING(sdate,1,3),'/', SUBSTRING(sdate,4,2), '/', SUBSTRING(sdate,6,2),'~',
                                                SUBSTRING(edate,1,3),'/', SUBSTRING(edate,4,2), '/', SUBSTRING(edate,6,2)
                                        ) AS sdate_edate_old
                FROM t04tb
                WHERE class='".$class."'
                AND term= '".$term."'
                ";
        $reportlistDate = DB::select($sqlDate);
        $dataArrDate=json_decode(json_encode(DB::select($sqlDate)), true);

        // 讀檔案
        /* '版本 新:0 舊:1
        */
        if($ratioInfo=="0") {

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
                    WHERE t53tb.times<>''
                    AND t53tb.class = '".$class."'
                    AND t53tb.term= '".$term."'
                    AND t53tb.times= IFNULL('".$times."',t53tb.times)
                    ORDER BY t53tb.class DESC

                    ";
            $reportlistTitle = DB::select($sqlTitle);
            $dataArrTitle=json_decode(json_encode(DB::select($sqlTitle)), true);

            //取得 固定題目統計
            $sql="SELECT
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=5 THEN 1 ELSE NULL  END))
                        ELSE 0
                        END),
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=4 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=4 THEN 1 ELSE NULL  END))
                        ELSE 0
                        END),
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=3 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=3 THEN 1 ELSE NULL  END))
                        ELSE 0
                        END),
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=2 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=2 THEN 1 ELSE NULL  END))
                        ELSE 0
                        END),
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=1 THEN 1 ELSE NULL  END))
                        ELSE 0
                        END)
                        FROM
                        (
                        SELECT  'q11' AS key1, 'Q11.在參訓報到時，我對研習目標與研習重點己經有瞭解' AS caption
                        UNION ALL
                        SELECT  'q12' AS key1, 'Q12.我認為本次研習內容的課程安排與研習目標確有關聯' AS caption
                        UNION ALL
                        SELECT  'q13' AS key1, 'Q13.我對本班輔導人員的說明與服務感到滿意' AS caption
                        UNION ALL
                        SELECT  'q14' AS key1, 'Q14.本次研習的教材資料很適當' AS caption
                        UNION ALL
                        SELECT  'q21' AS key1, 'Q21.教學環境與設備' AS caption
                        UNION ALL
                        SELECT  'q22' AS key1, 'Q22.餐飲' AS caption
                        UNION ALL
                        SELECT  'q23' AS key1, 'Q23.住宿' AS caption
                        UNION ALL
                        SELECT  'q31' AS key1, 'Q31.此次研習內容與我的實際工作相關' AS caption
                        UNION ALL
                        SELECT  'q32' AS key1, 'Q32.我能將研習內容應用在工作上' AS caption
                        UNION ALL
                        SELECT  'q33' AS key1, 'Q33.整體而言，此次研習班次符合個人工作與自我成長的需求' AS caption
                        ) A
                        INNER JOIN t55tb t55tb ON 1 = 1
                    WHERE t55tb.class= '".$class."'
                    AND t55tb.term= '".$term."'
                    AND t55tb.times= IFNULL('".$times."',t55tb.times)
                    GROUP BY A.caption, A.key1
                    ORDER BY A.caption
                    ";

            $reportlist = DB::select($sql);
            $dataArr=json_decode(json_encode(DB::select($sql)), true);
            //取出全部項目
            if(sizeof($reportlist) != 0) {
                $arraykeys=array_keys((array)$reportlist[0]);
            }

            //取得 A14:REMARK 1
            //不需在額外判斷最大次數
            $sqlRemark1="SELECT CONCAT('1.本次調查問卷計發出', A.lngCopy_Count, '份，共回收', B.lngBack_Count, '份，') AS REMARK1A,
                            CONCAT('回收率',
                                    CASE WHEN A.lngCopy_Count = 0 THEN
                                            '0%。'
                                        ELSE
                            CONCAT(FORMAT( B.lngBack_Count / A.lngCopy_Count * (100), 2), '%。')
                                        END ) AS REMARK1B,
                            CONCAT('受訓人數：', A.lngCopy_Count, '人 回收份數：', B.lngBack_Count, '人 ') AS TITLE_OLD
                            FROM (
                            SELECT copy AS lngCopy_Count
                            FROM t53tb
                            WHERE class= '".$class."'
                                AND term= '".$term."'
                                AND times= IFNULL('".$times."',times)
                            ) A LEFT JOIN
                            ( SELECT COUNT(*) AS lngBack_Count
                                FROM t55tb
                            WHERE class='".$class."'
                                AND term= '".$term."'
                                AND times= IFNULL('".$times."',times)
                            ) B ON 1 = 1
                            ";
            $reportlistRemark1 = DB::select($sqlRemark1);
            $dataArrRemark1=json_decode(json_encode(DB::select($sqlRemark1)), true);

            //取得 REMARK:AVG, STDDEV, sngMax, sngMin
            //比照EXCEL輸出結果, SELECT 不排除0項目, 故加IFNULL為0
            $sqlRemark2STDDEV="SELECT ROUND(AVG(IFNULL((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3),0)),2) AS AVG,
                                    ROUND(STDDEV_SAMP(IFNULL((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3),0)),2) AS STDDEV,
                                    ROUND(AVG(IFNULL((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3),0)),2) +
                                            (ROUND(STDDEV_SAMP(IFNULL((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3),0)),2) * 2) AS sngMax,
                                    ROUND(AVG(IFNULL((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3),0)),2) -
                                            (ROUND(STDDEV_SAMP(IFNULL((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3),0)),2) * 2) AS sngMin
                                            FROM (
                                                    SELECT
                                                    (CASE
                                                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=5 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=5 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=5 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=5 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=5 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=5 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=5 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=5 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=5 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=5 THEN 1 ELSE NULL  END))
                                                    ELSE 0
                                                    END) AS D3,
                                                    (CASE
                                                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=4 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=4 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=4 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=4 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=4 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=4 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=4 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=4 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=4 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=4 THEN 1 ELSE NULL  END))
                                                    ELSE 0
                                                    END) AS F3,
                                                    (CASE
                                                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=3 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=3 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=3 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=3 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=3 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=3 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=3 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=3 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=3 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=3 THEN 1 ELSE NULL  END))
                                                    ELSE 0
                                                    END) AS H3,
                                                    (CASE
                                                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=2 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=2 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=2 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=2 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=2 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=2 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=2 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=2 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=2 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=2 THEN 1 ELSE NULL  END))
                                                    ELSE 0
                                                    END) AS J3,
                                                    (CASE
                                                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=1 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=1 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=1 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=1 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=1 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=1 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=1 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q41' THEN COUNT((CASE WHEN q31=1 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q42' THEN COUNT((CASE WHEN q32=1 THEN 1 ELSE NULL  END))
                                                    WHEN A.key1='q43' THEN COUNT((CASE WHEN q33=1 THEN 1 ELSE NULL  END))
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
                                        SELECT  'q14' AS key1 FROM DUAL
                                        UNION ALL
                                        SELECT  'q21' AS key1 FROM DUAL
                                        UNION ALL
                                        SELECT  'q22' AS key1 FROM DUAL
                                        UNION ALL
                                        SELECT  'q23' AS key1 FROM DUAL
                                        UNION ALL
                                        SELECT  'q31' AS key1 FROM DUAL
                                        UNION ALL
                                        SELECT  'q32' AS key1 FROM DUAL
                                        UNION ALL
                                        SELECT  'q33' AS key1 FROM DUAL
                                        ) A
                                        INNER JOIN t55tb t55tb ON 1 = 1
                                        WHERE t55tb.class= '".$class."'
                                        AND t55tb.term= '".$term."'
                                        AND t55tb.times= IFNULL('".$times."',times)
                                        GROUP BY A.key1
                                        ) T
                    ";
            $reportlist2STDDEV = DB::select($sqlRemark2STDDEV);
            $dataArr2STDDEV=json_decode(json_encode(DB::select($sqlRemark2STDDEV)), true);

            //取得 REMARK3~4
            $sqlRemark34="SELECT worper, totper, teaper
                            FROM t57tb
                            WHERE class='".$class."'
                            AND term= '".$term."'
                            AND times= IFNULL('".$times."',times)
            ";
            $reportlistRemark34 = DB::select($sqlRemark34);
            $dataArrRemark34=json_decode(json_encode(DB::select($sqlRemark34)), true);


            //取得 講座統計
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
            //取得 REMARK3:兩個標準差範圍
            $sqlRemark2STDDEV1="SELECT ROUND(AVG((T.avg_ans1+T.avg_ans2+T.avg_ans3)/3),2) AS AVG,
                                    ROUND(STDDEV_SAMP((T.avg_ans1+T.avg_ans2+T.avg_ans3)/3),2) AS STDDEV,
                                    ROUND(AVG((T.avg_ans1+T.avg_ans2+T.avg_ans3)/3) + (ROUND(STDDEV_SAMP((T.avg_ans1+T.avg_ans2+T.avg_ans3)/3),2) * 2),2) AS sngMax,
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

            //取得 問答題
            /*
                q15, " '內容與服務5
                q24, " '研習環境方面-環境4
                qfood, " '餐飲
                qboard, " '住宿
                q34, " '工作相面-工作4
                q42, " '推薦講座及課程
                q51, " '整體1
                q52, " '整體2
                q55 " '整體5
            */
            $sql2="SELECT q15, q34, q42,
                            q24, qfood, qboard,
                            q51, q52, q55
                    FROM t55tb
                    WHERE class= '".$class."'
                    AND term= '".$term."'
                    AND times= IFNULL('".$times."',times)
            ";
            $reportlist2 = DB::select($sql2);
            $dataArr2=json_decode(json_encode(DB::select($sql2)), true);
            //取出全部項目
            if(sizeof($reportlist2) != 0) {
                    $arraykeys2=array_keys((array)$reportlist2[0]);
            }

            //檔案名稱
            $fileName = 'L19A';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            //$objPHPExcel = PHPExcel_IOFactory::load($filePath);
            $excelReader = PHPExcel_IOFactory::createReaderForFile($filePath);
            $excelReader->setReadDataOnly(false);
            $excelReader->setIncludeCharts(true);
            $objPHPExcel = $excelReader->load($filePath);

            //取得 固定題目統計
            //指定sheet
            $objActSheet = $objPHPExcel->getActiveSheet();
            $objActSheet = $objPHPExcel->getSheet(0);
            $reportlist = json_decode(json_encode($reportlist), true);
            //dd($reportlist);
            if(empty($dataArrTitle)){
                $objActSheet->setCellValue('A1', '訓練成效評估結果統計表');
                $objActSheet->setCellValue('A24', '訓期：');
            }else{
                $objActSheet->setCellValue('A1', $dataArrTitle[0]['TITLE']);
                $objActSheet->setCellValue('A24', str_replace('~0','~',str_replace('：0','：',$dataArrDate[0]['sdate_edate'])));
            }
            $objActSheet->setCellValue('G24','製表：'.(Date("Y")-'1911').Date("/m/d"));

            if(empty($dataArrRemark1)){
                    $objActSheet->setCellValue('A16', '1.本次調查問卷計發出0份，共回收0份，回收率0.00%。');
            }else{
                    $objActSheet->setCellValue('A16', $dataArrRemark1[0]['REMARK1A'].$dataArrRemark1[0]['REMARK1B']);
            }

            if(empty($dataArr2STDDEV)){
                $objActSheet->setCellValue('A17', '2.平均數係將各答項非常同意、同意、無意見、不同意、非常不同意，分別賦予5、4、3、2及1分，加總後之平均；總平均數為0.00。');
                $objActSheet->setCellValue('A20', '5.依常態分配之概念，平均數正負兩個標準差佔總次數分配的95%；以原始分數之平均數標準差(0.00)進行分析，2個標準差值之範圍為');
                $objActSheet->setCellValue('A21', '  0.00±(0.00)x2=0.00～0.00。');
            }else{
                $objActSheet->setCellValue('A17', '2.平均數係將各答項非常同意、同意、無意見、不同意、非常不同意，分別賦予5、4、3、2及1分，加總後之平均；總平均數為'.$dataArr2STDDEV[0]['AVG'].'。');
                $objActSheet->setCellValue('A20', '5.依常態分配之概念，平均數正負兩個標準差佔總次數分配的95%；以原始分數之平均數標準差('.$dataArr2STDDEV[0]['STDDEV'].')進行分析，2個標準差值之範圍為');
                $objActSheet->setCellValue('A21', '  '.$dataArr2STDDEV[0]['AVG'].'±('.$dataArr2STDDEV[0]['STDDEV'].')x2='.$dataArr2STDDEV[0]['sngMax'].'～'.$dataArr2STDDEV[0]['sngMin'].'。');
            }

            if(empty($dataArrRemark34)){
                    $objActSheet->setCellValue('A18', '3.本次問卷調查之平均數值如轉化為百分位數（以100分為滿分），相當於0.00分。（另「整體評價」一項之平均為0.00分）');
                    $objActSheet->setCellValue('A19', '4.本班(該次)學員滿意度之總平均為00.0分(為整體評價、講師授課、內容服務、環境設備、餐飲、住宿、工作相關性之平均)。');
            }else{
                //=CONCATENATE("3.本次問卷調查之平均數值如轉化為百分位數（以100分為滿分），相當於",M13,"分。（另「整體評價」一項之平均為89.86")
                $objActSheet->setCellValue('A18', '=CONCATENATE("3.本次問卷調查之平均數值如轉化為百分位數（以100分為滿分），相當於",M13,"分。（另「整體評價」一項之平均為'.$dataArrRemark34[0]['worper'].'分）")');
                $objActSheet->setCellValue('A19', '4.本班(該次)學員滿意度之總平均為'.$dataArrRemark34[0]['totper'].'分(為整體評價、講師授課、內容服務、環境設備、餐飲、住宿、工作相關性之平均)。');
            }

            $lineName = 'C';
            if(sizeof($reportlist) != 0) {
            //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    if($i==0){
                        $lineName = 'B';
                    } elseif($i==1){
                        $lineName = 'D';
                    } elseif($i==2){
                        $lineName = 'F';
                    } elseif($i==3){
                        $lineName = 'H';
                    } elseif($i==4){
                        $lineName = 'J';
                    }else {
                        //$NameFromNumber=$this->getNameFromNumber($i+2); //B
                        $lineName = 'A';
                    }
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlist); $j++) {
                        //3開始
                        $objActSheet->setCellValue($lineName.($j+3), $reportlist[$j][$arraykeys[$i]]);
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
            $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', NULL, NULL, 1));
            $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', '固定題目統計!$A$3:$A$12', NULL, 100));
            $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', '固定題目統計!$M$3:$M$12', NULL, 100));
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
            }else{
                $objActSheet->setCellValue('A1', $dataArrTitle[0]['TITLE']);
            }

            if(sizeof($reportlist1) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys1); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1); //B
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlist1); $j++) {

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
                $objActSheet->getStyle('A3:E'.($j+2))->applyFromArray($styleArray);

                //VERTICAL_CENTER 垂直置中
                //HORIZONTAL_CENTER 水平置中
                $styleArrayCenter = array(
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                        )
                );
                $objActSheet->setCellValue('A'.($j+3), '平　　均');
                $objActSheet->getStyle('A'.($j+3))->applyFromArray($styleArrayCenter);

                //=IF(ISERROR(AVERAGE(E3:E9)),0,AVERAGE(E3:E9))
                $objActSheet->setCellValue('E'.($j+3), '=IF(ISERROR(AVERAGE(E3:E'.($j+2).')),0,AVERAGE(E3:E'.($j+2).'))');
                //=IF(ISERROR(STDEV(E3:E9)),0,STDEV(E3:E9))
                $objActSheet->setCellValue('E'.($j+4), '=IF(ISERROR(STDEV(E3:E'.($j+2).')),0,STDEV(E3:E'.($j+2).'))');

                $styleArrayLeft = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    )
                );

                //remark1
                if(empty($dataArrRemark1)){
                    $objActSheet->setCellValue('A'.($j+4+2), '1.本次調查問卷計發出0份，共回收0份，回收率0.00%。');
                    $objActSheet->mergeCells('A'.($j+4+2).':E'.($j+4+2));
                    $objActSheet->getStyle('A'.($j+4+2))->applyFromArray($styleArrayLeft);
                }else{
                    $objActSheet->setCellValue('A'.($j+4+2), $dataArrRemark1[0]['REMARK1A'].$dataArrRemark1[0]['REMARK1B']);
                    $objActSheet->mergeCells('A'.($j+4+2).':E'.($j+4+2));
                    $objActSheet->getStyle('A'.($j+4+2))->applyFromArray($styleArrayLeft);
                }

                if(empty($dataArr2STDDEV1)){
                    $objActSheet->setCellValue('A'.($j+4+2+1), '2.本次問卷調查轉化為百分位數（以100分為滿分），相當於100分為滿分），相當於0.00分。');
                    $objActSheet->mergeCells('A'.($j+4+2+1).':E'.($j+4+2+1));
                    $objActSheet->getStyle('A'.($j+4+2+1))->applyFromArray($styleArrayLeft);

                    $objActSheet->setCellValue('A'.($j+4+2+1+1), '3.依常態分配之概念，平均數正負兩個標準差佔總次數分配的95%；以原始分數之平均數標準差(0.00)進行分析，2個標準差值之範圍為');
                    $objActSheet->mergeCells('A'.($j+4+2+1+1).':E'.($j+4+2+1+1));
                    $objActSheet->getStyle('A'.($j+4+2+1+1))->applyFromArray($styleArrayLeft);

                    $objActSheet->setCellValue('A'.($j+4+2+1+1+1), '  0.00±(0.00)x2=0.00～0.00。');
                    $objActSheet->mergeCells('A'.($j+4+2+1+1+1).':E'.($j+4+2+1+1+1));
                    $objActSheet->getStyle('A'.($j+4+2+1+1+1))->applyFromArray($styleArrayLeft);
                }else{
                    $objActSheet->setCellValue('A'.($j+4+2+1), '2.本次問卷調查轉化為百分位數（以100分為滿分），相當於100分為滿分），相當於'.$dataArr2STDDEV1[0]['AVG'].'分。');
                    $objActSheet->mergeCells('A'.($j+4+2+1).':E'.($j+4+2+1));
                    $objActSheet->getStyle('A'.($j+4+2+1))->applyFromArray($styleArrayLeft);

                    $objActSheet->setCellValue('A'.($j+4+2+1+1), '3.依常態分配之概念，平均數正負兩個標準差佔總次數分配的95%；以原始分數之平均數標準差('.$dataArr2STDDEV1[0]['STDDEV'].')進行分析，2個標準差值之範圍為');
                    $objActSheet->mergeCells('A'.($j+4+2+1+1).':E'.($j+4+2+1+1));
                    $objActSheet->getStyle('A'.($j+4+2+1+1))->applyFromArray($styleArrayLeft);

                    $objActSheet->setCellValue('A'.($j+4+2+1+1+1), '  '.$dataArr2STDDEV1[0]['AVG'].'±('.$dataArr2STDDEV1[0]['STDDEV'].')x2='.$dataArr2STDDEV1[0]['sngMax'].'～'.$dataArr2STDDEV1[0]['sngMin'].'。');
                    $objActSheet->mergeCells('A'.($j+4+2+1+1+1).':E'.($j+4+2+1+1+1));
                    $objActSheet->getStyle('A'.($j+4+2+1+1+1))->applyFromArray($styleArrayLeft);
                }

                if(empty($dataArrTitle)){
                    $objActSheet->setCellValue('A'.($j+4+2+1+1+1+5), '訓期：');

                }else{
                    $objActSheet->setCellValue('A'.($j+4+2+1+1+1+5), str_replace('~0','~',str_replace('：0','：',$dataArrDate[0]['sdate_edate'])));
                }

                $objActSheet->mergeCells('B'.($j+4+2+1+1+1+5).':E'.($j+4+2+1+1+1+5));
                $objActSheet->setCellValue('B'.($j+4+2+1+1+1+5),'製表：'.(Date("Y")-'1911').Date("/m/d"));
                $objActSheet->getStyle('B'.($j+4+2+1+1+1+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

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
            $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', '講座統計!$A$2', NULL, 1));
            if(sizeof($reportlist1)>0){
                $dsvnum = sizeof($reportlist1) - 1;
            }
            $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', '講座統計!$A$3:$A$'.($dsvnum+3), NULL, 100));
            $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', '講座統計!$E$3:$E$'.($dsvnum+3), NULL, 100));
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
            $objActSheet = $objPHPExcel->getSheet(4);
            $reportlist2 = json_decode(json_encode($reportlist2), true);
            //訓練成效評估結果統計表
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
                            //改採用Excel範本縮排方式替代原每80字元換行方式會截斷
                            /*
                            if($i>=3){
                                $comments = $comments.'        '.($k).'.'.$reportlist2[$j][$arraykeys2[$i]]."\n";
                            }else{
                                $comments = $comments.'    '.($k).'.'.$reportlist2[$j][$arraykeys2[$i]]."\n";
                            }
                            */
                            $comments = $comments.($k).'.'.$reportlist2[$j][$arraykeys2[$i]]."\n";
                            $k++;
                            //$linenum++;
                        }
                        if(($j+1)==sizeof($reportlist2)){
                            //從第四行開始,*3固定間隔三行
                            $objActSheet->setCellValue('A'.(4+($i)*3), $comments);
                            //$objActSheet->getRowDimension(($i+1)*3)->setRowHeight($k*16);
                            //$objActSheet->getRowDimension(($i+1)*3)->setRowHeight(-1);
                            $objActSheet->getStyle('A'.(4+($i)*3))->getAlignment()->setWrapText(true);
                        }
                    }
                }
            }

            $objActSheet = $objPHPExcel->getSheet(0);

            $outputname="訓練成效評估結果統計圖表(90~92)-新版問卷.xlsx";

        }else{

            //取得 TITLE
            $sqlTitleOld="SELECT DISTINCT t16tb.class, t01tb.name, t16tb.term, t16tb.times,
                            CONCAT(t01tb.name, '第',
                            CASE t16tb.term WHEN '01' THEN '1'
                                            WHEN '02' THEN '2'
                                            WHEN '03' THEN '3'
                                            WHEN '04' THEN '4'
                                            WHEN '05' THEN '5'
                                            WHEN '06' THEN '6'
                                            WHEN '07' THEN '7'
                                            WHEN '08' THEN '8'
                                            WHEN '09' THEN '9'
                                            ELSE t16tb.term END
                            , '期成效評估調查統計表' ,
                                                                            '(' ,
                                                                           t16tb.times
                                                                            ,')')  AS TITLE
                    FROM t16tb INNER JOIN t01tb ON t16tb.class = t01tb.class
                    WHERE t16tb.times<>''
                    AND t16tb.class = '".$class."'
                    AND t16tb.term= '".$term."'
                    AND t16tb.times= IFNULL('".$times."',t16tb.times)
                    ORDER BY t16tb.class DESC

                    ";
            $reportlistTitleOld = DB::select($sqlTitleOld);
            $dataArrTitleOld=json_decode(json_encode(DB::select($sqlTitleOld)), true);

            //取得備註 受訓人數：
            $sqlRemark1Old="SELECT CONCAT('受訓人數：', A.lngCopy_Count, '人 回收份數：', B.lngBack_Count, '人 ') AS TITLE_OLD
                            FROM (
                            SELECT copy AS lngCopy_Count
                            FROM t16tb
                            WHERE class= '".$class."'
                                AND term= '".$term."'
                                AND times= IFNULL('".$times."',times)
                            ) A LEFT JOIN
                            ( SELECT COUNT(*) AS lngBack_Count
                                FROM t17tb
                            WHERE class='".$class."'
                                AND term= '".$term."'
                                AND times= IFNULL('".$times."',times)
                            ) B ON 1 = 1
                            ";
            $reportlistRemark1Old = DB::select($sqlRemark1Old);
            $dataArrRemark1Old=json_decode(json_encode(DB::select($sqlRemark1Old)), true);

            //取得 活動滿意度
            $sql2Old="SELECT
                            (CASE
                            WHEN A.key1='whoans' THEN C.whoper
                            WHEN A.key1='teaans' THEN C.teaper
                            WHEN A.key1='couans' THEN C.couper
                            WHEN A.key1='matans' THEN C.matper
                            WHEN A.key1='lifans' THEN C.lifper
                            WHEN A.key1='affans' THEN C.affper
                            WHEN A.key1='fooans' THEN C.fooper
                            WHEN A.key1='boaans' THEN C.boaper
                            ELSE 0
                            END) ,
                            (CASE
                            WHEN A.key1='whoans' THEN COUNT((CASE WHEN whoans=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='teaans' THEN COUNT((CASE WHEN lifans=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='couans' THEN COUNT((CASE WHEN couans=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='matans' THEN COUNT((CASE WHEN matans=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='lifans' THEN COUNT((CASE WHEN lifans=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='affans' THEN COUNT((CASE WHEN affans=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='fooans' THEN COUNT((CASE WHEN fooans=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='boaans' THEN COUNT((CASE WHEN boaans=1 THEN 1 ELSE NULL  END))
                            ELSE 0
                            END) ,
                            (CASE
                            WHEN A.key1='whoans' THEN COUNT((CASE WHEN whoans=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='teaans' THEN COUNT((CASE WHEN lifans=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='couans' THEN COUNT((CASE WHEN couans=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='matans' THEN COUNT((CASE WHEN matans=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='lifans' THEN COUNT((CASE WHEN lifans=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='affans' THEN COUNT((CASE WHEN affans=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='fooans' THEN COUNT((CASE WHEN fooans=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='boaans' THEN COUNT((CASE WHEN boaans=2 THEN 1 ELSE NULL  END))
                            ELSE 0
                            END) ,
                            (CASE
                            WHEN A.key1='whoans' THEN COUNT((CASE WHEN whoans=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='teaans' THEN COUNT((CASE WHEN lifans=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='couans' THEN COUNT((CASE WHEN couans=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='matans' THEN COUNT((CASE WHEN matans=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='lifans' THEN COUNT((CASE WHEN lifans=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='affans' THEN COUNT((CASE WHEN affans=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='fooans' THEN COUNT((CASE WHEN fooans=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='boaans' THEN COUNT((CASE WHEN boaans=3 THEN 1 ELSE NULL  END))
                            ELSE 0
                            END) ,
                            (CASE
                            WHEN A.key1='whoans' THEN COUNT((CASE WHEN whoans=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='teaans' THEN COUNT((CASE WHEN lifans=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='couans' THEN COUNT((CASE WHEN couans=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='matans' THEN COUNT((CASE WHEN matans=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='lifans' THEN COUNT((CASE WHEN lifans=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='affans' THEN COUNT((CASE WHEN affans=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='fooans' THEN COUNT((CASE WHEN fooans=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='boaans' THEN COUNT((CASE WHEN boaans=4 THEN 1 ELSE NULL  END))
                            ELSE 0
                            END) ,
                            (CASE
                            WHEN A.key1='whoans' THEN COUNT((CASE WHEN whoans=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='teaans' THEN COUNT((CASE WHEN lifans=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='couans' THEN COUNT((CASE WHEN couans=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='matans' THEN COUNT((CASE WHEN matans=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='lifans' THEN COUNT((CASE WHEN lifans=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='affans' THEN COUNT((CASE WHEN affans=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='fooans' THEN COUNT((CASE WHEN fooans=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='boaans' THEN COUNT((CASE WHEN boaans=5 THEN 1 ELSE NULL  END))
                            ELSE 0
                            END)
                        FROM
                            (
                            SELECT '".$class."' AS class, '".$term."' AS term,'".$times."' AS times, 'whoans' AS key1, '1整體活動' AS caption
                            FROM DUAL
                            UNION ALL
                            SELECT '".$class."' AS class, '".$term."' AS term,'".$times."' AS times, 'couans' AS key1, '2課程安排' AS caption
                            FROM DUAL
                            UNION ALL
                            SELECT '".$class."' AS class, '".$term."' AS term,'".$times."' AS times, 'matans' AS key1, '3教材資料' AS caption
                            FROM DUAL
                            UNION ALL
                            SELECT '".$class."' AS class, '".$term."' AS term,'".$times."' AS times, 'lifans' AS key1, '4生活輔導' AS caption
                            FROM DUAL
                            UNION ALL
                            SELECT '".$class."' AS class, '".$term."' AS term,'".$times."' AS times, 'affans' AS key1, '5行政支援' AS caption
                            FROM DUAL
                            UNION ALL
                            SELECT '".$class."' AS class, '".$term."' AS term,'".$times."' AS times, 'fooans' AS key1, '6餐飲' AS caption
                            FROM DUAL
                            UNION ALL
                            SELECT '".$class."' AS class, '".$term."' AS term,'".$times."' AS times, 'boaans' AS key1, '7住宿' AS caption
                            FROM DUAL
                            ) A INNER JOIN t17tb B ON A.class=B.class AND A.term=B.term AND A.times=B.times
                        INNER JOIN t18tb C ON A.class=C.class AND A.term=C.term AND A.times=C.times
                        WHERE A.class= '".$class."'
                        AND A.term= '".$term."'
                        AND A.times= IFNULL('".$times."',A.times)
                        GROUP BY A.caption, A.key1, C.whoper, C.teaper, C.couper, C.matper, C.lifper, C.affper, C.fooper, C.boaper
                        ORDER BY A.caption
                ";
            $reportlist2Old = DB::select($sql2Old);
            $dataArr2Old=json_decode(json_encode(DB::select($sql2Old)), true);
            //取出全部項目
            if(sizeof($reportlist2Old) != 0) {
                $arraykeys2OLD=array_keys((array)$reportlist2Old[0]);
            }


            //取得 授課滿意度
            $sql2AOld="SELECT DISTINCT ABC.theme, ABC.D5, ABC.D4, ABC.D3, ABC.D2, ABC.D1, ABC.D0
                        FROM (
                                SELECT
                                        A.times, A.key1,
                                        A.theme,
                                        C.okrate AS D5,
                                        (CASE
                                        WHEN A.key1='item1' THEN COUNT((CASE WHEN B.item1='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item2' THEN COUNT((CASE WHEN B.item2='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item3' THEN COUNT((CASE WHEN B.item3='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item4' THEN COUNT((CASE WHEN B.item4='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item5' THEN COUNT((CASE WHEN B.item5='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item6' THEN COUNT((CASE WHEN B.item6='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item7' THEN COUNT((CASE WHEN B.item7='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item8' THEN COUNT((CASE WHEN B.item8='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item9' THEN COUNT((CASE WHEN B.item9='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item10' THEN COUNT((CASE WHEN B.item10='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item11' THEN COUNT((CASE WHEN B.item11='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item12' THEN COUNT((CASE WHEN B.item12='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item13' THEN COUNT((CASE WHEN B.item13='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item14' THEN COUNT((CASE WHEN B.item14='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item15' THEN COUNT((CASE WHEN B.item15='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item16' THEN COUNT((CASE WHEN B.item16='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item17' THEN COUNT((CASE WHEN B.item17='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item18' THEN COUNT((CASE WHEN B.item18='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item19' THEN COUNT((CASE WHEN B.item19='1' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item20' THEN COUNT((CASE WHEN B.item20='1' THEN 1 ELSE NULL  END))
                                        ELSE 0
                                        END) AS D4,
                                        (CASE
                                        WHEN A.key1='item1' THEN COUNT((CASE WHEN B.item1='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item2' THEN COUNT((CASE WHEN B.item2='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item3' THEN COUNT((CASE WHEN B.item3='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item4' THEN COUNT((CASE WHEN B.item4='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item5' THEN COUNT((CASE WHEN B.item5='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item6' THEN COUNT((CASE WHEN B.item6='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item7' THEN COUNT((CASE WHEN B.item7='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item8' THEN COUNT((CASE WHEN B.item8='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item9' THEN COUNT((CASE WHEN B.item9='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item10' THEN COUNT((CASE WHEN B.item10='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item11' THEN COUNT((CASE WHEN B.item11='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item12' THEN COUNT((CASE WHEN B.item12='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item13' THEN COUNT((CASE WHEN B.item13='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item14' THEN COUNT((CASE WHEN B.item14='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item15' THEN COUNT((CASE WHEN B.item15='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item16' THEN COUNT((CASE WHEN B.item16='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item17' THEN COUNT((CASE WHEN B.item17='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item18' THEN COUNT((CASE WHEN B.item18='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item19' THEN COUNT((CASE WHEN B.item19='2' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item20' THEN COUNT((CASE WHEN B.item20='2' THEN 1 ELSE NULL  END))
                                        ELSE 0
                                        END) AS D3,
                                        (CASE
                                        WHEN A.key1='item1' THEN COUNT((CASE WHEN B.item1='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item2' THEN COUNT((CASE WHEN B.item2='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item3' THEN COUNT((CASE WHEN B.item3='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item4' THEN COUNT((CASE WHEN B.item4='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item5' THEN COUNT((CASE WHEN B.item5='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item6' THEN COUNT((CASE WHEN B.item6='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item7' THEN COUNT((CASE WHEN B.item7='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item8' THEN COUNT((CASE WHEN B.item8='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item9' THEN COUNT((CASE WHEN B.item9='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item10' THEN COUNT((CASE WHEN B.item10='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item11' THEN COUNT((CASE WHEN B.item11='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item12' THEN COUNT((CASE WHEN B.item12='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item13' THEN COUNT((CASE WHEN B.item13='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item14' THEN COUNT((CASE WHEN B.item14='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item15' THEN COUNT((CASE WHEN B.item15='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item16' THEN COUNT((CASE WHEN B.item16='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item17' THEN COUNT((CASE WHEN B.item17='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item18' THEN COUNT((CASE WHEN B.item18='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item19' THEN COUNT((CASE WHEN B.item19='3' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item20' THEN COUNT((CASE WHEN B.item20='3' THEN 1 ELSE NULL  END))
                                        ELSE 0
                                        END) AS D2,
                                        (CASE
                                        WHEN A.key1='item1' THEN COUNT((CASE WHEN B.item1='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item2' THEN COUNT((CASE WHEN B.item2='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item3' THEN COUNT((CASE WHEN B.item3='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item4' THEN COUNT((CASE WHEN B.item4='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item5' THEN COUNT((CASE WHEN B.item5='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item6' THEN COUNT((CASE WHEN B.item6='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item7' THEN COUNT((CASE WHEN B.item7='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item8' THEN COUNT((CASE WHEN B.item8='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item9' THEN COUNT((CASE WHEN B.item9='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item10' THEN COUNT((CASE WHEN B.item10='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item11' THEN COUNT((CASE WHEN B.item11='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item12' THEN COUNT((CASE WHEN B.item12='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item13' THEN COUNT((CASE WHEN B.item13='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item14' THEN COUNT((CASE WHEN B.item14='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item15' THEN COUNT((CASE WHEN B.item15='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item16' THEN COUNT((CASE WHEN B.item16='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item17' THEN COUNT((CASE WHEN B.item17='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item18' THEN COUNT((CASE WHEN B.item18='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item19' THEN COUNT((CASE WHEN B.item19='4' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item20' THEN COUNT((CASE WHEN B.item20='4' THEN 1 ELSE NULL  END))
                                        ELSE 0
                                        END) AS D1,
                                        (CASE
                                        WHEN A.key1='item1' THEN COUNT((CASE WHEN B.item1='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item2' THEN COUNT((CASE WHEN B.item2='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item3' THEN COUNT((CASE WHEN B.item3='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item4' THEN COUNT((CASE WHEN B.item4='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item5' THEN COUNT((CASE WHEN B.item5='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item6' THEN COUNT((CASE WHEN B.item6='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item7' THEN COUNT((CASE WHEN B.item7='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item8' THEN COUNT((CASE WHEN B.item8='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item9' THEN COUNT((CASE WHEN B.item9='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item10' THEN COUNT((CASE WHEN B.item10='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item11' THEN COUNT((CASE WHEN B.item11='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item12' THEN COUNT((CASE WHEN B.item12='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item13' THEN COUNT((CASE WHEN B.item13='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item14' THEN COUNT((CASE WHEN B.item14='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item15' THEN COUNT((CASE WHEN B.item15='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item16' THEN COUNT((CASE WHEN B.item16='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item17' THEN COUNT((CASE WHEN B.item17='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item18' THEN COUNT((CASE WHEN B.item18='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item19' THEN COUNT((CASE WHEN B.item19='5' THEN 1 ELSE NULL  END))
                                        WHEN A.key1='item20' THEN COUNT((CASE WHEN B.item20='5' THEN 1 ELSE NULL  END))
                                        ELSE 0
                                        END) AS D0
                                        FROM
                                        (
                                        SELECT class, term, times, 'item1' AS key1, SUBSTRING(theme1,1,2) AS course,
                                        SUBSTRING(theme1,6,LENGTH(theme1)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item2' AS key1, SUBSTRING(theme2,1,2) AS course,
                                        SUBSTRING(theme2,6,LENGTH(theme2)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item3' AS key1, SUBSTRING(theme3,1,2) AS course,
                                        SUBSTRING(theme3,6,LENGTH(theme3)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item4' AS key1, SUBSTRING(theme4,1,2) AS course,
                                        SUBSTRING(theme4,6,LENGTH(theme4)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item5' AS key1, SUBSTRING(theme5,1,2) AS course,
                                        SUBSTRING(theme5,6,LENGTH(theme5)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item6' AS key1, SUBSTRING(theme6,1,2) AS course,
                                        SUBSTRING(theme6,6,LENGTH(theme6)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item7' AS key1, SUBSTRING(theme7,1,2) AS course,
                                        SUBSTRING(theme7,6,LENGTH(theme7)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item8' AS key1, SUBSTRING(theme8,1,2) AS course,
                                        SUBSTRING(theme8,6,LENGTH(theme8)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item9' AS key1, SUBSTRING(theme9,1,2) AS course,
                                        SUBSTRING(theme9,6,LENGTH(theme9)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item10' AS key1, SUBSTRING(theme10,1,2) AS course,
                                        SUBSTRING(theme10,6,LENGTH(theme10)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item11' AS key1, SUBSTRING(theme11,1,2) AS course,
                                        SUBSTRING(theme11,6,LENGTH(theme11)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item12' AS key1, SUBSTRING(theme12,1,2) AS course,
                                        SUBSTRING(theme12,6,LENGTH(theme12)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item13' AS key1, SUBSTRING(theme13,1,2) AS course,
                                        SUBSTRING(theme13,6,LENGTH(theme13)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item14' AS key1, SUBSTRING(theme14,1,2) AS course,
                                        SUBSTRING(theme14,6,LENGTH(theme14)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item15' AS key1, SUBSTRING(theme15,1,2) AS course,
                                        SUBSTRING(theme15,6,LENGTH(theme15)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item16' AS key1, SUBSTRING(theme16,1,2) AS course,
                                        SUBSTRING(theme16,6,LENGTH(theme16)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item17' AS key1, SUBSTRING(theme17,1,2) AS course,
                                        SUBSTRING(theme17,6,LENGTH(theme17)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item18' AS key1, SUBSTRING(theme18,1,2) AS course,
                                        SUBSTRING(theme18,6,LENGTH(theme18)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item19' AS key1, SUBSTRING(theme19,1,2) AS course,
                                        SUBSTRING(theme19,6,LENGTH(theme19)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        UNION ALL
                                        SELECT class, term, times, 'item20' AS key1, SUBSTRING(theme20,1,2) AS course,
                                        SUBSTRING(theme20,6,LENGTH(theme20)) AS theme
                                        FROM t16tb WHERE class='".$class."' AND term='".$term."'
                                        ) A INNER JOIN t17tb B ON A.class=B.class AND A.term=B.term AND A.times=B.times
                                            INNER JOIN t09tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                        WHERE A.course<>''
                                        AND ( A.times BETWEEN '1' AND '1' )
                                        GROUP BY A.times,A.theme,A.key1, C.okrate,C.idno
                                        ) ABC
                        ORDER BY times, key1
                ";
            $reportlist2AOld = DB::select($sql2AOld);
            $dataArr2AOld=json_decode(json_encode(DB::select($sql2AOld)), true);
            //取出全部項目
            if(sizeof($reportlist2AOld) != 0) {
                $arraykeys2AOLD=array_keys((array)$reportlist2AOld[0]);
            }


            //取得 Q4~Q6
            $sql2BOld="SELECT q1,q2,q3
                        FROM t16tb
                       WHERE class='".$class."'
                         AND term='".$term."'
                         AND times=IFNULL('".$times."',times)
                ";
            $reportlist2BOld = DB::select($sql2BOld);
            $dataArr2BOld=json_decode(json_encode(DB::select($sql2BOld)), true);
            //取出全部項目
            if(sizeof($reportlist2BOld) != 0) {
                $arraykeys2BOLD=array_keys((array)$reportlist2BOld[0]);
            }


            //取得 A4~A5
            $sql2COld="SELECT comment1,comment2,comment3
                        FROM t17tb
                       WHERE class='".$class."'
                         AND term='".$term."'
                         AND times=IFNULL('".$times."',times)
                ";
            $reportlist2COld = DB::select($sql2COld);
            $dataArr2COld=json_decode(json_encode(DB::select($sql2COld)), true);
            //取出全部項目
            if(sizeof($reportlist2COld) != 0) {
                $arraykeys2COLD=array_keys((array)$reportlist2COld[0]);
            }

            // 檔案名稱
            $fileName = 'L19B';
            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
            //讀取excel

            //$objPHPExcel = PHPExcel_IOFactory::load($filePath);
            $excelReader = PHPExcel_IOFactory::createReaderForFile($filePath);
            $excelReader->setReadDataOnly(false);
            $excelReader->setIncludeCharts(true);
            $objPHPExcel = $excelReader->load($filePath);


            //A式
            //指定sheet
            $objActSheet = $objPHPExcel->getActiveSheet();
            $objActSheet = $objPHPExcel->getSheet(0);
            $reportlist2Old = json_decode(json_encode($reportlist2Old), true);
            $reportlist2AOld = json_decode(json_encode($reportlist2AOld), true);
            $reportlist2BOld = json_decode(json_encode($reportlist2BOld), true);
            $reportlist2COld = json_decode(json_encode($reportlist2COld), true);

            //dd($reportlist);
            if(empty($dataArrTitleOld)){
                $objActSheet->setCellValue('A1', '成效評估調查統計表');
            }else{
                $objActSheet->setCellValue('A1', $dataArrTitleOld[0]['TITLE']);
            };

            if(empty($dataArrRemark1Old)){
                $objActSheet->setCellValue('A3', '受訓人數：人 回收份數：人 日期：');
            }else{
                $objActSheet->setCellValue('A3', $dataArrRemark1Old[0]['TITLE_OLD'].str_replace('~0','~',str_replace('：0','：',$dataArrDate[0]['sdate_edate_old'])));
            }

            //活動滿意度
            if(sizeof($reportlist2Old) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys2OLD); $i++) {
                    for ($j=0; $j < sizeof($reportlist2Old); $j++) {
                        $NameFromNumber=$this->getNameFromNumber($i+3); //C
                        if($i>0){
                            $objActSheet->setCellValue($NameFromNumber.($j+5), $reportlist2Old[$j][$arraykeys2OLD[$i]].'人');
                        } else {
                            $objActSheet->setCellValue($NameFromNumber.($j+5), $reportlist2Old[$j][$arraykeys2OLD[$i]].'%');
                        }
                    }
                }
            }

            $k=1;
            //授課滿意度
            if(sizeof($reportlist2AOld) != 0) {
                //項目數量迴圈
                $k=1;
                for ($i=0; $i < sizeof($arraykeys2AOLD); $i++) {
                    for ($j=0; $j < sizeof($reportlist2AOld); $j++) {
                        $NameFromNumber=$this->getNameFromNumber($i+2); //B
                        if($i>1){
                            $objActSheet->setCellValue($NameFromNumber.($j+5+sizeof($reportlist2Old)), $reportlist2AOld[$j][$arraykeys2AOLD[$i]].'人');
                        }elseif($i==1){
                            $objActSheet->setCellValue($NameFromNumber.($j+5+sizeof($reportlist2Old)), $reportlist2AOld[$j][$arraykeys2AOLD[$i]].'%');
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+5+sizeof($reportlist2Old)), $k.'.'.$reportlist2AOld[$j][$arraykeys2AOLD[$i]]);
                            $k++;
                        }

                    }
                }
            }
            //dd($k);

            //授課滿意度
            //有20固定項目，有才顯示欄位，最後一欄顯示框線
            if($k<=19 && $k>=4){
                for ($i=0; $i< (20-$k+1); $i++){
                    if($i==0){
                        $objActSheet->getRowDimension(31-$i)->setRowHeight(1);
                    }else{
                        $objActSheet->getRowDimension(31-$i)->setVisible(false);
                    }
                }
            }

            //Q4~Q6
            if(sizeof($reportlist2BOld) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys2BOLD); $i++) {
                    for ($j=0; $j < sizeof($reportlist2BOld); $j++) {
                        if($i==0){
                            $objActSheet->setCellValue('A33', '四、'.$reportlist2BOld[$j][$arraykeys2BOLD[$i]]);
                        }
                        if($i==1){
                            $objActSheet->setCellValue('A36', '五、'.$reportlist2BOld[$j][$arraykeys2BOLD[$i]]);
                        }
                        if($i==2){
                            $objActSheet->setCellValue('A39', '六、'.$reportlist2BOld[$j][$arraykeys2BOLD[$i]]);
                        }
                    }
                }
            }

            //A4~A6
            $A34=1;
            $comments34 ='';
            $A37=1;
            $comments37 ='';
            $A40=1;
            $comments40 ='';
            if(sizeof($reportlist2COld) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys2COLD); $i++) {
                    for ($j=0; $j < sizeof($reportlist2COld); $j++) {
                        if($i==0 && $reportlist2COld[$j][$arraykeys2COLD[$i]] <> ''){
                            $comments34 = $comments34.$A34.'.'.$reportlist2COld[$j][$arraykeys2COLD[$i]]."\n";
                            $A34++;
                        }
                        if($i==1 && $reportlist2COld[$j][$arraykeys2COLD[$i]] <> ''){
                            $comments37 = $comments37.$A37.'.'.$reportlist2COld[$j][$arraykeys2COLD[$i]]."\n";
                            $A37++;
                        }
                        if($i==2 && $reportlist2COld[$j][$arraykeys2COLD[$i]] <> ''){
                            $comments40 = $comments40.$A40.'.'.$reportlist2COld[$j][$arraykeys2COLD[$i]]."\n";
                            $A40++;
                        }
                    }
                }
            }
            $objActSheet->setCellValue('A34', $comments34);
            $objActSheet->getRowDimension(34)->setRowHeight(((strlen($comments34)/80+$A34)*14));
            $objActSheet->getStyle('A34')->getAlignment()->setWrapText(true);

            $objActSheet->setCellValue('A37', $comments37);
            $objActSheet->getRowDimension(37)->setRowHeight(((strlen($comments37)/80+$A37)*14));
            $objActSheet->getStyle('A37')->getAlignment()->setWrapText(true);

            $objActSheet->setCellValue('A40', $comments40);
            $objActSheet->getRowDimension(40)->setRowHeight(((strlen($comments40)/80+$A40)*14));
            $objActSheet->getStyle('A40')->getAlignment()->setWrapText(true);

            // 設定下載 Excel 的檔案名稱
            $outputname="訓練成效評估結果統計圖表(90~92)-舊版.xlsx";
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"3",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
