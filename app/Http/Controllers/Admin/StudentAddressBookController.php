<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use \PhpOffice\PhpWord\PhpWord;
use \PhpOffice\PhpWord\IOFactory;
use App\Services\User_groupService;

class StudentAddressBookController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_address_book', $user_group_auth)){
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
        $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
        FROM t04tb INNER JOIN
        t01tb ON t04tb.class = t01tb.class
        ORDER BY t04tb.class DESC");
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;
        $result = '';
        return view('admin/student_address_book/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    /*
    學員通訊錄 CSDIR4080
    參考Tables:
    使用範本:J9A.docs, J9B.docs (A:簡式, B:詳細)
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
        //1:簡式, 2:詳細
        $type = $request->input('type');

        //取得TITLE
        $sqlTITLE = "SELECT DISTINCT CONCAT(t01tb.name,'第',t04tb.term,'期 通訊錄'
                                        ) AS CLASSNAME,
                                CONCAT(substring(t04tb.sdate,1,3),'.',substring(t04tb.sdate,4,2),'.',substring(t04tb.sdate,6,2),
                                        ' ─ ',
                                        substring(t04tb.edate,1,3),'.',substring(t04tb.edate,4,2),'.',substring(t04tb.edate,6,2)
                                        ) AS CLASSDATE,
                                        t04tb.class AS CLASS
                                FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                                WHERE t04tb.class = '".$classes."'
                                AND t04tb.term = '".$term."'
                        ";
        $reportlistTitle = DB::select($sqlTITLE);
        $dataArrTitle = json_decode(json_encode($reportlistTitle), true);


        if ($type == '1') {
          $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J9A').'.docx');
          //TITLE
          $templateProcessor->setValue('CLASSNAME', $dataArrTitle[0]['CLASSNAME']);
          $templateProcessor->setValue('CLASSDATE', $dataArrTitle[0]['CLASSDATE']);
          $templateProcessor->setValue('CLASS', $dataArrTitle[0]['CLASS']);
        } else {
          $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J9B').'.docx');
        }

        //取得 學員通訊錄
        if ($type == '1') {
            $sql = "SELECT t13tb.no as NO,
                                t13tb.dept as DEPT,
                                t13tb.position as POSITION,
                                m02tb.cname as CNAME,
                                (CASE m02tb.sex WHEN 'F' THEN '女'
                                                WHEN 'M' THEN '男'
                                                ELSE '' END) AS SEX,
                                m02tb.offtela1 as OFFTELA1,
                                m02tb.offtelb1 as OFFTELB1,
                                m02tb.offtelc1 as OFFTELC1,
                                m02tb.homtela as HOMTELA,
                                m02tb.homtelb as HOMTELB,
                                m02tb.email as EMAIL,
                                m02tb.mobiltel as MOBILTEL
                    FROM t13tb INNER JOIN m02tb ON t13tb.idno = m02tb.idno
                    AND t13tb.class = '".$classes."'
                    AND t13tb.term= '".$term."'
                    AND t13tb.status='1'
                    ORDER BY t13tb.no
                    ";
        } else {
            $sql = " SELECT m02tb.cname as CNAME,
                                (CASE t13tb.type  WHEN '1' THEN '' WHEN '2' THEN '學員長' WHEN '3' THEN '' ELSE '' END) as TYPE,
                                        t13tb.no as NO,
                                        CONCAT(rtrim(t13tb.dept),t13tb.position) as NEWJOB,
                                        t13tb.education as EDUCATION,
                                        CONCAT(rtrim(m02tb.homaddr1),m02tb.homaddr2) as HOMADDR,
                                        m02tb.offtela1 as OFFTELA1,
                                        m02tb.offtelb1 as OFFTELB1,
                                        m02tb.offtelc1 as OFFTELC1,
                                        m02tb.homtela as HOMTELA,
                                        m02tb.homtelb as HOMTELB,
                                        m02tb.mobiltel as MOBILTEL,
                                        m02tb.email as EMAIL
                        FROM t13tb INNER JOIN m02tb ON t13tb.idno = m02tb.idno
                        AND t13tb.class = '".$classes."'
                        AND t13tb.term= '".$term."'
                        AND t13tb.status='1'
                        ORDER BY t13tb.no
                        ";
        }
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        if ($type == '1') {
            // 要放的資料筆數，先建 列
            $templateProcessor->cloneRow('CNAME', sizeof($dataArr));
            // 每列要放的資料(#1：第一列、以此類推)
            $OFFTEL = '';
            $HOMTEL = '';
            for($i=0; $i<sizeof($dataArr); $i++) {
                $templateProcessor->setValue('NO#'.($i+1), $dataArr[$i]['NO']);
                $templateProcessor->setValue('DEPT#'.($i+1), $dataArr[$i]['DEPT']);
                $templateProcessor->setValue('POSITION#'.($i+1), $dataArr[$i]['POSITION']);
                $templateProcessor->setValue('CNAME#'.($i+1), $dataArr[$i]['CNAME']);
                $templateProcessor->setValue('SEX#'.($i+1), $dataArr[$i]['SEX']);
                //(02)2XXX-XXXX轉XXX
                if($dataArr[$i]['OFFTELA1']<>''){
                    $OFFTEL = '('.$dataArr[$i]['OFFTELA1'].')';
                }
                if($dataArr[$i]['OFFTELB1']<>''){
                    $OFFTEL = $OFFTEL.$dataArr[$i]['OFFTELB1'];
                }
                if($dataArr[$i]['OFFTELC1']<>''){
                    $OFFTEL = $OFFTEL.'轉'.$dataArr[$i]['OFFTELC1'];
                }
                $templateProcessor->setValue('OFFTEL#'.($i+1), $OFFTEL);

                //(02)2XXX-XXXX
                if($dataArr[$i]['HOMTELA']<>''){
                    $HOMTEL = '('.$dataArr[$i]['HOMTELA'].')';
                }
                if($dataArr[$i]['HOMTELB']<>''){
                    $HOMTEL = $HOMTEL.$dataArr[$i]['HOMTELB'];
                }
                $templateProcessor->setValue('HOMTEL#'.($i+1), $HOMTEL);

                $templateProcessor->setValue('EMAIL#'.($i+1), $dataArr[$i]['EMAIL']);
                $templateProcessor->setValue('MOBILTEL#'.($i+1), $dataArr[$i]['MOBILTEL']);
            }
        }else{
            // 每列要放的資料(#1：第一列、以此類推)
            $OFFTEL = '';
            $HOMTEL = '';
            $A=1;
            $B=1;
            $C=1;
            $D=1;
            // 要放的資料筆數，先建 列
            $templateProcessor->cloneRow('CNAME1', ceil(sizeof($dataArr)/2));
            for($i=0; $i<sizeof($dataArr); $i++) {
                if($i%2==0){
                    $templateProcessor->setValue('CNAME1#'.($A), $dataArr[$i]['CNAME']);
                    $templateProcessor->setValue('NO1#'.($A), $dataArr[$i]['NO']);
                    $templateProcessor->setValue('NEWJOB1#'.($A), $dataArr[$i]['NEWJOB']);
                    $templateProcessor->setValue('EDUCATION1#'.($A), $dataArr[$i]['EDUCATION']);
                    $templateProcessor->setValue('HOMADDR1#'.($A), $dataArr[$i]['HOMADDR']);
                    //(02)2XXX-XXXX轉XXX
                    $OFFTEL = '';
                    if($dataArr[$i]['OFFTELA1']<>''){
                        $OFFTEL = '('.$dataArr[$i]['OFFTELA1'].')';
                    }
                    if($dataArr[$i]['OFFTELB1']<>''){
                        $OFFTEL = $OFFTEL.$dataArr[$i]['OFFTELB1'];
                    }
                    if($dataArr[$i]['OFFTELC1']<>''){
                        $OFFTEL = $OFFTEL.'轉'.$dataArr[$i]['OFFTELC1'];
                    }
                    $templateProcessor->setValue('OFFTEL1#'.($A), $OFFTEL);

                    //(02)2XXX-XXXX
                    $HOMTEL = '';
                    if($dataArr[$i]['HOMTELA']<>''){
                        $HOMTEL = '('.$dataArr[$i]['HOMTELA'].')';
                    }
                    if($dataArr[$i]['HOMTELB']<>''){
                        $HOMTEL = $HOMTEL.$dataArr[$i]['HOMTELB'];
                    }
                    $templateProcessor->setValue('HOMTEL1#'.($A), $HOMTEL);

                    $templateProcessor->setValue('EMAIL1#'.($A), $dataArr[$i]['EMAIL']);
                    $templateProcessor->setValue('MOBILTEL1#'.($A), $dataArr[$i]['MOBILTEL']);
                    $A++;
                }
                if($i%2==1){
                    //if(($i+1)< (sizeof($dataArr))){
                    if($dataArr[$i]['CNAME'] <> ''){
                        $templateProcessor->setValue('CNAME2#'.($B), $dataArr[$i]['CNAME']);
                        $templateProcessor->setValue('NO2#'.($B), $dataArr[$i]['NO']);
                        $templateProcessor->setValue('NEWJOB2#'.($B), $dataArr[$i]['NEWJOB']);
                        $templateProcessor->setValue('EDUCATION2#'.($B), $dataArr[$i]['EDUCATION']);
                        $templateProcessor->setValue('HOMADDR2#'.($B), $dataArr[$i]['HOMADDR']);
                        //(02)2XXX-XXXX轉XXX
                        $OFFTEL = '';
                        if($dataArr[$i]['OFFTELA1']<>''){
                            $OFFTEL = '('.$dataArr[$i]['OFFTELA1'].')';
                        }
                        if($dataArr[$i]['OFFTELB1']<>''){
                            $OFFTEL = $OFFTEL.$dataArr[$i]['OFFTELB1'];
                        }
                        if($dataArr[$i]['OFFTELC1']<>''){
                            $OFFTEL = $OFFTEL.'轉'.$dataArr[$i]['OFFTELC1'];
                        }
                        $templateProcessor->setValue('OFFTEL2#'.($B), $OFFTEL);

                        //(02)2XXX-XXXX
                        $HOMTEL = '';
                        if($dataArr[$i]['HOMTELA']<>''){
                            $HOMTEL = '('.$dataArr[$i]['HOMTELA'].')';
                        }
                        if($dataArr[$i]['HOMTELB']<>''){
                            $HOMTEL = $HOMTEL.$dataArr[$i]['HOMTELB'];
                        }
                        $templateProcessor->setValue('HOMTEL2#'.($B), $HOMTEL);

                        $templateProcessor->setValue('EMAIL2#'.($B), $dataArr[$i]['EMAIL']);
                        $templateProcessor->setValue('MOBILTEL2#'.($B), $dataArr[$i]['MOBILTEL']);
                    } else {
                        $templateProcessor->setValue('CNAME2#'.($B), '');
                        $templateProcessor->setValue('NO2#'.($B), '');
                        $templateProcessor->setValue('NEWJOB2#'.($B), '');
                        $templateProcessor->setValue('EDUCATION2#'.($B), '');
                        $templateProcessor->setValue('HOMADDR2#'.($B), '');
                        $templateProcessor->setValue('OFFTEL2#'.($B), '');
                        $templateProcessor->setValue('HOMTEL2#'.($B),'');
                        $templateProcessor->setValue('EMAIL2#'.($B), '');
                        $templateProcessor->setValue('MOBILTEL2#'.($B), '');
                    }

                    $B++;
                    //$templateProcessor->setValue('pageBreakHere', '<w:p><w:r><w:br w:type="page"/></w:r></w:p>');
                }
            }
            if((sizeof($dataArr)%2)!=0){
                $templateProcessor->setValue('CNAME2#'.($B), '');
                $templateProcessor->setValue('NO2#'.($B), '');
                $templateProcessor->setValue('NEWJOB2#'.($B), '');
                $templateProcessor->setValue('EDUCATION2#'.($B), '');
                $templateProcessor->setValue('HOMADDR2#'.($B), '');
                $templateProcessor->setValue('OFFTEL2#'.($B), '');
                $templateProcessor->setValue('HOMTEL2#'.($B),'');
                $templateProcessor->setValue('EMAIL2#'.($B), '');
                $templateProcessor->setValue('MOBILTEL2#'.($B), '');
            }


        }
        $outputname="";

        if ($type == '1') {
            $outputname="學員通訊錄-簡式";
        } else {
            $outputname="學員通訊錄-詳細";
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }
}
