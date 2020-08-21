<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use \PhpOffice\PhpWord\PhpWord;
use \PhpOffice\PhpWord\IOFactory;

class LectureCategoriesController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_categories', $user_group_auth)){
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
        $CatArr=$this->getCategory();

        $result="";
        return view('admin/lecture_categories/list',compact('result','CatArr'));
    }

    public function getCategory(){
        return DB::select("SELECT DISTINCT code,name FROM s01tb where type='B' ORDER BY s01tb.code");
    }

    public function export(Request $request){
        $category=$request->input('category');
        $expertise=str_replace("'","", $request->input('expertise'));
        $condition=$request->input('condition'); //1. category 2. expertise

        $sdatetw=$request->input('sdatetw');
        $edatetw=$request->input('edatetw');
        $sdate="";
        $edate="";
        $ttemp="";

        // Validate date value.
        try {
            $sdatetmp=explode("-",$sdatetw);
            $edatetmp=explode("-",$edatetw);
            $sdate=$sdatetmp[0].$sdatetmp[1].$sdatetmp[2];
            $edate=$edatetmp[0].$edatetmp[1].$edatetmp[2];
            $tflag="1";

        } catch (\Exception $e) {
            $ttemp="error";
        }
        if($ttemp=="error" || $sdate=="NaNundefinedundefined"|| $edate=="NaNundefinedundefined" )
        {
            $CatArr=$this->getCategory();
            $result = "日期格式錯誤，請重新輸入。";
            return view('admin/lecture_categories/list',compact('result','CatArr'));
        }

        $sql="";
        $sql1="SELECT DISTINCT CONCAT(m01tb.cname,t09tb.class,t09tb.term,t09tb.course)
         AS sort,
        m01tb.dept,
        m01tb.position,
        m01tb.idno AS idno,
        m01tb.cname AS  name,
        t01tb.name AS classname,
        t09tb.term AS term,
        t06tb.name AS coursename,
        t06tb.hour AS hour,
        t09tb.okrate AS okrate ";
        $sql2="";
        $sql3=" AND EXISTS ( SELECT * FROM t04tb Where class = t09tb.class AND term=t09tb.term AND ( sdate BETWEEN '".$sdate."' AND '".$edate."' ) ) " ;

        if($condition=="1"){
            $sql2.="FROM m16tb
            INNER JOIN t09tb ON m16tb.idno=t09tb.idno
            INNER JOIN t06tb ON t09tb.class=t06tb.class AND t09tb.term=t06tb.term AND t09tb.course=t06tb.course
            INNER  JOIN t01tb ON t09tb.class=t01tb.class
            INNER  JOIN m01tb ON m16tb.idno=m01tb.idno ";

            if(substr($category,-2)=="00"){
                $sql2.=" WHERE m16tb.specialty like '".substr($category,0,1)."%' ";
            }else{
                $sql2.=" WHERE m16tb.specialty = '".$category."' ";

            }

        }else{
            $sql2.="FROM s01tb
            INNER JOIN m16tb ON s01tb.code=m16tb.specialty
            INNER JOIN t09tb ON m16tb.idno=t09tb.idno
            INNER  JOIN t06tb ON t09tb.class=t06tb.class
            AND  t09tb.term=t06tb.term
            AND  t09tb.course=t06tb.course
            INNER  JOIN t01tb ON t09tb.class=t01tb.class
            INNER  JOIN m01tb ON m16tb.idno=m01tb.idno
            WHERE  s01tb.type='B'
            AND s01tb.name like '%".$expertise."%'";

        }

        $sql.=$sql1.$sql2.$sql3;

        $temp = json_decode(json_encode(DB::select($sql)), true);

        if($temp==[]){
            $CatArr=$this->getCategory();
            $result="此條件查無資料，請重新查詢。";
            return view('admin/lecture_categories/list',compact('result','CatArr'));
        }

        $data = $temp;
        $datakeys=array_keys((array)$data);

        $temp=DB::select("SELECT DISTINCT name FROM s01tb where type='B' AND s01tb.code='".$category."'");
        $temp = json_decode(json_encode( $temp), true);
        $typename=$temp;

        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H7A').'.docx');

        $templateProcessor->cloneRow('name', sizeof($data));

        //fill values
        for($i=0;$i<sizeof($data);$i++){
            $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["name"]);
            $templateProcessor->setValue('dept#'.strval($i+1),$data[$i]["dept"]);
            $templateProcessor->setValue('pos#'.strval($i+1),$data[$i]["position"]);
            $templateProcessor->setValue('class#'.strval($i+1),$data[$i]["classname"]."第".strval((int)$data[$i]["term"])."期");
            $templateProcessor->setValue('course#'.strval($i+1),$data[$i]["coursename"]);
            $templateProcessor->setValue('sat#'.strval($i+1),$data[$i]["okrate"]);
        }
        $templateProcessor->setValue('date',$sdatetmp[0]."/".$sdatetmp[1]."/".$sdatetmp[2]."~".$edate=$edatetmp[0]."/".$edatetmp[1]."/".$edatetmp[2]);
        $templateProcessor->setValue('type',isset($typename[0]["name"])?$typename[0]["name"]:"");
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"講座一覽表-類別");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
        
    }


}
