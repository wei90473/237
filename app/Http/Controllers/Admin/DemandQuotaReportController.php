<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\User_groupService;

class DemandQuotaReportController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('demand_quota_report', $user_group_auth)){
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
        return view('admin/demand_quota_report/list');
    }

    public function gettime(Request $request)
    {
        //old sample code
        //         $yerly = $request->input('yerly');
        //         $sql = "
        //         SELECT DISTINCT times FROM t01tb WHERE yerly='".$yerly."' GROUP BY times ORDER BY times";
        //         //
        //         //$data = DB::select("SELECT times FROM t01tb WHERE yerly=\''.$yerly.'\' GROUP BY times ORDER BY times");
        //         // print_r($data);
        //         $data = DB::select($sql);

        $result = '';
        $RptBasic = new \App\Rptlib\RptBasic();
        return $RptBasic->gettime($request->input('yerly'));
    }

    public function export(Request $request)
    {
        $yerly = $request->input('yerly');
        $temptimes = explode(",", $request->input('times'));
        $doctype = $request->input('doctype');

        $times="";

        for ($i=0; $i < sizeof($temptimes); $i++) {
            if ($i == sizeof($temptimes)-1) {
                $times=$times."'".$temptimes[$i]."'";
            } else {
                $times=$times."'".$temptimes[$i]."',";
            }
        }

//         $sql = "
//         SELECT
//         X.organ,
//         X.name
//         FROM
//         (
//         SELECT
//         A.organ,
//         C.rank,
//         RTRIM(C.lname) AS NAME
//         FROM (SELECT class,times from t01tb WHERE yerly='$yerly' AND  times IN ($times) ) as B
//         left JOIN t02tb A
//         ON A.class=B.class
//         left JOIN m13tb C
//         ON A.organ=C.organ
//         AND C.kind='Y'
//         GROUP BY A.organ,C.lname  ,C.rank

//         UNION  ALL

//         SELECT
//         A.organ,
//         A.organ,
//         RTRIM(C.name) AS NAME
//         FROM (SELECT class,times from t01tb WHERE yerly='$yerly' AND  times IN ($times) ) as B
//         left JOIN t02tb A
//         ON A.class=B.class
//         left JOIN m07tb C
//         ON A.organ=C.agency
//         GROUP BY A.organ,C.name
//         ) X
//         ORDER BY X.rank ";

//        原始SQL語法
        $sql = "
        SELECT
        X.organ,
        X.name
        FROM
        (
        SELECT
        A.organ,
        C.rank,
        RTRIM(C.lname) AS NAME
        FROM t02tb A
        INNER JOIN t01tb B
        ON A.class=B.class
        INNER JOIN m13tb C
        ON A.organ=C.organ
        WHERE B.yerly='$yerly'
        AND  B.times IN ($times)
        AND C.kind='Y'
        GROUP BY A.organ,C.lname  ,C.rank
        UNION  ALL
        SELECT
        A.organ,
        A.organ,
        RTRIM(C.name) AS NAME
        FROM t02tb A
        INNER JOIN t01tb B
        ON A.class=B.class
        INNER JOIN m07tb C
        ON A.organ=C.agency
        WHERE B.yerly='$yerly'
        AND  B.times IN ($times)
        GROUP BY A.organ,C.name
        ) X
        ORDER BY X.rank ";

        $organlist = DB::select($sql);
        $sql = " SELECT RTRIM(IFNULL(A.name,A.class)) as classname,";
        for ($i=0; $i < sizeof($organlist); $i++) {
            $sql.=" SUM(CASE WHEN B.organ='".$organlist[$i]->organ."'THEN B.demand ELSE 0 END ) AS '".$organlist[$i]->name."', ";
        }
        $sql.=" '' AS 合計
        FROM t01tb A
        INNER JOIN t02tb B
        ON A.class=B.class
        WHERE A.yerly='$yerly'
        AND A.times IN ($times)
        GROUP BY A.class,A.rank, A.name
        ORDER BY A.rank, A.class ";

       $reportlist = DB::select($sql);

        //取出全部機關名稱
        $arraykeys=array_keys((array)$reportlist[0]);

        // 檔案名稱
        $fileName = 'D2';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);

        // sample code
        // $excelReader = IOFactory::createReaderForFile($filePath);
        // $excelReader->setReadDataOnly(false);
        // $objPHPExcel = $excelReader->load($filePath);

        $objActSheet = $objPHPExcel->getActiveSheet();

        // //畫圖 sample code
        // $objDrawing = new Drawing();
        // $objDrawing->setName('avatar');
        // $objDrawing->setDescription('avatar');
        // $objDrawing->setPath('../example/img/'.iconv('UTF-8', 'GBK', $fileName).'.PNG');
        // // 不指定長寬，截圖大小就是圖片大小
        // // $objDrawing->setHeight(157.5);
        // // $objDrawing->setWidth(32.63);
        // $objDrawing->setCoordinates('A4');
        // //圖片塞進A4
        // $objDrawing->setWorksheet($objActSheet);


        //stdClass轉Array
        $reportlist = json_decode(json_encode($reportlist), true);
        //機關數量迴圈
        for ($i=0; $i < sizeof($arraykeys); $i++) {
            //excel 欄位 1 == A, etc
            $NameFromNumber=$this->getNameFromNumber($i+1);
            //資料by班別迴圈
            for ($j=0; $j < sizeof($reportlist); $j++) {
                //A5開始
                $objActSheet->setCellValue($NameFromNumber.($j+5), $reportlist[$j][$arraykeys[$i]]);
                //最下方合計語法
                if($j == sizeof($reportlist)-1){
                    if($i==0){
                        $objActSheet->setCellValue($NameFromNumber.($j+7), '合計');
                    }else{
                        $objActSheet->setCellValue($NameFromNumber.($j+7), '=IF('.$NameFromNumber.'5<>"",SUM('.$NameFromNumber.'5:'.$NameFromNumber.($j+5).'),"")');
                    }
                }
                //最右方合計語法
                if($i == sizeof($arraykeys)-1){
                    $PreNameFromNumber=$this->getNameFromNumber($i);
                    $objActSheet->setCellValue($NameFromNumber.($j+5), '=IF(B'.($j+5).'<>"",SUM(B'.($j+5).':'.$PreNameFromNumber.($j+5).'),"")');
                }
            }
            if($i>0){
                //C1開始
                $objActSheet->setCellValue($NameFromNumber."4", $arraykeys[$i]);
            }
        }
        //全部套框線
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $objActSheet->getStyle('A4:'.$NameFromNumber.($j+6))->applyFromArray($styleArray);
        $objActSheet->getColumnDimension($NameFromNumber)->setAutoSize(true);

        //凍結B5前所有資料
        $objPHPExcel->getActiveSheet()->freezePane('B5');

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$doctype,"需求名額統計表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename


        // if($doctype=="1")
        // {
        //     //export excel
        //     ob_end_clean();
        //     ob_start();

        //     // Redirect output to a client’s web browser (Excel2007)
        //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //     // 設定下載 Excel 的檔案名稱
        //     header('Content-Disposition: attachment;filename="需求名額統計表.xlsx"');
        //     header('Cache-Control: max-age=0');
        //     // If you're serving to IE 9, then the following may be needed
        //     header('Cache-Control: max-age=1');

        //     // If you're serving to IE over SSL, then the following may be needed
        //     header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        //     header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        //     header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        //     header ('Pragma: public'); // HTTP/1.0
        //     //凍結B5前所有資料
        //     $objPHPExcel->getActiveSheet()->freezePane('B5');
        //     //匯出
        //     //old code
        //     //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //     $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        //     $objWriter->save('php://output');
        //     exit;
        // }else{
        //     //export ods
        //     ob_end_clean();
        //     ob_start();
            
        //     // Redirect output to a client’s web browser (OpenDocument)
        //     header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
        //     header('Content-Disposition: attachment;filename="需求名額統計表.ods"');
        //     header('Cache-Control: max-age=0');
        //     // If you're serving to IE 9, then the following may be needed
        //     header('Cache-Control: max-age=1');

        //     // If you're serving to IE over SSL, then the following may be needed
        //     header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        //     header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        //     header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        //     header ('Pragma: public'); // HTTP/1.0
        //     //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OpenDocument');
        //     $objWriter = IOFactory::createWriter($objPHPExcel, 'Ods');
        //     $objWriter->save('php://output');
        //     exit;
        // }

    }

}
