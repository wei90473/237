<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\T04tb;
use DB;
use App\Services\User_groupService;

class LectureListController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_list', $user_group_auth)){
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
        $sess = $request->session()->get('lock_class');
        $queryData = array();
        if($sess){
            $queryData['class'] = $sess['class'];
            $queryData['term'] = $sess['term'];
            //$temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class WHERE t04tb.class ='".$queryData['class']."' and t04tb.term ='".$queryData['term']."' ORDER BY t04tb.class DESC");
            $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class ORDER BY t04tb.class DESC");
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            //$temp=DB::select("SELECT  DISTINCT term FROM t04tb WHERE class ='".$queryData['class']."' and term ='".$queryData['term']."' ORDER By term");
            $temp=DB::select("SELECT  DISTINCT term FROM t04tb WHERE class ='".$temp[0][$arraykeys[0]]."' ORDER By term");
            $termArr=$temp;
            $result = '';
            return view('admin/lecture_list/list',compact('classArr','termArr' ,'result','queryData'));
        }else{
            //取得班別
            $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class ORDER BY t04tb.class DESC");
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT  DISTINCT term FROM t04tb WHERE class ='".$temp[0][$arraykeys[0]]."' ORDER By term");
            $termArr=$temp;
            $result = '';
            return view('admin/lecture_list/list',compact('classArr','termArr' ,'result','queryData'));
        }

    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    public function  export(Request $request)
    {
        $class=$request->input('classes');
        $term=$request->input('term');
        $type=$request->input('type');
        $wclause=" WHERE A.class='".$class."' AND A.term='".$term."' ";

        if($type!="1")
            $wclause.="and A.hire='Y' ";

     //   T04tb::where('class', $class)->where('term', $term)->update($data);

        $sql="SELECT unit,course,unitname,classname,hour,name,position,offtelno1,offtelno2,homtel,liaison,offfax,homfax,mobiltel,email
        from (
        SELECT
        IFNULL(B.unit,'') AS unit,
        A.course ,
        IFNULL(C.name,'') AS unitname,
        IFNULL(B.name,'') AS classname,
        IFNULL(B.hour,0) AS hour,
        A.cname AS name,
        CONCAT(RTRIM( A.dept),RTRIM(A.position)) AS position,
        CONCAT(RTRIM(CASE A.offtela1  WHEN '' THEN   A.offtela1  ELSE CONCAT('(' , RTRIM(A.offtela1) ,')')   END ) ,
        RTRIM(CASE WHEN  LENGTH(RTRIM(A.offtelb1)) = 8  THEN CONCAT(substring(A.offtelb1,1,4) , '-' , substring(A.offtelb1,5,4))  ELSE A.offtelb1 END ) ,
        RTRIM(CASE A.offtelc1  WHEN '' THEN   A.offtelc1  ELSE CONCAT('轉' ,A.offtelc1)  END)) AS offtelno1,
        CONCAT(RTRIM(CASE A.offtela2  WHEN '' THEN   A.offtela2  ELSE CONCAT('(' , RTRIM(A.offtela2) ,')')   END ) ,
        RTRIM(CASE WHEN  LENGTH(RTRIM(A.offtelb2)) = 8  THEN CONCAT(substring(A.offtelb2,1,4) , '-' , substring(A.offtelb2,5,4))  ELSE A.offtelb2 END ) ,
        RTRIM(CASE A.offtelc2  WHEN '' THEN   A.offtelc2  ELSE CONCAT('轉',A.offtelc2)  END)) AS offtelno2,
        CONCAT(RTRIM(CASE A.homtela  WHEN '' THEN  A.homtela  ELSE CONCAT('(' ,RTRIM(A.homtela),')')   END ),
        RTRIM(CASE WHEN  LENGTH(RTRIM(A.homtelb)) = 8   THEN CONCAT(substring(A.homtelb,1,4) , '-' , substring(A.homtelb,5,4))  ELSE A.homtelb END )) AS homtel ,
        A.liaison ,
        CONCAT(RTRIM(CASE A.offfaxa  WHEN '' THEN  A.offfaxa  ELSE CONCAT('(' ,RTRIM(A.offfaxa),')')   END ),
        RTRIM(CASE WHEN  LENGTH(RTRIM(A.offfaxb)) = 8   THEN CONCAT(substring(A.offfaxb,1,4) , '-' , substring(A.offfaxb,5,4))  ELSE A.offfaxb END )) AS offfax ,
        CONCAT(RTRIM(CASE A.homfaxa  WHEN '' THEN  A.homfaxa  ELSE CONCAT('(' ,RTRIM(A.homfaxa),')')   END ),
        RTRIM(CASE WHEN  LENGTH(RTRIM(A.homfaxb)) = 8   THEN CONCAT(substring(A.homfaxb,1,4) , '-' , substring(A.homfaxb,5,4))  ELSE A.homfaxb END )) AS homfax ,
        RTRIM(A.mobiltel) AS mobiltel,

        #m01tb.email 講座基本資料檔.電子信箱
        IFNULL((SELECT email FROM m01tb WHERE idno=A.idno ),'') AS email
        FROM t08tb A
        LEFT JOIN t06tb B ON B.course=A.course
        AND B.class=A.class
        AND B.term=A.term
        LEFT JOIN t05tb C ON C.unit=B.unit
        and C.class=B.class
        and C.term=B.term "
        .$wclause." and A.idkind<>'1'

        UNION ALL

        SELECT IFNULL(B.unit,'') AS unit,
        A.course,
        IFNULL(C.name,'') AS unitname,
        IFNULL(B.name,'') AS classname,
        IFNULL(B.hour,0) AS hour,
        A.ename AS name,
        CONCAT(RTRIM( A.dept),RTRIM(A.position)) AS position,
        RTRIM(CASE A.offtela1  WHEN '' THEN   A.offtela1  ELSE CONCAT('(' , RTRIM(A.offtela1) ,')')   END ) +
        CONCAT(RTRIM(CASE WHEN  LENGTH(RTRIM(A.offtelb1)) = 8  THEN CONCAT(substring(A.offtelb1,1,4) , '-' , substring(A.offtelb1,5,4))  ELSE A.offtelb1 END ) ,
        RTRIM(CASE A.offtelc1  WHEN '' THEN   A.offtelc1  ELSE CONCAT('轉',A.offtelc1)  END)) AS offtelno1,
        CONCAT(RTRIM(CASE A.offtela2  WHEN '' THEN   A.offtela2  ELSE CONCAT('(' , RTRIM(A.offtela2) ,')')   END ) ,
        RTRIM(CASE WHEN  LENGTH(RTRIM(A.offtelb2)) = 8  THEN CONCAT(substring(A.offtelb2,1,4) , '-' , substring(A.offtelb2,5,4))  ELSE A.offtelb2 END ) ,
        RTRIM(CASE A.offtelc2  WHEN '' THEN   A.offtelc2  ELSE CONCAT('轉',A.offtelc2)  END)) AS offtelno2,
        CONCAT(RTRIM(CASE A.homtela  WHEN '' THEN  A.homtela  ELSE CONCAT('(',RTRIM(A.homtela),')')   END ),
        RTRIM(CASE WHEN  LENGTH(RTRIM(A.homtelb)) = 8   THEN CONCAT(substring(A.homtelb,1,4) , '-' , substring(A.homtelb,5,4))  ELSE A.homtelb END )) AS homtel ,
        A.liaison AS liaison ,
        CONCAT(RTRIM(CASE A.offfaxa  WHEN '' THEN  A.offfaxa  ELSE CONCAT('(' ,RTRIM(A.offfaxa),')')   END ),
        RTRIM(CASE WHEN  LENGTH(RTRIM(A.offfaxb)) = 8   THEN CONCAT(substring(A.offfaxb,1,4) , '-' , substring(A.offfaxb,5,4))  ELSE A.offfaxb END )) AS offfaxl ,
        CONCAT(RTRIM(CASE A.homfaxa  WHEN '' THEN  A.homfaxa  ELSE CONCAT('(' ,RTRIM(A.homfaxa),')')   END ),
        RTRIM(CASE WHEN  LENGTH(RTRIM(A.homfaxb)) = 8   THEN CONCAT(substring(A.homfaxb,1,4) , '-' , substring(A.homfaxb,5,4))  ELSE A.homfaxb END )) AS homfax ,
        RTRIM(A.mobiltel) AS mobiltel,

        #m01tb.email 講座基本資料檔.電子信箱
        IFNULL((SELECT email FROM m01tb WHERE idno=A.idno limit 1),'') AS email
        FROM t08tb A
        LEFT JOIN t06tb B ON B.course=A.course
        AND B.class=A.class
        AND B.term=A.term
        LEFT JOIN t05tb C ON C.unit=B.unit
        AND C.class=B.class
        and C.term=B.term "
        .$wclause." AND A.idkind='1'
          ) Z
        ORDER BY Z.unit, Z.course";

        $temp=DB::select($sql);

        if($temp==[]){
            $result ="此條件查無資料，請重新查詢";
            $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class ORDER BY t04tb.class DESC");
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT  DISTINCT term FROM t04tb WHERE class ='".$temp[0][$arraykeys[0]]."' ORDER By term");
            $termArr=$temp;
            return view('admin/lecture_list/list',compact('classArr','termArr' ,'result'));
        }

        $data=json_decode(json_encode($temp), true);

        $sql="select name from t01tb where class='".$class."'";
        $temp=DB::select($sql);
        $classname=json_decode(json_encode($temp), true);

        //H1A 聘定 H1B擬聘
        $outputfile="講座名單-";
        if($type=="1")
            $filename="H1B";
        else
        $filename="H1A";

        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $filename).'.docx');

        $templateProcessor->setValue('classterm',$classname[0]["name"]."第".strval((int)$term)."期");
        $templateProcessor->setValue('class',$class);

        $templateProcessor->cloneRow('course', sizeof($data));
        $h=0;

        if($type=="1"){
            $outputfile.="擬聘";
            $tel="";
            $fax="";
            $tmpcourse="";
            $course="";
            $hour="";
            for($i=0;$i<sizeof($data);$i++){
                if($tmpcourse!=$data[$i]["classname"]){
                    $h+=(float)$data[$i]["hour"];
                    $tmpcourse=$data[$i]["classname"];
                    $course=$data[$i]["classname"];
                    $hour=(float)$data[$i]["hour"];
                }else{
                    $course="";
                    $hour="";
                }

                $tel="";
                $fax="";
                if($data[$i]["offtelno1"]!="")
                    $tel.="O：".$data[$i]["offtelno1"]."<w:br />";
                if($data[$i]["offtelno2"]!="")
                    $tel.="O：".$data[$i]["offtelno2"]."<w:br />";
                if($data[$i]["homtel"]!="")
                    $tel.="H：".$data[$i]["homtel"]."<w:br />";
                if($data[$i]["mobiltel"]!="")
                    $tel.="M：".$data[$i]["mobiltel"]."<w:br />";
                if($data[$i]["email"]!="")
                    $fax.="E:".$data[$i]["email"]."<w:br />";

                if($data[$i]["liaison"]!="")
                    $fax.="聯絡人:".$data[$i]["liaison"]."<w:br />";
                if($data[$i]["offfax"]!="")
                    $fax.="F:".$data[$i]["offfax"]."<w:br />";
                if($data[$i]["homfax"]!="")
                    $fax.="F:".$data[$i]["homfax"]."<w:br />";

                if(substr($tel, -8)=="<w:br />")
                    $tel=substr($tel,0,-8);
                if(substr($tel, -8)=="<w:br />")
                    $fax=substr($tel,0,-8);

                $templateProcessor->setValue('course#'.strval($i+1),$course);
                $templateProcessor->setValue('h#'.strval($i+1),$hour);
                $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["name"]);
                $templateProcessor->setValue('pos#'.strval($i+1),$data[$i]["position"]);
                $templateProcessor->setValue('tel#'.strval($i+1),$tel);
                $templateProcessor->setValue('fax#'.strval($i+1),$fax);

            }



        }else{
            $remark="";
            $outputfile.="聘定";

            if($type=="3"){
                $outputfile.="(附電話)";
            }

            for($i=0;$i<sizeof($data);$i++){
                $h+=(float)$data[$i]["hour"];
                if($type=="3"){
                    $remark="";
                    if($data[$i]["offtelno1"]!="")
                        $remark.="O：".$data[$i]["offtelno1"]."<w:br />";
                    if($data[$i]["offtelno2"]!="")
                        $remark.="O：".$data[$i]["offtelno2"]."<w:br />";
                    if($data[$i]["homtel"]!="")
                        $remark.="H：".$data[$i]["homtel"]."<w:br />";
                    if($data[$i]["mobiltel"]!="")
                        $remark.="M：".$data[$i]["mobiltel"]."<w:br />";
                    if($data[$i]["liaison"]!="")
                        $remark.="聯絡人:".$data[$i]["liaison"]."<w:br />";
                    if(substr($remark, -8)=="<w:br />")
                        $remark=substr($remark,0,-8);
                }

                $templateProcessor->setValue('course#'.strval($i+1),$data[$i]["classname"]);
                $templateProcessor->setValue('h#'.strval($i+1),(float)$data[$i]["hour"]);
                $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["name"]);
                $templateProcessor->setValue('pos#'.strval($i+1),$data[$i]["position"]);
                $templateProcessor->setValue('remark#'.strval($i+1),$remark);

            }
        }
        $templateProcessor->setValue('total',$h);
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputfile);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel
        //$doctype:1.ooxml 2.odf
        //$filename:filename


    }

}
