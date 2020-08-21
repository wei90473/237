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

class ClassResult9092Controller extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_result_90_92', $user_group_auth)){
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
        return view('admin/class_result_90_92/list');
    }

    /*
    年度各班期訓練成效評估統計表(90~92年) CSDIR5030
    參考Tables:
    使用範本:L20B.docx (新版), L20A.docs (舊版)
    'History:
    '2004/01/16 Update
    '輸入年度需介於90年~92年
    '2003/11/21 Update
    '將【整體滿意】欄位名稱改為【整體評價】
    '範本:CSDIR5030b.xlt
    '2003/10/07 Update
    'Fix:
    '1.資料少一筆
    '2.當資料只有一筆時，劃框線有問題
    '3.t04tb加入times欄位，【舊版問卷】無法產生
    '2003/05/15 Update
    '將【工作】欄位名稱改為【工作相關性】
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //年
        $startYear = $request->input('startYear');
        //版本  0 '舊版 , 1 '新版
        $radioinfo = $request->input('info');

        //取得年度各班期訓練成效評估統計表
        if($radioinfo!="1"){
            $sql="SELECT
                        CONCAT(IFNULL(t01tb.name,''),
                        '第',
                                (CASE WHEN SUBSTRING(t18tb.term,1,1) ='0' THEN SUBSTRING(t18tb.term,2) ELSE t18tb.term END)
                                , '期\n',
                                SUBSTRING(t04tb.sdate,1,3) ,'/', SUBSTRING(t04tb.sdate,4,2), '/', SUBSTRING(t04tb.sdate,6,2), '~',
                                SUBSTRING(t04tb.edate,1,3) ,'/', SUBSTRING(t04tb.edate,4,2), '/', SUBSTRING(t04tb.edate,6,2)
                        ) as classname,
                        CONCAT( t01tb.period ,
                            case IFNULL(t01tb.kind,'') when '1' then '週'  when '2' then '天'  when '3' then '小時'  else ' ' end
                                ) AS period ,
                    t18tb.whoper as whoper,
                    t18tb.teaper as teaper,
                    t18tb.couper as couper,
                    t18tb.matper as matper,
                    t18tb.lifper as lifper,
                    t18tb.affper as affper,
                    t18tb.fooper as fooper,
                    t18tb.boaper as boaper,
                    t18tb.totper as totper
                From t18tb left outer join t04tb on t18tb.class=t04tb.class and t18tb.term=t04tb.term
                            left outer join t01tb on t18tb.class=t01tb.class
                where substring(t18tb.class,1,3) = LPAD('".$startYear."',3,'0')
                and t18tb.times=''
                order by t04tb.sdate";
        }else{
            $sql="SELECT
                        CONCAT(IFNULL(t01tb.name,''),
                            '第',
                                    (CASE WHEN SUBSTRING(t57tb.term,1,1) ='0' THEN SUBSTRING(t57tb.term,2) ELSE t57tb.term END)
                                    , '期\n',
                                    SUBSTRING(t04tb.sdate,1,3) ,'/', SUBSTRING(t04tb.sdate,4,2), '/', SUBSTRING(t04tb.sdate,6,2), '~',
                                    SUBSTRING(t04tb.edate,1,3) ,'/', SUBSTRING(t04tb.edate,4,2), '/', SUBSTRING(t04tb.edate,6,2)
                            ) as classname,
                        CONCAT( t01tb.period ,
                                case IFNULL(t01tb.kind,'') when '1' then '週'  when '2' then '天'  when '3' then '小時'  else ' ' end
                                    ) AS period ,
                        t57tb.whoper as whoper,
                    t57tb.teaper as teaper,
                    t57tb.conper as conper,
                    t57tb.envper as envper,
                    t57tb.fooper as fooper,
                    t57tb.boaper as boaper,
                    t57tb.worper as worper,
                        t57tb.totper as totper
                From t57tb
                    left outer join t04tb on t57tb.class=t04tb.class and t57tb.term=t04tb.term
                    left outer join t01tb on t57tb.class=t01tb.class
                where SUBSTRING(t57tb.class,1,3) = LPAD('".$startYear."',3,'0')
                    and t57tb.times=''
                order by t04tb.sdate";
        }

        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);

        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱,  A舊版, B新版
        if($radioinfo!="1"){
            $fileName = 'L20A';
        }
        else{
            $fileName = 'L20B';
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

        //
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

        $outputname="";
        // 設定下載 Excel 的檔案名稱  A舊版, B新版
        if($radioinfo!="1"){
            $outputname="年度各班期訓練成效評估統計表(90~92)-舊版問卷";
        }
        else{
            $outputname="年度各班期訓練成效評估統計表(90~92)-新版問卷";
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
