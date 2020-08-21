<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// use App\Models\T01tb;
use DB;

class EntrustedSaveAllController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('entrusted_save_all', $user_group_auth)){
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
        $temp=$RptBasic->gettdate();
        $tdateArr=$temp;
        $result="";
        return view('admin/entrusted_save_all/list',compact('result','tdateArr'));
    }
    public function export(Request $request)
    {
        $sql="SELECT date,
            SUM(CAST(SUBSTRING(amt, 1, 8) AS INT)+ CAST(CONCAT('0.',SUBSTRING(amt, 9, 2))AS float)) AS amt, COUNT(amt) AS mycount
            FROM t11tb WHERE transfor = '1' AND date='".$request->input('tdate')."' GROUP BY date";

        $temp = json_decode(json_encode(DB::select($sql)), true);
        if ($temp==[])
        {
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->gettdate();
            $temp=DB::select($sql);
            $tdateArr=$temp;
            $result="查無資料，請重新查詢";
            return view('admin/entrusted_save_all/list',compact('result','tdateArr'));
        }
        $data = $temp;

        $sql="SELECT csdiname, csdiaddress, postboss, postname,offno, post, girono , posttelno , postfaxno FROM  s02tb";
        $temp = json_decode(json_encode(DB::select($sql)), true);
        $basicdata=$temp;

        $accno=substr($basicdata[0]["girono"],0,7)."-".substr($basicdata[0]["girono"],-1);

        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H13').'.docx');

        //fill values
        $templateProcessor->setValue('date',$data[0]["date"]);
        $templateProcessor->setValue('cnt',$data[0]["mycount"]);
        $templateProcessor->setValue('amt',number_format($data[0]["amt"]));

        $templateProcessor->setValue('csdiname',$basicdata[0]["csdiname"]);
        $templateProcessor->setValue('csdiadd',$basicdata[0]["csdiaddress"]);
        $templateProcessor->setValue('postboss',$basicdata[0]["postboss"]);
        $templateProcessor->setValue('postname',$basicdata[0]["postname"]);
        $templateProcessor->setValue('offno',$basicdata[0]["offno"]);
        $templateProcessor->setValue('post',$basicdata[0]["post"]);
        $templateProcessor->setValue('girono',$accno);
        $templateProcessor->setValue('posttelno',$basicdata[0]["posttelno"]);
        $templateProcessor->setValue('postfaxno',$basicdata[0]["postfaxno"]);

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"委託郵局代存總表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }

}
