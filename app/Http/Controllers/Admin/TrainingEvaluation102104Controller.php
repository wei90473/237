<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// use App\Models\T01tb;
use DB;

class TrainingEvaluation102104Controller extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
          $user_data = \Auth::user();
          $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
          if(in_array('training_evaluation_102_104', $user_group_auth)){
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
        //return view('admin/training_evaluation_105/list');
        $classArr = $this->getclass();
        $result = '';
        return view('admin/training_evaluation_102_104/list', compact('classArr', 'result'));
    }

    // 搜尋下拉『班別』
    public function getclass() {
            $sql = "SELECT DISTINCT t53tb.class, t01tb.name
                      FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                     WHERE t53tb.times<>'' AND SUBSTRING(t53tb.class,1,3) between '102' and '104'
                     ORDER BY t53tb.class DESC  ";
            $classArr = DB::select($sql);
            return $classArr;
    }

    // 搜尋下拉『期別』
    public function getTermByClass(Request $request)
    {
      $RptBasic = new \App\Rptlib\RptBasic;
      return $RptBasic->getTermByClass($request->input('class'));
    }

    // 搜尋下拉『第幾次調查』
    public function getTimeByClass(Request $request)
    {
      $RptBasic = new \App\Rptlib\RptBasic;
      return $RptBasic->getTimeByClass($request->input('class'), $request->input('term'));
    }

    /*
    訓練成效評估表(102) CSDIR5013
    參考Tables:
    使用範本:L8A.docx , L8B.docx (基本資料列印選項,  L8A 不要包含, L8B 要包含 )
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //班別
        $class = $request->input('class');
        //期別
        $term = $request->input('term');
        //第幾次調查
        $times= $request->input('times');
        //'基本資料列印選項,  0 '不要包含,  1 '要包含
        $radio1 = $request->input('radio1');

        //取得TITLE
        $sql = "SELECT DISTINCT CONCAT(t01tb.name, '第', CASE WHEN SUBSTRING(t53tb.term,1,1) = '0' THEN SUBSTRING(t53tb.term,2) ELSE t53tb.term END
                                                       , '期研習意見調查表' ,
                                '(' ,
                                CASE t53tb.times WHEN 1 THEN '一' WHEN 2 THEN '二' WHEN 3 THEN '三' WHEN 4 THEN '四'  WHEN 5 THEN '五'
                                                WHEN 6 THEN '六' WHEN 7 THEN '七' WHEN 8 THEN '八' WHEN 9 THEN '九'  WHEN 10 THEN '十'
                                                ELSE t53tb.times END
                                , ')') AS TITLE
                  FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                 WHERE t53tb.times<>'' AND SUBSTRING(t53tb.class,1,3) between '102' and '104'
                   AND t53tb.class = '".$class."'
                   AND t53tb.term = '".$term."'
                   AND t53tb.times = '".$times."'
                ORDER BY 1 DESC";
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        // 讀檔案
        /* 固定項目:基本資料列印選項, 使用Word範本
          Case 0 '不要包含
                L8A
          Case 1 '要包含
                L8B
        */
        if ($radio1 == '0') {
          $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'L8A').'.docx');
        } else {
          $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'L8B').'.docx');
        }

        //TITLE
        $templateProcessor->setValue('title', $dataArr[0]['TITLE']);


        $sql = "SELECT IFNULL(m01.cname,'') as cname,
                       IFNULL(t06.name,'') as classname,
                       t54.course,
                       t54.idno
                  FROM t54tb t54 LEFT OUTER JOIN t06tb t06 on t06.class=t54.class AND t06.term=t54.term AND t06.course=t54.course
                                 LEFT OUTER JOIN m01tb m01 ON m01.idno=t54.idno
                 WHERE t54.class = '".$class."'
                   AND t54.term = '".$term."'
                   AND t54.times = '".$times."'
                   ORDER BY t54.sequence";
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        // 要放的資料筆數，先建 列
        $templateProcessor->cloneRow('cname', sizeof($dataArr));

        // 每列要放的資料(#1：第一列、以此類推)
        for($i=0; $i<sizeof($dataArr); $i++) {
            $templateProcessor->setValue('cname#'.($i+1), $dataArr[$i]['cname']);
            $templateProcessor->setValue('classname#'.($i+1), $dataArr[$i]['classname']);
        }

        $outputname="";
        /* 固定項目:基本資料列印選項, 使用Word範本
          Case 0 '不要包含
                L8A
          Case 1 '要包含
                L8B
        */
        if ($radio1 == '0') {
          $outputname="訓練成效評估表(102)-不包含基本資料";
        } else {
          $outputname="訓練成效評估表(102)-要包含基本資料";
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
