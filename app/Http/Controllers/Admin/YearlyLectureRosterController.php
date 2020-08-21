<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use \PhpOffice\PhpWord\PhpWord;
use \PhpOffice\PhpWord\IOFactory;
use App\Services\User_groupService;

class YearlyLectureRosterController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('yearly_lecture_roster', $user_group_auth)){
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
        return view('admin/yearly_lecture_roster/list',compact('result'));
    }

    public function export(Request $request){
        $sdatetw=$request->input('sdatetw');
        $edatetw=$request->input('edatetw');
        $ttemp="";
        $sdate="";
        $edate="";
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


        if($ttemp=="error" || $sdate=="NaNundefinedundefined" )
         {
            $result = "日期格式錯誤，請重新輸入。";
            return view('admin/yearly_lecture_roster/list',compact('result'));
        }

        $sql="SELECT C.cname, C.dept,  C.position, SUM(lecthr) AS hour,
        CASE
        WHEN RTRIM(homaddress)<>'' THEN homaddress
        ELSE
        (
        CASE WHEN RTRIM(offaddress)<>''
        THEN offaddress ELSE regaddress
        END
        )
        END AS addr,
        CONCAT(RTRIM( CASE  homtela  WHEN '' THEN  homtela Else CONCAT('(' ,RTRIM( homtela),')')   END ),
        RTRIM( CASE  WHEN  LENGTH(RTRIM( homtelb)) = 8 THEN CONCAT(SUBSTRING( homtelb,1,4) , '-' , SUBSTRING(homtelb,5,4))  ELSE homtelb END )) AS tel
        FROM t04tb A
        INNER JOIN t09tb B ON
        A.class=B.class AND A.term=B.term INNER JOIN m01tb C ON   B.idno=C.idno
        WHERE (A.sdate BETWEEN '".$sdate."' AND '".$edate."') OR ( A.edate BETWEEN '".$sdate."' AND '".$edate."')
        GROUP BY  B.idno,  C.cname, C.dept,  C.position, C.offaddress,  C.homaddress,  C.regaddress,  C.homtela,  C.homtelb
        ORDER BY C.dept,C.cname ";

        $temp = json_decode(json_encode(DB::select($sql)), true);
        $data = $temp;
        if($data==[]){
             $result = "查無資料，請重新查詢。";
             return view('admin/yearly_lecture_roster/list',compact('result'));
        }
        $datakeys=array_keys((array)$data[0]);



        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H6').'.docx');

        //fill values
        $templateProcessor->setValue('date',$sdatetmp[0]."/".$sdatetmp[1]."/".$sdatetmp[2]."～".$edatetmp[0]."/".$edatetmp[1]."/".$edatetmp[2]);
        $templateProcessor->cloneRow('name', sizeof($data));

        for($i=0;$i<sizeof($data);$i++){
                $templateProcessor->setValue('name#'.strval($i+1),$data[$i][$datakeys[0]]);
                $templateProcessor->setValue('dept#'.strval($i+1),$data[$i][$datakeys[1]]);
                $templateProcessor->setValue('pos#'.strval($i+1),$data[$i][$datakeys[2]]);
                $templateProcessor->setValue('h#'.strval($i+1),$data[$i][$datakeys[3]]);
                $templateProcessor->setValue('add#'.strval($i+1),$data[$i][$datakeys[4]]);
                $templateProcessor->setValue('tel#'.strval($i+1),$data[$i][$datakeys[5]]);
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"年度講座名冊錄");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 


    }


}
