<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpWord;
use PhpOffice\PHPWord_IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use App\Services\User_groupService;

class StudentSeatNamecardController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_seat_namecard', $user_group_auth)){
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
                $temp=$RptBasic->getclassEx();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '';
                return view('admin/student_seat_namecard/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    /*
    學員座位名牌卡 CSDIR4010
    參考Tables:
    使用範本:J3A...
        //1:單位職稱姓名(大), 2:姓名職稱(大), 3:姓名(大), 4:學號姓名(小), 5:班期姓名單位(大),
        //6:桌牌(學院), 7:桌牌(人事總處), 8:桌牌, 9:桌牌(A4)
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //班別
        $classes = $request->input('classes');
        //期別
        $term = $request->input('term');
        //1:單位職稱姓名(大), 2:姓名職稱(大), 3:姓名(大), 4:學號姓名(小), 5:班期姓名單位(大),
        //6:桌牌(學院), 7:桌牌(人事總處), 8:桌牌, 9:桌牌(A4)
        $outputtype = $request->input('outputtype');

        //取得 學員座位名牌卡
        $sql="SELECT  B.lname,
                        A.position,
                        B.fname,
                        A.dept,
                        B.offaddr1,
                        B.offaddr2,
                        B.offzip,
                        B.homaddr1,
                        B.homaddr2,
                        B.homzip,
                        B.send,
                        A.no,
                        C.name AS CLASSNAME,
                        CONCAT('第',
                                    CASE A.term
                                            WHEN '01' THEN '1'
                                        WHEN '02' THEN '2'
                                            WHEN '03' THEN '3'
                                            WHEN '04' THEN '4'
                                            WHEN '05' THEN '5'
                                            WHEN '06' THEN '6'
                                            WHEN '07' THEN '7'
                                            WHEN '08' THEN '8'
                                            WHEN '09' THEN '9'
                                        ELSE A.term
                                    END,'期') AS TERM
                FROM t13tb A LEFT JOIN m02tb B ON B.idno = A.idno
                             LEFT JOIN t01tb C ON C.class = A.class
                WHERE A.class = '".$classes."'
                AND A.term = '".$term."'
                AND A.status = '1'
                ORDER BY A.no
                ";
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        // 讀檔案
        //1:單位職稱姓名(大), 2:姓名職稱(大), 3:姓名(大), 4:學號姓名(小), 5:班期姓名單位(大),
        //6:桌牌(學院), 7:桌牌(人事總處), 8:桌牌, 9:桌牌(A4)
        if($outputtype=='1'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J3A1').'.docx');
        }elseif($outputtype=='2'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J3A2').'.docx');
        }elseif($outputtype=='3'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J3A3').'.docx');
        }elseif($outputtype=='4'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J3A4').'.docx');
        }elseif($outputtype=='5'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J3A5').'.docx');
        }elseif($outputtype=='6'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J3A6').'.docx');
        }elseif($outputtype=='7'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J3A7').'.docx');
        }elseif($outputtype=='8'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J3A8').'.docx');
        }elseif($outputtype=='9'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J3A9').'.docx');
        }

        if(sizeof($reportlist) != 0) {
            //$templateProcessor->cloneBlock('t',ceil(sizeof($dataArr)/2), true, true);
            if($outputtype=='4'){
                $templateProcessor->cloneRow('CNAME1', ceil(sizeof($dataArr)/3));
                $A=1;
                $B=1;
                $C=1;
                for($j=0; $j<sizeof($dataArr); $j++) {
                    if($j%3==0){
                        $templateProcessor->setValue('CNAME1#'.($A),  $dataArr[$j]['no'].' '.$dataArr[$j]['lname'].$dataArr[$j]['fname']);
                        $A++;
                    }
                    if($j%3==1){
                        $templateProcessor->setValue('CNAME2#'.($B),  $dataArr[$j]['no'].' '.$dataArr[$j]['lname'].$dataArr[$j]['fname']);
                        $B++;
                    }
                    if($j%3==2){
                        $templateProcessor->setValue('CNAME3#'.($C),  $dataArr[$j]['no'].' '.$dataArr[$j]['lname'].$dataArr[$j]['fname']);
                        $C++;
                    }
                }
                if((sizeof($dataArr)%3)==1){
                    $templateProcessor->setValue('CNAME2#'.($B), '');
                    $templateProcessor->setValue('CNAME3#'.($C), '');
                }elseif((sizeof($dataArr)%3)==2){
                    $templateProcessor->setValue('CNAME3#'.($C), '');
                }
            }elseif($outputtype>='6'){
                $templateProcessor->cloneRow('CNAME', sizeof($dataArr));
                for($j=0; $j<sizeof($dataArr); $j++) {
                    $templateProcessor->setValue('CNAME#'.($j+1),  $dataArr[$j]['no'].' '.$dataArr[$j]['lname'].$dataArr[$j]['fname']);
                    $templateProcessor->setValue('DEPT#'.($j+1),  $dataArr[$j]['dept']);
                    $templateProcessor->setValue('POS#'.($j+1),  $dataArr[$j]['position']);
                }
            }else{
                $templateProcessor->cloneRow('CNAME1', ceil(sizeof($dataArr)/2));
                $A=1;
                $B=1;
                for($j=0; $j<sizeof($dataArr); $j++) {
                    if($j%2==0){
                        if($outputtype=='1'){
                            $templateProcessor->setValue('DEPT1#'.($A),  $dataArr[$j]['dept']);
                            $templateProcessor->setValue('POS1#'.($A),  $dataArr[$j]['position']);
                            $templateProcessor->setValue('CNAME1#'.($A),  $dataArr[$j]['lname'].$dataArr[$j]['fname']);
                        }elseif($outputtype=='2'){
                            $templateProcessor->setValue('CNAME1#'.($A),  $dataArr[$j]['lname'].$dataArr[$j]['position'].$dataArr[$j]['fname']);
                        }elseif($outputtype=='3'){
                            $templateProcessor->setValue('CNAME1#'.($A),  $dataArr[$j]['lname'].$dataArr[$j]['fname']);
                        }elseif($outputtype=='5'){
                            $templateProcessor->setValue('CLASSNAME1#'.($A),  $dataArr[$j]['CLASSNAME'].$dataArr[$j]['TERM']);
                            $templateProcessor->setValue('DEPT1#'.($A),  $dataArr[$j]['dept']);
                            $templateProcessor->setValue('CNAME1#'.($A),  $dataArr[$j]['lname'].$dataArr[$j]['fname']);
                        }
                        $A++;
                    }
                    if($j%2==1){
                        if($outputtype=='1'){
                            $templateProcessor->setValue('DEPT2#'.($B),  $dataArr[$j]['dept']);
                            $templateProcessor->setValue('POS2#'.($B),  $dataArr[$j]['position']);
                            $templateProcessor->setValue('CNAME2#'.($B),  $dataArr[$j]['lname'].$dataArr[$j]['fname']);
                        }elseif($outputtype=='2'){
                            $templateProcessor->setValue('CNAME2#'.($B),  $dataArr[$j]['lname'].$dataArr[$j]['position'].$dataArr[$j]['fname']);
                        }elseif($outputtype=='3'){
                            $templateProcessor->setValue('CNAME2#'.($B),  $dataArr[$j]['lname'].$dataArr[$j]['fname']);
                        }elseif($outputtype=='5'){
                            $templateProcessor->setValue('CLASSNAME2#'.($B),  $dataArr[$j]['CLASSNAME'].$dataArr[$j]['TERM']);
                            $templateProcessor->setValue('DEPT2#'.($B),  $dataArr[$j]['dept']);
                            $templateProcessor->setValue('CNAME2#'.($B),  $dataArr[$j]['lname'].$dataArr[$j]['fname']);
                        }
                        $B++;
                    }
                }
                if((sizeof($dataArr)%2)!=0){
                    if($outputtype=='1'){
                        $templateProcessor->setValue('DEPT2#'.($B), '');
                        $templateProcessor->setValue('POS2#'.($B), '');
                        $templateProcessor->setValue('CNAME2#'.($B), '');
                    }elseif($outputtype=='2'){
                        $templateProcessor->setValue('CNAME2#'.($B), '');
                    }elseif($outputtype=='3'){
                        $templateProcessor->setValue('CNAME2#'.($B), '');
                    }elseif($outputtype=='5'){
                        $templateProcessor->setValue('CLASSNAME2#'.($B), '');
                        $templateProcessor->setValue('DEPT2#'.($B), '');
                        $templateProcessor->setValue('CNAME2#'.($B), '');
                    }
                }
            }
        }else{
            if($outputtype=='1'){
                $templateProcessor->setValue('DEPT1', '');
                $templateProcessor->setValue('POS1', '');
                $templateProcessor->setValue('CNAME1', '');
                $templateProcessor->setValue('DEPT2', '');
                $templateProcessor->setValue('POS2', '');
                $templateProcessor->setValue('CNAME2', '');
            }elseif($outputtype=='2'){
                $templateProcessor->setValue('CNAME1', '');
                $templateProcessor->setValue('CNAME2', '');
            }elseif($outputtype=='3'){
                $templateProcessor->setValue('CNAME1', '');
                $templateProcessor->setValue('CNAME2', '');
            }elseif($outputtype=='4'){
                $templateProcessor->setValue('CNAME1', '');
                $templateProcessor->setValue('CNAME2', '');
                $templateProcessor->setValue('CNAME3', '');
            }elseif($outputtype=='5'){
                $templateProcessor->setValue('CLASSNAME1', '');
                $templateProcessor->setValue('CNAME1', '');
                $templateProcessor->setValue('DEPT1', '');
                $templateProcessor->setValue('CLASSNAME2', '');
                $templateProcessor->setValue('CNAME2', '');
                $templateProcessor->setValue('DEPT2', '');
            }else{
                $templateProcessor->setValue('DEPT', '');
                $templateProcessor->setValue('POS', '');
                $templateProcessor->setValue('CNAME', '');
            }

        }

        header('Content-Type: application/vnd.ms-word');
        //1:單位職稱姓名(大), 2:姓名職稱(大), 3:姓名(大), 4:學號姓名(小), 5:班期姓名單位(大),
        //6:桌牌(學院), 7:桌牌(人事總處), 8:桌牌, 9:桌牌(A4)
        if($outputtype=='1'){
            header("Content-Disposition: attachment;filename=學員座位名牌卡-單位職稱姓名(大).docx");
        }elseif($outputtype=='2'){
            header("Content-Disposition: attachment;filename=學員座位名牌卡-姓名職稱(大).docx");
        }elseif($outputtype=='3'){
            header("Content-Disposition: attachment;filename=學員座位名牌卡-姓名(大).docx");
        }elseif($outputtype=='4'){
            header("Content-Disposition: attachment;filename=學員座位名牌卡-學號姓名(小).docx");
        }elseif($outputtype=='5'){
            header("Content-Disposition: attachment;filename=學員座位名牌卡-班期姓名單位(大).docx");
        }elseif($outputtype=='6'){
            header("Content-Disposition: attachment;filename=學員座位名牌卡-桌牌(學院).docx");
        }elseif($outputtype=='7'){
            header("Content-Disposition: attachment;filename=學員座位名牌卡-桌牌(人事總處).docx");
        }elseif($outputtype=='8'){
            header("Content-Disposition: attachment;filename=學員座位名牌卡-桌牌.docx");
        }elseif($outputtype=='9'){
            header("Content-Disposition: attachment;filename=學員座位名牌卡-桌牌(A4).docx");
        } else {
            header("Content-Disposition: attachment;filename=單位職稱姓名(大).docx");
        }

        header('Cache-Control: max-age=0');

        ob_clean();
        $templateProcessor->saveAs('php://output');
        exit;

    }

}
