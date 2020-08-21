<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Services\User_groupService;

class ParticipationReasonStatisticsController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('participation_reason_statistics', $user_group_auth)){
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
        //return view('admin/participation_reason_statistics/list');
        $sqlClass = "SELECT RTRIM(code) AS value,CONCAT(s01tb.code, ' ', s01tb.name) AS text,RTRIM(code)+' '+RTRIM(name) AS item FROM s01tb WHERE type='K'";
        $class = DB::select($sqlClass);
        $result = '';
        return view('admin/participation_reason_statistics/list', compact('class','result'));
    }

    /*
    參訓原因統計 CSDIR5100
    參考Tables:
    使用範本:L7.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //變數設定
        //年度
        $yerly = $request->input('yerly');
        $radioType = $request->input('radioType');
        //季
        $quarter = $request->input('quarter');
        //月份
        $month = $request->input('selectMonth');
        //班別性質
        $classes = $request->input('classes');

        //取得 參訓原因統計
        $sql=" SELECT   CONCAT(RTRIM(X.name),'第',X.term,'期','\n',
                            SUBSTRING(X.sdate,1,3), '/', SUBSTRING(X.sdate,4,2), '/', SUBSTRING(X.sdate,6,2),'～',
                            SUBSTRING(X.edate,1,3), '/', SUBSTRING(X.edate,4,2), '/', SUBSTRING(X.edate,6,2)) AS classname,
                        SUM(CASE WHEN Y.reason='1' THEN 1 ELSE 0 END) AS reason_1,
                        SUM(CASE WHEN Y.reason='1' THEN 1 ELSE 0 END) /
                            SUM(CASE WHEN Y.reason IN ('1','2','3','4') THEN 1 ELSE 0 END)  AS per_1,
                        SUM(CASE WHEN Y.reason='2' THEN 1 ELSE 0 END) AS reason_2,
                        SUM(CASE WHEN Y.reason='2' THEN 1 ELSE 0 END) /
                            SUM(CASE WHEN Y.reason IN ('1','2','3','4') THEN 1 ELSE 0 END)  AS per_2,
                        SUM(CASE WHEN Y.reason='3' THEN 1 ELSE 0 END) AS reason_3,
                        SUM(CASE WHEN Y.reason='3' THEN 1 ELSE 0 END) /
                            SUM(CASE WHEN Y.reason IN ('1','2','3','4') THEN 1 ELSE 0 END)  AS per_3,
                        SUM(CASE WHEN Y.reason='4' THEN 1 ELSE 0 END) AS reason_4,
                        SUM(CASE WHEN Y.reason='4' THEN 1 ELSE 0 END) /
                            SUM(CASE WHEN Y.reason IN ('1','2','3','4') THEN 1 ELSE 0 END)  AS per_4
                FROM (
                SELECT
                        A.class,
                        A.term,
                        MAX(C.times) AS times,
                        B.name,
                        A.sdate,
                        A.edate
                FROM t04tb A INNER JOIN t01tb B ON A.class=B.class
                    INNER JOIN t72tb C ON A.class=C.class AND A.term=C.term
                WHERE ( SUBSTRING(A.sdate,1,3) = LPAD('".$yerly."',3,'0') OR SUBSTRING(A.edate,1,3) = LPAD('".$yerly."',3,'0') )
                  AND (B.type = IFNULL(Left('".$classes."', 2) ,B.type) OR  '".$classes."' = '0')
                GROUP BY A.class, A.term, A.sdate, A.edate, B.name
                ) X
                INNER JOIN t72tb Y ON X.class=Y.class AND X.term=Y.term AND X.times=Y.times
                GROUP BY X.class, X.term, X.times, X.name,  X.sdate, X.edate
                ORDER BY X.sdate,X.class,X.term  ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'L7';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&18 '.substr($yerly,0,3).'年度參訓原因統計');

        $reportlist = json_decode(json_encode($reportlist), true);

        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist[$j][$arraykeys[$i]]);
                    //自動換行
                    $objActSheet->getStyle('A'.($j+3))->getAlignment()->setWrapText(true);
                    //高 33
                    $objActSheet->getRowDimension($j+3)->setRowHeight(33);
                }
            }

            $styleArray = [
                'borders' => [
            //只有外框           'outline' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $objActSheet->getStyle('A3:'.$NameFromNumber.($j+3))->applyFromArray($styleArray);
            //total
            $objActSheet->setCellValue('A'.($j+3), '總計');
            //=sum, =SUM(B3:B468)
            $objActSheet->setCellValue('B'.($j+3), '=SUM(B3:B'.($j+2).')');
            $objActSheet->setCellValue('D'.($j+3), '=SUM(D3:D'.($j+2).')');
            $objActSheet->setCellValue('F'.($j+3), '=SUM(F3:F'.($j+2).')');
            $objActSheet->setCellValue('H'.($j+3), '=SUM(H3:H'.($j+2).')');

            //=IF(ISERROR(B469/(B469+D469+F469+H469)),0,B469/(B469+D469+F469+H469))
            //B
            $objActSheet->setCellValue('C'.($j+3), '=IF(ISERROR(B'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).')),0,B'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'))');
            //=IF(ISERROR(D469/(B469+D469+F469+H469)),0,D469/(B469+D469+F469+H469))
            //D
            $objActSheet->setCellValue('E'.($j+3), '=IF(ISERROR(B'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).')),0,D'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'))');
            //=IF(ISERROR(F469/(B469+D469+F469+H469)),0,F469/(B469+D469+F469+H469))
            //F
            $objActSheet->setCellValue('G'.($j+3), '=IF(ISERROR(B'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).')),0,F'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'))');
            //=IF(ISERROR(H469/(B469+D469+F469+H469)),0,H469/(B469+D469+F469+H469))
            //H
            $objActSheet->setCellValue('I'.($j+3), '=IF(ISERROR(B'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).')),0,H'.($j+3).'/(B'.($j+3).'+D'.($j+3).'+F'.($j+3).'+H'.($j+3).'))');
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"參訓原因統計");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
