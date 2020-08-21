<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// use App\Models\T01tb;
use DB;

class LectureClassController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_class', $user_group_auth)){
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
                return view('admin/lecture_class/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }
    public function export(Request $request){
        $class=$request->input('classes');
        $term=$request->input('term');

        $sql="SELECT m01tb.cname as name,t06tb.name as classname,t06tb.hour as hour,t09tb.okrate AS okrate
        FROM t09tb inner join t06tb on t09tb.class=t06tb.class and t09tb.term=t06tb.term and t09tb.course=t06tb.course
        inner join m01tb on t09tb.idno=m01tb.idno
        where t09tb.class='".$class."' and t09tb.term='".$term."'
        order by t09tb.course " ;

        $temp = json_decode(json_encode(DB::select($sql)), true);

        if($temp==[]){

            $result="此條件查無資料，請重新查詢。";
            $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
            FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
            ORDER BY t04tb.class DESC");
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
            $termArr=$temp;
            return view('admin/lecture_class/list',compact('classArr','termArr' ,'result'));
        }

        $data = $temp;
        $datakeys=array_keys((array)$data);

        $temp=DB::select("SELECT DISTINCT RTRIM(name) as name FROM t01tb WHERE class='".$class."'");
        $temp = json_decode(json_encode( $temp), true);
        $classname=$temp;

        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H7B').'.docx');

        $templateProcessor->cloneRow('name', sizeof($data));

        //fill values
        for($i=0;$i<sizeof($data);$i++){
            $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["name"]);
            $templateProcessor->setValue('course#'.strval($i+1),$data[$i]["classname"]);
            $templateProcessor->setValue('h#'.strval($i+1),$data[$i]["hour"]);
            $templateProcessor->setValue('sat#'.strval($i+1),$data[$i]["okrate"]);
        }
        $templateProcessor->setValue('class',$classname[0]["name"]."第".strval((int)$term)."期");
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"講座一覽表-班期");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
