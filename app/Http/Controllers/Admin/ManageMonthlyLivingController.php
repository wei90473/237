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

class ManageMonthlyLivingController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('manage_monthly_living', $user_group_auth)){
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
        return view('admin/manage_monthly_living/list');
    }

    /*
    寢室住宿及休閒設施管理月報表 CSDIR6100
    參考Tables:
    使用範本:N9.xlsx
    '主要Table:
    '               t22tb(場地預約檔)
    '               t23tb(辦班需求確認檔)
    '               m14tb(場地基本資料檔)
    '日期不可空白
    '日期格式是否正確(EX:08801)
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

        //取得t23tb(辦班需求確認檔)的資料
        $sql="SELECT DD, W,
                        sum(sincnt) AS sincnt, sum(donecnt) AS donecnt, sum(dtwocnt) AS dtwocnt
                FROM (
                        SELECT SUBSTRING(date,6,2) AS DD,
                            CASE
                            WEEKDAY(str_to_date(CONCAT(SUBSTRING(date,1,3) + '1911',SUBSTRING(date,4,4)),'%Y%m%d') )
                                WHEN 0 THEN '一'
                                        WHEN 1 THEN '二'
                                        WHEN 2 THEN '三'
                                        WHEN 3 THEN '四'
                                        WHEN 4 THEN '五'
                                        WHEN 5 THEN '六'
                                        WHEN 6 THEN '日'
                            END AS W,
                            t23.sincnt AS sincnt, t23.donecnt AS donecnt, t23.dtwocnt AS dtwocnt
                        From t23tb AS t23
                        WHERE SUBSTRING(t23.date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                        AND t23.type='3'
                        UNION ALL
                        SELECT x.d AS DD,
                            CASE
                            WEEKDAY(str_to_date(CONCAT(
                                                        LPAD('".$startYear."',3,'0')
                                                        + '1911',
                                                        LPAD('".$startMonth."',2,'0')
                                                        , x.d),'%Y%m%d') )
                                WHEN 0 THEN '一'
                                        WHEN 1 THEN '二'
                                        WHEN 2 THEN '三'
                                        WHEN 3 THEN '四'
                                        WHEN 4 THEN '五'
                                        WHEN 5 THEN '六'
                                        WHEN 6 THEN '日'
                                ELSE ''
                            END AS W,
                            0 AS sincnt, 0 AS donecnt, 0 AS dtwocnt
                        FROM  ( SELECT '01' AS d UNION ALL
                                SELECT '02' AS d UNION ALL SELECT '03' AS d UNION ALL SELECT '04' AS d UNION ALL
                                SELECT '05' AS d UNION ALL SELECT '06' AS d UNION ALL SELECT '07' AS d UNION ALL
                                SELECT '08' AS d UNION ALL
                                SELECT '09' AS d UNION ALL SELECT '10' AS d UNION ALL SELECT '11' AS d UNION ALL
                                SELECT '12' AS d UNION ALL
                                SELECT '13' AS d UNION ALL SELECT '14' AS d UNION ALL SELECT '15' AS d UNION ALL
                                SELECT '16' AS d UNION ALL
                                SELECT '17' AS d UNION ALL SELECT '18' AS d UNION ALL SELECT '19' AS d UNION ALL
                                SELECT '20' AS d UNION ALL
                                SELECT '21' AS d UNION ALL SELECT '22' AS d UNION ALL SELECT '23' AS d UNION ALL
                                SELECT '24' AS d UNION ALL
                                SELECT '25' AS d UNION ALL SELECT '26' AS d UNION ALL SELECT '27' AS d UNION ALL
                                SELECT '28' AS d UNION ALL
                                SELECT '29' AS d UNION ALL SELECT '30' AS d UNION ALL SELECT '31' AS d ) AS x
                        ) AS T
                GROUP BY DD, W
                ORDER BY 1";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        //本月場次合計
        $sqlT="SELECT SUM(t23.sincnt) AS sincnt, SUM(t23.donecnt) AS donecnt, SUM(t23.dtwocnt) AS dtwocnt
                From t23tb AS t23
               WHERE SUBSTRING(t23.date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                 AND t23.type='3' ";

        $reportlistT = DB::select($sqlT);
        //取出全部項目
        if(sizeof($reportlistT) != 0) {
            $arraykeysT=array_keys((array)$reportlistT[0]);
        }

        //至本月累計場次
        /*計算至本月累計場計 計算至本月累計場計-->m14tb.type='4' AND m14tb.timetype='2' site= 'V01'
            參閱下列SQL取得col 5~9欄名稱與參數條件
            V01        行政套房一        4        2
            V02        行政套房二        4        2
            W01        附屬設施        4        1
            K01        KTV(一)        3        2
            K02        KTV(一)        3        2
        */
        $sqlM="SELECT SUM(A.V01), SUM(A.V02), SUM(A.W01), SUM(A.K01), SUM(A.K02)
        FROM (
        SELECT CASE WHEN T.site = 'V01' THEN SUM(V_COUNT) ELSE 0 END AS V01,
                      CASE WHEN T.site = 'V02' THEN SUM(V_COUNT) ELSE 0 END AS V02,
                      CASE WHEN T.site = 'W01' THEN SUM(V_COUNT) ELSE 0 END AS W01,
                      CASE WHEN T.site = 'K01' THEN SUM(V_COUNT) ELSE 0 END AS K01,
                      CASE WHEN T.site = 'K02' THEN SUM(V_COUNT) ELSE 0 END AS K02
                FROM (  SELECT 'V01' AS site, COUNT(*) AS V_COUNT FROM t22tb INNER JOIN m14tb ON t22tb.site = m14tb.site
                            WHERE m14tb.type='4'
                                AND m14tb.timetype= '2'
                                AND t22tb.site= 'V01'
                                AND SUBSTRING(date,1,3)>= LPAD('".$startYear."',3,'0')
                                AND SUBSTRING(date,1,5)<= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                            UNION ALL
                        SELECT 'V02' AS site, COUNT(*) AS V_COUNT FROM t22tb INNER JOIN m14tb ON t22tb.site = m14tb.site
                            WHERE m14tb.type='4'
                                AND m14tb.timetype= '2'
                                AND t22tb.site= 'V02'
                                AND SUBSTRING(date,1,3)>= LPAD('".$startYear."',3,'0')
                                AND SUBSTRING(date,1,5)<= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                            UNION ALL
                        SELECT 'W01' AS site, COUNT(*) AS V_COUNT FROM t22tb INNER JOIN m14tb ON t22tb.site = m14tb.site
                            WHERE m14tb.type='4'
                                AND m14tb.timetype= '1'
                                AND t22tb.site= 'W01'
                                AND SUBSTRING(date,1,3)>= LPAD('".$startYear."',3,'0')
                                AND SUBSTRING(date,1,5)<= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                            UNION ALL
                        SELECT 'K01' AS site, COUNT(*) AS V_COUNT FROM t22tb INNER JOIN m14tb ON t22tb.site = m14tb.site
                            WHERE m14tb.type='3'
                                AND m14tb.timetype= '2'
                                AND t22tb.site= 'K01'
                                AND SUBSTRING(date,1,3)>= LPAD('".$startYear."',3,'0')
                                AND SUBSTRING(date,1,5)<= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                            UNION ALL
                        SELECT 'K02' AS site, COUNT(*) AS V_COUNT FROM t22tb INNER JOIN m14tb ON t22tb.site = m14tb.site
                            WHERE m14tb.type='3'
                                AND m14tb.timetype= '2'
                                AND t22tb.site= 'K02'
                                AND SUBSTRING(date,1,3)>= LPAD('".$startYear."',3,'0')
                                AND SUBSTRING(date,1,5)<= CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                    ) T
                    GROUP BY T.site
                    ) A";
        $reportlistM = DB::select($sqlM);
        //取出全部項目
        if(sizeof($reportlistM) != 0) {
            $arraykeysM=array_keys((array)$reportlistM[0]);
        }

        //取得房間單價 管理服務費用 單人房
        /*'＜公式
            'sincnts 單人房                 sinunit     單人房-->單價
            'donecnts 雙人房(單床)      doneunit  雙人房(單床)-->單價
            'dtwocnts 雙人房(雙床)      dtwounit  雙人房(雙床)-->單價
            '
            ''單人房管理服務費
            'If sincnts + donecnts + dtwocnts > 2500 AND sincnts + donecnts>2500 Then
            '   單人房管理服務費
            '   =(2500-donecnts-dtwocnts)*sinunit+
            '      (sincnts+donecnts+dtwocnts-2500)*sinunit*(0.7)
            'Else
            '   單人房管理服務費 = sincnts * sinunit
            'End If
            '
            ''雙人房(單床)管理服務費
            '雙人房(單床)管理服務費=donecnts*doneunit
            '
            ''雙人房(雙床)管理服務費
            '雙人房(雙床)管理服務費=dtwocnts*dtwounit
            '公式>
            */
        $sql2="	 SELECT sinunit,doneunit*donecnt,dtwounit*dtwocnt
                   From t23tb
                  WHERE SUBSTRING(date,1,5) = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0'))
                    AND sinunit<>0 AND doneunit<>0 AND dtwounit<>0
                  ORDER BY sinunit,doneunit,dtwounit limit 1 ";

        $reportlist2 = DB::select($sql2);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        // 檔案名稱
        $fileName = 'N9';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&20'.'行政院人事行政總處公務人力發展學院'.substr($startYear,0,3).'年'.$startMonth.'月住宿及休閒設施管理月報表');

        $reportlist = json_decode(json_encode($reportlist), true);
        $reportlistT = json_decode(json_encode($reportlistT), true);  //本月場次合計
        $reportlistM = json_decode(json_encode($reportlistM), true);  //本月累計場計
        $reportlist2 = json_decode(json_encode($reportlist2), true);

        //'<<取得t23tb(辦班需求確認檔)的資料
        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //C
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //C3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist[$j][$arraykeys[$i]]);
                }
            }
        }

        //本月場次合計
        if(sizeof($reportlistT) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeysT); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+3); //C
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlistT); $j++) {
                    //C3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+34), $reportlistT[$j][$arraykeysT[$i]]);
                }
            }
        }

        //本月累計場計
        if(sizeof($reportlistM) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeysM); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+6); //F
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlistM); $j++) {
                    //C3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+35), $reportlistM[$j][$arraykeysM[$i]]);
                }
            }
        }

        //取得房間單價 管理服務費用 單人房
        if(sizeof($reportlist2) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys2); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+3); //C
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    //C3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+36), $reportlist2[$j][$arraykeys2[$i]]);
                }
            }
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"住宿及休閒設施管理月報表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
