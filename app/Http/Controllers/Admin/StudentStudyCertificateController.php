<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\T13tb;
use App\Models\T04tb;
use DB;
use App\Services\User_groupService;
use App\Services\SignatureService;


class StudentStudyCertificateController extends Controller
{
    public function __construct(SignatureService $signatureService, User_groupService $user_groupService)
    {
        $this->signatureService = $signatureService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_study_certificate', $user_group_auth)){
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
                //取得UI所需參數
                $RptBasic = new \App\Rptlib\RptBasic();
                $ctemp=$RptBasic->getclass();
                $classArr=$ctemp;
                $ctemp=json_decode(json_encode($ctemp), true);
                $carraykeys=array_keys((array)$ctemp[0]);

                $ttemp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$ctemp[0][$carraykeys[0]]."'");
                $termArr=$ttemp;
                $ttemp=json_decode(json_encode($ttemp), true);
                $tmpterm=isset($ttemp[0]["term"])?:"0";

                $temp=DB::select("SELECT RTRIM(diploma) AS diploma FROM t04tb WHERE class='".$ctemp[0][$carraykeys[0]]."' AND term='".$tmpterm."'");
                $typev="尚無設定證號";
                $type=json_decode(json_encode($temp), true);
                switch (isset($type[0]["diploma"])?:"1") {
                    case "1":
                        $typev="本學院";
                        break;
                    case "2":
                        $typev="人事總處";
                        break;
                    }
                $result = '';
                return view('admin/student_study_certificate/list',compact('classArr','termArr' ,'result','typev'));
    }


    
    //取得電子章單位
    public function getSignature(Request $request)
    {
        $data = $this->signatureService->getSignatures('');
        return $data;
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    public function getserial(Request $request)
    {
        $temp=DB::select("SELECT RTRIM(diploma) AS diploma FROM t04tb WHERE class='".$request->input('classes')."' AND term='".$request->input('term')."'");
        $typev=$temp;
        return $typev;
    }

    public function export(Request $request)
    {

 
        $class=$request->input('classes');
        $term=$request->input('term');
        $type=$request->input('type');
        $filename="J11A";
        $outputname="學員研習證書-";

        //一律以選擇的證號更新置資料庫
        $data['diploma'] = $type;
        T04tb::where('class', $class)->where('term', $term)->update($data);

        if($type=="1")
        {
            $filename="J11A";
            $outputname.="本學院";

        }else{
            $filename="J11B";
            $outputname.="人事總處";
        }

        $unit1= '';
        $signature1= '';
        $unit2= '';
        $signature2 = '';

        if(null !== $request->input('signature_type')){
          
            $signature_type1 = $request->input('signature_type1');
            $signature_type2 = $request->input('signature_type2');
        
            $signature_data1 = $this->signatureService->getSignature($signature_type1);
            $unit1= $signature_data1->name;
            $signature1= $signature_data1->img_path;
        
    
            $signature_data2 = $this->signatureService->getSignature($signature_type2);
            $unit2= $signature_data2->name;
            $signature2 = $signature_data2->img_path;
  
            if($type=="1")
            {
                $filename="J11A_S";
                $outputname.="本學院合作";

            }else{
                $filename="J11B_S";
                $outputname.="人事總處合作";
            }
        }




        $sql="SELECT class, term, idno , no , docno From t13tb
            WHERE t13tb.class = '".$class."' AND (t13tb.term ='".$term."') AND (t13tb.pass = 'Y')
            AND (t13tb.status = '1') AND (docno = '') ORDER BY class, term , no , idno ";
        $temp=DB::select($sql);
        $student=json_decode(json_encode($temp), true);

        $sql="SELECT max(docno) AS docno  From t13tb WHERE substring(class,1,3)='".substr($request->input('classes'),0,3)."'";

        $temp=DB::select($sql);
        $max=json_decode(json_encode($temp), true);
        $startnum=0;
        //設定流水號在1~99999之間
        if($max==[] || ((int)substr($max[0]["docno"],-5)==99999)){
            $startnum=1;
        }else{
            $startnum=((int)substr($max[0]["docno"],-5))+1;
        }

        for($i=0;$i<sizeof($student);$i++){
            //填入編號
            $serial['docno'] = substr($class,0,3).str_pad(strval($startnum), 5, "0", STR_PAD_LEFT);
            T13tb::where('class', $class)->where('term', $term)->where('idno', $student[$i]["idno"])->update($serial);
            //設定流水號在1~99999之間
            if($startnum==99999){
                $startnum=1;
            }else{
                $startnum++;
            }
        }

        $sql="SELECT t13tb.class, t13tb.term, t13tb.idno, m02tb.cname, t04tb.sdate, t04tb.edate,
            t01tb.name , t13tb.docno , t01tb.kind, t01tb.period
            FROM t13tb LEFT OUTER JOIN
            t04tb ON t13tb.term = t04tb.term AND t13tb.class = t04tb.class LEFT OUTER JOIN
            t01tb ON t13tb.class = t01tb.class LEFT OUTER JOIN
            m02tb ON t13tb.idno = m02tb.idno
            WHERE t13tb.class = '".$class."' AND (t13tb.term = '".$term."')  AND (t13tb.pass = 'Y') AND (t13tb.status = '1')
            ORDER BY  t13tb.class, t13tb.term, t13tb.docno , t13tb.idno";

        $temp=DB::select($sql);

        if(empty($temp)){
            $result ="此條件查無資料，請重新查詢";
            $RptBasic = new \App\Rptlib\RptBasic();
            $ctemp=$RptBasic->getclass();
            $classArr=$ctemp;
            $ctemp=json_decode(json_encode($ctemp), true);
            $carraykeys=array_keys((array)$ctemp[0]);

            $ttemp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$ctemp[0][$carraykeys[0]]."'");
            $termArr=$ttemp;
            $ttemp=json_decode(json_encode($ttemp), true);
            $tmpterm=isset($ttemp[0]["term"])?:"0";

            $temp=DB::select("SELECT RTRIM(diploma) AS diploma FROM t04tb WHERE class='".$ctemp[0][$carraykeys[0]]."' AND term='".$tmpterm."'");
            $typev="尚無設定證號";
            $type=json_decode(json_encode($temp), true);
            switch (isset($type[0]["diploma"])?:"1") {
                case "1":
                    $typev="本學院";
                    break;
                case "2":
                    $typev="人事總處";
                    break;
                }

            return view('admin/student_study_certificate/list',compact('classArr','termArr' ,'result','typev'));
        }

        $data=json_decode(json_encode($temp), true);
        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $filename).'.docx');

        $unit="";
        switch (strval($data[0]["kind"]))
        {
            case "1":
                $unit="週";
                break;
            case "2":
                $unit="天";
                break;
            case "3":
                $unit="小時";
                break;
        }

        $templateProcessor->setValue('sy',strval((int)substr($data[0]["sdate"],0,3)));
        $templateProcessor->setValue('sm',strval((int)substr($data[0]["sdate"],3,2)));
        $templateProcessor->setValue('sd',strval((int)substr($data[0]["sdate"],5,2)));
        $templateProcessor->setValue('ey',strval((int)substr($data[0]["edate"],0,3)));
        $templateProcessor->setValue('em',strval((int)substr($data[0]["edate"],3,2)));
        $templateProcessor->setValue('ed',strval((int)substr($data[0]["edate"],5,2)));
        $templateProcessor->setValue('t',(float)$data[0]["period"]);
        $templateProcessor->setValue('u',$unit);
        $templateProcessor->setValue('y',strval((int)substr($class,0,3)));
        $templateProcessor->setValue('classname',$data[0]["name"]);
        $templateProcessor->setValue('term',strval((int)$data[0]["term"]));
        if($unit1 != ''){

            $templateProcessor->setValue('unit1',$unit1);
            $templateProcessor->setValue('unit2',$unit2);
            $templateProcessor->setImageValue('signature1','/var/www/html/csdi/public/Uploads/signatures/'.$signature1);
            $templateProcessor->setImageValue('signature2','/var/www/html/csdi/public/Uploads/signatures/'.$signature2);
          
        }
        ini_set('pcre.backtrack_limit', 999999999);
        $templateProcessor->cloneBlock('b',sizeof($data), true, true);

        for($i=0;$i<sizeof($data);$i++){
            $templateProcessor->setValue('serial#'.strval($i+1),$data[$i]["docno"]);
            $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["cname"]);
            $templateProcessor->setValue('idno#'.strval($i+1),$data[$i]["idno"]);

        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
