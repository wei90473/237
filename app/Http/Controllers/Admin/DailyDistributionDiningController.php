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

class DailyDistributionDiningController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('daily_distribution_dining', $user_group_auth)){
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
        return view('admin/daily_distribution_dining/list');
    }

    /*
    用餐數量每日分配表 CSDIR6070
    參考Tables:
    使用範本:N7.xlsx
    'History:
    '2002/09/26
    '不再判別t38tb.meet第一碼-->全部的t38tb.meet
    't38tb 會議基本資料
    'meet  會議代號 char  7  ('')
    '第一碼:
    'T 訓練業務、M 行政會議、R 場地釋放
    'EXCEL範本檔案中有6列要填寫
    '1. 早餐
    '2. 午餐
    '3. 晚餐
    '4. 訂席桌餐
    '5. 自助餐
    '6. 茶點
    '抓出的RS中不同的RECORDSET(也許是首筆,也許是第3筆...)有許多欄位是放在EXCEL的同一列（同一個格子）
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //日期
        $sdatetw = $request->input('sdatetw');
        //取得 用餐數量每日分配表
        $sql="SELECT DISTINCT
                            '早餐' class_type,
                            CASE WHEN t23tb.meacnt > 0 THEN
                            CONCAT(IFNULL(t01tb.name,'') ,'第',t23tb.term,'期/', t23tb.meacnt, '人')
                        ELSE '' END	AS class_name,
                            CASE WHEN T.v_totoal > 0 THEN CONCAT(T.v_totoal,'人') ELSE '' END v_totoal
                    FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                            CROSS JOIN (
                                                    SELECT SUM(t23tb.meacnt)	AS v_totoal
                                                        FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                                        WHERE date= REPLACE('".$sdatetw."','/','')
                                                            AND t23tb.type = '3'
                                            ) T
                    WHERE date= REPLACE('".$sdatetw."','/','')
                    AND t23tb.type = '3'
                    UNION ALL
                    SELECT DISTINCT
                            '午餐' class_type,
                            CASE WHEN t23tb.luncnt > 0 THEN
                            CONCAT(IFNULL(t01tb.name,'') ,'第',t23tb.term,'期/', t23tb.luncnt, '人')
                            ELSE '' END AS class_name,
                            CASE WHEN T.v_totoal > 0 THEN CONCAT(T.v_totoal,'人') ELSE '' END v_totoal
                    FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                            CROSS JOIN (
                                                        SELECT SUM(t23tb.luncnt)	AS v_totoal
                                                        FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                                        WHERE date= REPLACE('".$sdatetw."','/','')
                                                            AND t23tb.type = '3'
                                            ) T
                    WHERE date= REPLACE('".$sdatetw."','/','')
                    AND t23tb.type = '3'
                    UNION ALL
                    SELECT DISTINCT
                            '晚餐' class_type,
                            CASE WHEN t23tb.dincnt > 0 THEN
                            CONCAT(IFNULL(t01tb.name,'') ,'第',t23tb.term,'期/', t23tb.dincnt, '人')
                            ELSE '' END AS class_name,
                            CASE WHEN T.v_totoal > 0 THEN CONCAT(T.v_totoal,'人') ELSE '' END v_totoal
                    FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                            CROSS JOIN (
                                                        SELECT SUM(t23tb.dincnt)	AS v_totoal
                                                        FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                                        WHERE date= REPLACE('".$sdatetw."','/','')
                                                            AND t23tb.type = '3'
                                            ) T
                    WHERE date= REPLACE('".$sdatetw."','/','')
                    AND t23tb.type = '3'
                    UNION ALL
                    SELECT DISTINCT
                            '訂席桌餐' class_type,
                            CASE WHEN t23tb.tabcnt > 0 THEN
                            CONCAT(IFNULL(t01tb.name,'') ,'第',t23tb.term,'期/',
                            CASE tabtype
                            WHEN '1' THEN
                                '午餐 '
                                WHEN '2' THEN
                                '晚餐 '
                            END
                            ,t23tb.tabcnt,
                            CASE WHEN tabvegan > 0 THEN
                            CONCAT('(', tabvegan ,')')
                            ELSE
                            ''
                            END
                            ,'人 * ', tabunit, '元')
                            ELSE '' END AS class_name,
                            '' AS v_totoal
                    FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                    WHERE date= REPLACE('".$sdatetw."','/','')
                    AND t23tb.type = '3'
                    UNION ALL
                    SELECT DISTINCT
                            '自助餐' class_type,
                            CASE WHEN t23tb.bufcnt > 0 THEN
                            CONCAT(IFNULL(t01tb.name,'') ,'第',t23tb.term,'期/',
                            CASE buftype
                            WHEN '1' THEN
                                '午餐 '
                                WHEN '2' THEN
                                '晚餐 '
                            END
                            ,t23tb.bufcnt,
                            CASE WHEN bufvegan > 0 THEN
                            CONCAT('(', bufvegan ,')')
                            ELSE
                            ''
                            END
                            ,'人 * ', bufunit, '元')
                            ELSE '' END AS class_name,
                            '' AS v_totoal
                    FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                    WHERE date= REPLACE('".$sdatetw."','/','')
                    AND t23tb.type = '3'
                    UNION ALL
                    SELECT DISTINCT
                            '茶點' class_type,
                            CASE WHEN t23tb.teacnt > 0 THEN
                            CONCAT(IFNULL(t01tb.name,'') ,'第',t23tb.term,'期/'
                            ,t23tb.teacnt
                            ,'人 * ', teaunit, '元 / ', teatime)
                            ELSE '' END AS class_name,
                            '' AS v_totoal
                    FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                    WHERE date= REPLACE('".$sdatetw."','/','')
                    AND t23tb.type = '3'
                        ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'N7';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setCellValue('A2', '日期：'.$sdatetw.'');

        $reportlist = json_decode(json_encode($reportlist), true);

        if(sizeof($reportlist) != 0) {
            //資料by迴圈
            $linenum=4;
            $tempCol1='';
            $tempCol2='';
            $tempCol3='';
            for ($j=0; $j < sizeof($reportlist); $j++) {
                //項目數量迴圈
                //for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    //$NameFromNumber=$this->getNameFromNumber($i+1); //A

                    if($reportlist[$j][$arraykeys[0]]!=$tempCol1){
                        //4開始
                        //$objActSheet->setCellValue($NameFromNumber.($j+4), $reportlist[$j][$arraykeys[$i]]);
                        //if($i==1){

                        //} else {
                            $objActSheet->setCellValue('A'.($linenum), $reportlist[$j][$arraykeys[0]]);
                            $objActSheet->setCellValue('B'.($linenum), $tempCol2);
                            //自動換行功能啓用
                            $objActSheet->getStyle('B'.($linenum))->getAlignment()->setWrapText(true);
                            $objActSheet->setCellValue('C'.($linenum), $tempCol3);
                        //}
                        //高 66
                        //$objActSheet->getRowDimension($j+4)->setRowHeight(66);
                        $objActSheet->getRowDimension($linenum)->setRowHeight(66);
                        $tempCol1=$reportlist[$j][$arraykeys[0]];
                        $tempCol2=$reportlist[$j][$arraykeys[1]];
                        $tempCol3=$reportlist[$j][$arraykeys[2]];
                        //newline
                        $linenum++;
                    } else {
                        //內容換行
                        $tempCol2=$tempCol2.$reportlist[$j][$arraykeys[1]].PHP_EOL;
                        $tempCol3=$reportlist[$j][$arraykeys[2]];

                    }
                //}

            }

            /*
            $styleArray = [
                'borders' => [
            //只有外框           'outline' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $objActSheet->getStyle('A4:'.$NameFromNumber.($j+3))->applyFromArray($styleArray);
            */
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"用餐數量每日分配表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
        
    }
}
