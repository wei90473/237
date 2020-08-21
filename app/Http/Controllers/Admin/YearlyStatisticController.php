<?php
namespace App\Http\Controllers\Admin;
set_time_limit(0);
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// use App\Models\T01tb;
use DB;

class YearlyStatisticController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('yearly_statistic', $user_group_auth)){
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
        return view('admin/yearly_statistic/list');
    }

    /*
    年度講座之滿意度統計表 CSDIR5040
    參考Tables:
    使用範本:L4.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //年
        $startYear = $request->input('startYear');

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'L4').'.docx');

        //TITLE
        $templateProcessor->setValue('title', $startYear);

        //取得 年度講座之滿意度統計表
        $sql = "SELECT  t09tb.idno,
                        m01tb.cname,
                        REPLACE(t06tb.NAME,'&','與') AS coursename,
                        CONCAT(t01tb.NAME, '第', t06tb.term,'期') AS classname,
                        t09tb.okrate AS okrate
                  FROM  t06tb INNER JOIN t09tb ON t06tb.class = t09tb.class  AND t06tb.term = t09tb.term  AND t06tb.course = t09tb.course
                              INNER JOIN t01tb ON t09tb.class = t01tb.class
                              INNER JOIN m01tb ON t09tb.idno = m01tb.idno
                WHERE SUBSTRING(t06tb.class,1,3) = LPAD('".$startYear."',3,'0')
                  AND t09tb.okrate > 0
                ORDER BY m01tb.idno, t01tb.class, t06tb.course";
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        // 要放的資料筆數，先建 列
        $templateProcessor->cloneRow('cname', sizeof($dataArr));

        // 每列要放的資料(#1：第一列、以此類推)
        for($i=0; $i<sizeof($dataArr); $i++) {
            $templateProcessor->setValue('cname#'.($i+1), $dataArr[$i]['cname']);
            $templateProcessor->setValue('coursename#'.($i+1), $dataArr[$i]['coursename']);
            $templateProcessor->setValue('classname#'.($i+1), $dataArr[$i]['classname']);
            $templateProcessor->setValue('okrate#'.($i+1), $dataArr[$i]['okrate']);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"年度講座之滿意度統計表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }

}
