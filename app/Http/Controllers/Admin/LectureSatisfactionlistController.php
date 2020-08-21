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

class LectureSatisfactionlistController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_satisfactionlist', $user_group_auth)){
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
        //return view('admin/lecture_satisfactionlist/list');
        $classArr = $this->getlecturer();
        $result = '';
        return view('admin/lecture_satisfactionlist/list', compact('classArr','result'));
    }

   // 搜尋下拉『講座姓名』
   public function getlecturer() {
    $sql = "SELECT T.cname, T.idno, T.dept, T.position
            FROM (
                    SELECT
                            (CASE WHEN B.cname IS NOT NULL THEN CONCAT(A.cname,' (',A.idno,') ', A.dept,'-', A.position )
                                ELSE A.cname END) AS cname,
                            A.idno, A.dept, A.position
                    FROM m01tb A LEFT OUTER JOIN
                            (  SELECT cname
                                FROM m01tb
                                GROUP BY cname
                                HAVING count(cname) > 1) B ON A.cname = B.cname
                        GROUP BY A.cname, A.idno, A.dept, A.position, B.cname
                    ) T
            ORDER BY 1 ";
    $classArr = DB::select($sql);
    return $classArr;
}

    /*
    講座滿意度一覽表 CSDIR5050
    參考Tables:
    使用範本:L5.xlsx
    */
    /**
     * 列印檔案
     *
     */
public function export(Request $request)
{
    //含無滿意度調查課程 Y/N
    $checkFlag = $request->input('checkFlag');
    //講座IDNO
    $idno = $request->input('lecturer');
    //期間, 年月
    $startYear = $request->input('startYear');
    if($startYear == NULL){
        $startYear = '080';
    }
    $startMonth = $request->input('startMonth');
    if($startMonth == NULL){
        $startMonth = '01';
    }    
    $endYear = $request->input('endYear');
    if($endYear == NULL){
        $endYear = '200';
    }    
    $endMonth = $request->input('endMonth');
    if($endMonth == NULL){
        $endMonth = '12';
    }        

    //取得講座姓名
    $sql = " SELECT DISTINCT cname FROM m01tb
             WHERE idno = '".$idno."'
            ";
    $reportlist = DB::select($sql);
    $dataArr = json_decode(json_encode($reportlist), true);

    //取得 講座滿意度一覽表
    $sql2 = "SELECT
                    CONCAT(RTRIM(C.name),'第',A.term,'期','(',A.class,')') AS class_name,
                    RTRIM(A.name) AS course_name,
                    A.hour,
                    B.okrate,
                    NULL AS REMARK
                FROM t06tb A INNER JOIN t09tb B ON A.class=B.class AND A.term=B.term AND A.course=B.course
                            INNER JOIN t01tb C ON B.class=C.class
                            INNER JOIN t04tb D ON A.class = D.class AND A.term = D.term
                WHERE B.idno = '".$idno."'
                AND 1 = (
                            CASE
                            WHEN 'Y' = IFNULL('".$checkFlag."','N') THEN 1
                            WHEN B.okrate > 0     THEN 1
                            END
                        )
                AND 1 = (
                            CASE
                            WHEN CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0')) = ''
                                THEN 1
                            WHEN SUBSTRING(D.sdate,1,5) BETWEEN
                                 CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0')) AND
                                 CONCAT(LPAD('".$endYear."',3,'0'),LPAD('".$endMonth."',2,'0'))
                                THEN 1
                            END
                        )
                ORDER BY C.class,A.term,A.course
                ";


    $reportlist2 = DB::select($sql2);
    //取出全部項目
    if(sizeof($reportlist2) != 0) {
        $arraykeys2=array_keys((array)$reportlist2[0]);
    }

    // 檔案名稱
    $fileName = 'L5';
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
    $objActSheet->setCellValue('A1', ''.$dataArr[0]['cname'].'講座滿意度一覽表');

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
                //高 33
                $objActSheet->getRowDimension($j+3)->setRowHeight(33);
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
        $objActSheet->getStyle('A3:'.$NameFromNumber.($j+3))->applyFromArray($arraykeys2);
        //total
        $objActSheet->setCellValue('A'.($j+3), '合計');
        //sum of columns C =SUM(C3:C24)
        $objActSheet->setCellValue('C'.($j+3),
                '=SUM('.'C3:C'.($j+2).')');
         //=AVERAGEIF(D3:D24,">0") , 排除無統計不平均
        $objActSheet->setCellValue('D'.($j+3),
                '=AVERAGEIF(D3:D'.($j+2).',">0")');
        //高 33
        $objActSheet->getRowDimension($j+3)->setRowHeight(33);
    }

    $RptBasic = new \App\Rptlib\RptBasic();
    $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"講座滿意度一覽表");
    //$obj: entity of file
    //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
    //$doctype:1.ooxml 2.odf
    //$filename:filename 

    }
}
