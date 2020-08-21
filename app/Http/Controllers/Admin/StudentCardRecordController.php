<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
//use PHPWord_IOFactory;
use PhpOffice\PhpWord;
use PhpOffice\PHPWord_IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use App\Services\User_groupService;

class StudentCardRecordController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_card_record', $user_group_auth)){
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
        return view('admin/student_card_record/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    public function export(Request $request)
    {
        $classes = $request->input('classes');
        $term = $request->input('term');

        //TITLE
        $sqlTITLE="SELECT   CONCAT(E.name, '第',C.term,'期') AS CLASSNAME,
                            (CASE E.branch
                                WHEN '1' THEN CONCAT(IFNULL(RTRIM(F.name),''),'(臺北院區)')
                                WHEN '2' THEN CONCAT(IFNULL(RTRIM(G.name),''),'(南投院區)')
                            END ) AS name,
                            CONCAT(SUBSTRING(C.sdate,1,3),'.',SUBSTRING(C.sdate,4,2),'.',SUBSTRING(C.sdate,6,2),
                                ' ─ ',
                                SUBSTRING(C.edate,1,3),'.',SUBSTRING(C.edate,4,2),'.',SUBSTRING(C.edate,6,2)) AS daterange,
                            CONCAT(SUBSTRING(C.dated,1,3),'.',SUBSTRING(C.dated,4,2),'.',SUBSTRING(C.dated,6,2)) AS dated,
                            C.dated AS qdate
                    FROM (  SELECT A.class,
                                    A.term,
                                    B.dated,
                                    A.sdate,
                                    A.edate
                            FROM t04tb A INNER JOIN t84tb B ON A.class = B.class AND A.term = B.term AND B.dated BETWEEN A.sdate AND A.edate
                            WHERE A.class = '".$classes."'
                            AND A.term= '".$term."'
                            GROUP BY A.class,A.term,A.sdate,A.edate,B.dated
                        ) C INNER JOIN t36tb D ON C.class = D.class AND C.term = D.term
                            INNER JOIN t01tb E ON D.class = E.class
                            LEFT JOIN m14tb F ON D.site = F.site
                            LEFT JOIN m25tb G ON D.site = G.site
                    GROUP BY E.name, C.dated, name
                    ORDER BY 1, 2, 5
                        ";
        $reportlistTITLE = DB::select($sqlTITLE);
        $dataArrTITLE = json_decode(json_encode($reportlistTITLE), true);
        $unit = $dataArrTITLE;
        if(sizeof($unit) != 0) {
            $unitkeys=array_keys((array)$unit[0]);
        }


         // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J10').'.docx');
        ini_set('pcre.backtrack_limit', 999999999);
        if(sizeof($unit) != 0) {
            $templateProcessor->cloneBlock('t',sizeof($unit), true, true);
            for($i=0;$i<sizeof($unit);$i++){

                $templateProcessor->setValue('classname#'.strval($i+1), $unit[$i][$unitkeys[0]]);
                $templateProcessor->setValue('class#'.strval($i+1), $unit[$i][$unitkeys[1]]);
                $templateProcessor->setValue('classtime#'.strval($i+1), $unit[$i][$unitkeys[2]]);
                $templateProcessor->setValue('classdate#'.strval($i+1), $unit[$i][$unitkeys[3]]);

                if(!($i==(sizeof($unit)-1))){
                    $templateProcessor->setValue('pb#'.strval($i+1), '<w:p><w:r><w:br w:type="page"/></w:r></w:p>');
                }else{
                    $templateProcessor->setValue('pb#'.strval($i+1),'');
                }

                $sql=" SELECT A.no,
                                        B.cname,
                                        IFNULL(C.dated,'') as dated,
                                        IFNULL(SUBSTRING(C.timed,1,4),'') AS timed1,
                                        IFNULL(SUBSTRING(C2.timed,1,4),'') AS timed2
                            FROM t13tb A LEFT JOIN m02tb B ON A.idno=B.idno
                                    LEFT JOIN (select class,term,idno,dated, timed
                                                from t84tb
                                                where t84tb.dated = '".$unit[$i][$unitkeys[4]]."'
                                                AND t84tb.class = '".$classes."'
                                                AND t84tb.term = '".$term."'
                                                AND status='A'
                                                group by class,term,idno,dated
                                                ) C ON A.class=C.class AND A.term=C.term  AND A.idno=C.idno
                                    LEFT JOIN (select class,term,idno,dated, timed
                                                                from t84tb
                                                                        where t84tb.dated = '".$unit[$i][$unitkeys[4]]."'
                                                                            AND t84tb.class = '".$classes."'
                                                                            AND t84tb.term = '".$term."'
                                                                                AND status='B'
                                                group by class,term,idno,dated
                                                ) C2 ON A.class=C2.class AND A.term=C2.term  AND A.idno=C2.idno
                            WHERE A.class='".$classes."'
                            AND A.term='".$term."'
                            AND A.no<>''
                            ORDER BY no
                            ";
                $reportlist = DB::select($sql);
                $dataArr = json_decode(json_encode($reportlist), true);

                //$templateProcessor->cloneRow('NAM1', ceil(sizeof($dataArr)/2));
                $templateProcessor->cloneRow('NAM1#'.strval($i+1), ceil(sizeof($dataArr)/2));
                $A=1;
                $B=1;
                $stime ='';
                $etime ='';
                for($j=0; $j<sizeof($dataArr); $j++) {
                    if($dataArr[$j]['timed1']<>''){
                        $stime =substr($dataArr[$j]['timed1'],0,2).':'.substr($dataArr[$j]['timed1'],2,2);
                    } else {
                        $stime ='';
                    }
                    $Rstime = new TextRun();
                    $Rstime->addText($stime, array( 'color' => 'red'));
                    if($dataArr[$j]['timed2']<>''){
                        $etime =substr($dataArr[$j]['timed2'],0,2).':'.substr($dataArr[$j]['timed2'],2,2);
                    } else {
                        $etime ='';
                    }
                    $Retime = new TextRun();
                    $Retime->addText($etime, array( 'color' => 'red'));
                    if($j%2==0){
                            $templateProcessor->setValue('N1#'.strval($i+1).'#'.($A), $dataArr[$j]['no']);
                            $templateProcessor->setValue('NAM1#'.strval($i+1).'#'.($A),  $dataArr[$j]['cname']);
                            if($stime <= '09:30'){
                                $templateProcessor->setValue('stime1#'.strval($i+1).'#'.($A), $stime);
                            } else {
                                $templateProcessor->setComplexValue('stime1#'.strval($i+1).'#'.($A),$Rstime);
                            }
                            if($etime <= '14:00'){
                                $templateProcessor->setValue('etime1#'.strval($i+1).'#'.($A), $etime);
                            } else{
                                $templateProcessor->setComplexValue('etime1#'.strval($i+1).'#'.($A),$Retime);
                            }
                            $A++;
                    }
                    if($j%2==1){
                            $templateProcessor->setValue('N2#'.strval($i+1).'#'.($B),  $dataArr[$j]['no']);
                            $templateProcessor->setValue('NAM2#'.strval($i+1).'#'.($B), $dataArr[$j]['cname']);
                            if($stime <= '09:30'){
                                $templateProcessor->setValue('stime2#'.strval($i+1).'#'.($B), $stime);
                            } else {
                                $templateProcessor->setComplexValue('stime2#'.strval($i+1).'#'.($B),$Rstime);
                            }
                            if($etime <= '14:00'){
                                $templateProcessor->setValue('etime2#'.strval($i+1).'#'.($B), $etime);
                            } else{
                                $templateProcessor->setComplexValue('etime2#'.strval($i+1).'#'.($B),$Retime);
                            }
                            $B++;
                    }
                }
                if((sizeof($dataArr)%2)!=0){
                        $templateProcessor->setValue('N2#'.strval($i+1).'#'.($B), ' ');
                        $templateProcessor->setValue('NAM2#'.strval($i+1).'#'.($B), ' ');
                        $templateProcessor->setValue('stime2#'.strval($i+1).'#'.($B), ' ');
                        $templateProcessor->setValue('etime2#'.strval($i+1).'#'.($B),' ');
                }

            }
        } else{
            $templateProcessor->setValue('classname', '');
            $templateProcessor->setValue('class', '');
            $templateProcessor->setValue('classtime', '');
            $templateProcessor->setValue('classdate', '');
            $templateProcessor->setValue('N1', '');
            $templateProcessor->setValue('NAM1', '');
            $templateProcessor->setValue('stime1', '');
            $templateProcessor->setValue('etime1', '');
            $templateProcessor->setValue('N2', '');
            $templateProcessor->setValue('NAM2', '');
            $templateProcessor->setValue('stime2', '');
            $templateProcessor->setValue('etime2', '');
            $templateProcessor->setValue('pb', '');
            $templateProcessor->setValue('t', '');
            $templateProcessor->setValue('/t', '');
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),"學員刷卡記錄");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }

}
