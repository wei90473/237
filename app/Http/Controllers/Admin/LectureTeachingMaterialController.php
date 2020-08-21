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

class LectureTeachingMaterialController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_teaching_material', $user_group_auth)){
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
        //return view('admin/lecture_teaching_material/list');
        $classArr = $this->getlecturer();
        $result = '';
        return view('admin/lecture_teaching_material/list', compact('classArr','result'));
    }

    // 搜尋下拉『班別』
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
    講座教材一覽表 CSDIR7050
    參考Tables:
        【t10tb 班別教材資料檔】
        【t04tb 開班資料檔】
        【t01tb 班別基本資料檔】
        【t06tb 課程表資料檔】
        【m08tb 教材基本資料檔】
        【m01tb 講座基本資料檔】
    使用範本:Q3B.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //1:授課, 2:編纂
        $radiotype = $request->input('radiotype');
        //講者IDNO
        //$lecturer = $request->input('lecturer');
        $idno = $request->input('lecturer');
        //取得講者姓名
        $sql = " SELECT DISTINCT cname FROM m01tb
                 WHERE idno = '".$idno."'
                ";
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        //判斷授課或者編纂, 1:授課, 2:編纂, 取得講座教材一覽表
        if($radiotype!='2'){
            $sql2 = "SELECT
                            CONCAT(
                            RTRIM( E.handout ),
                            '(',
                            RTRIM(
                            IFNULL( ( SELECT cname FROM m01tb WHERE idno = E.idno ), '' )),
                            ')'
                            ) AS COL1,
                            CONCAT(
                            SUBSTRING( E.date, 1, 3 ),
                            '/',
                            SUBSTRING( E.date, 4, 2 ),
                            '/',
                            SUBSTRING( E.date, 6, 2 )
                            ) AS COL2,
                            CONCAT( '(', SUBSTRING( A.class, 1, 3 ), ')', RTRIM( C.NAME ), A.term ) AS COL3,
                            D.NAME AS COL4,
                            CASE
                            WHEN E.archives = 'Y' THEN
                            '是' ELSE ''
                            END AS COL5,
                            E.remark AS REMARK
                        FROM t10tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
                                     INNER JOIN t01tb C ON A.class = C.class
                                     INNER JOIN t06tb D ON A.class = D.class AND A.term = D.term AND A.course = D.course
                                     INNER JOIN m08tb E ON A.handoutno = E.serno
                        WHERE A.idno = '".$idno."'
                        ORDER BY E.handout";
        }else{
            $sql2 = "SELECT
                            A.handout AS COL1,
                            CONCAT(
                            SUBSTRING( A.date, 1, 3 ),
                            '/',
                            SUBSTRING( A.date, 4, 2 ),
                            '/',
                            SUBSTRING( A.date, 6, 2 )
                            ) AS COL2,
                            CONCAT( '(', SUBSTRING( B.class, 1, 3 ), ')', RTRIM( C.NAME ), B.term ) AS COL3,
                            RTRIM( D.NAME ) AS COL4,
                            CASE
                            WHEN A.archives = 'Y' THEN
                            '是' ELSE ''
                            END AS COL5,
                            A.remark AS REMARK
                        FROM m08tb A INNER JOIN t10tb B ON A.serno = B.handoutno
                                     INNER JOIN t01tb C ON B.class = C.class
                                     INNER JOIN t06tb D ON B.class = D.class AND B.term = D.term AND B.course = D.course
                        WHERE A.idno = '".$idno."'
                        ORDER BY A.handout ";
        }


        $reportlist2 = DB::select($sql2);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        // 檔案名稱
        $fileName = 'Q3B';
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
        if(empty($dataArr)){
            $objActSheet->setCellValue('A1', '講座教材一覽表');
        }else{
            $objActSheet->setCellValue('A1', ''.$dataArr[0]['cname'].'講座教材一覽表');
        }


        //
        if(sizeof($reportlist2) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys2); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //C
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    //3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist2[$j][$arraykeys2[$i]]);
                    //高 40
                    $objActSheet->getRowDimension($j+3)->setRowHeight(40);
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
            $objActSheet->getStyle('A3:'.$NameFromNumber.($j+2))->applyFromArray($arraykeys2);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"講座教材一覽表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
