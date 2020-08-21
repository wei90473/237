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

class YearlyTeachingMaterialController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('yearly_teaching_material', $user_group_auth)){
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
        return view('admin/yearly_teaching_material/list');
    }

    /*
    年度教材總清冊 CSDIR7030
    參考Tables:
        【t10tb 班別教材資料檔】
        【t04tb 開班資料檔】
        【t01tb 班別基本資料檔】
        【t06tb 課程表資料檔】
        【m08tb 教材基本資料檔】
    使用範本:Q2.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //起始日期
        $sdatetw = $request->input('sdatetw');
        //截止日期
        $edatetw = $request->input('edatetw');

        //取得年度教材總清冊
        $sql="SELECT
                        CONCAT(
                                RTRIM( E.handout ),
                                '(',
                                RTRIM( IFNULL(( SELECT cname FROM m01tb WHERE idno = E.idno ), '' ) ),
                                ')'
                        ) ,
                        CONCAT(
                                SUBSTRING( E.date, 1, 3 ),
                                '/',
                                SUBSTRING( E.date, 4, 2 ),
                                '/',
                                SUBSTRING( E.date, 6, 2 )
                        ) ,
                        RTRIM(
                        IFNULL(( SELECT cname FROM m01tb WHERE idno = A.idno ), '' )),
                        CONCAT( '(', SUBSTRING( A.class, 1, 3 ), ')', RTRIM( C.NAME ), A.term )  ,
                        RTRIM( D.NAME ) ,
                CASE
                                WHEN E.archives = 'Y' THEN
                                '是' ELSE ''
                        END ,
                        RTRIM( E.remark )
                FROM
                        t10tb A
                        INNER JOIN t04tb B ON A.class = B.class
                        AND A.term = B.term
                        INNER JOIN t01tb C ON A.class = C.class
                        INNER JOIN t06tb D ON A.class = D.class
                        AND A.term = D.term
                        AND A.course = D.course
                        INNER JOIN m08tb E ON A.handoutno = E.serno
                WHERE B.sdate BETWEEN REPLACE('".$sdatetw."','/','') AND REPLACE('".$edatetw."','/','')
                ORDER BY E.handout ";

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'Q2';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setCellValue('E1', '期間：'.$sdatetw.'~'.$edatetw.'');

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
                    //高 40
                    $objActSheet->getRowDimension($j+3)->setRowHeight(40);
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

            $objActSheet->getStyle('A2:'.$NameFromNumber.($j+2))->applyFromArray($styleArray);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"年度教材總清冊");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
