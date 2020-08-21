<?php
namespace App\Http\Controllers\Admin;
use App\Services\User_groupService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Models\T01tb;
use DB;

class CourseAssignmentController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('course_assignment', $user_group_auth)){
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
        //取得班別
        $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
        FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
        ORDER BY t04tb.class DESC");
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;
        $result = '';
        return view('admin/course_assignment/list',compact('classArr','termArr' ,'result'));

    }


        public function getTerms(Request $request)
        {
            //取得期別
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
            $termArr=$temp;
            return $termArr;
        }

        public function export(Request $request)
        {
            //取得課程名稱
            $sql="select name from t01tb where class='".$request->input('classes')."'";
            $temp = DB::select($sql);
            $classData = json_decode(json_encode($temp), true);
            $classDatakeys=array_keys((array)$classData[0]);
            //目　　的：取得課程配當
            $sql="select A.unit as unit ,ifnull(B.name,'') as unitname,A.name as name,A.hour as hour,ifnull(B.remark,'') as remark
                 From t06tb A left join t05tb B on B.unit=A.unit  and  B.class=A.class  and  B.term=A.term
                 where A.class= '".$request->input('classes')."' and  A.term='".$request->input('term')."'
                 order by unit,course";
            $temp = DB::select($sql);
            if($temp==[])
            {
                $result="查無配當資料";
                $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                ORDER BY t04tb.class DESC");
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                return view('admin/course_assignment/list',compact('classArr','termArr' ,'result'));
            }
            $courseData = json_decode(json_encode($temp), true);
            $courseDatakeys=array_keys((array)$courseData[0]);
            //目　　的：以班別及期別加總總課程時數
            $sql="select sum(hour) as total_hour from t06tb where class='".$request->input('classes')."'and  term='".$request->input('term')."'";
            $temp = DB::select($sql);
            $total = json_decode(json_encode($temp), true);
            $totalkeys=array_keys((array)$total[0]);

            // 讀檔案
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F6').'.docx');
            $filename=$classData[0][$classDatakeys[0]]."第".(int)$request->input('term')."期課程時數配當表";

            $templateProcessor->setValue('class', $classData[0][$classDatakeys[0]]);
            $templateProcessor->setValue('term', (int)$request->input('term'));
            $templateProcessor->setValue('total', $total[0][$totalkeys[0]]);
            // 要放的資料筆數，先建 列
            $templateProcessor->cloneRow('name', sizeof($courseData));



            // // 每列要放的資料(#1：第一列、以此類推)
            for($i=0; $i<sizeof($courseData); $i++) {
                $templateProcessor->setValue('name#'.($i+1), $courseData[$i][$courseDatakeys[2]]);
                $templateProcessor->setValue('h#'.($i+1), $courseData[$i][$courseDatakeys[3]]);
                $templateProcessor->setValue('remark#'.($i+1), $courseData[$i][$courseDatakeys[4]]);
            }
            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$filename);
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 
        }
}
