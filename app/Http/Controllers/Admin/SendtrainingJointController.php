<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use \PhpOffice\PhpWord\PhpWord;
use \PhpOffice\PhpWord\IOFactory;

class SendtrainingJointController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
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

}
