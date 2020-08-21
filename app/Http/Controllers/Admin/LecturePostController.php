<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use \PhpOffice\PhpWord\PhpWord;
use \PhpOffice\PhpWord\IOFactory;

class LecturePostController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_post', $user_group_auth)){
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
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '';
                return view('admin/lecture_post/list',compact('classArr','termArr' ,'result'));
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
        $branch=$request->input('area');
        $condition=$request->input('condition');
        $outputtype=$request->input('outputtype');
        $sdatetw=$request->input('sdatetw');
        $edatetw=$request->input('edatetw');
        $sdate="";
        $edate="";
        $ttemp="";
        $filename="H5";//H5 or H5S
        $sql="SELECT C.cname, C.lname, C.position, C.fname, C.dept, C.offaddress, C.offzip, C.homaddress, C.homzip, C.regaddress, C.regzip,C.send
        FROM t04tb A
        INNER JOIN t09tb B ON A.class=B.class AND A.term=B.term
        INNER JOIN m01tb C ON B.idno=C.idno 
        INNER JOIN t01tb D ON A.class=D.class
        WHERE C.cname<>'' ";
        $esql=" GROUP BY C.cname, C.lname, C.position, C.fname, C.dept,  C.offaddress,  C.offzip,   C.homaddress,  C.homzip,   C.regaddress,  C.regzip , C.send
                ORDER BY C.dept,C.cname";

        // Validate date value.
        if($condition=="2"){
            try {
                $sdatetmp=explode("-",$sdatetw);
                $edatetmp=explode("-",$edatetw);
                $sdate=$sdatetmp[0].$sdatetmp[1].$sdatetmp[2];
                $edate=$edatetmp[0].$edatetmp[1].$edatetmp[2];
                $tflag="1";

            } catch (\Exception $e) {
                $ttemp="error";
        }

            if($ttemp=="error" || $sdate=="NaNundefinedundefined"|| $edate=="NaNundefinedundefined" )            {
                $RptBasic = new \App\Rptlib\RptBasic();
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = "日期格式錯誤，請重新輸入。";
                return view('admin/lecture_post/list',compact('classArr','termArr' ,'result'));
            }
            $sql.=" AND ( ( A.sdate BETWEEN ".$sdate."  AND  ".$edate." ) OR ( A.edate BETWEEN ".$sdate."  AND  ".$edate." ) ) ";
            if($branch!="3")
                $sql.=" AND D.branch='$branch' ";
        }else{
            $sql.=" AND (A.class='".$class."' AND A.term='".$term."' ) ";
            
        }

        if($branch=="2")    
            $filename="H5S";//H5 or H5S
        
        $sql.=$esql;

        $temp = json_decode(json_encode(DB::select($sql)), true);

        if($temp==[]){
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getclass();
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
            $termArr=$temp;
            $result = "此條件查無資料，請重新查詢。";
            return view('admin/lecture_post/list',compact('classArr','termArr' ,'result'));
        }

        $data = $temp;
        $datakeys=array_keys((array)$data);

        $outputname="講座郵寄名條";

         // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $filename).'.docx');

        //paging data
        $ctimes=ceil(sizeof($data)/2);
        ini_set('pcre.backtrack_limit', 999999999);
        $templateProcessor->cloneRow('post1', $ctimes);
        // $templateProcessor->cloneBlock('t',$ctimes, true, true);
        //fill values
        for($i=0;$i<sizeof($data);$i++){

            $name=$data[$i]["lname"].$data[$i]["position"]."　".$data[$i]["fname"];
            // if(strlen($name)>=30)
            //     $name.="\n　　　　　　　　　勛啟";
            // else
                $name.="　勛啟";

            $tt=$data[$i]["lname"].$data[$i]["position"]."　".$data[$i]["fname"];
            $post="";
            $address="";
            $dept="";
            if($condition=="1"){//依班期
                if($outputtype=="1"){
                    if($i==0)
                        $outputname.="-機關";
                    $post=$data[$i]["offzip"];
                    $address=$data[$i]["offaddress"];
                    $dept=$data[$i]["dept"];
                }elseif($outputtype=="2"){
                    if($i==0)
                        $outputname.="-住宅";
                    $post=$data[$i]["homzip"];
                    $address=$data[$i]["homaddress"];
                }else{
                    if($i==0)
                        $outputname.="-郵寄";
                    $post=$data[$i]["regzip"];
                    $address=$data[$i]["regaddress"];
                }

            }else{//依資料期間順序2:宅 1:公3:戶籍
                if($i==0)
                    $outputname.="-依資料期間";
                if($data[$i]["homaddress"]!=""){
                    $post=$data[$i]["homzip"];
                    $address=$data[$i]["homaddress"];


                }elseif($data[$i]["offaddress"]==""){
                    $post=$data[$i]["offzip"];
                    $address=$data[$i]["offaddress"];
                    $dept=$data[$i]["dept"];
                }else{
                    $post=$data[$i]["regzip"];
                    $address=$data[$i]["regaddress"];
                }
            }

            $row=ceil(($i+1)/2);
            $serial=($i+1)%2;
            if($serial==0)
                $serial=2;

            $templateProcessor->setValue('post'.$serial.'#'.$row,$post);
            $templateProcessor->setValue('address'.$serial.'#'.$row,$address);
            $templateProcessor->setValue('name'.$serial.'#'.$row,$name);
            $templateProcessor->setValue('dept'.$serial.'#'.$row,$dept);

        }

        for($j=sizeof($data);$j<$ctimes*2;$j++){
            $row=ceil(($j+1)/2);
            $serial=($j+1)%2;
            if($serial==0)
                $serial=2;
            $templateProcessor->setValue('post'.$serial.'#'.$row,"");
            $templateProcessor->setValue('address'.$serial.'#'.$row,"");
            $templateProcessor->setValue('name'.$serial.'#'.$row,"");
            $templateProcessor->setValue('dept'.$serial.'#'.$row,$dept);

        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
