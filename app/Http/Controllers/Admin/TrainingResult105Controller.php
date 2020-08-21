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

class TrainingResult105Controller extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('training_result_105', $user_group_auth)){
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
        //return view('admin/training_result_105/list');
        $classArr = $this->getclass();
        $result = '';
        return view('admin/training_result_105/list', compact('classArr', 'result'));
    }

    // 搜尋下拉『班別』
    public function getclass() {
            $sql = "SELECT DISTINCT t53tb.class, t01tb.name
                      FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                     WHERE t53tb.times<>'' AND SUBSTRING(t53tb.class,1,3)>='105'
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
    訓練成效評估結果統計圖表(105) CSDIR5025
    參考Tables:
    使用範本:L2.xlsx
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

        if ($times ==''){
            $sqlPara = '';
        }else{
            $sqlPara = " AND t95tb.times='".$times."' ";
        }

    //固定題目統計
    $sql="SELECT
                    (CASE
                    WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=5 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=5 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=5 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=5 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=5 THEN 1 ELSE NULL  END))
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
                    WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=4 THEN 1 ELSE NULL  END))
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
                    WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=3 THEN 1 ELSE NULL  END))
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
                    WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=2 THEN 1 ELSE NULL  END))
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
                    WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=1 THEN 1 ELSE NULL  END))
                    WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=1 THEN 1 ELSE NULL  END))

                    ELSE 0
                    END),
                    (CASE
                    WHEN A.key1='q11' THEN SUM((CASE WHEN q11 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q12' THEN SUM((CASE WHEN q12 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q13' THEN SUM((CASE WHEN q13 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q14' THEN SUM((CASE WHEN q14 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q15' THEN SUM((CASE WHEN q15 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q21' THEN SUM((CASE WHEN q21 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q22' THEN SUM((CASE WHEN q22 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q23' THEN SUM((CASE WHEN q23 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q31' THEN SUM((CASE WHEN q31 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q32' THEN SUM((CASE WHEN q32 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    WHEN A.key1='q33' THEN SUM((CASE WHEN q33 IN (1,2,3,4,5) THEN 0 ELSE 1  END))
                    ELSE 0
                    END)
                    FROM
                    (
                        SELECT  'q11' AS key1, 'Q11.我認為本研習班所訂研習目標符合機關的需求' AS caption
                        UNION ALL
                        SELECT  'q12' AS key1, 'Q12.我認為本研習班的課程內容有助於達成研習目標' AS caption
                        UNION ALL
                        SELECT  'q13' AS key1, 'Q13.我認為本研習班的課程內容與個人辦理之業務具有相關性' AS caption
                        UNION ALL
                        SELECT  'q14' AS key1, 'Q14.我認為參加本研習班對增進個人工作所需知能有助益' AS caption
                        UNION ALL
                        SELECT  'q15' AS key1, 'Q15.我樂意將本研習班推薦給其他同仁參加' AS caption
                        UNION ALL
                        SELECT  'q21' AS key1, 'Q21.我對本研習班各階段的學習活動都能夠投入' AS caption
                        UNION ALL
                        SELECT  'q22' AS key1, 'Q22.我能夠向其他人轉述本研習班課程所學知識內容' AS caption
                        UNION ALL
                        SELECT  'q23' AS key1, 'Q23.我認為在工作中可以運用本研習班課程所學' AS caption
                        UNION ALL
                        SELECT  'q31' AS key1, 'Q31.我認為本研習班輔導員的服務有助於學習' AS caption
                        UNION ALL
                        SELECT  'q32' AS key1, 'Q32.我認為本研習班的紙本或數位教材及相關資料有助於學習' AS caption
                        UNION ALL
                        SELECT  'q33' AS key1, 'Q33.我認為本研習班的教學設施有助於學習' AS caption
                    ) A
                    INNER JOIN t95tb t95tb ON 1 = 1
                WHERE t95tb.class= '".$class."'
                AND t95tb.term= '".$term."'
                ".$sqlPara.
                "GROUP BY A.caption, A.key1
                ORDER BY A.caption
                    ";


        $reportlist = DB::select($sql);
        $dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        if ($times ==''){
            $sqlPara = '';
        }else{
            $sqlPara = " AND t53tb.times='".$times."' ";
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
                ".$sqlPara.
                "ORDER BY t53tb.class DESC

        ";
    $reportlistTitle = DB::select($sqlTitle);
    $dataArrTitle=json_decode(json_encode(DB::select($sqlTitle)), true);

    //取得訓練, Begin Date & End Date
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

    if ($times ==''){
        $sqlPara = '';
    }else{
        $sqlPara = " AND times='".$times."' ";
    }        
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
            ".$sqlPara."
        ) A LEFT JOIN
        ( SELECT COUNT(*) AS lngBack_Count
            FROM t95tb
            WHERE class='".$class."'
            AND term= '".$term."'
            ".$sqlPara."
        ) B ON 1 = 1
        ";
    $reportlistRemark1 = DB::select($sqlRemark1);
    $dataArrRemark1=json_decode(json_encode(DB::select($sqlRemark1)), true);

    if ($times ==''){
        $sqlPara = '';
    }else{
        $sqlPara = " AND times='".$times."' ";
    }    
    //A15~A16:REMARK2
    //2.本次研習學員滿意度總平均為94.85分，其中「研習規劃」(佔23％)滿意度平均為93.84、「學習投入」(佔23％)滿意度平均為93.15、
    //「學習輔導」(佔10％)滿意度平均為91.53、「講座授課」(佔44％)滿意度平均為97.02，「講座授課」統計結果詳附表。
    $sqlRemark2="SELECT CONCAT('2.本次研習學員滿意度總平均為', totper, '分，') AS REMARK2A,
                        CONCAT('其中「研習規劃」(佔23％)滿意度平均為', conper,
                               '、「學習投入」(佔23％)滿意度平均為', attper,
								'、') AS REMARK2B,
                        CONCAT('「學習輔導」(佔10％)滿意度平均為', worper,
									'、「講座授課」(佔44％)滿意度平均為', teaper,
									'，「講座授課」統計結果詳附表。') AS REMARK2C
                    FROM t57tb
                    WHERE class='".$class."'
                    AND term= '".$term."'
                    ".$sqlPara."
    ";
    $reportlistRemark2 = DB::select($sqlRemark2);
    $dataArrRemark2=json_decode(json_encode(DB::select($sqlRemark2)), true);

    if ($times ==''){
        $sqlPara = '';
    }else{
        $sqlPara = " AND t95tb.times='".$times."' ";
    }       
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
                WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=5 THEN 1 ELSE NULL  END))
                WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=5 THEN 1 ELSE NULL  END))
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
                WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=4 THEN 1 ELSE NULL  END))
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
                WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=3 THEN 1 ELSE NULL  END))
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
                WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=2 THEN 1 ELSE NULL  END))
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
                WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=1 THEN 1 ELSE NULL  END))
                WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=1 THEN 1 ELSE NULL  END))
                WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=1 THEN 1 ELSE NULL  END))
                WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=1 THEN 1 ELSE NULL  END))
                WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=1 THEN 1 ELSE NULL  END))
                WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=1 THEN 1 ELSE NULL  END))
                WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=1 THEN 1 ELSE NULL  END))
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
                SELECT  'q15' AS key1 FROM DUAL
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
                INNER JOIN t95tb t95tb ON 1 = 1
            WHERE t95tb.class= '".$class."'
            AND t95tb.term= '".$term."'
            ".$sqlPara."
            GROUP BY A.key1
                ) T

        ";
        $reportlist2STDDEV = DB::select($sqlRemark2STDDEV);
        $dataArr2STDDEV=json_decode(json_encode(DB::select($sqlRemark2STDDEV)), true);

        if ($times ==''){
            $sqlPara = '';
        }else{
            $sqlPara = " AND t95tb.times='".$times."' ";
        }              
        //A18:檢核REMARK3:N欄 平均數
        $sqlRemark3N="SELECT ROUND((T.D3*5+T.F3*4+T.H3*3+T.J3*2+T.L3*1)/(T.D3+T.F3+T.H3+T.J3+T.L3),2) AS N
                FROM (
                        SELECT
                        (CASE
                        WHEN A.key1='q11' THEN COUNT((CASE WHEN q11=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q12' THEN COUNT((CASE WHEN q12=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q13' THEN COUNT((CASE WHEN q13=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q14' THEN COUNT((CASE WHEN q14=5 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=5 THEN 1 ELSE NULL  END))
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
                        WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=4 THEN 1 ELSE NULL  END))
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
                        WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=3 THEN 1 ELSE NULL  END))
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
                        WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=2 THEN 1 ELSE NULL  END))
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
                        WHEN A.key1='q15' THEN COUNT((CASE WHEN q15=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q21' THEN COUNT((CASE WHEN q21=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q22' THEN COUNT((CASE WHEN q22=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q23' THEN COUNT((CASE WHEN q23=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q31' THEN COUNT((CASE WHEN q31=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q32' THEN COUNT((CASE WHEN q32=1 THEN 1 ELSE NULL  END))
                        WHEN A.key1='q33' THEN COUNT((CASE WHEN q33=1 THEN 1 ELSE NULL  END))
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
            SELECT  'q15' AS key1 FROM DUAL
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
            INNER JOIN t95tb t95tb ON 1 = 1
            WHERE t95tb.class= '".$class."'
            AND t95tb.term= '".$term."'
            ".$sqlPara."
            GROUP BY A.key1
            ) T
        ";
        $reportlistRemark3N = DB::select($sqlRemark3N);
        $dataArrRemark3N=json_decode(json_encode(DB::select($sqlRemark3N)), true);
        //取出全部項目
        if(sizeof($reportlistRemark3N) != 0) {
        $arraykeysRemark3N=array_keys((array)$reportlistRemark3N[0]);
        }


        if ($times ==''){
            $sqlPara = '';
        }else{
            $sqlPara = " AND t54tb.times='".$times."' ";
        }          
    //講座統計
    //講者
    $sql1Name="SELECT D.cname, D.name,
                      CASE C.anstype WHEN 'ans1' THEN '教學技法'
                                     WHEN 'ans2' THEN '教學內容'
                                     WHEN 'ans3' THEN '教學態度'
                      END asntype,
                      C.ansname,
                      0 AS P_COUNT,
                      0 AS COL1, 0 AS COL2, 0 AS COL3, 0 AS COL4
                FROM (SELECT anstype, ans, ansname
                            FROM (
                            SELECT 'ans1' AS anstype
                            UNION ALL
                            SELECT 'ans2' AS anstype
                            UNION ALL
						    SELECT 'ans3' AS anstype
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
                        CROSS JOIN (SELECT m01tb.cname, t06tb.name, t54tb.class, t54tb.term, t54tb.course, t54tb.times, t54tb.idno
                                    FROM t54tb  LEFT JOIN m01tb ON t54tb.idno=m01tb.idno
                                                LEFT JOIN t06tb ON t54tb.class = t06tb.class
                                                                AND t54tb.term = t06tb.term
                                                                AND t54tb.course = t06tb.course
                                    WHERE t54tb.class= '".$class."'
                                    AND t54tb.term= '".$term."'
                                    ".$sqlPara."
                                    ) D
                ORDER BY D.course, D.idno , C.anstype, C.ans DESC
        ";
    $reportlist1Name = DB::select($sql1Name);
    $dataArr1Name=json_decode(json_encode(DB::select($sql1Name)), true);
    //取出全部項目
    if(sizeof($reportlist1Name) != 0) {
        $arraykeys1Name=array_keys((array)$reportlist1Name[0]);
    }

    if ($times ==''){
        $sqlPara = '';
    }else{
        $sqlPara = " AND t56tb.times='".$times."' ";
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
                    count(CASE WHEN ans2=1 THEN 1 ELSE NULL END ) AS ans2q1,
					count(CASE WHEN ans3=5 THEN 1 ELSE NULL END ) AS ans3q5,
					count(CASE WHEN ans3=4 THEN 1 ELSE NULL END ) AS ans3q4,
					count(CASE WHEN ans3=3 THEN 1 ELSE NULL END ) AS ans3q3,
					count(CASE WHEN ans3=2 THEN 1 ELSE NULL END ) AS ans3q2,
					count(CASE WHEN ans3=1 THEN 1 ELSE NULL END ) AS ans3q1
            FROM t56tb
            WHERE t56tb.class= '".$class."'
            AND t56tb.term= '".$term."'
            ".$sqlPara."
            GROUP BY t56tb.class, t56tb.term, t56tb.times, t56tb.course, t56tb.idno
            ORDER BY t56tb.course, t56tb.idno
        ";
    $reportlist1 = DB::select($sql1);
    $dataArr1=json_decode(json_encode(DB::select($sql1)), true);
    //取出全部項目
    if(sizeof($reportlist1) != 0) {
        $arraykeys1=array_keys((array)$reportlist1[0]);
    }

    if ($times ==''){
        $sqlPara = '';
    }else{
        $sqlPara = " AND times='".$times."' ";
    }       
    //問答題
    $sql2="	 SELECT  t95tb.note
               FROM  t95tb
              WHERE class= '".$class."'
                AND term= '".$term."'
                ".$sqlPara."
                AND note <> '' AND note IS NOT NULL
              ORDER BY serno
            ";
    $reportlist2 = DB::select($sql2);
    $dataArr2=json_decode(json_encode(DB::select($sql2)), true);
    //取出全部項目
    if(sizeof($reportlist2) != 0) {
    $arraykeys2=array_keys((array)$reportlist2[0]);
    }


    // 檔案名稱
    $fileName = 'L2';
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
        $objActSheet->setCellValue('A18', '1.本次調查問卷計發出0份，共回收0份，回收率0.00%。');
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
        $objActSheet->getCell('A18')->setValue($objRichText);
    }

    //$objActSheet->setCellValue('A16', str_replace('%AVG%',$dataArrAVG[0]['AVG'],$dataArrRemark2[0]['REMARK2A']));
    //$objActSheet->setCellValue('A17', $dataArrRemark2[0]['REMARK2B']);
    if(empty($dataArrRemark2)){
        $objActSheet->setCellValue('A19', '2.本次研習學員滿意度總平均為0.00分，其中「研習規劃」滿意度平均為0.00、「對公務執行有助益」者0.00、「學習輔導」滿意度平均為0.00');
        $objActSheet->setCellValue('A20', '、「講座授課」滿意度平均為0.00，「講座授課」統計結果詳附表。');
    }else{
        $objRichText2 = new PHPExcel_RichText();
        $objRichText2->createText($dataArrRemark2[0]['REMARK2A']);
        $objFont = $objRichText2->createTextRun($dataArrRemark2[0]['REMARK2B']);
        $objFont->getFont()->setBold(false);
        $objFont->getFont()->setUnderline(false);
        $objFont->getFont()->setName("標楷體");
        $objFont->getFont()->setSize("12");
        $objActSheet->getCell('A19')->setValue($objRichText2);
        $objActSheet->setCellValue('A20', $dataArrRemark2[0]['REMARK2C']);
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
                //4開始
                $objActSheet->setCellValue($lineName.($j+4), $reportlist[$j][$arraykeys[$i]]);
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
        $REMARK3 = '3.本次問卷統計結果各題滿意度平均數均無偏高或偏低之情形。(均位於總平均數2個標準差範圍內)。';
    }
    if($strMax!=''){
        $REMARK3 = '3.本次問卷統計結果第'.($strMax).'題滿意度平均數有偏高情形（高於兩個標準差範圍）';
    }
    if($strMin!=''){
        $REMARK3 = $REMARK3.'3.本次問卷統計結果第'.($strMin).'題滿意度平均數有偏低情形（低於兩個標準差範圍）';
    }
    $objActSheet->setCellValue('A21', $REMARK3);

    //固定題目統計圖表
    //指定sheet
    $objActSheet = $objPHPExcel->getSheet(1);
    if(empty($dataArrTitle)){
        $title = new PHPExcel_Chart_Title('訓練成效評估結果統計圖');
    }else{
        $title = new PHPExcel_Chart_Title(str_replace('訓練成效評估結果統計表','訓練成效評估結果統計圖',$dataArrTitle[0]['TITLE']));
    }
    $X_title = new PHPExcel_Chart_Title('各題次問項內容');
    $Y_title = new PHPExcel_Chart_Title('百分位數');
    $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', '固定題目統計!$B$3', NULL, 1));
    $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', '固定題目統計!$C$4:$C$14', NULL, 100));
    $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', '固定題目統計!$O$4:$O$14', NULL, 100));
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
            // =IF(ISERROR(E4/SUM(E4:E8)*100),0,E4/SUM(E4:E8)*100)
            //$objActSheet->setCellValue('F'.($j+4), '=E'.($j+4).'/SUM(E'.(($k*5)+4).':E'.(($k*5)+8).')*100');
            $objActSheet->setCellValue('F'.($j+4), '=IF(ISERROR(E'.($j+4).'/SUM(E'.(($k*5)+4).':E'.(($k*5)+8).')*100),0,E'.($j+4).'/SUM(E'.(($k*5)+4).':E'.(($k*5)+8).')*100)');
            if((($j+1)%5)==0){
                $k++;
            }
            if((($j+1)%15)==0){
                //合併, 帶入公式
                $objActSheet->mergeCells('A'.(($l*15)+4).':A'.(($l*15)+18));
                $objActSheet->mergeCells('B'.(($l*15)+4).':B'.(($l*15)+18));

                $objActSheet->mergeCells('C'.(($l*15)+4).':C'.(($l*15)+8));
                $objActSheet->mergeCells('C'.(($l*15)+4+5).':C'.(($l*15)+8+5));
                $objActSheet->mergeCells('C'.(($l*15)+4+5+5).':C'.(($l*15)+8+5+5));

                $objActSheet->mergeCells('G'.(($l*15)+4).':G'.(($l*15)+8));
                $objActSheet->mergeCells('G'.(($l*15)+4+5).':G'.(($l*15)+8+5));
                $objActSheet->mergeCells('G'.(($l*15)+4+5+5).':G'.(($l*15)+8+5+5));
                //帶入公式=SUM(E4:E8)
                $objActSheet->setCellValue('G'.(($l*15)+4), '=SUM(E'.(($l*15)+4).':E'.(($l*15)+8).')');
                $objActSheet->setCellValue('G'.(($l*15)+4+5), '=SUM(E'.(($l*15)+4+5).':E'.(($l*15)+8+5).')');
                $objActSheet->setCellValue('G'.(($l*15)+4+5+5), '=SUM(E'.(($l*15)+4+5+5).':E'.(($l*15)+8+5+5).')');

                //特別注意此功能大部份同CSDIR5023(102年度)僅在範本N10.xlsx滿意度算法不同, 與在下列講座滿意度算法不同
                //此有包含等距的權重
                //帶入公式=SUM(E4*100+E5*80+E6*60+E7*40+E8*20)/(SUM(E4:E8)*100)*100
                      //帶入公式=F4+F5
                      //=IF(ISERROR(SUM(E4*100+E5*80+E6*60+E7*40+E8*20)/(SUM(E4:E8)*100)*100),"",SUM(E4*100+E5*80+E6*60+E7*40+E8*20)/(SUM(E4:E8)*100)*100)
                $objActSheet->mergeCells('H'.(($l*15)+4).':H'.(($l*15)+8));
                $objActSheet->mergeCells('H'.(($l*15)+4+5).':H'.(($l*15)+8+5));
                $objActSheet->mergeCells('H'.(($l*15)+4+5+5).':H'.(($l*15)+8+5+5));
                //$objActSheet->setCellValue('H'.(($l*15)+4), '=SUM(E'.(($l*15)+4).'*100+E'.(($l*15)+5).'*80+E'.(($l*15)+6).'*60+E'.(($l*15)+7).'*40+E'.(($l*15)+8).'*20)/(SUM(E'.(($l*15)+4).':E'.(($l*15)+8).')*100)*100');
                //$objActSheet->setCellValue('H'.(($l*15)+4+5), '=SUM(E'.(($l*15)+4+5).'*100+E'.(($l*15)+5+5).'*80+E'.(($l*15)+6+5).'*60+E'.(($l*15)+7+5).'*40+E'.(($l*15)+8+5).'*20)/(SUM(E'.(($l*15)+4+5).':E'.(($l*15)+8+5).')*100)*100');
                //$objActSheet->setCellValue('H'.(($l*15)+4+5+5), '=SUM(E'.(($l*15)+4+5+5).'*100+E'.(($l*15)+5+5+5).'*80+E'.(($l*15)+6+5+5).'*60+E'.(($l*15)+7+5+5).'*40+E'.(($l*15)+8+5+5).'*20)/(SUM(E'.(($l*15)+4+5+5).':E'.(($l*15)+8+5+5).')*100)*100');
                $objActSheet->setCellValue('H'.(($l*15)+4), '=IF(ISERROR(SUM(E'.(($l*15)+4).'*100+E'.(($l*15)+5).'*80+E'.(($l*15)+6).'*60+E'.(($l*15)+7).'*40+E'.(($l*15)+8).'*20)/(SUM(E'.(($l*15)+4).':E'.(($l*15)+8).')*100)*100),0,SUM(E'.(($l*15)+4).'*100+E'.(($l*15)+5).'*80+E'.(($l*15)+6).'*60+E'.(($l*15)+7).'*40+E'.(($l*15)+8).'*20)/(SUM(E'.(($l*15)+4).':E'.(($l*15)+8).')*100)*100)');
                $objActSheet->setCellValue('H'.(($l*15)+4+5), '=IF(ISERROR(SUM(E'.(($l*15)+4+5).'*100+E'.(($l*15)+5+5).'*80+E'.(($l*15)+6+5).'*60+E'.(($l*15)+7+5).'*40+E'.(($l*15)+8+5).'*20)/(SUM(E'.(($l*15)+4+5).':E'.(($l*15)+8+5).')*100)*100),0,SUM(E'.(($l*15)+4+5).'*100+E'.(($l*15)+5+5).'*80+E'.(($l*15)+6+5).'*60+E'.(($l*15)+7+5).'*40+E'.(($l*15)+8+5).'*20)/(SUM(E'.(($l*15)+4+5).':E'.(($l*15)+8+5).')*100)*100)');
                $objActSheet->setCellValue('H'.(($l*15)+4+5+5), '=IF(ISERROR(SUM(E'.(($l*15)+4+5+5).'*100+E'.(($l*15)+5+5+5).'*80+E'.(($l*15)+6+5+5).'*60+E'.(($l*15)+7+5+5).'*40+E'.(($l*15)+8+5+5).'*20)/(SUM(E'.(($l*15)+4+5+5).':E'.(($l*15)+8+5+5).')*100)*100),0,SUM(E'.(($l*15)+4+5+5).'*100+E'.(($l*15)+5+5+5).'*80+E'.(($l*15)+6+5+5).'*60+E'.(($l*15)+7+5+5).'*40+E'.(($l*15)+8+5+5).'*20)/(SUM(E'.(($l*15)+4+5+5).':E'.(($l*15)+8+5+5).')*100)*100)');

                $objActSheet->mergeCells('I'.(($l*15)+4).':I'.(($l*15)+18));
                //帶入公式=(H4+H9+H14)/3
                $objActSheet->setCellValue('I'.(($l*15)+4), '=(H'.(($l*15)+4).'+H'.(($l*15)+9).'+H'.(($l*15)+14).')/3');

                //圖表使用
                //帶入 講者(課程)
                $objActSheet->setCellValue('J'.($l+4), $reportlist1Name[(($l*15)+4)][$arraykeys1Name[0]].'('.$reportlist1Name[(($l*15)+4)][$arraykeys1Name[1]].')');
                //帶入公式=I4
                $objActSheet->setCellValue('K'.($l+4), '=I'.(($l*15)+4));

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
            $objActSheet->setCellValue('I2', '=AVERAGE(I4:I'.((($l-1)*15)+18).')');
        } else {
            $objActSheet->setCellValue('I2', '=AVERAGE(I4:I18)');
        }

    }

    //dd(sizeof($reportlist1));
    //人數
    if(sizeof($reportlist1) != 0) {
        for ($j=0; $j < sizeof($reportlist1); $j++) {
            for ($i=0; $i < sizeof($arraykeys1); $i++) {
                //$objActSheet->setCellValue('E'.($j*10+4+$i), $reportlist1[$j][$arraykeys1[$i]]);
                $objActSheet->setCellValue('E'.($j*15+4+$i), $reportlist1[$j][$arraykeys1[$i]]);
            }
        }
    }


    //講座統計圖表
    //指定sheet
    $objActSheet = $objPHPExcel->getSheet(3);
    if(empty($dataArrTitle)){
        $title = new PHPExcel_Chart_Title('訓練成效評估結果統計圖（講座滿意度）');
    }else{
        $title = new PHPExcel_Chart_Title(str_replace('訓練成效評估結果統計表','訓練成效評估結果統計圖（講座滿意度）',$dataArrTitle[0]['TITLE']));
    }
    $X_title = new PHPExcel_Chart_Title('各題次問項內容');
    $Y_title = new PHPExcel_Chart_Title('百分位數');
    $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', '講座統計!$B$3', NULL, 1));
    $dsvnum = 0;
    if(sizeof($reportlist1Name)>0){
        $dsvnum = ((sizeof($reportlist1Name) / 15) - 1) ;
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

    $objActSheet = $objPHPExcel->getSheet(0);

    $RptBasic = new \App\Rptlib\RptBasic();
    $RptBasic->exportfile($objPHPExcel,"3",$request->input('doctype'),"訓練成效評估結果統計圖表(105)");
    //$obj: entity of file
    //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
    //$doctype:1.ooxml 2.odf
    //$filename:filename 

    }
}
