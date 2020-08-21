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

class CourseTeachingMaterialController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('course_teaching_material', $user_group_auth)){
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
        //eturn view('admin/course_teaching_material/list');
        $classArr = $this->getclass();
        $result = '';
        return view('admin/course_teaching_material/list', compact('classArr', 'result'));
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
    開班教材清單 CSDIR7060
    參考Tables:
        【t06tb 課程表資料檔】
    使用範本:Q3C.xlsx
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

        //取得開班教材一覽表
        $sql2 = "SELECT RTRIM(t06tb.NAME) AS NAME,
                        IFNULL(m01tb.cname,'') AS CNAME,
                        NULL AS COL1,NULL AS COL2,NULL AS COL3,NULL AS COL4,NULL AS COL5
                FROM t06tb INNER JOIN t09tb ON t06tb.class = t09tb.class AND t06tb.term = t09tb.term AND t06tb.course = t09tb.course
                        LEFT OUTER JOIN m01tb ON t09tb.idno = m01tb.idno
                WHERE t06tb.class = '".$class."'
                AND t06tb.term = '".$term."'
                ORDER BY t06tb.date,t06tb.stime ";

        $reportlist2 = DB::select($sql2);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        // 檔案名稱
        $fileName = 'Q3C';
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

        //dd($reportlist2);
        //
        if(sizeof($reportlist2) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys2); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //C
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    //3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+4), $reportlist2[$j][$arraykeys2[$i]]);
                    //高 40
                    $objActSheet->getRowDimension($j+4)->setRowHeight(40);
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
            $objActSheet->getStyle('A4:'.$NameFromNumber.($j+3))->applyFromArray($arraykeys2);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"開班教材一覽表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename
    }
}
