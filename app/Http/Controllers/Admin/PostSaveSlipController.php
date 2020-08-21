<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpWord;
use PhpOffice\PHPWord_IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use App\Services\User_groupService;

class PostSaveSlipController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('post_save_slip', $user_group_auth)){
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
        return view('admin/post_save_slip/list',compact('result','tdateArr'));
    }

    public function gettdate(){

        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->gettdate();
        $tdateArr=$temp;
        return $tdateArr;
    }

    public function export(Request $request){
        $tdate = $request->input('tdate');

        //sql for title
        $sqlTITLE="SELECT B.post,
                            CONCAT(SUBSTRING(B.offno,1,6),'-', SUBSTRING(B.offno,7,1)) AS offno,
                            CONCAT(SUBSTRING(B.girono,1,7),'-', SUBSTRING(B.girono,8,1)) AS girono,
                            CONCAT(SUBSTRING(A.date,1,3),'/',SUBSTRING(A.date,4,2),'/', SUBSTRING(A.date,6,2)) AS BDATE,
                            COUNT(A.amt) BCOUNT,
                            FORMAT(SUM(CAST(SUBSTRING(A.amt, 1, 8) AS DECIMAL(8,2))+
                                    CAST(CONCAT('0.',SUBSTRING(A.amt, 9, 2)) AS DECIMAL(8,2))),0) BSUM,
                            B.csdiname,
                            B.posttelno,
                            B.csdiaddress,
                            B.postboss,
                            B.postname,
                            B.postfaxno
                    FROM t11tb A INNER JOIN s02tb B ON 1 = 1
                    WHERE A.transfor = '1'
                    AND A.date='".$tdate."'
                    GROUP BY A.date
                ";
        $reportlistTITLE = DB::select($sqlTITLE);
        $dataArrTITLE = json_decode(json_encode($reportlistTITLE), true);

        $sql="SELECT serno,
                    postcode,
                    CONCAT(SUBSTRING(postno,1,6),'-', SUBSTRING(postno,7,1)) AS postno,
                    accname,
                    idno,
                    CAST(SUBSTRING(amt, 1, 8) AS DECIMAL(8,2))
                        + CAST(CONCAT('0.',SUBSTRING(amt, 9, 2)) AS DECIMAL(8,2)) AS AMT,
                    FORMAT(CAST(SUBSTRING(amt, 1, 8) AS DECIMAL(8,2))
                        + CAST(CONCAT('0.',SUBSTRING(amt, 9, 2)) AS DECIMAL(8,2)),0) AS SAMT
                from t11tb
                WHERE transfor = '1' AND t11tb.date='".$tdate."'
                ORDER BY serno
                	";
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H14').'.docx');

        if(sizeof($reportlistTITLE) != 0) {
            $templateProcessor->setValue('POST',  $dataArrTITLE[0]['post']);
            $templateProcessor->setValue('OFFNO',  $dataArrTITLE[0]['offno']);
            $templateProcessor->setValue('GIRONO',  $dataArrTITLE[0]['girono']);
            $templateProcessor->setValue('BDATE',  $dataArrTITLE[0]['BDATE']);
            $templateProcessor->setValue('BCOUNT',  $dataArrTITLE[0]['BCOUNT']);
            $templateProcessor->setValue('BSUM',  $dataArrTITLE[0]['BSUM']);
            $templateProcessor->setValue('CSDINAME',  $dataArrTITLE[0]['csdiname']);
            $templateProcessor->setValue('POSTTELNO',  $dataArrTITLE[0]['posttelno']);
            $templateProcessor->setValue('CSDIADDRESS',  $dataArrTITLE[0]['csdiaddress']);
            $templateProcessor->setValue('POSTBOSS',  $dataArrTITLE[0]['postboss']);
            $templateProcessor->setValue('POSTNAME',  $dataArrTITLE[0]['postname']);
        }

        if(sizeof($reportlist) != 0) {
            $add=1;
            if(sizeof($dataArr)%20==0)
                $add=0;

            $templateProcessor->cloneRow('SERNO', sizeof($dataArr)+floor(sizeof($dataArr)/20)+$add);
            $v_total = 0;
            $v_count = 0;
            $p = 1;
            for($j=0; $j<sizeof($dataArr); $j++) {
                $templateProcessor->setValue('SERNO#'.($p),  $dataArr[$j]['serno']);
                $templateProcessor->setValue('POSTCODE#'.($p),  $dataArr[$j]['postcode']);
                $templateProcessor->setValue('POSTNO#'.($p),  $dataArr[$j]['postno']);
                $templateProcessor->setValue('ACCNAME#'.($p),  $dataArr[$j]['accname']);
                $templateProcessor->setValue('IDNO#'.($p),  $dataArr[$j]['idno']);
                $templateProcessor->setValue('SAMT#'.($p),  $dataArr[$j]['SAMT']);
                $p++;
                //頁面小計 筆數金額, 每20筆換頁,插入一筆小計
                $v_total = $v_total + $dataArr[$j]['AMT'];
                $v_count++;
                if(($j+1)%20==0 || ($j+1)==sizeof($dataArr)){
                    $templateProcessor->setValue('SERNO#'.($p) , "本頁筆數小計：");
                    $templateProcessor->setValue('POSTCODE#'.($p),  $v_count);
                    $templateProcessor->setValue('POSTNO#'.($p),  "");
                    $templateProcessor->setValue('ACCNAME#'.($p),  "");
                    $templateProcessor->setValue('IDNO#'.($p),  "本頁轉存金額累計：");
                    $templateProcessor->setValue('SAMT#'.($p) , number_format($v_total,0,'.',','));
                    $v_total = 0;
                    $v_count = 0;
                    $p++;
                }
            }
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"郵政存款單");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
