<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
//use PhpOffice\PhpSpreadsheet\IOFactory;
//use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
//use PhpOffice\PhpSpreadsheet\Style\Border;
//use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPEXcel_RichText;
use PHPExcel_Chart;
use PHPExcel_Chart_Title;
use PHPExcel_Chart_Layout;
use PHPExcel_Chart_Axis;


class SiteSurvey101Controller extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_survey_101', $user_group_auth)){
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
        return view('admin/site_survey_101/list');
    }

    /*
    第幾次調查
    */
    public function getTimeBySite(Request $request) {
        $yerly= $request->input('yerly');
        $sql = " SELECT times
                   FROM t73tb
                  WHERE year= '".$yerly."'
                    AND times<>''
                  GROUP BY times
                  ORDER BY times";
        $classArr = DB::select($sql);
        return $classArr;
    }

    /*
    場地問卷及統計表(101) CSDIR6151
    參考Tables:
    使用範本:N12.xlsx, N12.docx
    N12.docx範本是空白問卷
    N12.xlsx範本是場地問卷及統計表
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //1:空白問卷 ,2:場地問卷及統計表
        $radiotype= $request->input('radiotype');

        if($radiotype=='2'){
            //年度
            $yerly= $request->input('yerly');
            //第幾次調查
            $times= $request->input('times');

            //固定題目統計
            $sql="SELECT
                        A.caption,
                        (CASE
                            WHEN A.key1='q1' THEN COUNT((CASE WHEN q1=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q2' THEN COUNT((CASE WHEN q2=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q3' THEN COUNT((CASE WHEN q3=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q4' THEN COUNT((CASE WHEN q4=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q5' THEN COUNT((CASE WHEN q5=5 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q6' THEN COUNT((CASE WHEN q6=5 THEN 1 ELSE NULL  END))
                            ELSE 0
                        END) A5,
                        (CASE
                            WHEN A.key1='q1' THEN COUNT((CASE WHEN q1=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q2' THEN COUNT((CASE WHEN q2=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q3' THEN COUNT((CASE WHEN q3=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q4' THEN COUNT((CASE WHEN q4=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q5' THEN COUNT((CASE WHEN q5=4 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q6' THEN COUNT((CASE WHEN q6=4 THEN 1 ELSE NULL  END))
                            ELSE 0
                        END) A4,
                        (CASE
                            WHEN A.key1='q1' THEN COUNT((CASE WHEN q1=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q2' THEN COUNT((CASE WHEN q2=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q3' THEN COUNT((CASE WHEN q3=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q4' THEN COUNT((CASE WHEN q4=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q5' THEN COUNT((CASE WHEN q5=3 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q6' THEN COUNT((CASE WHEN q6=3 THEN 1 ELSE NULL  END))
                            ELSE 0
                        END) AS A3,
                        (CASE
                            WHEN A.key1='q1' THEN COUNT((CASE WHEN q1=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q2' THEN COUNT((CASE WHEN q2=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q3' THEN COUNT((CASE WHEN q3=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q4' THEN COUNT((CASE WHEN q4=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q5' THEN COUNT((CASE WHEN q5=2 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q6' THEN COUNT((CASE WHEN q6=2 THEN 1 ELSE NULL  END))
                            ELSE 0
                        END) AS A2,
                        (CASE
                            WHEN A.key1='q1' THEN COUNT((CASE WHEN q1=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q2' THEN COUNT((CASE WHEN q2=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q3' THEN COUNT((CASE WHEN q3=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q4' THEN COUNT((CASE WHEN q4=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q5' THEN COUNT((CASE WHEN q5=1 THEN 1 ELSE NULL  END))
                            WHEN A.key1='q6' THEN COUNT((CASE WHEN q6=1 THEN 1 ELSE NULL  END))
                            ELSE 0
                        END) AS A1
                    FROM
                        (
                            SELECT LPAD('".$yerly."',3,'0') AS year,'".$times."' AS times, 'q1' AS key1, '本學院網站提供場地設施介紹資訊及相關輔助說明之完整性' AS caption
                                    FROM DUAL
                            UNION ALL
                            SELECT LPAD('".$yerly."',3,'0') AS year,'".$times."' AS times, 'q2' AS key1, '本學院網路預約場地申辦便利性及回覆速度' AS caption
                                    FROM DUAL
                            UNION ALL
                            SELECT LPAD('".$yerly."',3,'0') AS year,'".$times."' AS times, 'q3' AS key1, '會館場地業務接洽人員服務態度及行政效率' AS caption
                                    FROM DUAL
                            UNION ALL
                            SELECT LPAD('".$yerly."',3,'0') AS year,'".$times."' AS times, 'q4' AS key1, '場地設施及視聽教學設備功能' AS caption
                                    FROM DUAL
                            UNION ALL
                            SELECT LPAD('".$yerly."',3,'0') AS year,'".$times."' AS times, 'q5' AS key1, '會館場地現場工作人員服務態度及行政效率' AS caption
                                    FROM DUAL
                            UNION ALL
                            SELECT LPAD('".$yerly."',3,'0') AS year,'".$times."' AS times, 'q6' AS key1, '會館整體服務品質' AS caption
                                    FROM DUAL
                        ) A
                    INNER JOIN t73tb B ON A.year=B.year AND A.times=B.times
                    GROUP BY  A.key1 , A.caption
                    ORDER BY A.key1 ";

            $reportlist = DB::select($sql);
            $dataArr=json_decode(json_encode(DB::select($sql)), true);
            //取出全部項目
            if(sizeof($reportlist) != 0) {
                $arraykeys=array_keys((array)$reportlist[0]);
            }


            //開放性問題
            $sql2="SELECT comment FROM t73tb
                    WHERE year= LPAD('".$yerly."',3,'0')
                    AND times= '".$times."'
                    AND comment<>''
                    ORDER BY serno  ";
            $reportlist2 = DB::select($sql2);
            $dataArr2=json_decode(json_encode(DB::select($sql2)), true);
            //取出全部項目
            if(sizeof($reportlist2) != 0) {
                $arraykeys2=array_keys((array)$reportlist2[0]);
            }

            //基本資料統計
            $sql3="SELECT CONCAT('政府機關（',dept_1,'及', ROUND(dept_1/(dept_1+dept_2+dept_3+dept_4+dept_5+dept_6) * 100,2) ,'%）'),
                            CONCAT('公營事業機構（',dept_2,'及', ROUND(dept_2/(dept_1+dept_2+dept_3+dept_4+dept_5+dept_6) * 100,2) ,'%）'),
                            CONCAT('學校（',dept_3,'及', ROUND(dept_3/(dept_1+dept_2+dept_3+dept_4+dept_5+dept_6) * 100,2) ,'%）'),
                                CONCAT('民營企業（',dept_4,'及', ROUND(dept_4/(dept_1+dept_2+dept_3+dept_4+dept_5+dept_6) * 100,2) ,'%）'),
                                CONCAT('非營利機構（',dept_5,'及', ROUND(dept_5/(dept_1+dept_2+dept_3+dept_4+dept_5+dept_6) * 100,2) ,'%）'),
                                CONCAT('其他（',dept_6,'及', ROUND(dept_6/(dept_1+dept_2+dept_3+dept_4+dept_5+dept_6) * 100,2) ,'%）'),
                                CONCAT('1樓前瞻廳(國際會議廳)（',site_1,'及', ROUND(site_1/(site_1+site_2+site_3) * 100,2) ,'%）'),
                                CONCAT('2樓卓越堂(集會堂)（',site_2,'及', ROUND(site_2/(site_1+site_2+site_3) * 100,2) ,'%）'),
                                CONCAT('14樓貴賓廳（',site_3,'及', ROUND(site_3/(site_1+site_2+site_3) * 100,2) ,'%）')
                    FROM (
                            SELECT
                            COUNT(CASE WHEN dept='1' THEN 1 ELSE NULL END) AS dept_1,
                            COUNT(CASE WHEN dept='2' THEN 1 ELSE NULL END) AS dept_2,
                            COUNT(CASE WHEN dept='3' THEN 1 ELSE NULL END) AS dept_3,
                            COUNT(CASE WHEN dept='4' THEN 1 ELSE NULL END) AS dept_4,
                            COUNT(CASE WHEN dept='5' THEN 1 ELSE NULL END) AS dept_5,
                            COUNT(CASE WHEN dept='6' THEN 1 ELSE NULL END) AS dept_6,
                            COUNT(CASE WHEN site1='1' THEN 1 ELSE NULL END) AS site_1,
                            COUNT(CASE WHEN site2='1' THEN 1 ELSE NULL END) AS site_2,
                            COUNT(CASE WHEN site3='1' THEN 1 ELSE NULL END) AS site_3,
                            COUNT(CASE WHEN apply='1' THEN 1 ELSE NULL END) AS apply_1,
                            COUNT(CASE WHEN apply='2' THEN 1 ELSE NULL END) AS apply_2,
                            COUNT(CASE WHEN apply='3' THEN 1 ELSE NULL END) AS apply_3,
                                year,times
                            FROM t73tb
                            WHERE year=  LPAD('".$yerly."',3,'0')
                            AND times= '".$times."'
                                GROUP BY year, times
                        ) T	";
            $reportlist3 = DB::select($sql3);
            $dataArr3=json_decode(json_encode(DB::select($sql3)), true);
            //取出全部項目
            if(sizeof($reportlist3) != 0) {
                $arraykeys3=array_keys((array)$reportlist3[0]);
            }

            // 檔案名稱
            $fileName = 'N12';
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
            $objActSheet->setCellValue('A1', '公務人力發展學院福華國際文教會館'.$yerly.'年度第'.$times.'次場地設施服務滿意度統計表');
            $lineName = 'B';
            if(sizeof($reportlist) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    if($i==1){
                        $lineName = 'C';
                    } elseif($i==2){
                        $lineName = 'E';
                    } elseif($i==3){
                        $lineName = 'G';
                    } elseif($i==4){
                        $lineName = 'I';
                    } elseif($i==5){
                        $lineName = 'K';
                    }else {
                        //$NameFromNumber=$this->getNameFromNumber($i+2); //B
                        $lineName = 'B';
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
            $title = new PHPExcel_Chart_Title('公務人力發展學院福華國際文教會館'.$yerly.'年度第'.$times.'次場地設施服務滿意度統計表');
            $X_title = new PHPExcel_Chart_Title('各題次問項內容');
            $Y_title = new PHPExcel_Chart_Title('百分位數');
            $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String', '固定題目統計!$B$2', NULL, 1));
            $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', '固定題目統計!$B$3:$B$8', NULL, 100));
            $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', '固定題目統計!$N$3:$N$8', NULL, 100));
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

            //開放性問題
            //指定sheet
            $objActSheet = $objPHPExcel->getSheet(2);
            $reportlist2 = json_decode(json_encode($reportlist2), true);
            if(sizeof($reportlist2) != 0) {
                //項目數量迴圈
                for ($i=0; $i < sizeof($arraykeys2); $i++) {
                    //excel 欄位 1 == A, etc
                    $NameFromNumber=$this->getNameFromNumber($i+1); //A
                    //資料by班別迴圈
                    for ($j=0; $j < sizeof($reportlist2); $j++) {
                        //3開始
                        $objActSheet->setCellValue($NameFromNumber.($j+3), ($j+1).'.'.$reportlist2[$j][$arraykeys2[$i]]);
                    }
                }
            }

            //基本資料統計
            //指定sheet
            $objActSheet = $objPHPExcel->getSheet(3);
            $reportlist3 = json_decode(json_encode($reportlist3), true);
            if(sizeof($reportlist3) != 0) {
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist3); $j++) {
                    //項目數量迴圈
                    for ($i=0; $i < sizeof($arraykeys3); $i++) {
                        //3開始
                        if($i>=6){
                            $objActSheet->setCellValue('B'.($i+3+1), $reportlist3[$j][$arraykeys3[$i]]);
                        }
                        else{
                            $objActSheet->setCellValue('B'.($i+3), $reportlist3[$j][$arraykeys3[$i]]);
                        }
                    }
                }
            }

            $objActSheet = $objPHPExcel->getSheet(0);

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"場地問卷及統計表(101)-統計表");

        } else {
            // 讀檔案
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'N12').'.docx');

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"場地問卷及統計表(101)-空白問卷");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 
        }


    }
}
