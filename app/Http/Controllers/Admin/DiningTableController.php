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

class DiningTableController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('dining_table', $user_group_auth)){
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
        return view('admin/dining_table/list');
    }

    /*
    用餐人數概況表 CSDIR6190
    參考Tables:
    使用範本:N3.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //日期
        $sdatetw = $request->input('sdatetw');
        $edatetw = $request->input('edatetw');
        //地區:台北, 南投
        $area = $request->input('area');
        //取得 用餐人數概況表
        $sql="SELECT    X.classname,
                        X.term,
                        CONCAT(SUBSTRING(X.sdate,1,3),'/',SUBSTRING(X.sdate,4,2),'/',SUBSTRING(X.sdate,6,2),'-',
                                    SUBSTRING(X.edate,1,3),'/',SUBSTRING(X.edate,4,2),'/',SUBSTRING(X.edate,6,2)) AS sedate,
                        X.day,
                        SUM(X.amt) AS amt,
                        SUM(X.nonvegan) AS nonvegan,
                        SUM(X.vegan) AS vegan,
                        SUM(X.ednonvegan) AS ednonvegan,
                        SUM(X.edvegan) AS edvegan,
                        RTRIM(X.username) AS username,
                        NULL AS REMARK
                FROM (
                    SELECT
                            RTRIM(B.name) AS classname ,
                            A.class,
                            A.term,
                            A.sdate,
                            A.edate,
                            B.day,
                            (CASE C.status WHEN '1' THEN 1 ELSE 0 END) AS amt,
                            (CASE WHEN C.status = '1' AND C.vegan = 'N' THEN 1 ELSE 0 END) AS nonvegan,
                            (CASE WHEN C.status = '1' AND C.vegan = 'Y' THEN 1 ELSE 0 END) AS vegan,
                            (CASE WHEN C.status = '1' AND C.vegan = 'N' AND extradorm = 'Y' THEN 1 ELSE 0 END) AS ednonvegan,
                            (CASE WHEN C.status = '1' AND C.vegan = 'Y' AND extradorm = 'Y' THEN 1 ELSE 0 END) AS edvegan,
                            IFNULL(D.username,'') AS username
                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                                LEFT JOIN t13tb C ON A.class = C.class AND A.term = C.term
                                LEFT JOIN m09tb D ON A.sponsor = D.userid
                WHERE 1 = (
                                    CASE
                                    WHEN B.yerly >= '110' and B.process = '4' THEN 0
                                    WHEN REPLACE('".$sdatetw."','/','') = '' THEN 1
                                    WHEN A.sdate BETWEEN REPLACE('".$sdatetw."','/','')  AND REPLACE('".$edatetw."','/','')
                                        OR A.edate BETWEEN REPLACE('".$sdatetw."','/','')  AND REPLACE('".$edatetw."','/','')
                                        OR REPLACE('".$sdatetw."','/','')  BETWEEN A.sdate AND A.edate
                                        OR REPLACE('".$edatetw."','/','')   BETWEEN A.sdate AND A.edate THEN 1
                                    ELSE 0
                                    END
                                )
                AND branch = '".$area."'  ) X
                GROUP BY X.classname, X.class, X.term,  X.sdate, X.edate, X.day, X.username
                ORDER BY X.sdate, X.class
        ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'N3';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        //$objActSheet->setCellValue('A2', '日期：'.$sdatetw.'');

        $AreaName='';
        if($area=='1'){
            $AreaName='臺北院區';
        }else{
            $AreaName='南投院區';
        }
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&14'.'行政院人事行政總處公務人力發展學院('.$AreaName.') 用餐人數概況表 &R列印日期：&D　頁次：&P
        '.$sdatetw.'至'.$edatetw);
        $reportlist = json_decode(json_encode($reportlist), true);

        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+2);
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //5開始
                    $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist[$j][$arraykeys[$i]]);
                    $objActSheet->setCellValue('A'.($j+3), $j+1);
                    //高 57.7
                    $objActSheet->getRowDimension($j+3)->setRowHeight(57.7);
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
            //高 57.7
            $objActSheet->getRowDimension($j+3)->setRowHeight(57.7);

            //total
            $objActSheet->mergeCells('A'.($j+3).':E'.($j+3));
            $objActSheet->setCellValue('A'.($j+3), '合計');
            //sum of columns F~J =SUM(F3:F9)
            for($t=6;$t<11;$t++)
            {
                $NameFromNumber=$this->getNameFromNumber($t); //F~J
                $objActSheet->setCellValue($NameFromNumber.($j+3),
                '=SUM('.$this->getNameFromNumber($t).'3:'.$this->getNameFromNumber($t).($j+2).')');
            }

        }

        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="用餐人數概況表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        //匯出
        //old code
        //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $objWriter->save('php://output');

        exit;

    }
}
