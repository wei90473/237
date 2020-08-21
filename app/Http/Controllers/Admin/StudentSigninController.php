<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// use App\Models\T01tb;
use DB;

class StudentSigninController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_signin', $user_group_auth)){
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
                FROM t04tb INNER JOIN
                t01tb ON t04tb.class = t01tb.class
                ORDER BY t04tb.class DESC");
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '';
                return view('admin/student_signin/list',compact('classArr','termArr' ,'result'));
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

        #取得班名
        $sql="SELECT name FROM t01tb WHERE class ='".$class."'";
        $temp=DB::select($sql);
        $classname=json_decode(json_encode($temp), true);

        #目　　的：依班別及期別至t04tb捉取上課的起始日期及結束日期
        $sql="SELECT CONCAT(substring(t04tb.sdate,1,3) ,'.' , substring(t04tb.sdate,4,2) , '.' , substring(t04tb.sdate,6,2)
        , ' ─ ' ,  substring(t04tb.edate,1,3) ,'.' , substring(t04tb.edate,4,2) , '.' , substring(t04tb.edate,6,2))
        as classdate
        FROM t04tb
        where t04tb.class ='".$class."'
        and t04tb.term='".$term."'";
        $temp=DB::select($sql);
        $sdate=json_decode(json_encode($temp), true);

        #取得學員名冊
        $sql="SELECT t13tb.no as no,m02tb.cname as cname
        FROM t13tb
        JOIN m02tb ON t13tb.idno = m02tb.idno and t13tb.status='1'
        and t13tb.class = '".$class."'
        and t13tb.term='".$term."'
        ORDER BY t13tb.no";
        $temp=DB::select($sql);
        $data=json_decode(json_encode($temp), true);

        #目　　的：依班別及期別至t36tb捉取實際上課的日期及m14tb的教室名稱  ?並存入ArryT36tb中?
        $sql="SELECT CONCAT(substring(t36tb.date,1,3),'.',substring(t36tb.date,4,2),'.',substring(t36tb.date,6,2))
        as classdate,m14tb.name
        FROM t36tb
        left JOIN m14tb ON t36tb.site = m14tb.site
        where t36tb.class = '".$class."'
        and t36tb.term='".$term."' order by date";
        $temp=DB::select($sql);
        $classdate=json_decode(json_encode($temp), true);

        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J5').'.docx');
        ini_set('pcre.backtrack_limit', 999999999);
        //fill values
        $templateProcessor->setValue('title',$classname[0]["name"]."第".intval($term)."期");
        $templateProcessor->setValue('sdate',$sdate[0]["classdate"]);
        $templateProcessor->setValue('class',$class);

        if(ceil(sizeof($data)/40)==1){ //paging data, 40 records a page
            for($i=0;$i<40;$i++){
                if($i<sizeof($data)){
                    $templateProcessor->setValue('no'.strval($i+1),$data[$i]["no"]);
                    $templateProcessor->setValue('name'.strval($i+1),$data[$i]["cname"]);
                }else{
                    $templateProcessor->setValue('no'.strval($i+1),"");
                    $templateProcessor->setValue('name'.strval($i+1),"");

                }
            }
            if(sizeof($classdate)>1){
                $templateProcessor->cloneBlock('d',sizeof($classdate), true, true);
                for($i=0;$i<sizeof($classdate);$i++){
                    $templateProcessor->setValue('classroom#'.strval($i+1),$classdate[$i]["name"]);
                    $templateProcessor->setValue('date#'.strval($i+1),$classdate[$i]["classdate"]);
                }

            }else{
                $templateProcessor->setValue('classroom',$classdate[0]["name"]);
                $templateProcessor->setValue('date',$classdate[0]["classdate"]);
            }

        }else{

            $templateProcessor->cloneBlock('d',intval(ceil(sizeof($data)/40))*sizeof($classdate), true, true);

            $pageid=1;
            for($i=0;$i<sizeof($classdate);$i++){
                for($j=0;$j<intval(ceil(sizeof($data)/40));$j++){
                    $templateProcessor->setValue('classroom#'.strval($pageid),$classdate[intval(ceil($pageid/2))-1]["name"]);
                    $templateProcessor->setValue('date#'.strval($pageid),$classdate[intval(ceil($pageid/2))-1]["classdate"]);
                    for($k=0;$k<40;$k++){
                        $templateProcessor->setValue('no'.strval($k+1).'#'.$pageid,isset($data[$k+$j*40]["no"])?$data[$k+$j*40]["no"]:"");
                        $templateProcessor->setValue('name'.strval($k+1).'#'.$pageid,isset($data[$k+$j*40]["cname"])?$data[$k+$j*40]["cname"]:"");
                    }
                    $pageid++;
                }
            }
            
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"學員簽到表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }

}
