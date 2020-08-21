<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Services\User_groupService;

class OrganSidComparisonController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('organ_sid_comparison', $user_group_auth)){
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
        //取得班別
        $temp=DB::select("SELECT DISTINCT class,RTRIM(name) as name FROM t01tb
        WHERE EXISTS (  SELECT * FROM t04tb WHERE class=t01tb.class) ORDER BY class DESC");
        $classArr=$temp;
         $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;
        $result = '';
        return view('admin/organ_sid_comparison/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    /*
    學員服務機關與學號對照表 CSDIR4190
    參考Tables:
    使用範本:J20.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //班別
        $classes = $request->input('classes');
        //期別
        $term = $request->input('term');

        //取得Title
        $sqlTitle = "SELECT DISTINCT CONCAT(t01tb.name, '第',
                                            CASE t04tb.term
                                                        WHEN '01' THEN '1'
                                                        WHEN '02' THEN '2'
                                                        WHEN '03' THEN '3'
                                                        WHEN '04' THEN '4'
                                                        WHEN '05' THEN '5'
                                                        WHEN '06' THEN '6'
                                                        WHEN '07' THEN '7'
                                                        WHEN '08' THEN '8'
                                                        WHEN '09' THEN '9'
                                                        ELSE t04tb.term
                                            END
                                            , '期服務機關與學號對照表') AS TITLE
                            FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                            WHERE t04tb.class = '".$classes."'
                            AND t04tb.term= '".$term."'
                            ORDER BY t04tb.class DESC
                ";
        $reportlistTitle = DB::select($sqlTitle);
        $dataArrTitle = json_decode(json_encode($reportlistTitle), true);

        //取得 日期起迄
        $sql = "SELECT CONCAT(SUBSTRING(sdate,1,3), '.', SUBSTRING(sdate,4,2), '.', SUBSTRING(sdate,6,2), '~',
                              SUBSTRING(edate,1,3), '.', SUBSTRING(edate,4,2), '.', SUBSTRING(edate,6,2))
                                AS sedate
                FROM t04tb
                WHERE class= '".$classes."'
                AND term='".$term."'
                    ";
        $reportlist = DB::select($sql);
        $dataArr= json_decode(json_encode($reportlist), true);

        //取得 學員服務機關與學號對照表
        $sql2 = "SELECT T.organ_name,
                        CASE WHEN MIN(T.no) = MAX(T.no) THEN MIN(T.no)
                            WHEN CAST((MAX(T.no) - MIN(T.no)) AS signed) + 1 <> COUNT(T.no) THEN CONCAT(MIN(T.no), ',',MAX(T.no))
                            ELSE CONCAT(MIN(T.no), '～',MAX(T.no))
                        END AS NO_MIN_MAX,
                        NULL AS COL1, NULL AS COL2
                    FROM (
                            SELECT A.organ, RTRIM(B.lname) AS organ_name, A.no
                            FROM t13tb A INNER JOIN m13tb B ON A.organ=B.organ
                            WHERE A.class='".$classes."'
                            AND A.term='".$term."'
                            AND A.status= '1'
                            AND A.no<>''
                                        ORDER BY A.organ,no
                            ) T
                    GROUP BY T.organ, T.organ_name
                    ORDER BY T.organ, 2
                    ";
        $reportlist2 = DB::select($sql2);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        // 檔案名稱
        $fileName = 'J20';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $reportlist2 = json_decode(json_encode($reportlist2), true);

        //TITLE
        $objActSheet->setCellValue('A1', ''.$dataArrTitle[0]['TITLE']);

        //日期起迄
        $objActSheet->setCellValue('A2', ''.$dataArr[0]['sedate']);

        //
        if(sizeof($reportlist2) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys2); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    //3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist2[$j][$arraykeys2[$i]]);
                    //高 27
                    $objActSheet->getRowDimension($j+3)->setRowHeight(27);
                }
            }
            $arraykeys2 = [
                'borders' => [
            //只有外框           'outline' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];
            $objActSheet->getStyle('A3:D'.($j+2))->applyFromArray($arraykeys2);
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"學員服務機關與學號對照表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
