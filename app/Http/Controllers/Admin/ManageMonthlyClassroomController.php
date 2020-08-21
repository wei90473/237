<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ManageMonthlyClassroomController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
          $user_data = \Auth::user();
          $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
          if(in_array('manage_monthly_classroom', $user_group_auth)){
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
        return view('admin/manage_monthly_classroom/list');
    }

    /*
    教室場地管理月報表 CSDIR6080
    參考Tables:
    使用範本:N8.xlsx
    'History:
    '2002/10/16 Update
    '除電腦教室以小時計算，其它以場次計算。
    'm14tb.timetype 預約類型（1：時段；2：時間）
    '  When m14tb.tiemtype='2'
    '  場次以時數計算 (結束時間 - 開始時間), 不到一小時, 以一小時計
    '主要Table:
    '               t22tb(場地預約檔)
    '               m14tb(場地基本資料檔)
    '日期不可空白
    '日期格式是否正確(EX:08801)
    '場地類型:(type='1' -->教室)
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //年
        $startYear = $request->input('startYear');
        //月
        $startMonth = $request->input('startMonth');

        /*
        if($startMonth<10){
            $startMonth='0'.$startMonth;
        }
        */

        //取月份的每日的星期數
        $sql="  SELECT
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'01'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D01,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'02'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D02,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'03'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D03,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'04'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D04,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'05'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D05,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'06'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D06,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'07'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D07,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'08'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D08,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'09'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D09,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'10'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D10,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'11'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D11,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'12'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D12,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'13'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D13,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'14'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D14,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'15'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D15,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'16'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D16,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'17'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D17,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'18'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D18,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'19'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D19,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'20'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D20,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'21'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D21,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'22'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D22,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'23'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D23,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'24'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D24,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'25'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D25,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'26'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D26,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'27'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D27,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'28'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                  END AS D28,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'29'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                                  ELSE NULL
                  END AS D29,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'30'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                                  ELSE NULL
                  END AS D30,
                  CASE WEEKDAY(str_to_date(CONCAT(SUBSTRING(d.date,1,3) + '1911',SUBSTRING(d.date,4,2),'31'),'%Y%m%d') )
                      WHEN 0 THEN '一' WHEN 1 THEN '二' WHEN 2 THEN '三' WHEN 3 THEN '四' WHEN 4 THEN '五' WHEN 5 THEN '六' WHEN 6 THEN '日'
                                  ELSE NULL
                  END AS D31,
                          NULL AS fee
              FROM (SELECT CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0')) AS date
                      FROM dual
                          ) d ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        //*教室編號site, 日期D1~31, 管理服務費用統計fee
        /*僅列Excel結果有的項目*/
        $sql2="	SELECT
                        sum(A.D01) AS D01, sum(A.D02) AS D02, sum(A.D03) AS D03, sum(A.D04) AS D04, sum(A.D05) AS D05,
                            sum(A.D06) AS D06, sum(A.D07) AS D07, sum(A.D08) AS D08, sum(A.D09) AS D09, sum(A.D10) AS D10,
                            sum(A.D11) AS D11, sum(A.D12) AS D12, sum(A.D13) AS D13, sum(A.D14) AS D14, sum(A.D15) AS D15,
                            sum(A.D16) AS D16, sum(A.D17) AS D17, sum(A.D18) AS D18, sum(A.D19) AS D19, sum(A.D20) AS D20,
                            sum(A.D21) AS D21, sum(A.D22) AS D22, sum(A.D23) AS D23, sum(A.D24) AS D24, sum(A.D15) AS D25,
                            sum(A.D26) AS D26, sum(A.D27) AS D27, sum(A.D28) AS D28, sum(A.D29) AS D29, sum(A.D30) AS D30,
                        sum(A.D31) AS D31,
                        sum(A.D01+A.D02+A.D03+A.D04+A.D05+A.D06+A.D07+A.D08+A.D09+A.D10+
                            A.D11+A.D12+A.D13+A.D14+A.D15+A.D16+A.D17+A.D18+A.D19+A.D20+
                            A.D21+A.D22+A.D23+A.D24+A.D25+A.D26+A.D27+A.D28+A.D29+A.D30+A.D31)
                        AS D_TOTAL,
                        sum(A.fee) AS fee
                FROM (
                    SELECT T.site,
                        CASE WHEN T.DD='01' THEN count(T.DD) ELSE 0 END AS D01,
                                CASE WHEN T.DD='02' THEN count(T.DD) ELSE 0 END AS D02,
                                CASE WHEN T.DD='03' THEN count(T.DD) ELSE 0 END AS D03,
                                CASE WHEN T.DD='04' THEN count(T.DD) ELSE 0 END AS D04,
                                CASE WHEN T.DD='05' THEN count(T.DD) ELSE 0 END AS D05,
                                CASE WHEN T.DD='06' THEN count(T.DD) ELSE 0 END AS D06,
                                CASE WHEN T.DD='07' THEN count(T.DD) ELSE 0 END AS D07,
                                CASE WHEN T.DD='08' THEN count(T.DD) ELSE 0 END AS D08,
                                CASE WHEN T.DD='09' THEN count(T.DD) ELSE 0 END AS D09,
                                CASE WHEN T.DD='10' THEN count(T.DD) ELSE 0 END AS D10,
                                CASE WHEN T.DD='11' THEN count(T.DD) ELSE 0 END AS D11,
                                CASE WHEN T.DD='12' THEN count(T.DD) ELSE 0 END AS D12,
                                CASE WHEN T.DD='13' THEN count(T.DD) ELSE 0 END AS D13,
                                CASE WHEN T.DD='14' THEN count(T.DD) ELSE 0 END AS D14,
                                CASE WHEN T.DD='15' THEN count(T.DD) ELSE 0 END AS D15,
                                CASE WHEN T.DD='16' THEN count(T.DD) ELSE 0 END AS D16,
                                CASE WHEN T.DD='17' THEN count(T.DD) ELSE 0 END AS D17,
                                CASE WHEN T.DD='18' THEN count(T.DD) ELSE 0 END AS D18,
                                CASE WHEN T.DD='19' THEN count(T.DD) ELSE 0 END AS D19,
                                CASE WHEN T.DD='20' THEN count(T.DD) ELSE 0 END AS D20,
                                CASE WHEN T.DD='21' THEN count(T.DD) ELSE 0 END AS D21,
                                CASE WHEN T.DD='22' THEN count(T.DD) ELSE 0 END AS D22,
                                CASE WHEN T.DD='23' THEN count(T.DD) ELSE 0 END AS D23,
                                CASE WHEN T.DD='24' THEN count(T.DD) ELSE 0 END AS D24,
                                CASE WHEN T.DD='25' THEN count(T.DD) ELSE 0 END AS D25,
                                CASE WHEN T.DD='26' THEN count(T.DD) ELSE 0 END AS D26,
                                CASE WHEN T.DD='27' THEN count(T.DD) ELSE 0 END AS D27,
                                CASE WHEN T.DD='28' THEN count(T.DD) ELSE 0 END AS D28,
                                CASE WHEN T.DD='29' THEN count(T.DD) ELSE 0 END AS D29,
                                CASE WHEN T.DD='30' THEN count(T.DD) ELSE 0 END AS D30,
                                CASE WHEN T.DD='31' THEN count(T.DD) ELSE 0 END AS D31,
                            SUM(T.fee) AS Fee
                    FROM (SELECT m14tb.site AS site, 0 AS fee,
                                    NULL AS DD
                                    FROM m14tb
                                    WHERE type = '1'
                                    UNION ALL
                                    SELECT t22tb.site AS site, t22tb.fee AS fee,
                                            SUBSTRING(t22tb.date,6,2) AS DD
                                     FROM t22tb AS t22tb INNER JOIN m14tb ON t22tb.site = m14tb.site
                                    WHERE type='1'
                                      AND SUBSTRING(date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                            ) T
                    WHERE T.site IN ('101','103','201','202','203','204','205','303','304','305',
                                    '401','402','403','404','405','501','502','601','602')
                    GROUP BY T.site, T.DD
                    ) A
                    GROUP BY A.site
                    ORDER BY A.site ";

        $reportlist2 = DB::select($sql2);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }


        // 檔案名稱
        $fileName = 'N8';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&22'.'行政院人事行政總處公務人力發展學院'.substr($startYear,0,3).'年'.$startMonth.'月各教室場地管理月報表');
        $reportlist = json_decode(json_encode($reportlist), true);
        $reportlist2 = json_decode(json_encode($reportlist2), true);

        //取月份的每日的星期數
        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+3); //C
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //C2開始
                    $objActSheet->setCellValue($NameFromNumber.($j+2), $reportlist[$j][$arraykeys[$i]]);
                }
            }
        }


        //教室編號site, 日期D1~31, 管理服務費用統計fee
        if(sizeof($reportlist2) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys2); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+3); //C
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    //C3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist2[$j][$arraykeys2[$i]]);
                }
            }
        }


        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"教室場地管理月報表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
