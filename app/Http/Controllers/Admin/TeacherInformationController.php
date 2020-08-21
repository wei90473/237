<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpWord;
use PhpOffice\PHPWord_IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use App\Services\User_groupService;

class TeacherInformationController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teacher_information', $user_group_auth)){
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
                $temp=$RptBasic->getclass();
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '';
                return view('admin/teacher_information/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    public function export(Request $request){

        $classes = $request->input('classes');
        $term = $request->input('term');
        //班別：1, 講座姓名：2, 空白表格：3, 個資授權書:4
        $type = $request->input('type');
        $lname = $request->input('name');
        //1:講座, 2:助理
        $formtype = $request->input('formtype');

        ini_set('pcre.backtrack_limit', 999999999);

        /*send 通訊地址  --> 1:公2:宅3:戶籍*/
        if($type=='1'){
            $sql="SELECT m01tb.cname,
                            m01tb.ename,
                            (CASE m01tb.sex WHEN 'M' THEN '男' ELSE '女' END) AS SEX,
                            (CASE WHEN m01tb.idkind IN ('3','4','7') THEN CONCAT('其他：',m01tb.citizen) ELSE '本國籍' END) AS idkind,
                            m01tb.idno,
                            (CASE WHEN m01tb.birth <> '' THEN
                                    CONCAT(SUBSTRING(m01tb.birth,1,3),'年',SUBSTRING(m01tb.birth,4,2),'月',SUBSTRING(m01tb.birth,6,2),'日')
                                        ELSE ''
                            END) AS birth,
                            m01tb.dept,
                            TRIM(m01tb.position) AS position,
                            TRIM(m01tb.offzip) AS offzip,
                            TRIM(m01tb.offaddress) AS offaddress,
                            m01tb.send,
                            TRIM(m01tb.homzip) AS homzip,
                            TRIM(m01tb.homaddress) AS homaddress,
                            TRIM(m01tb.regzip) AS regzip,
                            TRIM(m01tb.regaddress) AS regaddress,
                            Trim(m01tb.offtela1) AS offtela1,
                            Trim(m01tb.offtelb1) AS offtelb1,
                            Trim(m01tb.offtelc1) AS offtelc1,
                            Trim(m01tb.homtela) AS homtela,
                            Trim(m01tb.homtelb) AS homtelb,
                            Trim(m01tb.offfaxa) AS offfaxa,
                            Trim(m01tb.offfaxb) AS offfaxb,
                            Trim(m01tb.homfaxa) AS homfaxa,
                            Trim(m01tb.homfaxb) AS homfaxb,
                            Trim(m01tb.mobiltel) AS mobiltel,
                            Trim(m01tb.email) AS email,
                            Trim(m01tb.liaison) AS liaison,
                            Trim(m01tb.education) AS education,
                            Trim(m01tb.experience) AS experience,
                            Trim(m01tb.award) AS award,
                            m01tb.major1, m01tb.major2, m01tb.major3, m01tb.major4, m01tb.major5,
                            m01tb.major6, m01tb.major7, m01tb.major8, m01tb.major9, m01tb.major10,
                            (CASE WHEN m01tb.insurekind1 = 'Y' THEN '■是□否' ELSE '□是■否' END) AS insurekind1,
                            Trim(m01tb.remark) AS remark,
                            Trim(m01tb.Postcode) AS Postcode,
                            Trim(m01tb.Postno) AS Postno,
                            Trim(m01tb.bank) AS bank,
                            Trim(m01tb.bankno) AS bankno
                    FROM m01tb
                    WHERE EXISTS(
                                    SELECT
                                    *
                                    FROM t09tb
                                    WHERE class = '".$classes."'
                                    AND term = '".$term."'
                                    AND idno = m01tb.idno
                                    )
                    ORDER BY idno";
        //}elseif($type=='2'){
        }else{
            $sql="SELECT m01tb.cname,
                        m01tb.ename,
                        (CASE m01tb.sex WHEN 'M' THEN '男' ELSE '女' END) AS SEX,
                        (CASE WHEN m01tb.idkind IN ('3','4','7') THEN CONCAT('其他：',m01tb.citizen) ELSE '本國籍' END) AS idkind,
                        m01tb.idno,
                        (CASE WHEN m01tb.birth <> '' THEN
                                CONCAT(SUBSTRING(m01tb.birth,1,3),'年',SUBSTRING(m01tb.birth,4,2),'月',SUBSTRING(m01tb.birth,6,2),'日')
                                    ELSE ''
                        END) AS birth,
                        m01tb.dept,
                        TRIM(m01tb.position) AS position,
                        TRIM(m01tb.offzip) AS offzip,
                        TRIM(m01tb.offaddress) AS offaddress,
                        m01tb.send,
                        TRIM(m01tb.homzip) AS homzip,
                        TRIM(m01tb.homaddress) AS homaddress,
                        TRIM(m01tb.regzip) AS regzip,
                        TRIM(m01tb.regaddress) AS regaddress,
                        Trim(m01tb.offtela1) AS offtela1,
                        Trim(m01tb.offtelb1) AS offtelb1,
                        Trim(m01tb.offtelc1) AS offtelc1,
                        Trim(m01tb.homtela) AS homtela,
                        Trim(m01tb.homtelb) AS homtelb,
                        Trim(m01tb.offfaxa) AS offfaxa,
                        Trim(m01tb.offfaxb) AS offfaxb,
                        Trim(m01tb.homfaxa) AS homfaxa,
                        Trim(m01tb.homfaxb) AS homfaxb,
                        Trim(m01tb.mobiltel) AS mobiltel,
                        Trim(m01tb.email) AS email,
                        Trim(m01tb.liaison) AS liaison,
                        Trim(m01tb.education) AS education,
                        Trim(m01tb.experience) AS experience,
                        Trim(m01tb.award) AS award,
                        m01tb.major1, m01tb.major2, m01tb.major3, m01tb.major4, m01tb.major5,
                        m01tb.major6, m01tb.major7, m01tb.major8, m01tb.major9, m01tb.major10,
                        (CASE WHEN m01tb.insurekind1 = 'Y' THEN '■是□否' ELSE '□是■否' END) AS insurekind1,
                        Trim(m01tb.remark) AS remark,
                        Trim(m01tb.Postcode) AS Postcode,
                        Trim(m01tb.Postno) AS Postno,
                        Trim(m01tb.bank) AS bank,
                        Trim(m01tb.bankno) AS bankno
                FROM m01tb
                WHERE cname LIKE '%".$lname."%'
                ORDER BY idno";
        }
        //$reportlist = DB::select($sql);
        //$dataArr = json_decode(json_encode($reportlist), true);
        $dataArr = json_decode(json_encode(DB::select($sql)), true);
        $unit = $dataArr;
        if(sizeof($unit) != 0) {
            $unitkeys=array_keys((array)$unit[0]);
        }

        $sql2="SELECT   name AS CLASSNAME,
                        CONCAT(RTRIM(section),' ',RTRIM(username)) AS user_name,
                        IFNULL(RTRIM(ext),'') AS ext,
                        IFNULL(RTRIM(email),'') AS email,
                        CASE B.branch
                            WHEN '1' THEN
                                                (CASE section
                                                            WHEN '綜合規劃組' THEN '（02）83695616'
                                                            WHEN '培育發展組' THEN '（02）83695611'
                                                            WHEN '專業訓練組' THEN '（02）83695615'
                                                            WHEN '秘書室'     THEN '（02）83695613'
                                                            WHEN '人事室'     THEN '（02）83695618'
                                                            WHEN '主計室'     THEN '（02）83695619'
                                                            ELSE ''
                                                    END )
                            WHEN '2' THEN
                                            (CASE section
                                                            WHEN '綜合規劃組' THEN '（049）2332723'
                                                            WHEN '培育發展組' THEN '（049）2370962'
                                                            WHEN '專業訓練組' THEN '（049）2332724'
                                                            WHEN '數位學習組' THEN '（049）2352979'
                                                            WHEN '秘書室'     THEN '（049）2351627'
                                                            WHEN '人事室'     THEN '（049）2359871'
                                                            WHEN '主計室'     THEN '（049）2359871'
                                                            ELSE ''
                                                    END )
                            ELSE ''
                        END AS fax,
                        IFNULL(B.branch,'') AS branch,
                        CASE WHEN LEFT(A.ext,1) = '8' THEN '1'
                                ELSE '2'
                        END ext_branch,
                        (CASE B.branch
                            WHEN '1' THEN '（02）83691399'
                            WHEN '2' THEN '（049）2332131'
                            ELSE ''
                        END
                        ) AS rep_line
                FROM m09tb A LEFT JOIN t01tb B ON 1 = 1
                WHERE 1 = 1
                AND B.class = '".$classes."'
                    AND userid IN (
                                    SELECT sponsor AS userid
                                    FROM t04tb
                                    WHERE class = '".$classes."'
                                    AND term = '".$term."'
                                )
                ";
        $reportlist2 = DB::select($sql2);
        $dataArr2 = json_decode(json_encode($reportlist2), true);

        //dd($type);
        //班別：1, 講座姓名：2, 空白表格：3, 個資授權書:4
        if($type=='1'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H2A').'.docx');
        }elseif($type=='2'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H2A').'.docx');    
        }elseif($type=='3'){
            //1:講座, 2:助理
            if($formtype=='1'){
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H2B1').'.docx');
            }else{
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H2B2').'.docx');
            }
        }elseif($type=='4'){
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'H2C').'.docx');
        }


        //班別：1, 講座姓名：2, 空白表格：3, 個資授權書:4
        if($type=='2' or $type=='1'){
            //dd(sizeof($reportlist));
            if(sizeof($unit) != 0) {
                //dd(sizeof($unit));
                $templateProcessor->cloneBlock('t',sizeof($unit), true, true);
                for($j=0;$j<sizeof($unit);$j++){
                    if(!($j==(sizeof($unit)-1))){
                        $templateProcessor->setValue('pb#'.strval($j+1), '<w:p><w:r><w:br w:type="page"/></w:r></w:p>');
                    }else{
                        $templateProcessor->setValue('pb#'.strval($j+1),'');
                    }
                    
                    //for($j=0; $j<sizeof($dataArr); $j++) {
                        if($dataArr[$j]['cname']<>''){
                            
                            $templateProcessor->setValue('CNAME#'.strval($j+1), $dataArr[$j]['cname']);
                        }
                        $templateProcessor->setValue('ENAME#'.($j+1), $dataArr[$j]['ename']);
                        $templateProcessor->setValue('SEX#'.($j+1), $dataArr[$j]['SEX']);
                        $templateProcessor->setValue('IDKIND#'.($j+1), $dataArr[$j]['idkind']);

                        if($dataArr[$j]['idno']<>''){
                            $templateProcessor->setValue('I1#'.($j+1), substr($dataArr[$j]['idno'],0,1));
                            $templateProcessor->setValue('I2#'.($j+1), substr($dataArr[$j]['idno'],1,1));
                            $templateProcessor->setValue('I3#'.($j+1), substr($dataArr[$j]['idno'],2,1));
                            $templateProcessor->setValue('I4#'.($j+1), substr($dataArr[$j]['idno'],3,1));
                            $templateProcessor->setValue('I5#'.($j+1), substr($dataArr[$j]['idno'],4,1));
                            $templateProcessor->setValue('I6#'.($j+1), substr($dataArr[$j]['idno'],5,1));
                            $templateProcessor->setValue('I7#'.($j+1), substr($dataArr[$j]['idno'],6,1));
                            $templateProcessor->setValue('I8#'.($j+1), substr($dataArr[$j]['idno'],7,1));
                            $templateProcessor->setValue('I9#'.($j+1), substr($dataArr[$j]['idno'],8,1));
                            $templateProcessor->setValue('IA#'.($j+1), substr($dataArr[$j]['idno'],9,1));
                        }else{
                            $templateProcessor->setValue('I1#'.($j+1), '');
                            $templateProcessor->setValue('I2#'.($j+1), '');
                            $templateProcessor->setValue('I3#'.($j+1), '');
                            $templateProcessor->setValue('I4#'.($j+1), '');
                            $templateProcessor->setValue('I5#'.($j+1), '');
                            $templateProcessor->setValue('I6#'.($j+1), '');
                            $templateProcessor->setValue('I7#'.($j+1), '');
                            $templateProcessor->setValue('I8#'.($j+1), '');
                            $templateProcessor->setValue('I9#'.($j+1), '');
                            $templateProcessor->setValue('IA#'.($j+1), '');
                        }

                        $templateProcessor->setValue('BIRTH#'.($j+1), ltrim($dataArr[$j]['birth'],'0'));
                        $templateProcessor->setValue('DEPT#'.($j+1), $dataArr[$j]['dept']);
                        $templateProcessor->setValue('POSITION#'.($j+1), $dataArr[$j]['position']);

                        $templateProcessor->setValue('OFFZIP#'.($j+1), $dataArr[$j]['offzip']);
                        $templateProcessor->setValue('OFFADDRESS#'.($j+1), $dataArr[$j]['offaddress']);
                        //'通訊地址  --> 1:公2:宅3:戶籍
                        $ZIP='';
                        $ADDRESS='';
                        if($dataArr[$j]['send']=='1'){
                            $ZIP=$dataArr[$j]['offzip'];
                            $ADDRESS=$dataArr[$j]['offaddress'];
                        }elseif($dataArr[$j]['send']=='2'){
                            $ZIP=$dataArr[$j]['homzip'];
                            $ADDRESS=$dataArr[$j]['homaddress'];
                        }elseif($dataArr[$j]['send']=='3'){
                            $ZIP=$dataArr[$j]['regzip'];
                            $ADDRESS=$dataArr[$j]['regaddress'];
                        }
                        $templateProcessor->setValue('ZIP#'.($j+1), $ZIP);
                        $templateProcessor->setValue('ADDRESS#'.($j+1), $ADDRESS);
                        $templateProcessor->setValue('HOMEZIP#'.($j+1), $dataArr[$j]['regzip']);
                        $templateProcessor->setValue('HOMEADDRESS#'.($j+1), $dataArr[$j]['regaddress']);
                        //offtela1, offtelb1, offtelc1
                        $OFFTEL='';
                        if($dataArr[$j]['offtela1']<>''){
                            $OFFTEL='('.$dataArr[$j]['offtela1'].')';
                        }
                        if($dataArr[$j]['offtelb1']<>''){
                            $OFFTEL=$OFFTEL.$dataArr[$j]['offtelb1'];
                        }
                        if($dataArr[$j]['offtelc1']<>''){
                            $OFFTEL=$OFFTEL.'轉'.$dataArr[$j]['offtelc1'];
                        }
                        $templateProcessor->setValue('OFFTEL#'.($j+1), $OFFTEL);
                        //homtela, homtelb
                        $HOMTEL='';
                        if($dataArr[$j]['homtela']<>''){
                            $HOMTEL='('.$dataArr[$j]['homtela'].')';
                        }
                        if($dataArr[$j]['homtelb']<>''){
                            $HOMTEL=$HOMTEL.$dataArr[$j]['homtelb'];
                        }
                        $templateProcessor->setValue('HOMETEL#'.($j+1), $HOMTEL);

                        $FAX='';
                        if($dataArr[$j]['offfaxa']<>''){
                            $FAX=$FAX.'('.$dataArr[$j]['offfaxa'].')';
                        }
                        if($dataArr[$j]['offfaxb']<>''){
                            $FAX=$FAX.$dataArr[$j]['offfaxb'];
                        }else{
                            if($dataArr[$j]['homfaxa']<>''){
                                $FAX=$FAX.'('.$dataArr[$j]['homfaxa'].')';
                            }
                            if($dataArr[$j]['homfaxb']<>''){
                                $FAX=$FAX.$dataArr[$j]['homfaxb'];
                            }
                        }
                        $templateProcessor->setValue('FAX#'.($j+1), $FAX);
                        $templateProcessor->setValue('MOBILE#'.($j+1), $dataArr[$j]['mobiltel']);
                        $templateProcessor->setValue('EMAIL#'.($j+1), $dataArr[$j]['email']);
                        $templateProcessor->setValue('LIAISON#'.($j+1), $dataArr[$j]['liaison']);
                        $templateProcessor->setValue('EDU#'.($j+1), $dataArr[$j]['education']);
                        $templateProcessor->setValue('EXP#'.($j+1), $dataArr[$j]['experience']);
                        $templateProcessor->setValue('REMARK#'.($j+1), $dataArr[$j]['remark']);
                        $templateProcessor->setValue('AWARD#'.($j+1), $dataArr[$j]['award']);

                        /*專長領域*/
                        $sqlSP="SELECT idno, no, specialty
                                From m16tb WHERE idno = '".$dataArr[$j]['idno']."'
                                ORDER BY no ";
                        $reportlistSP = DB::select($sqlSP);
                        $dataArrSP = json_decode(json_encode($reportlistSP), true);
                        for($s=0;$s<sizeof($reportlistSP);$s++){
                            if($dataArrSP[$s]['specialty']<>''){
                                $templateProcessor->setValue('SP'.($s+1).'#'.($j+1), $dataArrSP[$s]['specialty']);
                            }
                        }
                        for($e=$s;$e<5;$e++){
                            $templateProcessor->setValue('SP'.($e+1).'#'.($j+1),  '');
                        }

                        $MA='';
                        if($dataArr[$j]['major1']<>''){
                            $MA=$MA.$dataArr[$j]['major1'].';';
                        }
                        if($dataArr[$j]['major2']<>''){
                            $MA=$MA.$dataArr[$j]['major2'].';';
                        }
                        if($dataArr[$j]['major3']<>''){
                            $MA=$MA.$dataArr[$j]['major3'].';';
                        }
                        if($dataArr[$j]['major4']<>''){
                            $MA=$MA.$dataArr[$j]['major4'].';';
                        }
                        if($dataArr[$j]['major5']<>''){
                            $MA=$MA.$dataArr[$j]['major5'].';';
                        }
                        if($dataArr[$j]['major6']<>''){
                            $MA=$MA.$dataArr[$j]['major6'].';';
                        }
                        if($dataArr[$j]['major7']<>''){
                            $MA=$MA.$dataArr[$j]['major7'].';';
                        }
                        if($dataArr[$j]['major8']<>''){
                            $MA=$MA.$dataArr[$j]['major8'].';';
                        }
                        if($dataArr[$j]['major9']<>''){
                            $MA=$MA.$dataArr[$j]['major9'].';';
                        }
                        if($dataArr[$j]['major10']<>''){
                            $MA=$MA.$dataArr[$j]['major10'].';';
                        }
                        $templateProcessor->setValue('MA#'.($j+1), rtrim($MA,';'));

                        $templateProcessor->setValue('IN#'.($j+1), $dataArr[$j]['insurekind1']);

                        if($dataArr[$j]['Postcode']<>''){
                            $templateProcessor->setValue('P1#'.($j+1), substr($dataArr[$j]['Postcode'],0,1));
                            $templateProcessor->setValue('P2#'.($j+1), substr($dataArr[$j]['Postcode'],1,1));
                            $templateProcessor->setValue('P3#'.($j+1), substr($dataArr[$j]['Postcode'],2,1));
                            $templateProcessor->setValue('P4#'.($j+1), substr($dataArr[$j]['Postcode'],3,1));
                            $templateProcessor->setValue('P5#'.($j+1), substr($dataArr[$j]['Postcode'],4,1));
                            $templateProcessor->setValue('P6#'.($j+1), substr($dataArr[$j]['Postcode'],5,1));
                            $templateProcessor->setValue('P7#'.($j+1), substr($dataArr[$j]['Postcode'],6,1));
                        }else{
                            $templateProcessor->setValue('P1#'.($j+1), '');
                            $templateProcessor->setValue('P2#'.($j+1), '');
                            $templateProcessor->setValue('P3#'.($j+1), '');
                            $templateProcessor->setValue('P4#'.($j+1), '');
                            $templateProcessor->setValue('P5#'.($j+1), '');
                            $templateProcessor->setValue('P6#'.($j+1), '');
                            $templateProcessor->setValue('P7#'.($j+1), '');
                        }

                        if($dataArr[$j]['Postno']<>''){
                            $templateProcessor->setValue('N1#'.($j+1), substr($dataArr[$j]['Postno'],0,1));
                            $templateProcessor->setValue('N2#'.($j+1), substr($dataArr[$j]['Postno'],1,1));
                            $templateProcessor->setValue('N3#'.($j+1), substr($dataArr[$j]['Postno'],2,1));
                            $templateProcessor->setValue('N4#'.($j+1), substr($dataArr[$j]['Postno'],3,1));
                            $templateProcessor->setValue('N5#'.($j+1), substr($dataArr[$j]['Postno'],4,1));
                            $templateProcessor->setValue('N6#'.($j+1), substr($dataArr[$j]['Postno'],5,1));
                            $templateProcessor->setValue('N7#'.($j+1), substr($dataArr[$j]['Postno'],6,1));
                        }else{
                            $templateProcessor->setValue('N1#'.($j+1), '');
                            $templateProcessor->setValue('N2#'.($j+1), '');
                            $templateProcessor->setValue('N3#'.($j+1), '');
                            $templateProcessor->setValue('N4#'.($j+1), '');
                            $templateProcessor->setValue('N5#'.($j+1), '');
                            $templateProcessor->setValue('N6#'.($j+1), '');
                            $templateProcessor->setValue('N7#'.($j+1), '');
                        }

                        if($dataArr[$j]['bank']<>''){
                            $templateProcessor->setValue('B1#'.($j+1), substr($dataArr[$j]['bank'],0,1));
                            $templateProcessor->setValue('B2#'.($j+1), substr($dataArr[$j]['bank'],1,1));
                            $templateProcessor->setValue('B3#'.($j+1), substr($dataArr[$j]['bank'],2,1));
                            $templateProcessor->setValue('B4#'.($j+1), substr($dataArr[$j]['bank'],3,1));
                            $templateProcessor->setValue('B5#'.($j+1), substr($dataArr[$j]['bank'],4,1));
                            $templateProcessor->setValue('B6#'.($j+1), substr($dataArr[$j]['bank'],5,1));
                            $templateProcessor->setValue('B7#'.($j+1), substr($dataArr[$j]['bank'],6,1));
                        }else{
                            $templateProcessor->setValue('B1#'.($j+1), '');
                            $templateProcessor->setValue('B2#'.($j+1), '');
                            $templateProcessor->setValue('B3#'.($j+1), '');
                            $templateProcessor->setValue('B4#'.($j+1), '');
                            $templateProcessor->setValue('B5#'.($j+1), '');
                            $templateProcessor->setValue('B6#'.($j+1), '');
                            $templateProcessor->setValue('B7#'.($j+1), '');
                        }

                        if($dataArr[$j]['bankno']<>''){
                            $templateProcessor->setValue('C1#'.($j+1), substr($dataArr[$j]['bankno'],0,1));
                            $templateProcessor->setValue('C2#'.($j+1), substr($dataArr[$j]['bankno'],1,1));
                            $templateProcessor->setValue('C3#'.($j+1), substr($dataArr[$j]['bankno'],2,1));
                            $templateProcessor->setValue('C4#'.($j+1), substr($dataArr[$j]['bankno'],3,1));
                            $templateProcessor->setValue('C5#'.($j+1), substr($dataArr[$j]['bankno'],4,1));
                            $templateProcessor->setValue('C6#'.($j+1), substr($dataArr[$j]['bankno'],5,1));
                            $templateProcessor->setValue('C7#'.($j+1), substr($dataArr[$j]['bankno'],6,1));
                            $templateProcessor->setValue('C8#'.($j+1), substr($dataArr[$j]['bankno'],7,1));
                            $templateProcessor->setValue('C9#'.($j+1), substr($dataArr[$j]['bankno'],8,1));
                            $templateProcessor->setValue('CA#'.($j+1), substr($dataArr[$j]['bankno'],9,1));
                            $templateProcessor->setValue('CB#'.($j+1), substr($dataArr[$j]['bankno'],10,1));
                            $templateProcessor->setValue('CC#'.($j+1), substr($dataArr[$j]['bankno'],11,1));
                            $templateProcessor->setValue('CD#'.($j+1), substr($dataArr[$j]['bankno'],12,1));
                            $templateProcessor->setValue('CE#'.($j+1), substr($dataArr[$j]['bankno'],13,1));
                        } else{
                            $templateProcessor->setValue('C1#'.($j+1), '');
                            $templateProcessor->setValue('C2#'.($j+1), '');
                            $templateProcessor->setValue('C3#'.($j+1), '');
                            $templateProcessor->setValue('C4#'.($j+1), '');
                            $templateProcessor->setValue('C5#'.($j+1), '');
                            $templateProcessor->setValue('C6#'.($j+1), '');
                            $templateProcessor->setValue('C7#'.($j+1), '');
                            $templateProcessor->setValue('C8#'.($j+1), '');
                            $templateProcessor->setValue('C9#'.($j+1), '');
                            $templateProcessor->setValue('CA#'.($j+1), '');
                            $templateProcessor->setValue('CB#'.($j+1), '');
                            $templateProcessor->setValue('CC#'.($j+1), '');
                            $templateProcessor->setValue('CD#'.($j+1), '');
                            $templateProcessor->setValue('CE#'.($j+1), '');
                        }
                
                    //}
                }
            }else{
                $templateProcessor->setValue('t','');
                $templateProcessor->setValue('/t','');
                $templateProcessor->setValue('pb','');

                $templateProcessor->setValue('CNAME','');
                $templateProcessor->setValue('ENAME','');
                $templateProcessor->setValue('SEX','');
                $templateProcessor->setValue('IDKIND','');
                $templateProcessor->setValue('I1','');
                $templateProcessor->setValue('I2','');
                $templateProcessor->setValue('I3','');
                $templateProcessor->setValue('I4','');
                $templateProcessor->setValue('I5','');
                $templateProcessor->setValue('I6','');
                $templateProcessor->setValue('I7','');
                $templateProcessor->setValue('I8','');
                $templateProcessor->setValue('I9','');
                $templateProcessor->setValue('IA','');
                $templateProcessor->setValue('BIRTH','');
                $templateProcessor->setValue('DEPT','');
                $templateProcessor->setValue('POSITION','');
                $templateProcessor->setValue('OFFZIP','');
                $templateProcessor->setValue('OFFADDRESS','');
                $templateProcessor->setValue('ZIP','');
                $templateProcessor->setValue('ADDRESS','');
                $templateProcessor->setValue('HOMEZIP','');
                $templateProcessor->setValue('HOMEADDRESS','');
                $templateProcessor->setValue('OFFTEL','');
                $templateProcessor->setValue('HOMETEL','');
                $templateProcessor->setValue('FAX','');
                $templateProcessor->setValue('MOBILE','');
                $templateProcessor->setValue('EMAIL','');
                $templateProcessor->setValue('LIAISON','');
                $templateProcessor->setValue('EDU','');
                $templateProcessor->setValue('EXP','');
                $templateProcessor->setValue('REMARK','');
                $templateProcessor->setValue('AWARD','');
                $templateProcessor->setValue('SP1','');
                $templateProcessor->setValue('SP2','');
                $templateProcessor->setValue('SP3','');
                $templateProcessor->setValue('SP4','');
                $templateProcessor->setValue('SP5','');
                $templateProcessor->setValue('MA','');
                $templateProcessor->setValue('IN','□是□否');
                $templateProcessor->setValue('P1','');
                $templateProcessor->setValue('P2','');
                $templateProcessor->setValue('P3','');
                $templateProcessor->setValue('P4','');
                $templateProcessor->setValue('P5','');
                $templateProcessor->setValue('P6','');
                $templateProcessor->setValue('P7','');
                $templateProcessor->setValue('N1','');
                $templateProcessor->setValue('N2','');
                $templateProcessor->setValue('N3','');
                $templateProcessor->setValue('N4','');
                $templateProcessor->setValue('N5','');
                $templateProcessor->setValue('N6','');
                $templateProcessor->setValue('N7','');
                $templateProcessor->setValue('B1','');
                $templateProcessor->setValue('B2','');
                $templateProcessor->setValue('B3','');
                $templateProcessor->setValue('B4','');
                $templateProcessor->setValue('B5','');
                $templateProcessor->setValue('B6','');
                $templateProcessor->setValue('B7','');
                $templateProcessor->setValue('C1','');
                $templateProcessor->setValue('C2','');
                $templateProcessor->setValue('C3','');
                $templateProcessor->setValue('C4','');
                $templateProcessor->setValue('C5','');
                $templateProcessor->setValue('C6','');
                $templateProcessor->setValue('C7','');
                $templateProcessor->setValue('C8','');
                $templateProcessor->setValue('C9','');
                $templateProcessor->setValue('CA','');
                $templateProcessor->setValue('CB','');
                $templateProcessor->setValue('CC','');
                $templateProcessor->setValue('CD','');
                $templateProcessor->setValue('CE','');
            }
        }elseif($type=='3'){
            //1:講座, 2:助理
            if($formtype=='1'){
                if(sizeof($reportlist2) != 0) {
                    $TEL='';
                    $templateProcessor->setValue('USERNAME',  $dataArr2[0]['user_name']);
                    if($dataArr2[0]['rep_line']<>''){
                        $TEL = $dataArr2[0]['rep_line'];
                        if($dataArr2[0]['ext']<>''){
                            $TEL = $TEL.'轉'.$dataArr2[0]['ext'];
                        }
                    }
                    $templateProcessor->setValue('TEL',  $TEL);
                    $templateProcessor->setValue('FAX',  $dataArr2[0]['fax']);
                    $templateProcessor->setValue('EMAIL',  $dataArr2[0]['email']);
                }else{
                    //$templateProcessor->setValue('USERNAME', '');
                    //$templateProcessor->setValue('TEL', '');
                    //$templateProcessor->setValue('FAX', '');
                    //$templateProcessor->setValue('EMAIL', '');
                    $templateProcessor->setValue('USERNAME', '資訊室 侯冠州');
                    $templateProcessor->setValue('TEL', '（049）2332131轉6030');
                    $templateProcessor->setValue('FAX', '');
                    $templateProcessor->setValue('EMAIL', 'kchou@hrd.gov.tw');                    
                }
            }
        }elseif($type=='4'){
            if(sizeof($reportlist2) != 0) {
                $TEL='';
                $templateProcessor->setValue('USERNAME',  $dataArr2[0]['user_name']);
                if($dataArr2[0]['rep_line']<>''){
                    $TEL = $dataArr2[0]['rep_line'];
                    if($dataArr2[0]['ext']<>''){
                        $TEL = $TEL.'轉'.$dataArr2[0]['ext'];
                    }
                }
                $templateProcessor->setValue('TEL',  $TEL);
                $templateProcessor->setValue('FAX',  $dataArr2[0]['fax']);
                $templateProcessor->setValue('EMAIL',  $dataArr2[0]['email']);
            }else{
                //$templateProcessor->setValue('USERNAME', '');
                //$templateProcessor->setValue('TEL', '');
                //$templateProcessor->setValue('FAX', '');
                //$templateProcessor->setValue('EMAIL', '');
                $templateProcessor->setValue('USERNAME', '資訊室 侯冠州');
                $templateProcessor->setValue('TEL', '（049）2332131轉6030');
                $templateProcessor->setValue('FAX', '');
                $templateProcessor->setValue('EMAIL', 'kchou@hrd.gov.tw');                
            }
        }


        $outputname="";
        if($type=='1'){
            $outputname="講師基本資料表-依班期";
        }elseif($type=='2'){
            $outputname="講師基本資料表-依班期講師姓名";
        }elseif($type=='3'){
            //1:講座, 2:助理
            if($formtype=='1'){
                $outputname="講師基本資料表-空白表格-講座";
            }else{
                $outputname="講師基本資料表-空白表格-助理";
            }
        }elseif($type=='4'){
            $outputname="講師基本資料表-空白表格-個資授權書";
        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 


   }
}
