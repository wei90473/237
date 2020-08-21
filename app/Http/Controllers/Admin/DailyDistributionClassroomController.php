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

class DailyDistributionClassroomController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('daily_distribution_classroom', $user_group_auth)){
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
        return view('admin/daily_distribution_classroom/list');
    }

    /*
    教室場地每日分配表 CSDIR6040
    參考Tables:
    使用範本:N4.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //日期
        $sdatetw = $request->input('sdatetw');
        //取得 教室場地每日分配表
        $sql=" SELECT MAX(T.DAY_A), MAX(T.DAY_B), MAX(T.DAY_C)
                FROM (
                                SELECT t22tb.site,
                                            CASE WHEN t22tb.time = 'A' THEN
                                                        CONCAT(IFNULL(t01tb.name,''), IFNULL(t38tb.name,''), '(',sum(t22tb.cnt),'人)',
                                            CASE WHEN t22tb.seattype = 'B' THEN '馬蹄型'
                                                        WHEN t22tb.seattype = 'C' THEN 'T型'
                                                        WHEN t22tb.seattype = 'D' THEN '菱型'
                                                        WHEN t22tb.seattype = 'E' THEN '其他'
                                                        ELSE '' END) ELSE NULL END AS DAY_A,
                                            CASE WHEN t22tb.time = 'B' THEN
                                                        CONCAT(IFNULL(t01tb.name,''), IFNULL(t38tb.name,''), '(',sum(t22tb.cnt),'人)',
                                            CASE WHEN t22tb.seattype = 'B' THEN '馬蹄型'
                                                        WHEN t22tb.seattype = 'C' THEN 'T型'
                                                        WHEN t22tb.seattype = 'D' THEN '菱型'
                                                        WHEN t22tb.seattype = 'E' THEN '其他'
                                                        ELSE '' END) ELSE NULL END AS DAY_B,
                                            CASE WHEN t22tb.time = 'C' THEN
                                                        CONCAT(IFNULL(t01tb.name,''), IFNULL(t38tb.name,''), '(',sum(t22tb.cnt),'人)',
                                            CASE WHEN t22tb.seattype = 'B' THEN '馬蹄型'
                                                        WHEN t22tb.seattype = 'C' THEN 'T型'
                                                        WHEN t22tb.seattype = 'D' THEN '菱型'
                                                        WHEN t22tb.seattype = 'E' THEN '其他'
                                                        ELSE '' END) ELSE NULL END AS DAY_C
                                FROM t22tb  LEFT OUTER JOIN t38tb ON t22tb.class = t38tb.meet AND t22tb.term = t38tb.serno
                                            LEFT OUTER JOIN t01tb ON t22tb.class = t01tb.class
                                            LEFT OUTER JOIN m14tb ON t22tb.site = m14tb.site
                                WHERE ( t22tb.site = '101' OR
                                        t22tb.site = '103' OR t22tb.site = '201' OR  t22tb.site = '202' OR
                                        t22tb.site = '203' OR t22tb.site = '204' OR
                                        t22tb.site = '205' OR t22tb.site = '303' OR
                                        t22tb.site = '304' OR t22tb.site = '305' OR
                                        t22tb.site = '401' OR t22tb.site = '402' OR
                                        t22tb.site = '403' OR t22tb.site = '404' OR
                                        t22tb.site = '405' OR t22tb.site = '501' OR
                                        t22tb.site = '502' OR t22tb.site = '601' OR
                                        t22tb.site = '602')
                                    AND (m14tb.type = '1')
                                    AND t22tb.date = REPLACE('".$sdatetw."','/','')
                                GROUP BY t22tb.date, t22tb.site, t22tb.time, IFNULL(t22tb.seattype,''), t01tb.name, t38tb.name, t22tb.seattype
                                UNION ALL
                                                SELECT '101' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '103' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '201' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '202' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '203' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '204' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '205' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '303' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '304' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '305' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '401' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '402' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '403' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '404' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '405' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '501' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '502' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '601' AS site, NULL, NULL, NULL
                                                UNION ALL
                                                SELECT '602' AS site, NULL, NULL, NULL
                                                ) T
                GROUP BY T.site
                ORDER BY T.site           ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'N4';
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
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+4);
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //5開始
                    $objActSheet->setCellValue($NameFromNumber.($j+5), $reportlist[$j][$arraykeys[$i]]);
                }
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

            $objActSheet->getStyle('A2:'.$NameFromNumber.($j+1))->applyFromArray($styleArray);
            */
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"教室場地每日分配表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
