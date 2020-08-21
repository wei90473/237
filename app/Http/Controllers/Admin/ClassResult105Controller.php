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

class ClassResult105Controller extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_result_105', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    /*
    年度各班期訓練成效評估統計表 CSDIR5035
    參考Tables:
    使用範本:L3A.xlsx, L3B.xlsx (區別是有無行政服務欄位項目)
    */
    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin/class_result_105/list');
    }

    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //起始年
        $startYear = $request->input('startYear');
        //起始月
        $startMonth = $request->input('startMonth');
        //結束年
        $endYear = $request->input('endYear');
        //結束月
        $endMonth = $request->input('endMonth');
        //行政服務
        $checkboxservice = $request->input('service');

        if($startMonth<10){
            $startMonth='0'.$startMonth;
        }
        if($endMonth<10){
            $endMonth='0'.$endMonth;
        }

        //取得  年度各班期訓練成效評估統計表
        if($checkboxservice!="2"){
            $sql="SELECT CONCAT(IFNULL(t01tb.name,''),
                        '第',
                        (CASE WHEN SUBSTRING(t57tb.term,1,1) ='0' THEN SUBSTRING(t57tb.term,2) ELSE t57tb.term END)
                        , '期\n',
                                SUBSTRING(t04tb.sdate,1,3) ,'/', SUBSTRING(t04tb.sdate,4,2), '/', SUBSTRING(t04tb.sdate,6,2), '~',
                                SUBSTRING(t04tb.edate,1,3) ,'/', SUBSTRING(t04tb.edate,4,2), '/', SUBSTRING(t04tb.edate,6,2)
                    ) AS classname,
                CONCAT( t01tb.period ,
                        case IFNULL(t01tb.kind,'') when '1' then '週'  when '2' then '天'  when '3' then '小時'  else ' ' end
                                ) AS period ,
            t57tb.conper AS conper,
            t57tb.attper AS worper,
            t57tb.worper AS worper1,
            t57tb.teaper AS teaper,
            t57tb.totper AS totper
            FROM t57tb
            LEFT OUTER JOIN t04tb ON t57tb.class=t04tb.class AND t57tb.term=t04tb.term
            LEFT OUTER JOIN t01tb ON t57tb.class=t01tb.class
                    CROSS JOIN (SELECT LPAD('".$startYear.$startMonth."',5,'0') AS sdate, LPAD('".$endYear.$endMonth."',5,'0') AS edate FROM dual) D
            WHERE (D.sdate BETWEEN SUBSTR(t04tb.sdate,1,5) AND SUBSTR(t04tb.edate,1,5)
            OR D.edate BETWEEN SUBSTR(t04tb.sdate,1,5) AND SUBSTR(t04tb.edate,1,5)
            OR SUBSTR(t04tb.sdate,1,5) BETWEEN D.sdate AND D.edate
            OR SUBSTR(t04tb.edate,1,5) BETWEEN D.sdate AND D.edate )
            AND t57tb.times=''
            ORDER BY t04tb.sdate";
        }else{
            $sql="SELECT CONCAT(IFNULL(t01tb.name,''),
                        '第',
                        (CASE WHEN SUBSTRING(t57tb.term,1,1) ='0' THEN SUBSTRING(t57tb.term,2) ELSE t57tb.term END)
                        , '期\n',
                                SUBSTRING(t04tb.sdate,1,3) ,'/', SUBSTRING(t04tb.sdate,4,2), '/', SUBSTRING(t04tb.sdate,6,2), '~',
                                SUBSTRING(t04tb.edate,1,3) ,'/', SUBSTRING(t04tb.edate,4,2), '/', SUBSTRING(t04tb.edate,6,2)
                    ) AS classname,
                CONCAT( t01tb.period ,
                        case IFNULL(t01tb.kind,'') when '1' then '週'  when '2' then '天'  when '3' then '小時'  else ' ' end
                                ) AS period ,
            t57tb.conper AS conper,
            t57tb.attper AS worper,
            t57tb.worper AS worper1,
            t57tb.teaper AS teaper,
            t57tb.totper AS totper,
            t57tb.envper AS envper
            FROM t57tb
            LEFT OUTER JOIN t04tb ON t57tb.class=t04tb.class AND t57tb.term=t04tb.term
            LEFT OUTER JOIN t01tb ON t57tb.class=t01tb.class
                    CROSS JOIN (SELECT LPAD('".$startYear.$startMonth."',5,'0') AS sdate, LPAD('".$endYear.$endMonth."',5,'0') AS edate FROM dual) D
            WHERE (D.sdate BETWEEN SUBSTR(t04tb.sdate,1,5) AND SUBSTR(t04tb.edate,1,5)
            OR D.edate BETWEEN SUBSTR(t04tb.sdate,1,5) AND SUBSTR(t04tb.edate,1,5)
            OR SUBSTR(t04tb.sdate,1,5) BETWEEN D.sdate AND D.edate
            OR SUBSTR(t04tb.edate,1,5) BETWEEN D.sdate AND D.edate )
            AND t57tb.times=''
            ORDER BY t04tb.sdate";
        }

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);

        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        if($checkboxservice!="2"){
            $fileName = 'L3B';
        }
        else{
            $fileName = 'L3A';
        }

        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&14 '.$startYear.'年度各班期訓練成效評估(滿意度)統計表');
        $reportlist = json_decode(json_encode($reportlist), true);

        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1);
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //A2開始
                    $objActSheet->setCellValue($NameFromNumber.($j+2), $reportlist[$j][$arraykeys[$i]]);
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

            $objActSheet->getStyle('A2:'.$NameFromNumber.($j+1))->applyFromArray($styleArray);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"年度各班期訓練成效評估統計表(105)");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
