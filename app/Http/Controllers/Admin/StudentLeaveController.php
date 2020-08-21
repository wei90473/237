<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;

class StudentLeaveController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_leave', $user_group_auth)){
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
             return view('admin/student_leave/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    public function export(Request $request){

        $class = $request->input('classes');
        $term = $request->input('term');
        $type = $request->input('type');
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->toCHTnum2(12);
        $sql="select type from t01tb where class='".$class."'";
        $temp = DB::select($sql);
        $classtype = json_decode(json_encode($temp), true);

        if($type=="2"){//明細
            $sql="SELECT t13tb.dept as dept,t13tb.no AS no, m02tb.cname AS cname, t14tb.*
                    FROM t13tb
                    JOIN t14tb ON t13tb.idno = t14tb.idno
                    JOIN m02tb ON t14tb.idno = m02tb.idno
                    and t14tb.idno = m02tb.idno
                    and t14tb.class ='".$class."'
                    and t14tb.term='".$term."'
                    and t13tb.class = '".$class."'
                    and t13tb.term='".$term."'
                    and t13tb.status='1'
                    ORDER BY t13tb.no ";

            $temp = DB::select($sql);

            if(sizeof($temp) == 0) {
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '查無資料，請重新查詢';
                return view('admin/student_leave/list',compact('classArr','termArr' ,'result'));
            }

            $data = json_decode(json_encode($temp), true);

            $sql="select name from t01tb where class='".$class."'";
            $temp = DB::select($sql);
            $classname = json_decode(json_encode($temp), true);


            $sql="SELECT A.idno,COUNT(A.idno) AS cnt
                FROM t13tb A INNER JOIN t14tb B ON A.class = B.class AND A.term = B.term AND A.idno = B.idno
                INNER JOIN m02tb C ON A.idno = C.idno
                WHERE A.status = '1' AND A.class = '".$class."' AND A.term = '".$term."' GROUP BY A.idno";
            $temp = DB::select($sql);
            $cnt = json_decode(json_encode($temp), true);

            $title=$classname[0]["name"]."第".$RptBasic->toCHTnum2((int)$term)."期";

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J7B').'.docx');
            ini_set('pcre.backtrack_limit', 999999999);
            //fill values
            $templateProcessor->setValue('title',$title);
            if(sizeof($cnt)==1){ //handle single record
                if(sizeof($data)==1){

                    $templateProcessor->setValue('dept',$data[0]["dept"]);
                    $templateProcessor->setValue('name',$data[0]["cname"]);

                    $ltype="";
                    switch ($data[0]["type"]) {
                        case '1':
                            $ltype="事假";
                            break;
                        case '2':
                            $ltype="病假";
                            break;
                        case '3':
                            $ltype="喪假";
                            break;
                        }

                    $ltime=
                    $RptBasic->toCHTnum2((int)substr($data[0]["sdate"],3,2))."月".
                    $RptBasic->toCHTnum2((int)substr($data[0]["sdate"],5,2))."日".
                    $RptBasic->toCHTnum2((int)substr($data[0]["stime"],0,2))."時".
                    $RptBasic->toCHTnum2((int)substr($data[0]["stime"],2,2))."分至".
                    $RptBasic->toCHTnum2((int)substr($data[0]["edate"],3,2))."月".
                    $RptBasic->toCHTnum2((int)substr($data[0]["edate"],5,2))."日".
                    $RptBasic->toCHTnum2((int)substr($data[0]["etime"],0,2))."時".
                    $RptBasic->toCHTnum2((int)substr($data[0]["etime"],2,2))."分止";

                    $hr=$RptBasic->toCHTnum2($data[0]["hour"]);
                    $templateProcessor->setValue('t1',$ltype);
                    $templateProcessor->setValue('h1',$hr."小時");
                    $templateProcessor->setValue('time1',$ltime);
                    $templateProcessor->setValue('reason1',$data[0]["reason"]);

                    for($i=2;$i<9;$i++){
                        $templateProcessor->setValue('t'.$i,"");
                        $templateProcessor->setValue('h'.$i,"");
                        $templateProcessor->setValue('time'.$i,"");
                        $templateProcessor->setValue('reason'.$i,"");
                    }


                }else{
                    $templateProcessor->setValue('dept',$data[0]["dept"]);
                    $templateProcessor->setValue('name',$data[0]["cname"]);

                    if(sizeof($data)>8){
                        $templateProcessor->cloneRow('t1', sizeof($data));
                        for($i=0;$i<sizeof($data);$i++){

                            $ltype="";
                            switch ($data[$i]["type"]) {
                                case '1':
                                    $ltype="事假";
                                    break;
                                case '2':
                                    $ltype="病假";
                                    break;
                                case '3':
                                    $ltype="喪假";
                                    break;
                                }

                            $ltime=
                            $RptBasic->toCHTnum2((int)substr($data[$i]["sdate"],3,2))."月".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["sdate"],5,2))."日".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["stime"],0,2))."時".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["stime"],2,2))."分至".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["edate"],3,2))."月".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["edate"],5,2))."日".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["etime"],0,2))."時".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["etime"],2,2))."分止";

                            $hr=$RptBasic->toCHTnum2($data[$i]["hour"]);
                            $templateProcessor->setValue('t1#'.strval($i+1),$ltype);
                            $templateProcessor->setValue('h1#'.strval($i+1),$hr."小時");
                            $templateProcessor->setValue('time1#'.strval($i+1),$ltime);
                            $templateProcessor->setValue('reason1#'.strval($i+1),$data[$i]["reason"]);
                        }

                        for($i=2;$i<9;$i++){
                            $templateProcessor->setValue('t'.$i,"");
                            $templateProcessor->setValue('h'.$i,"");
                            $templateProcessor->setValue('time'.$i,"");
                            $templateProcessor->setValue('reason'.$i,"");
                        }

                    }else{
                        for($i=0;$i<sizeof($data);$i++){
                            $ltype="";
                            switch ($data[$i]["type"]) {
                                case '1':
                                    $ltype="事假";
                                    break;
                                case '2':
                                    $ltype="病假";
                                    break;
                                case '3':
                                    $ltype="喪假";
                                    break;
                                }

                            $ltime=
                            $RptBasic->toCHTnum2((int)substr($data[$i]["sdate"],3,2))."月".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["sdate"],5,2))."日".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["stime"],0,2))."時".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["stime"],2,2))."分至".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["edate"],3,2))."月".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["edate"],5,2))."日".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["etime"],0,2))."時".
                            $RptBasic->toCHTnum2((int)substr($data[$i]["etime"],2,2))."分止";

                            $hr=$RptBasic->toCHTnum2($data[$i]["hour"]);
                            $templateProcessor->setValue('t'.strval($i+1),$ltype);
                            $templateProcessor->setValue('h'.strval($i+1),$hr."小時");
                            $templateProcessor->setValue('time'.strval($i+1),$ltime);
                            $templateProcessor->setValue('reason'.strval($i+1),$data[$i]["reason"]);
                        }
                        for($i=2;$i<9;$i++){
                            $templateProcessor->setValue('t'.$i,"");
                            $templateProcessor->setValue('h'.$i,"");
                            $templateProcessor->setValue('time'.$i,"");
                            $templateProcessor->setValue('reason'.$i,"");
                        }
                    }

                }

            }else{ //handle multi-records
                $templateProcessor->cloneBlock('b',sizeof($cnt), true, true);

                for($k=0;$k<sizeof($cnt);$k++){
                    $sql="SELECT t13tb.dept as dept,t13tb.no AS no, m02tb.cname AS cname, t14tb.*
                    FROM t13tb
                    JOIN t14tb ON t13tb.idno = t14tb.idno
                    JOIN m02tb ON t14tb.idno = m02tb.idno
                    and t14tb.idno = m02tb.idno
                    and t14tb.class ='".$class."'
                    and t14tb.term='".$term."'
                    and t13tb.class = '".$class."'
                    and t13tb.term='".$term."'
                    and t13tb.status='1' and t13tb.idno='".$cnt[$k]["idno"]."'
                    ORDER BY t13tb.no ";
                    $temp = DB::select($sql);
                    $data = json_decode(json_encode($temp), true);

                    $templateProcessor->setValue('dept#'.strval($k+1),$data[0]["dept"]);
                    $templateProcessor->setValue('name#'.strval($k+1),$data[0]["cname"]);

                    if($cnt[$k]["cnt"]==1){

                        $ltype="";
                        switch ($data[0]["type"]) {
                            case '1':
                                $ltype="事假";
                                break;
                            case '2':
                                $ltype="病假";
                                break;
                            case '3':
                                $ltype="喪假";
                                break;
                            }

                        $ltime=
                        $RptBasic->toCHTnum2((int)substr($data[0]["sdate"],3,2))."月".
                        $RptBasic->toCHTnum2((int)substr($data[0]["sdate"],5,2))."日".
                        $RptBasic->toCHTnum2((int)substr($data[0]["stime"],0,2))."時".
                        $RptBasic->toCHTnum2((int)substr($data[0]["stime"],2,2))."分至".
                        $RptBasic->toCHTnum2((int)substr($data[0]["edate"],3,2))."月".
                        $RptBasic->toCHTnum2((int)substr($data[0]["edate"],5,2))."日".
                        $RptBasic->toCHTnum2((int)substr($data[0]["etime"],0,2))."時".
                        $RptBasic->toCHTnum2((int)substr($data[0]["etime"],2,2))."分止";

                        $hr=$RptBasic->toCHTnum2($data[0]["hour"]);
                        $templateProcessor->setValue('t1#'.strval($k+1),$ltype);
                        $templateProcessor->setValue('h1#'.strval($k+1),$hr."小時");
                        $templateProcessor->setValue('time1#'.strval($k+1),$ltime);
                        $templateProcessor->setValue('reason1#'.strval($k+1),$data[0]["reason"]);

                        for($i=2;$i<9;$i++){
                            $templateProcessor->setValue('t'.$i.'#'.strval($k+1),"");
                            $templateProcessor->setValue('h'.$i.'#'.strval($k+1),"");
                            $templateProcessor->setValue('time'.$i.'#'.strval($k+1),"");
                            $templateProcessor->setValue('reason'.$i.'#'.strval($k+1),"");
                        }

                    }else{
                        if($cnt[$k]["cnt"]>8){
                            $templateProcessor->cloneRow('t1#'.strval($k+1), $cnt[$k]["cnt"]);
                            for($i=0;$i<$cnt[$k]["cnt"];$i++){

                                $ltype="";
                                switch ($data[$i]["type"]) {
                                    case '1':
                                        $ltype="事假";
                                        break;
                                    case '2':
                                        $ltype="病假";
                                        break;
                                    case '3':
                                        $ltype="喪假";
                                        break;
                                    }

                                $ltime=
                                $RptBasic->toCHTnum2((int)substr($data[$i]["sdate"],3,2))."月".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["sdate"],5,2))."日".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["stime"],0,2))."時".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["stime"],2,2))."分至".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["edate"],3,2))."月".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["edate"],5,2))."日".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["etime"],0,2))."時".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["etime"],2,2))."分止";

                                $hr=$RptBasic->toCHTnum2($data[$i]["hour"]);
                                $templateProcessor->setValue('t1#'.strval($k+1).'#'.strval($i+1),$ltype);
                                $templateProcessor->setValue('h1#'.strval($k+1).'#'.strval($i+1),$hr."小時");
                                $templateProcessor->setValue('time1#'.strval($k+1).'#'.strval($i+1),$ltime);
                                $templateProcessor->setValue('reason1#'.strval($k+1).'#'.strval($i+1),$data[$i]["reason"]);


                            }

                            for($i=2;$i<9;$i++){
                                $templateProcessor->setValue('t'.$i.'#'.strval($k+1),"");
                                $templateProcessor->setValue('h'.$i.'#'.strval($k+1),"");
                                $templateProcessor->setValue('time'.$i.'#'.strval($k+1),"");
                                $templateProcessor->setValue('reason'.$i.'#'.strval($k+1),"");
                            }

                        }else{
                            for($i=0;$i<$cnt[$k]["cnt"];$i++){

                                $ltype="";
                                switch ($data[$i]["type"]) {
                                    case '1':
                                        $ltype="事假";
                                        break;
                                    case '2':
                                        $ltype="病假";
                                        break;
                                    case '3':
                                        $ltype="喪假";
                                        break;
                                    }

                                $ltime=
                                $RptBasic->toCHTnum2((int)substr($data[$i]["sdate"],3,2))."月".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["sdate"],5,2))."日".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["stime"],0,2))."時".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["stime"],2,2))."分至".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["edate"],3,2))."月".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["edate"],5,2))."日".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["etime"],0,2))."時".
                                $RptBasic->toCHTnum2((int)substr($data[$i]["etime"],2,2))."分止";

                                $hr=$RptBasic->toCHTnum2($data[$i]["hour"]);
                                $templateProcessor->setValue('t'.strval($i+1).'#'.strval($k+1),$ltype);
                                $templateProcessor->setValue('h'.strval($i+1).'#'.strval($k+1),$hr."小時");
                                $templateProcessor->setValue('time'.strval($i+1).'#'.strval($k+1),$ltime);
                                $templateProcessor->setValue('reason'.strval($i+1).'#'.strval($k+1),$data[$i]["reason"]);


                            }

                            for($i=2;$i<9;$i++){
                                $templateProcessor->setValue('t'.$i.'#'.strval($k+1),"");
                                $templateProcessor->setValue('h'.$i.'#'.strval($k+1),"");
                                $templateProcessor->setValue('time'.$i.'#'.strval($k+1),"");
                                $templateProcessor->setValue('reason'.$i.'#'.strval($k+1),"");
                            }
                        }
                    }
                }
            }

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"學員請假-明細");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel
            //$doctype:1.ooxml 2.odf
            //$filename:filename

        }else if($type=="1"){//總表

            if($classtype[0]["type"]=="13"){//游於藝
                $sql="SELECT distinct t13tb.idno AS idno, m02tb.cname AS cname, t13tb.dept as dept,t13tb.no
                    FROM t13tb
                    JOIN t14tb ON t13tb.idno = t14tb.idno
                    JOIN m02tb ON t14tb.idno = m02tb.idno
                    and t14tb.idno = m02tb.idno
                    and t14tb.class = '".$class."'
                    and t14tb.term='".$term."'
                    and t13tb.class = '".$class."'
                    and t13tb.term='".$term."'
                    and t13tb.status='1'
                    ORDER BY t13tb.no";
                    $temp = DB::select($sql);
                    $student = json_decode(json_encode($temp), true);

                    if(sizeof($temp) == 0) {
                        $temp=$RptBasic->getclass();
                        $classArr=$temp;
                        $temp=json_decode(json_encode($temp), true);
                        $arraykeys=array_keys((array)$temp[0]);
                        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                        $termArr=$temp;
                        $result = '查無資料，請重新查詢';
                        return view('admin/student_leave/list',compact('classArr','termArr' ,'result'));
                    }

                $sql="";
                for($i=0;$i<sizeof($student);$i++){
                    $sql.=" select distinct '".$student[$i]["cname"]."' as cname,'".$student[$i]["dept"]."' as dept,'".$student[$i]["no"]."' as no,
                            TAB1.hour as type1hour,
                            TAB2.hour as type2hour,
                            TAB1.hour+TAB2.hour as total_hour
                            from t14tb,
                            (select ifnull(sum(hour),0) as hour from t14tb where class='".$class."' and term='".$term."'  and idno='".$student[$i]["idno"]."' and type='4') TAB1,
                            (select ifnull(sum(hour),0) as hour from t14tb where class='".$class."' and term='".$term."'  and idno='".$student[$i]["idno"]."' and type='5') TAB2
                            where class='".$class."'
                            and term='".$term."'
                            and idno='".$student[$i]["idno"]."' " ;

                    if($i==(sizeof($student)-1)){
                        $sql.=" order by no ";
                    }else{
                        $sql.=" union all ";
                    }

                }
                $temp = DB::select($sql);
                $data = json_decode(json_encode($temp), true);

                $sql="select name from t01tb where class='".$class."'";
                $temp = DB::select($sql);
                $classname = json_decode(json_encode($temp), true);
                $title=$classname[0]["name"]."第".$RptBasic->toCHTnum2((int)$term)."期";

                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J7C').'.docx');
                $templateProcessor->setValue('class',$title);
                $templateProcessor->cloneRow('name', sizeof($data));

                for($i=0;$i<sizeof($data);$i++){
                    $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["cname"]);
                    $templateProcessor->setValue('dept#'.strval($i+1),$data[$i]["dept"]);
                    $templateProcessor->setValue('t1#'.strval($i+1),$data[$i]["type1hour"]);
                    $templateProcessor->setValue('t2#'.strval($i+1),$data[$i]["type2hour"]);
                    $templateProcessor->setValue('h#'.strval($i+1),$data[$i]["total_hour"]);
                }

                $RptBasic = new \App\Rptlib\RptBasic();
                $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"學員請假-總表");
                //$obj: entity of file
                //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel
                //$doctype:1.ooxml 2.odf
                //$filename:filename


            }else{//非游於藝
                $sql="SELECT distinct t13tb.idno AS idno, m02tb.cname AS cname, t13tb.dept as dept,t13tb.no
                    FROM t13tb
                    JOIN t14tb ON t13tb.idno = t14tb.idno
                    JOIN m02tb ON t14tb.idno = m02tb.idno
                    and t14tb.idno = m02tb.idno
                    and t14tb.class = '".$class."'
                    and t14tb.term='".$term."'
                    and t13tb.class = '".$class."'
                    and t13tb.term='".$term."'
                    ORDER BY t13tb.no ";

                $temp = DB::select($sql);
                $student = json_decode(json_encode($temp), true);

                if(sizeof($temp) == 0) {
                    $temp=$RptBasic->getclass();
                    $classArr=$temp;
                    $temp=json_decode(json_encode($temp), true);
                    $arraykeys=array_keys((array)$temp[0]);
                    $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                    $termArr=$temp;
                    $result = '查無資料，請重新查詢';
                    return view('admin/student_leave/list',compact('classArr','termArr' ,'result'));
                }
                $sql="";
                for($i=0;$i<sizeof($student);$i++){
                $sql.=" select distinct '".$student[$i]["cname"]."' as cname,'".$student[$i]["dept"]."' as dept,'".$student[$i]["no"]."' as no,
                        TAB1.hour as type1hour,
                        TAB2.hour as type2hour,
                        TAB3.hour as type3hour,
                        TAB1.hour+TAB2.hour+TAB3.hour as total_hour
                        from t14tb,
                        (select ifnull(sum(hour),0) as hour from t14tb where class='".$class."' and term='".$term."'
                        and idno='".$student[$i]["idno"]."' and type='1') TAB1,
                        (select ifnull(sum(hour),0) as hour from t14tb where class='".$class."' and term='".$term."'
                        and idno='".$student[$i]["idno"]."' and type='2') TAB2,
                        (select ifnull(sum(hour),0) as hour from t14tb where class='".$class."' and term='".$term."'
                        and idno='".$student[$i]["idno"]."' and type='3') TAB3
                        where class='".$class."'
                        and   term='".$term."'
                        and   idno='".$student[$i]["idno"]."' ";

                    if($i==(sizeof($student)-1)){
                        $sql.=" order by no ";
                    }else{
                        $sql.=" union all ";
                    }

                }
                $temp = DB::select($sql);
                $data = json_decode(json_encode($temp), true);

                $sql="select name from t01tb where class='".$class."'";
                $temp = DB::select($sql);
                $classname = json_decode(json_encode($temp), true);
                $title=$classname[0]["name"]."第".$RptBasic->toCHTnum2((int)$term)."期";

                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J7A').'.docx');
                $templateProcessor->setValue('class',$title);
                $templateProcessor->cloneRow('name', sizeof($data));

                for($i=0;$i<sizeof($data);$i++){
                    $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["cname"]);
                    $templateProcessor->setValue('dept#'.strval($i+1),$data[$i]["dept"]);
                    $templateProcessor->setValue('t1#'.strval($i+1),$data[$i]["type1hour"]);
                    $templateProcessor->setValue('t2#'.strval($i+1),$data[$i]["type2hour"]);
                    $templateProcessor->setValue('t3#'.strval($i+1),$data[$i]["type3hour"]);
                    $templateProcessor->setValue('h#'.strval($i+1),$data[$i]["total_hour"]);
                }

                $RptBasic = new \App\Rptlib\RptBasic();
                $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"學員請假-總表");
                //$obj: entity of file
                //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel
                //$doctype:1.ooxml 2.odf
                //$filename:filename

            }
        }else if($type=="3"){

            $sql="select name from t01tb where class='".$class."'";
            $temp = DB::select($sql);
            $classname = json_decode(json_encode($temp), true);
            $title=$classname[0]["name"]."第".$term."期";
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J7D').'.docx');
            $templateProcessor->setValue('class',$title);
            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"學員請假單");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel
            //$doctype:1.ooxml 2.odf
            //$filename:filename
        }


    }

}
