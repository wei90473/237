<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use \PhpOffice\PhpWord\PhpWord;
use \PhpOffice\PhpWord\IOFactory;

use App\Services\SignupService;
use App\Services\Term_processService;
use App\Models\T04tb;
use App\Models\T51tb;

class SendtrainingJointController extends Controller
{
    public function __construct(User_groupService $user_groupService,SignupService $signupService, Term_processService $term_processService)
    {
        $this->user_groupService = $user_groupService;
        $this->signupService = $signupService;
        $this->term_processService = $term_processService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('sendtraining_joint', $user_group_auth)){
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
        // 取得起始年
        $queryData['yerly'] = str_pad($request->get('yerly') ,3,'0',STR_PAD_LEFT);
        if($queryData['yerly']=="000")
            $queryData['yerly'] = "109";
        // 取得起始月
        $queryData['month'] = str_pad($request->get('month') ,2,'0',STR_PAD_LEFT);
        if ( $queryData['month']=="00" )
            $queryData['month'] = "01";
         // 排序欄位
         $queryData['_sort_field'] = $request->get('_sort_field');
         // 排序方向
         $queryData['_sort_mode'] = $request->get('_sort_mode');
         // 每頁幾筆
         $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 10;
         // 取得列表資料
         $data = $RptBasic->getClassBysDate($request,$queryData);
         $result='';
         return view('admin/sendtraining_joint/list', compact('data', 'queryData','result'));

    }

    public function exportreciever($yerly,$month)
    {
        $sdate=str_pad($yerly ,3,'0',STR_PAD_LEFT).str_pad($month ,2,'0',STR_PAD_LEFT);
        $RptBasic = new \App\Rptlib\RptBasic();
        $sdatenext=$RptBasic->getnextDate($sdate);
        $sql="SELECT RTRIM(D.lname) as col_01, '正本' as col_02, '' as col_03 , '' as col_04 , B.organ as col_05 ,'電子交換' as col_06
        FROM
        (
        SELECT A.class, A.term
        FROM t04tb A INNER JOIN t01tb B ON A.class=B.class AND B.type<>'13' LEFT JOIN m09tb C ON A.sponsor=C.userid
        WHERE
           (
             CASE
              WHEN A.sdate LIKE '".$sdate."%' AND B.classified<>'3' THEN 1 /* 該月非混成班 */
              WHEN A.sdate LIKE '.$sdatenext.%' AND B.classified='3'  THEN 1 /* 下月混成班 */
             END
            ) = 1
            AND A.notice ='Y' AND A.pubedate<>''
            AND EXISTS (
                SELECT class
                FROM t51tb
                WHERE class=A.class
                AND term=A.term
            )
        ORDER BY A.section,A.sdate,A.edate,A.class,A.term
        ) AS AA
        INNER JOIN t51tb B ON AA.class = B.class AND AA.term = B.term
        INNER JOIN m17tb C ON B.organ = C.enrollorg AND C.grade = '1' INNER JOIN m13tb D
        ON C.organ = D.organ
        GROUP BY B.organ,D.lname,D.rank
        ORDER BY D.rank ";

        $rows =json_decode(json_encode(DB::select($sql)), true);
        //輸出CSV
        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=聯合派訓通知-聯合派訓受文者清單.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

            $callback = function() use ($rows) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                foreach ($rows as $r) {
                    fputcsv($file,$r);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
    }

    public function exportclass(Request $request,$yerly,$month)
    {
        $sdate=str_pad($yerly ,3,'0',STR_PAD_LEFT).str_pad($month ,2,'0',STR_PAD_LEFT);
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp = json_decode(json_encode($RptBasic->getClassBysDate2($sdate)), true);
        $data = $temp;
        $datakeys=array_keys((array)$data[0]);
        //get number of units
        $sdatenext=$RptBasic->getnextDate($sdate);
        $sql="SELECT A.section,count(*)
                FROM t04tb A INNER JOIN t01tb B ON A.class=B.class AND B.type<>'13' LEFT JOIN m09tb C ON A.sponsor=C.userid
                WHERE
                ( CASE
                    WHEN A.sdate LIKE '".$sdate."%' AND B.classified<>'3' THEN 1 /* 該月非混成班 */
                    WHEN A.sdate LIKE '".$sdatenext."%' AND B.classified='3'  THEN 1 /* 下月混成班 */
                    END ) = 1
				GROUP BY A.section
                ORDER BY A.section";
        $temp = json_decode(json_encode(DB::select($sql)), true);
        $unit = $temp;
        if($unit==[])
        {
            $RptBasic = new \App\Rptlib\RptBasic();
            $queryData['yerly'] = str_pad($request->get('yerly') ,3,'0',STR_PAD_LEFT);
            if($queryData['yerly']=="000")
                $queryData['yerly'] = "109";
            $queryData['month'] = str_pad($request->get('month') ,2,'0',STR_PAD_LEFT);
            if ( $queryData['month']=="00" )
                $queryData['month'] = "01";
             $queryData['_sort_field'] = $request->get('_sort_field');
             $queryData['_sort_mode'] = $request->get('_sort_mode');
             $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
             $data = $RptBasic->getClassBysDate($request,$queryData);
             $result = "查無資料，請重新查詢。";
             return view('admin/sendtraining_joint/list', compact('data', 'queryData','result'));
        }
        $unitkeys=array_keys((array)$unit[0]);

         // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F1').'.docx');
        ini_set('pcre.backtrack_limit', 999999999);
        $templateProcessor->cloneBlock('t',sizeof($unit), true, true);

        //填入值
        for($i=0;$i<sizeof($unit);$i++){

            $templateProcessor->setValue('unit#'.strval($i+1), $unit[$i][$unitkeys[0]]);
            $templateProcessor->setValue('year#'.strval($i+1), $yerly);
            $templateProcessor->setValue('month#'.strval($i+1), $month);

            if(!($i==(sizeof($unit)-1)))
                $templateProcessor->setValue('pb#'.strval($i+1), '<w:p><w:r><w:br w:type="page"/></w:r></w:p>');
            else
                $templateProcessor->setValue('pb#'.strval($i+1),'');

            $templateProcessor->cloneRow('class#'.strval($i+1), (int)$unit[$i][$unitkeys[1]]);

             for($j=0;$j<sizeof($data);$j++){
                $tempdate=explode("~", $data[$j][$datakeys[6]]);
                $tdate=$tempdate[0]."\n~".$tempdate[1];
                $templateProcessor->setValue('class#'.strval($i+1).'#'.strval($j+1),$data[$j][$datakeys[1]]);
                $templateProcessor->setValue('place#'.strval($i+1).'#'.strval($j+1),$data[$j][$datakeys[15]]);
                $templateProcessor->setValue('object#'.strval($i+1).'#'.strval($j+1),$data[$j][$datakeys[2]]);
                $templateProcessor->setValue('term#'.strval($i+1).'#'.strval($j+1),$data[$j][$datakeys[3]]);
                $templateProcessor->setValue('date#'.strval($i+1).'#'.strval($j+1),$tdate);
                $templateProcessor->setValue('sdate#'.strval($i+1).'#'.strval($j+1),$data[$j]['報名開始日期']);
                $templateProcessor->setValue('edate#'.strval($i+1).'#'.strval($j+1),$data[$j]['報名截止日期']);
                $templateProcessor->setValue('name#'.strval($i+1).'#'.strval($j+1),$data[$j][$datakeys[9]]);
                $templateProcessor->setValue('a#'.strval($i+1).'#'.strval($j+1),$data[$j][$datakeys[10]]);
                $templateProcessor->setValue('b#'.strval($i+1).'#'.strval($j+1),$data[$j][$datakeys[11]]);
                $templateProcessor->setValue('c#'.strval($i+1).'#'.strval($j+1),$data[$j][$datakeys[11]]);
             }



        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"聯合派訓通知-開班一覽表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }

    public function edit(Request $request, $class, $term)
    {
        // 取得班別
        $queryData['class'] = $class;
        // 取得期別
        $queryData['term'] = $term;
        // 取得列表資料
        $data = $this->signupService->getSignupList($queryData);
        // dd($data);
        // 測試用資料
        // $data = array(
        //     json_decode('{"\u6a5f\u95dc\u4ee3\u78bc":"A00000000A","\u6a5f\u95dc\u540d\u7a31":"\u884c\u653f\u9662","\u5e74\u5ea6\u5206\u914d\u4eba\u6578":1,"\u7dda\u4e0a\u5206\u914d\u4eba\u6578":1}'),
        //     json_decode('{"\u6a5f\u95dc\u4ee3\u78bc":"301000000A","\u6a5f\u95dc\u540d\u7a31":"\u5167\u653f\u90e8","\u5e74\u5ea6\u5206\u914d\u4eba\u6578":2,"\u7dda\u4e0a\u5206\u914d\u4eba\u6578":2}'),
        //     json_decode('{"\u6a5f\u95dc\u4ee3\u78bc":"303000000B","\u6a5f\u95dc\u540d\u7a31":"\u5916\u4ea4\u90e8","\u5e74\u5ea6\u5206\u914d\u4eba\u6578":0,"\u7dda\u4e0a\u5206\u914d\u4eba\u6578":0}'),
        //     json_decode('{"\u6a5f\u95dc\u4ee3\u78bc":"305000000C","\u6a5f\u95dc\u540d\u7a31":"\u570b\u9632\u90e8","\u5e74\u5ea6\u5206\u914d\u4eba\u6578":1,"\u7dda\u4e0a\u5206\u914d\u4eba\u6578":1}'),
        //     json_decode('{"\u6a5f\u95dc\u4ee3\u78bc":"307000000D","\u6a5f\u95dc\u540d\u7a31":"\u8ca1\u653f\u90e8","\u5e74\u5ea6\u5206\u914d\u4eba\u6578":6,"\u7dda\u4e0a\u5206\u914d\u4eba\u6578":6}'),
        // );
        // $t01tb = $this->signupService->getT01tb($class);
        // 取得派訓日期
        $dateData = $this->signupService->getDateData($queryData);
        // 取得課程列表
        // $classList = $this->signupService->getClassList($queryData);
        $t04tb_info = [
            "class" => $class,
            "term" => $term
        ];
        $t04tb = $this->signupService->getT04tb($t04tb_info);
        $t01tb = $t04tb->t01tb;
        $online_apply_organs = $t04tb->online_apply_organs;

        return view('admin/signup/form', compact('data', 'queryData', 'classList', 'dateData', 't04tb','online_apply_organs'));
    }

    public function setdate(Request $request)
    {

        // 取得班別,期數
        $classterm =explode("_", $request->input('classterm'));

        $class = $classterm[0];
        $term = $classterm[1];

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('signup_edit_type1', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }
        $freeze = $this->term_processService->getFreeze('signup_edit_type2', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        // 取得日期
        $data = $request->only([
            'pubsdate',
            'pubedate'
        ]);

        // 更新T04tb
        T04tb::where('class', $class)->where('term', $term)->update($data);
        // 更新T51tb
        T51tb::where('class', $class)->where('term', $term)->update(['pubsdate' => $data['pubsdate'], 'pubedate' => $data['pubedate']]);

        $value = is_array($request->input('value'))? $request->input('value') : array();
        $this->signupService->updateT51tb($class, $term, $data['pubsdate'], $data['pubedate'], $value);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

}
