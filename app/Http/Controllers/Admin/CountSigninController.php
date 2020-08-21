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

class CountSigninController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('count_signin', $user_group_auth)){
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
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclasstypek();
        $class=$temp;
        $result="";

        return view('admin/count_signin/list',compact('result','class'));
    }

    /*
    報到人數統計表 CSDIR4110
    參考Tables:
    使用範本:J14.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //日期範圍,民國年月日
        $sdatetw = $request->input('sdatetw');
        $edatetw = $request->input('edatetw');
        //班別性質
        $outputtype = $request->input('outputtype');
        //訓練班別
        $classtype = $request->input('classtype');
        //dd($classtype);

        //取得 報到人數統計表
        if($outputtype=="2"){
            $sql = " SELECT RTRIM(B.name) classname,
                            CONCAT('第',A.term,'期') AS term,
                            CONCAT(SUBSTRING(A.sdate, 1, 3),'.',SUBSTRING(A.sdate, 4, 2),'.',SUBSTRING(A.sdate, 6, 2)) AS sdate,
                            CONCAT(SUBSTRING(A.edate, 1, 3),'.',SUBSTRING(A.edate, 4, 2),'.',SUBSTRING(A.edate, 6, 2)) AS edate,
                        A.quota,
                        (SELECT COUNT(*) FROM t13tb WHERE
                        class = A.class And term = A.term
                        AND status = '1'),
                        (SELECT COUNT(*) FROM t13tb WHERE
                        class = A.class And term = A.term
                        AND status = '2'),
                        (SELECT COUNT(*) FROM t13tb WHERE
                        class = A.class And term = A.term
                        AND status = '3'),
                        0,
                        '' AS remark
                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                    WHERE A.edate BETWEEN REPLACE('".$sdatetw."','/','') AND REPLACE('".$edatetw."','/','')
                    AND type='13'
                    Order by B.rank, A.class, A.term, A.sdate, A.edate
                                ";
        }elseif($classtype<>'0'){
            $sql = " SELECT RTRIM(B.name) classname,
                            CONCAT('第',A.term,'期') AS term,
                            CONCAT(SUBSTRING(A.sdate, 1, 3),'.',SUBSTRING(A.sdate, 4, 2),'.',SUBSTRING(A.sdate, 6, 2)) AS sdate,
                            CONCAT(SUBSTRING(A.edate, 1, 3),'.',SUBSTRING(A.edate, 4, 2),'.',SUBSTRING(A.edate, 6, 2)) AS edate,
                        A.quota,
                        (SELECT COUNT(*) FROM t13tb WHERE
                        class = A.class And term = A.term
                        AND status = '1'),
                        (SELECT COUNT(*) FROM t13tb WHERE
                        class = A.class And term = A.term
                        AND status = '2'),
                        (SELECT COUNT(*) FROM t13tb WHERE
                        class = A.class And term = A.term
                        AND status = '3'),
                        0,
                        '' AS remark
                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                    WHERE A.edate BETWEEN REPLACE('".$sdatetw."','/','') AND REPLACE('".$edatetw."','/','')
                    AND EXISTS (SELECT *
                                 FROM t01tb
                                WHERE type= '".$classtype."'
                                  AND class=A.class
                                )
                    Order by B.rank, A.class, A.term, A.sdate, A.edate
                                ";
        }else{
            $sql = " SELECT RTRIM(B.name) classname,
                            CONCAT('第',A.term,'期') AS term,
                            CONCAT(SUBSTRING(A.sdate, 1, 3),'.',SUBSTRING(A.sdate, 4, 2),'.',SUBSTRING(A.sdate, 6, 2)) AS sdate,
                            CONCAT(SUBSTRING(A.edate, 1, 3),'.',SUBSTRING(A.edate, 4, 2),'.',SUBSTRING(A.edate, 6, 2)) AS edate,
                        A.quota,
                        (SELECT COUNT(*) FROM t13tb WHERE
                        class = A.class And term = A.term
                        AND status = '1'),
                        (SELECT COUNT(*) FROM t13tb WHERE
                        class = A.class And term = A.term
                        AND status = '2'),
                        (SELECT COUNT(*) FROM t13tb WHERE
                        class = A.class And term = A.term
                        AND status = '3'),
                        0,
                        '' AS remark
                    FROM t04tb A INNER JOIN t01tb B ON A.class = B.class
                    WHERE A.edate BETWEEN REPLACE('".$sdatetw."','/','') AND REPLACE('".$edatetw."','/','')
                    AND type<>'13'
                    Order by B.rank, A.class, A.term, A.sdate, A.edate
                                ";
        }


        $reportlist = DB::select($sql);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'J14';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $reportlist = json_decode(json_encode($reportlist), true);

        //查詢條件日期
        $objActSheet->setCellValue('A1', '日期:'.substr($sdatetw,0,3).'年'.substr($sdatetw,4,2).'月'.substr($sdatetw,7,2).'日~'.substr($edatetw,0,3).'年'.substr($edatetw,4,2).'月'.substr($edatetw,7,2).'日');

        //
        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //3開始
                    if($i==8){
                        //=IF(E3>0,IF(F3>0,(F3/E3),0),"")
                        $objActSheet->setCellValue('I'.($j+3),'=IF(E'.($j+3).'>0,IF(F'.($j+3).'>0,(F'.($j+3).'/E'.($j+3).'),0),"")');
                    } else{
                        $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist[$j][$arraykeys[$i]]);
                    }

                    //高 40
                    //$objActSheet->getRowDimension($j+3)->setRowHeight(40);
                }
            }

            //總計使用公式加總
            $objActSheet->setCellValue('A'.($j+3),'總計');
            //=SUM(E3:E102)
            $objActSheet->setCellValue('E'.($j+3),'=SUM(E3:E'.($j+2).')');
            $objActSheet->setCellValue('F'.($j+3),'=SUM(F3:F'.($j+2).')');
            $objActSheet->setCellValue('G'.($j+3),'=SUM(G3:G'.($j+2).')');
            $objActSheet->setCellValue('H'.($j+3),'=SUM(H3:H'.($j+2).')');
            //=AVERAGE(I3:I102)
            $objActSheet->setCellValue('I'.($j+3),'=AVERAGE(I3:I'.($j+2).')');
            $arraykeys = [
                'borders' => [
            //只有外框           'outline' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];
            $objActSheet->getStyle('A3:J'.($j+3))->applyFromArray($arraykeys);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"報到人數統計表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    
    }
}
