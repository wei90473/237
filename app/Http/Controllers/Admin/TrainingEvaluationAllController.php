<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;

class TrainingEvaluationAllController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
          $user_data = \Auth::user();
          $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
          if(in_array('training_evaluation_all', $user_group_auth)){
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
        //return view('admin/training_evaluation_all/list');
        //$classArr = $this->getclass($request);
        //$result = '';
        //return view('admin/training_evaluation_all/list', compact('classArr', 'result'));
        return view('admin/training_evaluation_all/list');
    }

    // 搜尋下拉『班別』
    public function getclass(Request $request) {
        /* '問卷版本 新:0 舊:1 */
        $ratioInfo = $request->input('info');
        if($ratioInfo=='0') {
            $sql = "SELECT DISTINCT t53tb.class, t01tb.name
                      FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                     WHERE t53tb.times<>''
                     ORDER BY t53tb.class DESC  ";
        }
        else{
            $sql = "SELECT DISTINCT t19tb.class, t01tb.name
                      FROM t19tb INNER JOIN t01tb ON t19tb.class = t01tb.class
                     ORDER BY t19tb.class DESC";
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
      if($ratioInfo=='0') {
        $RptBasic = new \App\Rptlib\RptBasic;
        return $RptBasic->getTermByClass($request->input('class'));
      }
      else{
        $sql = "SELECT DISTINCT term FROM t19tb
                WHERE class = '".$class."'
                 ORDER By 1";
        $classArr = DB::select($sql);
        return $classArr;
      }

    }

    /*
    訓練成效反應表 CSDIR5060
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
        //擲回日期(民國年月日)
        $sdatetw= $request->input('sdatetw');
        //版本 新:0 舊:1
        $ratioInfo = $request->input('info');

        //取得TITLE
        //版本 新:0 舊:1
        if($ratioInfo=='0') {
            $sql = "SELECT DISTINCT CONCAT(t01tb.name, '第', CASE WHEN SUBSTRING(t53tb.term,1,1) = '0' THEN SUBSTRING(t53tb.term,2) ELSE t53tb.term END
                                                        , '期') AS TITLETERM,
                                    t01tb.name AS TITLE
                    FROM t53tb INNER JOIN t01tb ON t53tb.class = t01tb.class
                    WHERE t53tb.times<>''
                    AND t53tb.class = '".$class."'
                    AND t53tb.term = '".$term."'
                    ORDER BY 1 DESC";
        }
        else {
            $sql = "SELECT DISTINCT CONCAT(t01tb.name, '第', CASE WHEN SUBSTRING(t19tb.term,1,1) = '0' THEN SUBSTRING(t19tb.term,2) ELSE t19tb.term END
                                                        , '期') AS TITLETERM,
                                    t01tb.name AS TITLE
                    FROM t19tb INNER JOIN t01tb ON t19tb.class = t01tb.class
                    WHERE t19tb.class = '".$class."'
                    AND t19tb.term = '".$term."'
                    ORDER BY 1 DESC";
        }
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        // 讀檔案
        /* '問卷版本 新:0 舊:1
        */
        if($ratioInfo=='0')
          $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'L21B').'.docx');
        else
          $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'L21A').'.docx');

        //TITLE
        $templateProcessor->setValue('TITLETERM', $dataArr[0]['TITLETERM']);
        $templateProcessor->setValue('TITLE', $dataArr[0]['TITLE']);

        //DUEDATE
        $templateProcessor->setValue('DUEDATE', $sdatetw);

        //取得 訓練成效反應表
        //問卷版本 新:0 舊:1
        if($ratioInfo=='0') {
            $sql = "SELECT IFNULL(m01.cname,'') as cname,
                            CASE WHEN t06.name IS NULL THEN '' ELSE
                                CONCAT(t54.sequence, '.', t06.name) END AS CLASSNAME,
                            t54.course,
                            t54.idno
                    FROM t54tb t54 LEFT OUTER JOIN t06tb t06 on t06.class=t54.class AND t06.term=t54.term AND t06.course=t54.course
                                    LEFT OUTER JOIN m01tb m01 ON m01.idno=t54.idno
                    WHERE t54.class = '".$class."'
                    AND t54.term = '".$term."'
                    ORDER BY t54.sequence";
        } else {
            $sql = "SELECT t01tb.name AS TITLE,
                            CONCAT(SUBSTRING(t19tb.duedate,1,3),'年',
                                   SUBSTRING(t19tb.duedate,4,2),'月',
                                   SUBSTRING(t19tb.duedate,6,2),'日') AS DUEDATE,
                            CONCAT(t01tb.name,'第', t19tb.term, '期') AS TITLETERM,
                            CONCAT(SUBSTRING(t04tb.sdate,1,3), '年',SUBSTR(t04tb.sdate,4,2),'月',SUBSTR(t04tb.sdate,6,2),'日','至',
                                   SUBSTRING(t04tb.sdate,1,3), '年',SUBSTR(t04tb.sdate,4,2),'月',SUBSTR(t04tb.sdate,6,2),'日')
                                        AS SEDATE,
                            t19tb.themea1 AS THEMEA1,
                            t19tb.themeb1 AS THEMEB1,
                            t04tb.sdate as sdate,
                            t04tb.edate as edate
                    from t19tb inner join t04tb on t19tb.class=t04tb.class and t19tb.term=t04tb.term
                            INNER JOIN t01tb ON t19tb.class = t01tb.class
                    where t19tb.class = '".$class."'
                    and t19tb.term = '".$term."'
                    ";
        }

        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        if($ratioInfo!="1") {
          // 要放的資料筆數，先建 列
          $templateProcessor->cloneRow('CLASSNAME', sizeof($dataArr));

          // 每列要放的資料(#1：第一列、以此類推)
          for($i=0; $i<sizeof($dataArr); $i++) {
              $templateProcessor->setValue('CLASSNAME#'.($i+1), $dataArr[$i]['CLASSNAME']);
          }
        } else{
            //$templateProcessor->setValue('DUEDATE', $dataArr[0]['DUEDATE']);
            $templateProcessor->setValue('SEDATE', $dataArr[0]['SEDATE']);
            $templateProcessor->setValue('THEMEA1', $dataArr[0]['THEMEA1']);
            $templateProcessor->setValue('THEMEB1', $dataArr[0]['THEMEB1']);
        }

        $outputname="";
        /* '問卷版本 新:0 舊:1  	*/
        if($ratioInfo=='0')
          $outputname="訓後成效評估表-新版問卷";
        else
        $outputname="訓後成效評估表-舊版問卷";

          $RptBasic = new \App\Rptlib\RptBasic();
          $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputname);
          //$obj: entity of file
          //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
          //$doctype:1.ooxml 2.odf
          //$filename:filename 
    }


}
