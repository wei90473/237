<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// use App\Models\T01tb;
use DB;

class InterbankRemittanceFormController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('interbank_remittance_form', $user_group_auth)){
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
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->gettdatebank();
        $tdateArr=$temp;
        $result="";
        return view('admin/interbank_remittance_form/list',compact('result','tdateArr'));
    }

    public function export(Request $request){
        $RptBasic = new \App\Rptlib\RptBasic();
        $sql=" SELECT  RTRIM(accname) AS accname,amt,RTRIM(bank) AS bank,
        SUBSTRING(bankno,1,1) AS A,
        SUBSTRING(bankno,2,1) AS B,
        SUBSTRING(bankno,3,1) AS C,
        SUBSTRING(bankno,4,1) AS D,
        SUBSTRING(bankno,5,1) AS E,
        SUBSTRING(bankno,6,1) AS F,
        SUBSTRING(bankno,7,1) AS G,
        SUBSTRING(bankno,8,1) AS H,
        SUBSTRING(bankno,9,1) AS I,
        SUBSTRING(bankno,10,1) AS J,
        SUBSTRING(bankno,11,1) AS K,
        SUBSTRING(bankno,12,1) AS L,
        SUBSTRING(bankno,13,1) AS M,
        SUBSTRING(bankno, 14, 1) As N,
        SUBSTRING(date, 1, 3) As year,
        SUBSTRING(date, 4, 2) As month,
        SUBSTRING(date, 6, 2) As day,
        SUBSTRING(bankcode,1,1) AS b1,
        SUBSTRING(bankcode,2,1) AS b2,
        SUBSTRING(bankcode,3,1) AS b3,
        SUBSTRING(bankcode,4,1) AS b4,
        SUBSTRING(bankcode,5,1) AS b5,
        SUBSTRING(bankcode,6,1) AS b6,
        SUBSTRING(bankcode,7,1) AS b7
        FROM t11tb
        WHERE  date='".$request->input('tdate')."' AND  transfor='2'  ORDER BY serno";

        $temp = json_decode(json_encode(DB::select($sql)), true);
        if ($temp==[])
        {
            $temp=$RptBasic->gettdatebank();
            $temp=DB::select($sql);
            $tdateArr=$temp;
            $result="查無資料，請重新查詢";
            return view('admin/interbank_remittance_form/list',compact('result','tdateArr'));
        }
        $data = $temp;
        $ymd=explode("/",$request->input('tdate'));


        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H15').'.docx');
        ini_set('pcre.backtrack_limit', 999999999);
        $templateProcessor->cloneBlock('t',sizeof($data), true, true);

        //fill values
        for($i=0;$i<(sizeof($data));$i++){
            $templateProcessor->setValue('y#'.strval($i+1),$data[$i]["year"]);
            $templateProcessor->setValue('m#'.strval($i+1),$data[$i]["month"]);
            $templateProcessor->setValue('d#'.strval($i+1),$data[$i]["day"]);
            $templateProcessor->setValue('bank#'.strval($i+1),$data[$i]["bank"]);
            $templateProcessor->setValue('accname#'.strval($i+1),$data[$i]["accname"]);
            $templateProcessor->setValue('amt#'.strval($i+1),$RptBasic->toCHTnum(((int)$data[$i]["amt"])/100));
            $templateProcessor->setValue('A#'.strval($i+1),$data[$i]["A"]);
            $templateProcessor->setValue('B#'.strval($i+1),$data[$i]["B"]);
            $templateProcessor->setValue('C#'.strval($i+1),$data[$i]["C"]);
            $templateProcessor->setValue('D#'.strval($i+1),$data[$i]["D"]);
            $templateProcessor->setValue('E#'.strval($i+1),$data[$i]["E"]);
            $templateProcessor->setValue('F#'.strval($i+1),$data[$i]["F"]);
            $templateProcessor->setValue('G#'.strval($i+1),$data[$i]["G"]);
            $templateProcessor->setValue('H#'.strval($i+1),$data[$i]["H"]);
            $templateProcessor->setValue('I#'.strval($i+1),$data[$i]["I"]);
            $templateProcessor->setValue('J#'.strval($i+1),$data[$i]["J"]);
            $templateProcessor->setValue('K#'.strval($i+1),$data[$i]["K"]);
            $templateProcessor->setValue('L#'.strval($i+1),$data[$i]["L"]);
            $templateProcessor->setValue('M#'.strval($i+1),$data[$i]["M"]);
            $templateProcessor->setValue('N#'.strval($i+1),$data[$i]["N"]);
            $templateProcessor->setValue('b1#'.strval($i+1),$data[$i]["b1"]);
            $templateProcessor->setValue('b2#'.strval($i+1),$data[$i]["b2"]);
            $templateProcessor->setValue('b3#'.strval($i+1),$data[$i]["b3"]);
            $templateProcessor->setValue('b4#'.strval($i+1),$data[$i]["b4"]);
            $templateProcessor->setValue('b5#'.strval($i+1),$data[$i]["b5"]);
            $templateProcessor->setValue('b6#'.strval($i+1),$data[$i]["b6"]);
            $templateProcessor->setValue('b7#'.strval($i+1),$data[$i]["b7"]);
            if($i<(sizeof($data)-1))
                $templateProcessor->setValue('pagebreak#'.strval($i+1), '</w:t></w:r>'.'<w:r><w:br w:type="page"/></w:r>'.'<w:r><w:t>');
            else
                $templateProcessor->setValue('pagebreak#'.strval($i+1), '');
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"郵政跨行匯款申請書");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel
        //$doctype:1.ooxml 2.odf
        //$filename:filename

    }

}
