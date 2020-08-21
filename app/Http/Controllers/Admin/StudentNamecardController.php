<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpWord;
use PhpOffice\PHPWord_IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use App\Services\User_groupService;

class StudentNamecardController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_namecard', $user_group_auth)){
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
                $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class ORDER BY t04tb.class DESC");
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '';
                return view('admin/student_namecard/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    /*
    學員名牌 CSDIR4020
    參考Tables:
    使用範本:J20.xlsx
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
        //學號
        $tname = $request->input('tname');

        //1:中心, 2:人事總處
        $type = $request->input('type');
        //組別列印： 1是, 2:否
        $team = $request->input('team');
        //條碼列印： 1是, 2:否
        $barcode = $request->input('barcode');
        //列印空白表格、記者証、工作人員、來賓: 1是, 否
        $check = $request->input('check');

        //取得 學員名牌
        if($check=='1'){
            $sql=" SELECT t01tb.name AS CLASSNAME,
                            CONCAT('第',t04tb.term,'期') AS TERM,
                            CONCAT(substring(t04tb.sdate,1,3),'.',substring(t04tb.sdate,4,2),'.',substring(t04tb.sdate,6,2),
                                    ' 至 ',
                                    substring(t04tb.edate,1,3),'.',substring(t04tb.edate,4,2),'.',substring(t04tb.edate,6,2))
                            as CLASSDATE
                    from t04tb LEFT OUTER JOIN t01tb ON t04tb.class = t01tb.class
                    where t04tb.class = '".$classes."'
                    and t04tb.term = '".$term."'
                ";
        }else{
            //學號 , 串接條件, 如:001,002,...
            if($tname<>''){
                $tname = " AND t13tb.NO IN ('".str_replace(',',"','",str_replace(' ','',$tname))."')";
                //dd($tname);
            } else {
                $tname = '';
            }
            $sql="SELECT t01tb.name AS CLASSNAME,
                        CONCAT('第',t04tb.term,'期') AS TERM,
                        CONCAT('組別：',t13tb.groupno) as GROUPNO,
                        t13tb.position as POSITION,
                        t13tb.no as NO,
                        IFNULL(m02tb.cname,'') as CNAME,
                        t13tb.dept as DEPT,
                        CONCAT(substring(t04tb.sdate,1,3),'.',substring(t04tb.sdate,4,2),'.',substring(t04tb.sdate,6,2),
                                ' 至 ',
                                substring(t04tb.edate,1,3),'.',substring(t04tb.edate,4,2),'.',substring(t04tb.edate,6,2))
                        as CLASSDATE,
                        CONCAT('Ì',m02tb.idno,'Î') as IDNO
                    FROM t13tb LEFT OUTER JOIN m02tb ON t13tb.idno = m02tb.idno
                            LEFT OUTER JOIN t04tb ON t13tb.class = t04tb.class AND t13tb.term = t04tb.term
                                    LEFT OUTER JOIN t01tb ON t13tb.class = t01tb.class
                    where t13tb.class = '".$classes."'
                    and t13tb.term= '".$term."'
                    ".$tname."
                    and t13tb.status='1'
                    ORDER BY t13tb.NO
                ";
        }

        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        $filename="J4A";

        $sql="select branch from t01tb where class='$classes'";
        $branch = json_decode(json_encode(DB::select($sql)), true);

        $sql="select RTRIM(B.name) AS name from t04tb A INNER JOIN m14tb B ON  A.site=B.site where A.class='$classes' AND A.term='$term'";
        $temp = json_decode(json_encode(DB::select($sql)), true);
        $site=isset($temp[0]["name"])?$temp[0]["name"]:"";

        // 讀檔案
        if($type=='1' && $check != '1'){
            $filename="J4A";
        }elseif($type=='1' && $check == '1'){
            $filename="J4A1";
        }elseif($type=='2' && $check != '1'){
            if($branch[0]["branch"]=="1")
                $filename="J4B";
            else
                $filename="J4BS";
        }elseif($type=='2' && $check == '1'){
            if($branch[0]["branch"]=="1")
            $filename="J4B1";
        else
            $filename="J4B1S";
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $filename).'.docx');
        
        ini_set('pcre.backtrack_limit', 999999999);

        $templateProcessor->setValue('site',$site);
        $templateProcessor->setValue('floor',""); //住宿樓別
        $templateProcessor->setValue('room',""); //寢室編號
        

        if(sizeof($reportlist) != 0) {
            if($check=='1'){  //J4A1 J4B1 J4B1S
                for($j=0; $j<sizeof($dataArr); $j++) {

                    $templateProcessor->setValue('TERM1',  $dataArr[$j]['TERM']);
                    $templateProcessor->setValue('CLASSDATE1',  $dataArr[$j]['CLASSDATE']);
                    $templateProcessor->setValue('TERM2',  $dataArr[$j]['TERM']);
                    $templateProcessor->setValue('CLASSDATE2',  $dataArr[$j]['CLASSDATE']);
                    $templateProcessor->setValue('CLASSNAME1', $dataArr[$j]['CLASSNAME']);
                    $templateProcessor->setValue('CLASSNAME2', $dataArr[$j]['CLASSNAME']);
                }
            } else {
                $templateProcessor->cloneBlock('t',ceil(sizeof($dataArr)/2), true, true);
                
                for($j=0; $j<sizeof($dataArr); $j++) {
                    $templateProcessor->setValue('CLASSNAME1#'.($j+1), $dataArr[$j]['CLASSNAME']);
                    $templateProcessor->setValue('TERM1#'.($j+1),  $dataArr[$j]['TERM']);
                    if($team=='1'){
                        $templateProcessor->setValue('GROUPNO1#'.($j+1), $dataArr[$j]['GROUPNO']);
                    }else{
                        $templateProcessor->setValue('GROUPNO1#'.($j+1), '');
                    }
                    $templateProcessor->setValue('POSITION1#'.($j+1),  $dataArr[$j]['POSITION']);
                    $templateProcessor->setValue('CNAME1#'.($j+1),  $dataArr[$j]['NO'].$dataArr[$j]['CNAME']);
                    $templateProcessor->setValue('DEPT1#'.($j+1),  $dataArr[$j]['DEPT']);
                    $templateProcessor->setValue('CLASSDATE1#'.($j+1),  $dataArr[$j]['CLASSDATE']);
                    if($barcode=='1'){
                        $templateProcessor->setValue('IDNO1#'.($j+1), $dataArr[$j]['IDNO']);
                    }else{
                        $templateProcessor->setValue('IDNO1#'.($j+1), '');
                    }
                }
                //$templateProcessor->cloneRow('CLASSNAME1', ceil(sizeof($dataArr)/2));
                // $A=1;
                // $B=1;
                // for($j=0; $j<sizeof($dataArr); $j++) {
                //     if($j%2==0){
                //         $templateProcessor->setValue('CLASSNAME1#'.($A), $dataArr[$j]['CLASSNAME']);
                //         $templateProcessor->setValue('TERM1#'.($A),  $dataArr[$j]['TERM']);
                //         if($team=='1'){
                //             $templateProcessor->setValue('GROUPNO1#'.($A), $dataArr[$j]['GROUPNO']);
                //         }else{
                //             $templateProcessor->setValue('GROUPNO1#'.($A), '');
                //         }
                //         $templateProcessor->setValue('POSITION1#'.($A),  $dataArr[$j]['POSITION']);
                //         $templateProcessor->setValue('CNAME1#'.($A),  $dataArr[$j]['NO'].$dataArr[$j]['CNAME']);
                //         $templateProcessor->setValue('DEPT1#'.($A),  $dataArr[$j]['DEPT']);
                //         $templateProcessor->setValue('CLASSDATE1#'.($A),  $dataArr[$j]['CLASSDATE']);
                //         if($barcode=='1'){
                //             $templateProcessor->setValue('IDNO1#'.($A), $dataArr[$j]['IDNO']);
                //         }else{
                //             $templateProcessor->setValue('IDNO1#'.($A), '');
                //         }
                //         $A++;
                //     }
                //     if($j%2==1){
                //         $templateProcessor->setValue('CLASSNAME2#'.($B), $dataArr[$j]['CLASSNAME']);
                //         $templateProcessor->setValue('TERM2#'.($B),  $dataArr[$j]['TERM']);
                //         if($team=='1'){
                //             $templateProcessor->setValue('GROUPNO2#'.($B),  $dataArr[$j]['GROUPNO']);
                //         }else{
                //             $templateProcessor->setValue('GROUPNO2#'.($B),  '');
                //         }
                //         $templateProcessor->setValue('POSITION2#'.($B),  $dataArr[$j]['POSITION']);
                //         $templateProcessor->setValue('CNAME2#'.($B),  $dataArr[$j]['NO'].$dataArr[$j]['CNAME']);
                //         $templateProcessor->setValue('DEPT2#'.($B),  $dataArr[$j]['DEPT']);
                //         $templateProcessor->setValue('CLASSDATE2#'.($B),  $dataArr[$j]['CLASSDATE']);
                //         if($barcode=='1'){
                //             $templateProcessor->setValue('IDNO2#'.($B), $dataArr[$j]['IDNO']);
                //         }else{
                //             $templateProcessor->setValue('IDNO2#'.($B), '');
                //         }
                //         $B++;
                //     }
                // }
                // if((sizeof($dataArr)%2)!=0){
                //     $templateProcessor->setValue('CLASSNAME2#'.($B), '');
                //     $templateProcessor->setValue('TERM2#'.($B), '');
                //     $templateProcessor->setValue('GROUPNO2#'.($B), '');
                //     $templateProcessor->setValue('POSITION2#'.($B), '');
                //     $templateProcessor->setValue('CNAME2#'.($B), '');
                //     $templateProcessor->setValue('DEPT2#'.($B), '');
                //     $templateProcessor->setValue('CLASSDATE2#'.($B), '');
                //     $templateProcessor->setValue('IDNO2#'.($B), '');
                // }
            }
        }else{
            if($check=='1'){
                $templateProcessor->setValue('CLASSNAME1', '');
                $templateProcessor->setValue('CLASSNAME2', '');
                $templateProcessor->setValue('TERM1', '');
                $templateProcessor->setValue('TERM2', '');
                $templateProcessor->setValue('CLASSDATE1', '');
                $templateProcessor->setValue('CLASSDATE2', '');
            } else {
                $templateProcessor->setValue('CLASSNAME1', '');
                $templateProcessor->setValue('CLASSNAME2', '');
                $templateProcessor->setValue('TERM1', '');
                $templateProcessor->setValue('TERM2', '');
                $templateProcessor->setValue('GROUPNO1', '');
                $templateProcessor->setValue('GROUPNO2', '');
                $templateProcessor->setValue('POSITION1', '');
                $templateProcessor->setValue('POSITION2', '');
                $templateProcessor->setValue('CNAME1', '');
                $templateProcessor->setValue('CNAME2', '');
                $templateProcessor->setValue('DEPT1', '');
                $templateProcessor->setValue('DEPT2', '');
                $templateProcessor->setValue('CLASSDATE1', '');
                $templateProcessor->setValue('CLASSDATE2', '');
                $templateProcessor->setValue('IDNO1', '');
                $templateProcessor->setValue('IDNO2', '');

                $templateProcessor->setValue('t', '');
                $templateProcessor->setValue('/t', '');
                $templateProcessor->setValue('pb', '');
            }

        }



        if($type=='1' && $check == '1'){
            $outputname="學員名牌-中心-空白表格、記者証、工作人員、來賓";
        } elseif($type=='2' && $check == '1'){
            $outputname="學員名牌-人事總處-空白表格、記者証、工作人員、來賓";
        } elseif($type=='1' && $team == '1' && $barcode =='1'){
            $outputname="學員名牌-中心-組別-條碼";
        } elseif($type=='2' && $team == '1' && $barcode =='1'){
            $outputname="學員名牌-人事總處-組別-條碼";
        } elseif($type=='1' && $team == '1' && $barcode !='1'){
            $outputname="學員名牌-中心-組別";
        } elseif($type=='2' && $team == '1' && $barcode !='1'){
            $outputname="學員名牌-人事總處-組別";
        } elseif($type=='1' && $team != '1' && $barcode =='1'){
            $outputname="學員名牌-中心-條碼";
        } elseif($type=='2' && $team != '1' && $barcode =='1'){
            $outputname="學員名牌-人事總處-條碼";
        } elseif($type=='1'){
            $outputname="學員名牌-中心";
        } elseif($type=='2'){
            $outputname="學員名牌-人事總處";
        } else {
            $outputname="學員名牌-中心";
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
