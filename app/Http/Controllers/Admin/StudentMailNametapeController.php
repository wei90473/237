<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// use App\Models\T01tb;
use DB;

class StudentMailNametapeController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_mail_nametape', $user_group_auth)){
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
        return view('admin/student_mail_nametape/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    public function export(Request $request)
    {
        $classes = $request->input('classes');
        $term = $request->input('term');
        //1:機關, 2:住家, 3:郵寄
        $type = $request->input('type');

        $sql = "SELECT  RTRIM(A.lname) as lname,
                        TRIM(A.position) AS position,
                        RTRIM(A.fname) as fname,
                        TRIM(B.dept) AS dept,
                        TRIM(A.offaddr1) AS offaddr1,
                        TRIM(A.offaddr2) AS offaddr2,
                        TRIM(A.offzip) AS offzip,
                        TRIM(A.homaddr1) AS homaddr1,
                        TRIM(A.homaddr2) AS homaddr2,
                        TRIM(A.homzip) AS homzip,
                        A.send
                    FROM m02tb A LEFT JOIN  t13tb B ON A.idno = B.idno
                    WHERE B.class = '".$classes."'
                    AND B.term = '".$term."'
                    AND B.status = '1'
                    ORDER BY B.no
                        ";

        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);



        $sql="select branch from t01tb where class='$classes'";
        $branch = json_decode(json_encode(DB::select($sql)), true);

        $filename='J12';
        if($branch[0]["branch"]=="2"){
            $filename='J12S';
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $filename).'.docx');

        // 每列要放的資料(#1：第一列、以此類推)
        $DEPT ='';
        $ZIP ='';
        $AddRESS = '';
        $A=1;
        $B=1;
        // 要放的資料筆數，先建 列
        $templateProcessor->cloneRow('CNAME1', ceil(sizeof($dataArr)/2));
        for($i=0; $i<sizeof($dataArr); $i++) {
            if($type=='1' || $type=='3' ){
                $DEPT = $dataArr[$i]['dept'];
                $ZIP = $dataArr[$i]['offzip'];
                $AddRESS = $dataArr[$i]['offaddr1'].$dataArr[$i]['offaddr2'];
            } else {
                $DEPT = ' ';
                $ZIP = $dataArr[$i]['homzip'];
                $AddRESS = $dataArr[$i]['homaddr1'].$dataArr[$i]['homaddr2'];
            }

                if($i%2==0){
                    $templateProcessor->setValue('Z1#'.($A), $ZIP);
                    $templateProcessor->setValue('DEPT1#'.($A), $DEPT);
                    $templateProcessor->setValue('ADDRESS1#'.($A), $AddRESS);
                    //劉副分處長 XX 勛啟
                    $templateProcessor->setValue('CNAME1#'.($A), $dataArr[$i]['lname'].$dataArr[$i]['position'].' '.$dataArr[$i]['fname'].' 勛啟');
                    $A++;
                }
                if($i%2==1){
                    $templateProcessor->setValue('Z2#'.($B), $ZIP);
                    $templateProcessor->setValue('DEPT2#'.($B), $DEPT);
                    $templateProcessor->setValue('ADDRESS2#'.($B), $AddRESS);
                    $templateProcessor->setValue('CNAME2#'.($B), $dataArr[$i]['lname'].$dataArr[$i]['position'].' '.$dataArr[$i]['fname'].' 勛啟');
                    $B++;
                }
        }
            if((sizeof($dataArr)%2)!=0){
                $templateProcessor->setValue('Z2#'.($B), ' ');
                $templateProcessor->setValue('DEPT2#'.($B), ' ');
                $templateProcessor->setValue('ADDRESS2#'.($B), ' ');
                $templateProcessor->setValue('CNAME2#'.($B),' ');
            }
        $outputname="";
        //1:機關, 2:住家, 3:郵寄
        if ($type == '1') {
            $outputname="學員郵寄名條-機關";
        }
        if ($type == '2') {
            $outputname="學員郵寄名條-住家";
        }
        if ($type == '3') {
            $outputname="學員郵寄名條-郵寄";
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }

}
