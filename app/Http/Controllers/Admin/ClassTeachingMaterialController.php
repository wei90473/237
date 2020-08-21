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

class ClassTeachingMaterialController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_teaching_material', $user_group_auth)){
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
        //eturn view('admin/class_teaching_material/list');
        $classArr = $this->getclass();
        $result = '';
        return view('admin/class_teaching_material/list', compact('classArr', 'result'));
    }

    // 搜尋下拉『班別』
    public function getclass() {
        $sql = "SELECT DISTINCT class,RTRIM(name) AS name FROM t01tb ORDER BY class DESC";
        $classArr = DB::select($sql);
        return $classArr;
    }

    // 搜尋下拉『期別』
    public function getTermByOP(Request $request)
    {
        $class = $request->input('class');
        $sql = "SELECT DISTINCT term FROM t04tb WHERE class='".$class."'
                 ORDER BY 1 ";
        $classArr = DB::select($sql);
        return $classArr;
    }

    /*
    班別教材一覽表 CSDIR7040
    參考Tables:
        【t10tb 班別教材資料檔】
        【t04tb 開班資料檔】
        【t01tb 班別基本資料檔】
        【t06tb 課程表資料檔】
        【m08tb 教材基本資料檔】
        【m01tb 講座基本資料檔】
        【t08tb 擬聘講座資料檔】
    使用範本:Q3A.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        $class = $request->input('class');
        $term = $request->input('term');

        //取得TITLE
        $sql = "SELECT DISTINCT CONCAT(t01tb.name, '第', CASE WHEN SUBSTRING(t53tb.term,1,1) = '0' THEN SUBSTRING(t53tb.term,2) ELSE t53tb.term END
                                                       , '教材一覽表' ) AS TITLE
                  FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                 WHERE t53tb.times<>''
                   AND t53tb.class = '".$class."'
                   AND t53tb.term = '".$term."'
                ORDER BY 1 DESC";
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        //取得班別教材一覽表
        $sql2 = "SELECT
                        CASE
                                WHEN
                                    A.date <> '' THEN
                                            CONCAT(
                                                    SUBSTRING( A.date, 1, 3 ),
                                                    '/',
                                                    SUBSTRING( A.date, 4, 2 ),
                                                    '/',
                                                    SUBSTRING( A.date, 6, 2 )
                                            ) ELSE ''
                                    END AS YYYMMDD,
                                    RTRIM( A.NAME ) AS NAME,
                                    IFNULL(( SELECT cname FROM m01tb WHERE idno = F.idno ), '' ),
                                    CONCAT(
                                            RTRIM ( E.handout ),
                                            '(',
                                            RTRIM(
                                            IFNULL( ( SELECT cname FROM m01tb WHERE idno = E.idno ), '' )),
                                            ')'
                                    ) AS handout,
                                    CONCAT(
                                            SUBSTRING( E.date, 1, 3 ),
                                            '/',
                                            SUBSTRING( E.date, 4, 2 ),
                                            '/',
                                            SUBSTRING( E.date, 6, 2 )
                                    ) ,
                                    CASE
                                            WHEN E.publish = 'Y' THEN
                                            '是' ELSE ''
                                    END AS COL1,
                                    CASE
                                            WHEN E.archives = 'Y' THEN
                                            '是' ELSE ''
                                    END AS COL2,
                                    E.remark
                            FROM t06tb A
                                    LEFT JOIN t04tb B ON A.class = B.class
                                    AND A.term = B.term
                                    LEFT JOIN t10tb C ON A.class = C.class
                                    AND A.term = C.term
                                    AND A.course = C.course
                                    LEFT JOIN t01tb D ON A.class = D.class
                                    LEFT JOIN m08tb E ON C.handoutno = E.serno
                                    LEFT JOIN t08tb F ON A.class = F.class
                                    AND A.term = F.term
                                    AND A.course = F.course
                            WHERE A.date <> ''
                                    AND F.idno <> ''
                                    AND F.hire = 'Y'
                                    AND A.class = '".$class."'
                                    AND A.term = '".$term."'
                            ORDER BY A.date, A.stime ";

        $reportlist2 = DB::select($sql2);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        // 檔案名稱
        $fileName = 'Q3A';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $reportlist = json_decode(json_encode($reportlist), true);
        $reportlist2 = json_decode(json_encode($reportlist2), true);

        //TITLE
        if(empty($dataArr)){
            $objActSheet->setCellValue('A1', '教材一覽表');
        }else{
            $objActSheet->setCellValue('A1', $dataArr[0]['TITLE']);
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
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"名額班別教材一覽表分配表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
