<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// use App\Models\T01tb;
use DB;

class InterbankRemittanceDetailController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('interbank_remittance_detail', $user_group_auth)){
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
        return view('admin/interbank_remittance_detail/list',compact('result','tdateArr'));
    }

    public function export(Request $request){
        $RptBasic = new \App\Rptlib\RptBasic();

        $showdate="";

        if(strlen($request->input('tdate'))==7)
            $showdate=substr($request->input('tdate'),0,3)."/".substr($request->input('tdate'),3,2)."/".substr($request->input('tdate'),5,2);

        $sql="SELECT
        RIGHT(CONCAT('00',CAST(ROW_NUMBER() OVER(ORDER BY serno) AS CHAR)),3) AS 編號,
        RTRIM(bank) AS 解款行,
        RTRIM(bankcode) AS  代號,
        RTRIM(bankno) AS 轉存帳號,
        RTRIM(accname) AS 戶名,
        CAST(CAST(amt AS int)/100 AS int ) AS 轉存金額,
        serno
        FROM t11tb
        WHERE transfor = '2'
        AND date='".$request->input('tdate')."'
       UNION
       SELECT
        '' AS col_01,
        '合計' AS col_02,
        '' AS col_03,
        '' AS col_04,
        '' AS col_05,
        CONCAT('$',format(SUM(轉存金額),0))  AS col_06,
        '99999999' as serno
        FROM
        (
         SELECT
        RIGHT(CONCAT('00',CAST(ROW_NUMBER() OVER(ORDER BY serno) AS CHAR)),3) AS 編號,
        RTRIM(bank) AS 解款行,
        RTRIM(bankcode) AS  代號,
        RTRIM(bankno) AS 轉存帳號,
        RTRIM(accname) AS 戶名,
        CAST(CAST(amt AS int)/100 AS int ) AS 轉存金額
        FROM t11tb
        WHERE transfor = '2'
        AND date='".$request->input('tdate')."'
        ) AS AA ORDER BY serno";

        $temp = json_decode(json_encode(DB::select($sql)), true);
        if ($temp==[])
        {
            $temp=$RptBasic->gettdatebank();
            $temp=DB::select($sql);
            $tdateArr=$temp;
            $result="查無資料，請重新查詢";
            return view('admin/interbank_remittance_detail/list',compact('result','tdateArr'));
        }
        $data = $temp;
        $ymd=explode("/",$request->input('tdate'));

        $sql=" SELECT DISTINCT branch FROM t11tb WHERE transfor = '2' AND date='".$request->input('tdate')."' ";
        $temp = json_decode(json_encode(DB::select($sql)), true);
        $branchdata=$temp;
        $branch="";
        if($branchdata!=[]){
            if($branchdata[0]["branch"]=="1")
                $branch="(臺北院區)";
            elseif($branchdata[0]["branch"]=="2")
            $branch="(南投院區)";
        }

        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H16').'.docx');

        $templateProcessor->cloneRow('s',sizeof($data)-1);

        //fill values
        $templateProcessor->setValue('branch',$branch);
        $templateProcessor->setValue('date',$showdate);
        $templateProcessor->setValue('total',$data[sizeof($data)-1]["轉存金額"]);

        for($i=0;$i<(sizeof($data)-1);$i++){
            $templateProcessor->setValue('s#'.strval($i+1),$data[$i]["編號"]);
            $templateProcessor->setValue('bank#'.strval($i+1),$data[$i]["解款行"]);
            $templateProcessor->setValue('code#'.strval($i+1),$data[$i]["代號"]);
            $templateProcessor->setValue('account#'.strval($i+1),$data[$i]["轉存帳號"]);
            $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["戶名"]);
            $templateProcessor->setValue('amt#'.strval($i+1),$data[$i]["轉存金額"]);
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"跨行匯款明細表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
