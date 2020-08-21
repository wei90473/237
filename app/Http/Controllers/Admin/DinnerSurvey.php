<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;

class DinnerSurvey extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('dinner_survey', $user_group_auth)){
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
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
        $termArr=$temp;
        $result="";
        return view('admin/dinner_survey/list',compact('classArr','termArr' ,'result'));

    }

    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }

    public function export(Request $request)
    {

        //班別
        $classes = $request->input('classes');
        //期別
        $terms = $request->input('terms');

        //班別
        $sql = "SELECT DISTINCT t01tb.name AS CLASSNAME
                  FROM t01tb
                 WHERE t01tb.class = '".$classes."'
                ";
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'N15').'.docx');

        //帶入班名與期別
        $templateProcessor->setValue('CLASSNAME', $classes.' '.$dataArr[0]['CLASSNAME']);
        $templateProcessor->setValue('terms', $terms);

        //docx
        header('Content-Type: application/vnd.ms-word');
        header("Content-Disposition: attachment;filename=不用晚餐調查表.docx");
        header('Cache-Control: max-age=0');
        ob_clean();
        $templateProcessor->saveAs('php://output');
        exit;
    }


}
