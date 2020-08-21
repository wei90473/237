<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// use App\Models\T01tb;
use DB;

class LectureCourseController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_course', $user_group_auth)){
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
        $result="";
        return view('admin/lecture_course/list',compact('result'));
    }

    public function export(Request $request){
        $course=$request->input('course');

        $sdatetw=$request->input('sdatetw');
        $edatetw=$request->input('edatetw');
        $sdate="";
        $edate="";
        $ttemp="";

        try {
            $sdatetmp=explode("-",$sdatetw);
            $edatetmp=explode("-",$edatetw);
            $sdate=$sdatetmp[0].$sdatetmp[1].$sdatetmp[2];
            $edate=$edatetmp[0].$edatetmp[1].$edatetmp[2];
            $tflag="1";
            // Validate the value...
        } catch (\Exception $e) {
                $ttemp="error";
        }
        if($ttemp=="error" || $sdate=="NaNundefinedundefined"|| $edate=="NaNundefinedundefined" )
        {
            $result = "日期格式錯誤，請重新輸入。";
            return view('admin/lecture_categories/list',compact('result','CatArr'));
        }

        $sql="SELECT DISTINCT
        CONCAT(m01tb.cname,t09tb.class,t09tb.term,t09tb.course) AS sort,
        m01tb.dept,
        m01tb.position,
        t09tb.idno AS idno,
        m01tb.cname AS  name,
        t01tb.name AS classname,
        t09tb.term AS term,
        t06tb.name AS coursename,
        t06tb.hour AS hour,
        t09tb.okrate AS okrate
        FROM t06tb INNER JOIN t09tb ON t06tb.class=t09tb.class AND t06tb.term=t09tb.term AND t06tb.course=t09tb.course
        INNER JOIN  t01tb ON t09tb.class=t01tb.class
        INNER JOIN m01tb ON t09tb.idno=m01tb.idno
        WHERE t06tb.name LIKE '%".$course."%'
        AND EXISTS
        (SELECT * FROM t04tb Where class = t09tb.class AND term=t09tb.term AND ( sdate BETWEEN '".$sdate."' AND  '".$edate."') )
        ORDER BY  sort " ;

        $temp = json_decode(json_encode(DB::select($sql)), true);

        if($temp==[]){
            $result="此條件查無資料，請重新查詢。";
            return view('admin/lecture_course/list',compact('result'));
        }


        $data = $temp;
        $datakeys=array_keys((array)$data);

        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H7C').'.docx');

        $templateProcessor->cloneRow('name', sizeof($data));

        //fill values
        for($i=0;$i<sizeof($data);$i++){
            $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["name"]);
            $templateProcessor->setValue('dept#'.strval($i+1),$data[$i]["dept"]);
            $templateProcessor->setValue('pos#'.strval($i+1),$data[$i]["position"]);
            $templateProcessor->setValue('class#'.strval($i+1),$data[$i]["classname"]."第".strval((int)$data[$i]["term"])."期");
            $templateProcessor->setValue('course#'.strval($i+1),str_replace("&","與",$data[$i]["coursename"]));
            $templateProcessor->setValue('h#'.strval($i+1),$data[$i]["hour"]);
            $templateProcessor->setValue('sat#'.strval($i+1),$data[$i]["okrate"]);
        }
        $templateProcessor->setValue('date',$sdatetmp[0]."/".$sdatetmp[1]."/".$sdatetmp[2]."~".$edate=$edatetmp[0]."/".$edatetmp[1]."/".$edatetmp[2]);
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"講座一覽表-課程");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }

}
