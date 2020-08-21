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

class DailyDistributionLivingController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('daily_distribution_living', $user_group_auth)){
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
        return view('admin/daily_distribution_living/list');
    }

    /*
    住宿及休閒設施每日分配表 CSDIR6060
    參考Tables:
    使用範本:N6.xlsx
    'CSDIR6060  教育訓練資訊系統--住宿及休閒設施每日分配表
    'History:
    '2002/09/25
    '不再判別t38tb.meet第一碼-->全部的t38tb.meet
    't38tb 會議基本資料
    'meet  會議代號 char  7  ('')
    '第一碼:
    'T 訓練業務、M 行政會議、R 場地釋放
   '原陣列格式如下
    '       名稱                 使用班期名稱       小計
   '單人房         (0,0)           (1,0)           (2,0)
   '雙人房 (單床)  (0,1)           (1,1)           (2,1)
   '雙人房 (雙床)  (0,2)           (1,2)           (2,2)
   '行政套房       (0,3)           (1,3)           (2,3)
   '附屬設施       (0,4)           (1,4)           (2,4)
   'KTV1 -1        (0,5)           (1,5)           (2,5)
   'KTV1 -2        (0,6)           (1,6)           (2,6)
   'KTV2 -1        (0,7)           (1,7)           (2,7)
   'KTV2 -2        (0,8)           (1,8)           (2,8)

    '原EXCEL範本檔案中有9列要填寫
    ' 使用ADORS
    '1. 單人        WHERE sincnt>0
    '2. 雙人房      WHERE donecnt>0
    '3. 雙人雙床    WHERE meacnt>0
    ' 使用ADORS2
    '4. 行政套房  WHERE  m14tb.type=4  m14tb.timetype=2
    '5. 附屬設施  WHERE  m14tb.type=4  m14tb.timetype=1
    '6. ktv1-1    WHERE  m14tb.type=3  t22tb.time=E  t22tb.site=K01
    '7. ktv1-2    WHERE  m14tb.type=3  t22tb.time=F  t22tb.site=K01
    '8. ktv2-1    WHERE  m14tb.type=3  t22tb.time=E  t22tb.site=K02
    '9. ktv2-2    WHERE  m14tb.type=3  t22tb.time=F  t22tb.site=K02
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //日期
        $sdatetw = $request->input('sdatetw');
        //取得 住宿及休閒設施每日分配表
        $sql="SELECT DISTINCT
                                '單人房' class_type,
                                CASE WHEN t23tb.sincnt > 0 THEN
                                CONCAT(IFNULL(t01tb.name,'') ,'第',t23tb.term,'期 / ', t23tb.sincnt, '間')
                                                ELSE '' END        AS class_name,
                                                CASE WHEN T.v_totoal > 0 THEN CONCAT(T.v_totoal,'間') ELSE '' END v_totoal
                        FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                        CROSS JOIN (
                                                SELECT SUM(t23tb.sincnt)	AS v_totoal
                                                    FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                                    WHERE date= REPLACE('".$sdatetw."','/','')
                                                        AND t23tb.type = '3'
                                        ) T
                        WHERE date= REPLACE('".$sdatetw."','/','')
                        AND t23tb.type = '3'
                        UNION ALL
                        SELECT DISTINCT
                                '雙人、愛心房(單床)' class_type,
                                CASE WHEN t23tb.donecnt > 0 THEN
                                CONCAT(IFNULL(t01tb.name,'') ,'第',t23tb.term,'期 / ', t23tb.donecnt, '間')
                                ELSE '' END AS class_name,
                                CASE WHEN T.v_totoal > 0 THEN CONCAT(T.v_totoal,'間') ELSE '' END v_totoal
                        FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                        CROSS JOIN (
                                                SELECT SUM(t23tb.donecnt)	AS v_totoal
                                                    FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                                    WHERE date= REPLACE('".$sdatetw."','/','')
                                                        AND t23tb.type = '3'
                                        ) T
                        WHERE date= REPLACE('".$sdatetw."','/','')
                        AND t23tb.type = '3'
                        UNION ALL
                        SELECT DISTINCT
                                '雙人房(雙床)' class_type,
                                CASE WHEN t23tb.dtwocnt > 0 THEN
                                CONCAT(IFNULL(t01tb.name,'') ,'第',t23tb.term,'期 / ', t23tb.dtwocnt, '間')
                                ELSE '' END AS class_name,
                                CASE WHEN T.v_totoal > 0 THEN CONCAT(T.v_totoal,'間') ELSE '' END v_totoal
                        FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                        CROSS JOIN (
                                                SELECT SUM(t23tb.dtwocnt)	AS v_totoal
                                                    FROM t23tb LEFT JOIN t01tb ON t23tb.class = t01tb.class
                                                    WHERE date= REPLACE('".$sdatetw."','/','')
                                                        AND t23tb.type = '3'
                                        ) T
                        WHERE date= REPLACE('".$sdatetw."','/','')
                        AND t23tb.type = '3'
                        UNION ALL
                        SELECT DISTINCT
                                '行政套房' class_type,
                                CONCAT(IFNULL(t01tb.name,t38tb.name) ,'第',t22tb.term,'期') ,
                                '' AS v_totoal
                        FROM t22tb LEFT JOIN t01tb ON t22tb.class = t01tb.class
                            LEFT JOIN t38tb ON t22tb.class = t38tb.meet
                            LEFT OUTER JOIN m14tb ON t22tb.site = m14tb.site AND t22tb.term = t38tb.serno
                        WHERE m14tb.type=4 AND m14tb.timetype=2
                        AND t22tb.date= REPLACE('".$sdatetw."','/','')
                        UNION ALL
                        SELECT DISTINCT
                                '附屬設施' class_type,
                                CONCAT(IFNULL(t01tb.name,t38tb.name) ,'第',t22tb.term,'期',
                                    SUBSTRING(t22tb.stime,1,2) , ':' , SUBSTRING(t22tb.stime,3,2) , '~',
                                    SUBSTRING(t22tb.etime,1,2) , ':' , SUBSTRING(t22tb.etime,3,2)) ,
                                '' AS v_totoal
                        FROM t22tb LEFT JOIN t01tb ON t22tb.class = t01tb.class
                            LEFT JOIN t38tb ON t22tb.class = t38tb.meet
                            LEFT OUTER JOIN m14tb ON t22tb.site = m14tb.site AND t22tb.term = t38tb.serno
                        WHERE m14tb.type=4
                        AND m14tb.timetype=1
                        AND t22tb.date= REPLACE('".$sdatetw."','/','')
                        UNION ALL
                        SELECT DISTINCT
                                'KTV1-1' class_type,
                                CONCAT(IFNULL(t01tb.name,t38tb.name) ,'第',t22tb.term,'期') ,
                                '' AS v_totoal
                        FROM t22tb LEFT JOIN t01tb ON t22tb.class = t01tb.class
                            LEFT JOIN t38tb ON t22tb.class = t38tb.meet
                            LEFT OUTER JOIN m14tb ON t22tb.site = m14tb.site AND t22tb.term = t38tb.serno
                        WHERE m14tb.type=3 AND t22tb.time='E' AND t22tb.site = 'K01'
                        AND t22tb.date= REPLACE('".$sdatetw."','/','')
                        UNION ALL
                        SELECT DISTINCT
                                'KTV1-2' class_type,
                                CONCAT(IFNULL(t01tb.name,t38tb.name) ,'第',t22tb.term,'期') ,
                                '' AS v_totoal
                        FROM t22tb LEFT JOIN t01tb ON t22tb.class = t01tb.class
                            LEFT JOIN t38tb ON t22tb.class = t38tb.meet AND t22tb.term = t38tb.serno
                            LEFT OUTER JOIN m14tb ON t22tb.site = m14tb.site
                        WHERE m14tb.type=3 AND t22tb.site = 'K01' AND t22tb.time='F'
                        AND t22tb.date= REPLACE('".$sdatetw."','/','')
                        UNION ALL
                        SELECT DISTINCT
                                'KTV2-1' class_type,
                                CONCAT(IFNULL(t01tb.name,t38tb.name) ,'第',t22tb.term,'期') ,
                                '' AS v_totoal
                        FROM t22tb LEFT JOIN t01tb ON t22tb.class = t01tb.class
                            LEFT JOIN t38tb ON t22tb.class = t38tb.meet  AND t22tb.term = t38tb.serno
                            LEFT OUTER JOIN m14tb ON t22tb.site = m14tb.site
                        WHERE m14tb.type=3 AND t22tb.site = 'K02' AND t22tb.time='E'
                        AND t22tb.date= REPLACE('".$sdatetw."','/','')
                        UNION ALL
                        SELECT DISTINCT 'KTV2-2' class_type,
                                CONCAT(IFNULL(t01tb.name,t38tb.name) ,'第',t22tb.term,'期') AS class_name,
                                '' AS v_totoal
                        FROM t22tb LEFT JOIN t01tb ON t22tb.class = t01tb.class
                                                        LEFT JOIN t38tb ON t22tb.class = t38tb.meet AND t22tb.term = t38tb.serno
                                                        LEFT OUTER JOIN m14tb ON t22tb.site = m14tb.site
                        WHERE m14tb.type=3 AND t22tb.site = 'K02' AND t22tb.time='F'
                        AND t22tb.date= REPLACE('".$sdatetw."','/','')
                                                ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'N6';
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
            //$linenum=4;
            $tempCol1=' ';
            $tempCol2=' ';
            $tempCol3=' ';
            //$tempCol2Number='B4';
            for ($j=0; $j < sizeof($reportlist); $j++) {
                //項目數量迴圈
                //for ($i=0; $i < sizeof($arraykeys); $i++) {
                    //excel 欄位 1 == A, etc
                    //$NameFromNumber=$this->getNameFromNumber($i+1); //A
                    //dd($reportlist);
                    if($reportlist[$j][$arraykeys[0]]=='單人房'){
                        $linenum='4';
                        $tempCol2Number='B4';
                    }elseif ($reportlist[$j][$arraykeys[0]]=='雙人、愛心房(單床)'){
                        $linenum='5';
                        $tempCol2Number='B5';
                    }elseif($reportlist[$j][$arraykeys[0]]=='雙人房(雙床)'){
                        $linenum='6';
                        $tempCol2Number='B6';
                    }elseif($reportlist[$j][$arraykeys[0]]=='KTV1-1'){
                        $linenum='7';
                        $tempCol2Number='B7';
                    }elseif($reportlist[$j][$arraykeys[0]]=='附屬設施'){
                        $linenum='8';
                        $tempCol2Number='B8';
                    }elseif($reportlist[$j][$arraykeys[0]]=='KTV1-1'){
                        $linenum='9';
                        $tempCol2Number='C9';
                    }elseif($reportlist[$j][$arraykeys[0]]=='KTV1-2'){
                        $linenum='10';
                        $tempCol2Number='C10';
                    }elseif($reportlist[$j][$arraykeys[0]]=='KTV2-1'){
                        $linenum='11';
                        $tempCol2Number='C11';
                    }elseif($reportlist[$j][$arraykeys[0]]=='KTV2-2'){
                        $linenum='12';
                        $tempCol2Number='C12';
                    }else{
                        $tempCol2Number='B4';
                        $linenum='4';
                    }

                    if($reportlist[$j][$arraykeys[0]]!=$tempCol1 || $j==0){
                        //4開始
                        //$objActSheet->setCellValue($NameFromNumber.($j+4), $reportlist[$j][$arraykeys[$i]]);
                        $tempCol1=$reportlist[$j][$arraykeys[0]];
                        //內容換行
                        $tempCol2=$reportlist[$j][$arraykeys[1]].PHP_EOL;
                        $tempCol3=$reportlist[$j][$arraykeys[2]];
                        //newline
                        //$linenum++;
                    } else {
                        $tempCol1=$reportlist[$j][$arraykeys[0]];;
                        //內容換行
                        if($reportlist[$j][$arraykeys[1]]!=''){
                            $tempCol2=$tempCol2.$reportlist[$j][$arraykeys[1]].PHP_EOL;
                        }
                        $tempCol3=$reportlist[$j][$arraykeys[2]];
                        $objActSheet->setCellValue('A'.($linenum), $reportlist[$j][$arraykeys[0]]);
                        $objActSheet->setCellValue($tempCol2Number, $tempCol2);
                        //自動換行功能啓用
                        //$objActSheet->getStyle('B'.($linenum))->getAlignment()->setWrapText(true);
                        $objActSheet->getStyle($tempCol2Number)->getAlignment()->setWrapText(true);
                        $objActSheet->setCellValue('D'.($linenum), $tempCol3);
                        //高 66
                        //$objActSheet->getRowDimension($j+4)->setRowHeight(66);
                        //$objActSheet->getRowDimension($linenum)->setRowHeight(66);
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
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"住宿及休閒設施每日分配表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

        exit;

    }
}
