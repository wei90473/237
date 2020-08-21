<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;

class TrainingEvaluation9092Controller extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
          $user_data = \Auth::user();
          $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
          if(in_array('training_evaluation_90_92', $user_group_auth)){
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
        //$classArr = $this->getclass($request);
        //$result = '';
        //return view('admin/training_evaluation_90_92/list', compact('classArr', 'result'));
        return view('admin/training_evaluation_90_92/list');
    }

    // 搜尋下拉『班別』
    public function getclass(Request $request) {
        /* '問卷版本 新:0 舊:1 */
        $ratioInfo = $request->input('info');
        if($ratioInfo=="0") {
            $sql = "SELECT DISTINCT t53tb.class, t01tb.name
                      FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                     WHERE t53tb.times<>''
                     ORDER BY t53tb.class DESC  ";
        }
        else{
            $sql = "SELECT DISTINCT t16tb.class, t01tb.name
                      FROM t16tb INNER JOIN t01tb ON t16tb.class = t01tb.class
                     ORDER BY t16tb.class DESC";
        }
        $classArr = DB::select($sql);
        return $classArr;
    }

    // 搜尋下拉『期別』
    public function getTermByClass(Request $request)
    {
      /* '問卷版本 新:0 舊:1  		 */
      $ratioInfo = $request->input('info');
      $class = $request->input('class');
      if($ratioInfo=="0") {
        $RptBasic = new \App\Rptlib\RptBasic;
        return $RptBasic->getTermByClass($request->input('class'));
      }
      else{
        $sql = "SELECT DISTINCT term FROM t16tb
                WHERE class = '".$class."'
                 ORDER By 1";
        $classArr = DB::select($sql);
        return $classArr;
      }

    }

    // 搜尋下拉『第幾次調查』
    public function getTimeByClass(Request $request)
    {
      /* '問卷版本 新:0 舊:1  		 */
      $ratioInfo = $request->input('info');
      $class = $request->input('class');
      $term = $request->input('term');
      if($ratioInfo=="0") {
        $RptBasic = new \App\Rptlib\RptBasic;
        return $RptBasic->getTimeByClass($request->input('class'), $request->input('term'));
      }
      else{
        $sql = "SELECT DISTINCT times FROM t16tb
                 WHERE class = '".$class."'
                   AND term = '".$term."'
                 ORDER By 1";
        $classArr = DB::select($sql);
        return $classArr;
      }
    }

    /*
    訓練成效評估表(90~92) CSDIR5010
    參考Tables:
    使用範本:L21A.docx (新版), L21B.docs (舊版)
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
        //'版本 新:0 舊:1
        $ratioInfo = $request->input('info');

        //取得 TITLE
        //行政院人事行政總處公務人力發展中心觀光客倍增研習會第一期研習意見調查表(一)
        if($ratioInfo=="0") {
            $sql = "SELECT DISTINCT CONCAT(t01tb.name, '第', CASE WHEN SUBSTRING(t53tb.term,1,1) = '0' THEN SUBSTRING(t53tb.term,2) ELSE t53tb.term END
                                                        , '期') AS ClassName_TITLE,
                                    CONCAT(
                                    '(' ,
                                    CASE t53tb.times WHEN 1 THEN '一' WHEN 2 THEN '二' WHEN 3 THEN '三' WHEN 4 THEN '四'  WHEN 5 THEN '五'
                                                    WHEN 6 THEN '六' WHEN 7 THEN '七' WHEN 8 THEN '八' WHEN 9 THEN '九'  WHEN 10 THEN '十'
                                                    ELSE t53tb.times END
                                    , ')') AS Times_TITLE
                    FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                    WHERE t53tb.times<>''
                    AND t53tb.class = '".$class."'
                    AND t53tb.term = '".$term."'
                    AND t53tb.times = '".$times."'
                    ORDER BY 1 DESC";
        }
        else {
            $sql = "SELECT DISTINCT CONCAT(t01tb.name, '第', CASE WHEN SUBSTRING(t53tb.term,1,1) = '0' THEN SUBSTRING(t53tb.term,2) ELSE t53tb.term END
                                                        , '期') AS ClassName_TITLE,
                                    CONCAT(
                                    '(' ,
                                    CASE t53tb.times WHEN 1 THEN '一' WHEN 2 THEN '二' WHEN 3 THEN '三' WHEN 4 THEN '四'  WHEN 5 THEN '五'
                                                    WHEN 6 THEN '六' WHEN 7 THEN '七' WHEN 8 THEN '八' WHEN 9 THEN '九'  WHEN 10 THEN '十'
                                                    ELSE t53tb.times END
                                    , ')') AS Times_TITLE
                    FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                    WHERE t53tb.class = '".$class."'
                    AND t53tb.term = '".$term."'
                    ORDER BY 1 DESC";
        }
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        // 讀檔案
        /* '問卷版本 新:0 舊:1
        */
        if($ratioInfo!="1")
          $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'L18B').'.docx');
        else
          $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'L18A').'.docx');


        //TITLE
        if($ratioInfo=="0") {
            $templateProcessor->setValue('Title', $dataArr[0]['ClassName_TITLE'].$dataArr[0]['Times_TITLE']);
        }
        else {
            $templateProcessor->setValue('ClassName', $dataArr[0]['ClassName_TITLE']);
            $templateProcessor->setValue('Times', $dataArr[0]['Times_TITLE']);
        }

        //取得 訓練成效評估表
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

        if($ratioInfo=="0") {
          // 要放的資料筆數，先建 列
          $templateProcessor->cloneRow('cname', sizeof($dataArr));

          // 每列要放的資料(#1：第一列、以此類推)
          for($i=0; $i<sizeof($dataArr); $i++) {
              $templateProcessor->setValue('cname#'.($i+1), $dataArr[$i]['cname']);
              $templateProcessor->setValue('classname#'.($i+1), $dataArr[$i]['classname']);
          }
        }
        
        $outputname="";
        /* '問卷版本 新:0 舊:1  	*/
        if($ratioInfo=="0")
          $outputname="訓練成效評估表(90~92)-新版問卷";
        else
          $outputname="訓練成效評估表(90~92)-舊版問卷";

          $RptBasic = new \App\Rptlib\RptBasic();
          $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputname);
          //$obj: entity of file
          //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
          //$doctype:1.ooxml 2.odf
          //$filename:filename 
    }
}
