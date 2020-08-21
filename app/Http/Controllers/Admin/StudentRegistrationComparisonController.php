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

class StudentRegistrationComparisonController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_registration_comparison', $user_group_auth)){
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
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;
        $result = '';
        return view('admin/student_registration_comparison/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    /*
    學員報名狀況對照表 CSDIR4180
    參考Tables:
    使用範本:J19.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //班別
        $classes = $request->input('classes');
        //期別起迄
        $startterm = $request->input('startterm');
        $endterm = $request->input('endterm');

        //取得TITLE
        $sqlTitle = "SELECT DISTINCT CONCAT(t01tb.name, '第',
                                            CASE '".$startterm."'
                                                        WHEN '01' THEN '1'
                                                        WHEN '02' THEN '2'
                                                        WHEN '03' THEN '3'
                                                        WHEN '04' THEN '4'
                                                        WHEN '05' THEN '5'
                                                        WHEN '06' THEN '6'
                                                        WHEN '07' THEN '7'
                                                        WHEN '08' THEN '8'
                                                        WHEN '09' THEN '9'
                                                        ELSE '".$startterm."'
                                            END,
                                            '期至第',
                                            CASE '".$endterm."'
                                                        WHEN '01' THEN '1'
                                                        WHEN '02' THEN '2'
                                                        WHEN '03' THEN '3'
                                                        WHEN '04' THEN '4'
                                                        WHEN '05' THEN '5'
                                                        WHEN '06' THEN '6'
                                                        WHEN '07' THEN '7'
                                                        WHEN '08' THEN '8'
                                                        WHEN '09' THEN '9'
                                                        ELSE '".$endterm."'
                                            END,
                                            '期學員報名狀況對照表') AS TITLE
                            FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                            WHERE t04tb.class = '".$classes."'
                            AND t04tb.term= '".$startterm."'
                            ORDER BY t04tb.class DESC
                ";
        $reportlistTitle = DB::select($sqlTitle);
        $dataArrTitle = json_decode(json_encode($reportlistTitle), true);

        //TITLE TERM & DATE
        $sql = "SELECT CONCAT('\n第',CASE term
                                    WHEN '01' THEN '1'
                                    WHEN '02' THEN '2'
                                    WHEN '03' THEN '3'
                                    WHEN '04' THEN '4'
                                    WHEN '05' THEN '5'
                                    WHEN '06' THEN '6'
                                    WHEN '07' THEN '7'
                                    WHEN '08' THEN '8'
                                    WHEN '09' THEN '9'
                                    ELSE term
                                END, '期\n',
                              SUBSTRING(sdate,1,3), '/', SUBSTRING(sdate,4,2), '/', SUBSTRING(sdate,6,2), '\n',
                              '│\n',
                              SUBSTRING(edate,1,3), '/', SUBSTRING(edate,4,2), '/', SUBSTRING(edate,6,2))
                                AS sedate
                FROM t04tb
                WHERE class= '".$classes."'
                AND term BETWEEN '".$startterm."' AND '".$endterm."'
                ORDER BY term
                    ";
        $reportlist = DB::select($sql);
        $dataArr= json_decode(json_encode($reportlist), true);

        //取得 各TERM查詢條件參數
        $sql2P = "SELECT term
                    FROM t04tb
                    WHERE class= '".$classes."'
                    AND term BETWEEN '".$startterm."' AND '".$endterm."'
                    ORDER BY term
                    ";
        $reportlist2P = DB::select($sql2P);
        $dataArr2P= json_decode(json_encode($reportlist2P), true);
        $sqlTerm  ='';
        if(sizeof($reportlist2P) != 0) {
            for ($j=0; $j < sizeof($reportlist2P); $j++) {
                $sqlTerm = $sqlTerm.' SUM(CASE WHEN A.term=\''.($dataArr2P[$j]['term']).'\' THEN A.quota ELSE 0 END), ';
                $sqlTerm = $sqlTerm.' SUM(CASE WHEN A.term=\''.($dataArr2P[$j]['term']).'\' THEN t13tb_count ELSE 0 END), ';
            }
        }

        //取得 學員報名狀況對照表
        $sql2 = "SELECT CASE
                            WHEN B.lname IS NULL
                            THEN RTRIM(C.name)
                            ELSE RTRIM(B.lname)
                            END AS lname, ".$sqlTerm."SUM(A.quota), SUM(t13tb_count)
                    FROM (
                                SELECT  X.class,
                                                X.term,
                                                Y.organ,
                                                X.quota,
                                                COUNT(Z.idno) AS t13tb_count
                                FROM t51tb X INNER JOIN m17tb Y  ON X.organ=Y.enrollorg AND Y.grade='1'
                                                            LEFT JOIN t13tb Z ON X.class=Z.class AND X.term=Z.term AND Y.organ=Z.organ
                                WHERE X.class= '".$classes."'
                                    AND X.term BETWEEN '".$startterm."' AND '".$endterm."'
                                GROUP BY X.class, X.term, Y.organ, X.quota
                                UNION ALL
                                SELECT class,
                                            term,
                                            organ,
                                            0 AS quota,
                                            COUNT(idno) AS t13tb_count
                                FROM t13tb
                                WHERE class='".$classes."'
                                    AND term BETWEEN '".$startterm."' AND '".$endterm."'
                                    AND NOT EXISTS(
                                            SELECT *
                                                FROM t51tb INNER JOIN m17tb ON t51tb.organ=m17tb.enrollorg AND m17tb.grade='1'
                                                WHERE t51tb.class=t13tb.class
                                                    AND t51tb.term=t13tb.term
                                                AND m17tb.organ=t13tb.organ )
                    GROUP BY class,term,organ
                            ) A LEFT JOIN m13tb B ON A.organ=B.organ
                                        LEFT JOIN m07tb C ON A.organ=C.agency
                    GROUP BY A.organ, B.lname,B.rank, C.name
                    ORDER BY B.rank
                    ";
        $reportlist2 = DB::select($sql2);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        // 檔案名稱
        $fileName = 'J19';
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
        $objActSheet->setCellValue('A2', ''.$dataArrTitle[0]['TITLE']);

        //日期起迄
        for ($i=0; $i < sizeof($reportlist); $i++) {
            $NameFromNumberA=$this->getNameFromNumber(($i+1)*2); //A
            $NameFromNumberB=$this->getNameFromNumber(($i+1)*2+1); //A
            //設定欄寬
            $objActSheet->getColumnDimension($NameFromNumberA)->setWidth(16);
            $objActSheet->getColumnDimension($NameFromNumberB)->setWidth(16);
            $objActSheet->mergeCells($NameFromNumberA.'4:'.$NameFromNumberB.'4');

            $objActSheet->setCellValue($NameFromNumberA.'5','線上分配');
            $objActSheet->setCellValue($NameFromNumberB.'5','報名人數');
            $objActSheet->setCellValue($NameFromNumberA.'4', ''.$dataArr[$i]['sedate']);
        }
        $NameFromNumberA=$this->getNameFromNumber(($i+1)*2); //A
        $NameFromNumberB=$this->getNameFromNumber(($i+1)*2+1); //A
        $objActSheet->getColumnDimension($NameFromNumberA)->setWidth(16);
        $objActSheet->getColumnDimension($NameFromNumberB)->setWidth(16);
        $objActSheet->mergeCells($NameFromNumberA.'4:'.$NameFromNumberB.'4');
        $objActSheet->setCellValue($NameFromNumberA.'4', '合計');
        $objActSheet->setCellValue($NameFromNumberA.'5','線上分配');
        $objActSheet->setCellValue($NameFromNumberB.'5','報名人數');

        //dd($reportlist2);
        if(sizeof($reportlist2) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys2); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    //6開始
                    $objActSheet->setCellValue($NameFromNumber.($j+6), $reportlist2[$j][$arraykeys2[$i]]);
                    //合計
                    if($i>=1 && $j==0){
                        //=SUM(B6:B49)
                        $objActSheet->setCellValue(($NameFromNumber.'50'), '=SUM('.$NameFromNumber.'6:'.$NameFromNumber.'49)');
                    }
                    //若分配人數與報名人數不一致，字型改成(粗體)
                    //若【分配人數】與報名人數不一致，將【報名人數】的字體改成(粗體+紅色)
                    if($i>=1 && ($i%2)==0){
                        if($reportlist2[$j][$arraykeys2[$i]]<>$reportlist2[$j][$arraykeys2[$i-1]]){
                            //將單元格設置爲粗體字
                            $objActSheet->getStyle($NameFromNumber.($j+6))->getFont()->setBold(true);
                            //將單元格文字顏色設爲紅色
                            $objActSheet->getStyle($NameFromNumber.($j+6))->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                        }
                    }
                    //高 27
                    //$objActSheet->getRowDimension($j+3)->setRowHeight(27);
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
            $objActSheet->getStyle('A6:'.$NameFromNumberB.'50')->applyFromArray($arraykeys2);
            $objActSheet->getStyle('B4:'.$NameFromNumberB.'50')->applyFromArray($arraykeys2);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"名額分配及需求填報對照表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
