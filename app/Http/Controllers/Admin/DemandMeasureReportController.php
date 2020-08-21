<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use \PhpOffice\PhpWord\PhpWord;
use \PhpOffice\PhpWord\IOFactory;
use App\Services\User_groupService;

class DemandMeasureReportController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('demand_measure_report', $user_group_auth)){
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
//         $sql = "SELECT DISTINCT yerly FROM t01tb";
//         $timeslist = DB::select($sql);
        //print_r($timeslist);
        // return view('admin/demand_measure_report/list', compact('timeslist'));
        $result="";
        return view('admin/demand_measure_report/list',compact('result') );
    }

    public function gettime(Request $request)
    {   
        $RptBasic = new \App\Rptlib\RptBasic();
        return $RptBasic->gettime($request->input('yerly'));
    }

    public function export(Request $request)
    {
        $yerly = $request->input('yerly');
        // $temptimes = $request->input('times');
        $temptimes = explode(",", $request->input('times'));
        $doctype = $request->input('doctype');
        $branch = $request->input('area');
        $condition="";
        $times="";
        for ($i=0; $i < sizeof($temptimes); $i++) {
            if ($i == sizeof($temptimes)-1) {
                $times=$times."'".$temptimes[$i]."'";
            } else {
                $times=$times."'".$temptimes[$i]."',";
            }
        }
        if( sizeof($temptimes)==0){
            $result="請選擇調查次數。";
            return view('admin/demand_measure_report/list',compact('result') );
        }

        if ($branch!="3"){
            $condition.=" AND branch ='".$branch."' ";
        }


        

        $sql = "SELECT type, name, target, object, content, remark FROM t01tb 
                WHERE yerly = '".$yerly."' AND times In (".$times.") AND type <> '13' ".$condition." ORDER BY type, rank, class";

        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);
        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'D1').'.docx');
        
        $templateProcessor->setValue('year', $yerly);

        // 要放的資料筆數，先建 列
        $templateProcessor->cloneRow('name', sizeof($dataArr));

        // 每列要放的資料(#1：第一列、以此類推)
        for($i=0; $i<sizeof($dataArr); $i++) {
            $templateProcessor->setValue('name#'.($i+1), $dataArr[$i]['name']);
            $templateProcessor->setValue('target#'.($i+1), $dataArr[$i]['target']);
            $templateProcessor->setValue('object#'.($i+1), $dataArr[$i]['object']);
            $templateProcessor->setValue('content#'.($i+1), $dataArr[$i]['content']);
            $templateProcessor->setValue('count#'.($i+1), '');
            $templateProcessor->setValue('suggest#'.($i+1), '');
            $templateProcessor->setValue('remark#'.($i+1), $dataArr[$i]['remark']);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"需求名額統計表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename

        // if($doctype=="1")
        // {
        //     //docx
        //     header('Content-Type: application/vnd.ms-word');
        //     header("Content-Disposition: attachment;filename=需求調查表.docx");
        //     header('Cache-Control: max-age=0');
        //     ob_clean();
        //     $templateProcessor->saveAs('php://output');
        //     exit;
        // }else{
        //     //odt
        //     $tmpdoc="tmpdoc".time();
        //     $templateProcessor->saveAs('../public/backend/attachments/'.$tmpdoc.'.docx');
        //     $phpWord =IOFactory::load('../public/backend/attachments/'.$tmpdoc.'.docx');
        //     $objWriter = IOFactory::createWriter($phpWord, 'ODText');
        //     header('Content-Type: application/vnd.oasis.opendocument.text');
        //     header("Content-Disposition: attachment;filename=需求調查表.odt");    
            
        //     header('Cache-Control: max-age=0');
        //     ob_clean();
            
        //     $objWriter->save('php://output');
        //     exit;
        // }




    }

}
