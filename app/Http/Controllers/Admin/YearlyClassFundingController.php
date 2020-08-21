<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;

class YearlyClassFundingController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('yearly_class_funding', $user_group_auth)){
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
        $result = '';
        return view('admin/yearly_class_funding/list',compact('result'));
    }

    public function export(Request $request)
    {
        $doctype = $request->input('doctype');
        $year=$request->get('Year');
        $month=$request->get('Month');
        $year=str_pad($year,3,'0',STR_PAD_LEFT);
        $month=str_pad($month,2,'0',STR_PAD_LEFT);
        $montheday=substr(date('Y-m-t', strtotime(strval($year+1911)."/"."$month"."/01")),8,2);
        $sdate="";
        $edate="";

        if($month=="00"){
            $sdate=$year."0101";
            $edate=$year."1231";
            $A2=$year."年度班期費用統計表";
        }else{
            $sdate=$year.$month."01";
            $edate=$year.$month.$montheday;
            $A2=$year."年度".$month."月班期費用統計表";
        }
        //取得列的資料
        $sql="Select
        CONCAT(t01tb.name,'第', CAST(CAST(t07tb.term AS INT) AS CHAR),'期'),
        inlectamt+burlectamt+outlectamt+othlectamt+motoramt+trainamt+planeamt+noteamt+speakamt,
        drawamt,vipamt+doneamt+sinamt,meaamt+lunamt+dinamt,docamt,penamt,insamt+actamt+caramt,placeamt,
        teaamt+prizeamt+birthamt+unionamt+setamt+dishamt+otheramt1+otheramt2
        From t01tb, t04tb, t07tb
        Where t01tb.class = t07tb.class and t04tb.class=t07tb.class and t04tb.term=t07tb.term
        AND t07tb.type='2' AND ( t04tb.edate BETWEEN ".$sdate." AND ".$edate.")
        Order by t04tb.sdate";

        $temp=json_decode(json_encode(DB::select($sql)), true);

        if($temp==[]){
            $result = '查無此期間的資料。';
            return view('admin/yearly_class_funding/list',compact('result'));
        }

        $data=$temp;
        $datakey=array_keys((array)$data[0]);


        // 檔案名稱
        $fileName = 'F14';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel，
        $objPHPExcel = IOFactory::load($filePath);

        $objSheet = $objPHPExcel->getsheet(0);
        $objSheet->setCellValue('A2', $A2);
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        //fill values
        for($i=0;$i<sizeof($data);$i++){
            for($j=0;$j<sizeof($datakey);$j++){
                 $objSheet->setCellValue($this->getNameFromNumber($j+1).($i+4),$data[$i][$datakey[$j]]);
                 $objSheet->getRowDimension($i+4)->setRowHeight(30);
            }
            $objSheet->setCellValue('K'.($i+4),'=SUM(B'.($i+4).':J'.($i+4).')');
        }

        if(sizeof($data)>15){
            for($j=2;$j<12;$j++){
                $objSheet->setCellValue($this->getNameFromNumber($j).(sizeof($data)+4),
                '=SUM('.$this->getNameFromNumber($j).'4:'.$this->getNameFromNumber($j).(sizeof($data)+3).')');
            }

            $objSheet->setCellValue('A'.(sizeof($data)+4),'合計');
            $objSheet->getRowDimension((sizeof($data)+4))->setRowHeight(30);
            //apply borders
            $objSheet->getStyle('A3:'.'L'.(sizeof($data)+4))->applyFromArray($styleArray);

         }else{
            for($k=4;$k<20;$k++){
                 $objSheet->getRowDimension($k)->setRowHeight(30);
            }
            $objSheet->setCellValue('A20','合計');
            for($j=2;$j<12;$j++){
                $objSheet->setCellValue($this->getNameFromNumber($j).'20',
                '=SUM('.$this->getNameFromNumber($j).'4:'.$this->getNameFromNumber($j).'19)');

            }
            $objSheet->getRowDimension('20')->setRowHeight(30);
            //apply borders
            $objSheet->getStyle('A3:L20')->applyFromArray($styleArray);

         }
         $RptBasic = new \App\Rptlib\RptBasic();
         $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"年度班期費用統計表");
         //$obj: entity of file
         //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
         //$doctype:1.ooxml 2.odf
         //$filename:filename 
       

    }
}
