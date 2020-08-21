<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use \PhpOffice\PhpWord\PhpWord;
use \PhpOffice\PhpWord\IOFactory;
use App\Services\User_groupService;

class StudentSeatListController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_seat_list', $user_group_auth)){
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
                $ctemp=json_decode(json_encode($temp), true);
                $carraykeys=array_keys((array)$ctemp[0]);

                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$ctemp[0]["class"]."'");
                $termArr=$temp;
                $ttemp=json_decode(json_encode($temp), true);

                $siteArr=[];
                if($termArr!=[]){
                    $tarraykeys=array_keys((array)$ctemp[0]);
                    $temp=DB::select("SELECT site FROM t04tb WHERE class='".$ctemp[0]["class"]."' AND term='".$ttemp[0]["term"]."'");
                    $siteArr= $temp;
                }

                $result = '';
                return view('admin/student_seat_list/list',compact('classArr','termArr','siteArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }

    //取得教室
    public function getSites(Request $request)
    {
        /*
        $temp=DB::select("SELECT branch FROM t01tb WHERE class='".$request->input('classes')."'");
        $branch=json_decode(json_encode($temp), true);
        $tb="m14tb";

        if($branch[0]["branch"]=="2"){
            $tb="m25tb";
        }
        $temp=DB::select("SELECT A.site,B.name FROM (SELECT site FROM t04tb WHERE class='".$request->input('classes')."' AND term='".$request->input('term')."') AS A LEFT JOIN ".$tb." AS B ON A.site=B.site");
        */
        $temp=DB::select("SELECT site, RTRIM(name) AS name FROM m14tb WHERE (type='1' AND seat<>'3') OR site='C14' OR site='303' AND type IS NOT NULL  ORDER BY site ");
        $siteArr=$temp;
        return $siteArr;
    }

    /*
    學員座位表 CSDIR4030
    參考Tables:
    使用範本:J6D....xlsx
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
        $site = $request->input('site'); //教室
        //'座位類型-->A (標準型);B (馬蹄型);C (T型);D (菱型)
        $optType = $request->input('optType'); //座位類型
        //'針對303 座位類型-->'1: 變動座位(30人), 2:變動座位(40人)
        $seat = $request->input('seat'); //座位類型
        $optFormat = $request->input('optFormat'); //'排列方式-->'0:學生; 1:老師
        $optSet = $request->input('optSet'); //'排列方式-->'0:學號; 1:組別
        $strTemp='';
        $strRoomType = '';
        $strSet='';
        $strDoor='';
        $strDotName='';
        $strFormat='';
        $strSeat='';
        $str303='';
        $optTypeName ='';
        $optFormatName='';
        $filename='';

        //班別, 教室
        $sqlTITLE ="SELECT DISTINCT RTRIM(A.name) AS CLASSNAME,
                                    B.SITENAME
                    FROM t01tb A INNER JOIN (SELECT SITE, RTRIM(name) AS SITENAME
                                             FROM m14tb
                                             WHERE (type='1' AND seat<>'3')
                                                OR SITE='C14'
                                                OR SITE='303'
                                                AND TYPE IS NOT NULL
                                                ) B ON 1 = 1
                        WHERE A.class = '".$classes."'
                        AND B.SITE = '".$site."'
                        ";
        $reportlistTitle = DB::select($sqlTITLE);
        $dataArrTitle = json_decode(json_encode($reportlistTitle), true);

        if($site!='303'){
            //'座位類型-->A (標準型);B (馬蹄型);C (T型);D (菱型)
            $strTemp = $optType;
            if($optType=='A'){
                $optTypeName = '標準型';
            }elseif($optType=='B'){
                $optTypeName ='馬蹄型';
            }elseif($optType=='C'){
                $optTypeName ='-T型';
            }elseif($optType=='D'){
                $optTypeName ='-菱型';
            }
        }else{
            if($seat=='1'){
                $str303='A';
            }elseif($seat=='2'){
                $str303='B';
            }
        }

        //列印格式
        //'排列方式-->'0:學生; 1:老師
        if($optFormat=='0'){
            $strTemp = $strTemp;
            $optFormatName='學員';
        }elseif($optFormat=='1'){
            $strTemp = $strTemp.'T';
            $optFormatName='講座';
        }
        $strRoomType = $strTemp;

        //'排列方式:學號;組別
        if($optSet=='0'){
            $strSet = '依學號';
        }elseif($optSet=='1'){
            $strSet = '依組別';
        }

        //取得m14tb.seat, m14tb.door的資料
        //'seat  座位類型 char  1  ('1') 1:變動座位(30人)2:變動座位(40人)3:固定座位
        //'door  教室門口 char  1  ('1') 1:右側  2:左側
        $sqlSD = "SELECT seat, door FROM m14tb WHERE site='".$site."'
                        ";
        $reportlistSD = DB::select($sqlSD);
        $dataArrSD = json_decode(json_encode($reportlistSD), true);

        /*
        'm14tb 場地基本資料檔
        'seat  座位類型 char  1  ('1')
        '1 變動座位(30人)
        '2 變動座位(40人)
        '3 固定座位
        '4 變動座位(80人)
        '5 變動座位(90人)
        */

        if($dataArrSD[0]['seat']=='1'){
            $strTemp = '30';
            $optTypeName = '30人';

        }elseif($dataArrSD[0]['seat']=='2'){
            $strTemp = '40';
            $optTypeName = '40人';
        }
        $strSeat=$strTemp;

        //'教室門口－－＞1:右側；2:左側
        if($optFormat=='0'){
            /*學生*/
            if($dataArrSD[0]['door']=='1'){
                $strDoor='R';
            }elseif($dataArrSD[0]['door']=='2'){
                $strDoor='L';
            }
        }elseif($optFormat=='1'){
            /*老師*/
            if($dataArrSD[0]['door']=='1'){
                $strDoor='L';
            }elseif($dataArrSD[0]['door']=='2'){
                $strDoor='R';
            }
        }

        //dd($dataArrSD[0]['door']);
        /*
        '範本名稱
        '種型-->A (標準型);B (馬蹄型);C (T型);D (菱型)
        'm14tb 場地基本資料檔
        'seat  座位類型 char  1  ('1')
        '1 變動座位(30人)
        '2 變動座位(40人)
        '3 固定座位
        */
        if($site=='404' OR $site=='405'){
            $strDotName='4XX'.$strRoomType;
            $strFormat='000';
            $strDoor='L';
        }elseif($site=='C14'){
            $strDotName='C14'.$strRoomType;
            $strFormat='000';
            $strDoor='R';
        }elseif($site=='303'){
            $strDotName='303'.$str303.$strRoomType;
            $strFormat='000';
            $strDoor='R';
        }else{
            $strDotName=$strRoomType.$strSeat.$strDoor;
            $strFormat='00';
        }

        //'取得報表資料t13tb+m02tb
        $sqlPAMA='';
        if($optSet=='1'){
            /*依組別*/
            $sqlPAMA=' ORDER BY sort, A.groupno, A.no ';
        }else{
            /*依學號*/
            $sqlPAMA=' ORDER BY A.no ';
        }
        $sql = "SELECT A.no AS NO,
                       RTRIM(IFNULL(B.cname,'')) AS CNAME,
                       RTRIM(A.groupno) AS GROUPNO,
                       CONCAT(RTRIM(A.groupno),'99') AS sort
                FROM t13tb  A LEFT JOIN m02tb B ON A.idno = B.idno
                WHERE A.class = '".$classes."'
                AND A.term = '".$term."'
                AND A.status='1'
                AND RTRIM(A.no)<>''
                ".$sqlPAMA;
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        //座位類型-->A (標準型);B (馬蹄型);C (T型);D (菱型)
        //排列方式-->學生 Null; 老師 T
        //教室
            //4XX(404, 405教室), L左側門
                //4XXA, 4XXAT, -->標準型-->可坐112人
                //4XXB, 4XXBT, -->馬蹄型78人
                //4XXC, 4XXCT, -->T型60人
                //4XXD, 4XXDT, -->菱型-->6*16->可坐96人
            //303教室, R右側門
                //303A, 303AT, -->可坐30人
                //303B, 303BT, -->可坐40人
            //C14 14樓貴賓廳, R右側門
                //C14A,C14AT, -->標準型-->可坐80人
                //C14B,C14BT, -->馬蹄型144人
                //C14C,C14CT, -->T型90人
                //C14D,C14DT, -->菱型 74人 -->可坐11人
            //其他教室, L左側門/R右側門, 均統一設成48可坐
                //A30L, AT30L, -->標準型30人 左側門 -->可坐36人
                //A30R, AT30R, -->標準型30人 右側門 -->可坐36人
                //A40L, AT40L, -->標準型40人 左側門
                //A40R, AT40R, -->標準型40人 右側門
                //B30L, BT30L, -->馬蹄型30人 左側門
                //B30R, BT30R, -->馬蹄型30人 右側門
                //B40L, BT40L, -->馬蹄型40人 左側門 -->可坐46人
                //B40R, BT40R, -->馬蹄型40人 右側門 -->可坐46人
                //C30L, CT30L, -->T型30人 左側門 -->可坐35人
                //C30R, CT30R, -->T型30人 右側門 -->可坐35人
                //C40L, CT40L, -->T型40人 左側門
                //C40R, CT40R, -->T型40人 右側門
                //D30L, DT30L, -->菱型30人 左側門
                //D30R, DT30R, -->菱型30人 右側門
                //D40L, DT40L, -->菱型40人 左側門 -->可坐48個人
                //D40R, DT40R, -->菱型40人 右側門 -->可坐48個人
                //共52種輸出格式範本
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'J6D'.$strDotName).'.docx');
        //取大可坐位數
        $MaxNum=48;
                if($strDotName=='4XXA' OR $strDotName=='4XXAT'){
                    $MaxNum=112;
                }elseif($strDotName=='4XXB' OR $strDotName=='4XXBT'){
                    $MaxNum=78;
                }elseif($strDotName=='4XXC' OR $strDotName=='4XXCT'){
                    $MaxNum=60;
                }elseif($strDotName=='4XXD' OR $strDotName=='4XXDT'){
                    $MaxNum=96;
                }elseif($strDotName=='303A' OR $strDotName=='303AT'){
                    $MaxNum=30;
                }elseif($strDotName=='303B' OR $strDotName=='303BT'){
                    $MaxNum=40;
                }elseif($strDotName=='C14A' OR $strDotName=='C14AT'){
                    $MaxNum=80;
                }elseif($strDotName=='C14B' OR $strDotName=='C14BT'){
                    $MaxNum=144;
                }elseif($strDotName=='C14C' OR $strDotName=='C14CT'){
                    $MaxNum=90;
                }elseif($strDotName=='C14D' OR $strDotName=='C14DT'){
                    $MaxNum=112;
                }else{
                    $MaxNum=48;
                }
                //班別：簡報表達技巧基礎研習班
                $templateProcessor->setValue('CLASSNAME', $dataArrTitle[0]['CLASSNAME']);
                //教室：201教室
                $templateProcessor->setValue('SITENAME', $dataArrTitle[0]['SITENAME']);

        if(sizeof($reportlist) != 0) {

            //其他教室, L左側門/R右側門 ,依學號排序 , 取最大48位
            $j=0;
            for($s=0;$s<$MaxNum;$s++){
                for(; $j<sizeof($dataArr); ) {
                    if($dataArr[$j]['NO']==(str_pad($s+1,3,'0',STR_PAD_LEFT))){
                        //001 廖XX
                        $templateProcessor->setValue('S'.($s+1), $dataArr[$j]['NO'].' '.$dataArr[$j]['CNAME']);
                        $j++;
                        break;
                    }else{
                        $templateProcessor->setValue('S'.($s+1),'');
                        break;
                    }
                }
                if($s+1>$j){
                    $templateProcessor->setValue('S'.($s+1),'');
                }
            }


        }else{

            //無學員
            for($s=0;$s<$MaxNum;$s++){
                $templateProcessor->setValue('S'.($s+1),'');
            }

        }

        //輸出檔名:
        //201_201教室-T型-依學號-學員
        //303_303研討室-30人-依學號-學員
        $filename=$site.'_'.$dataArrTitle[0]['CLASSNAME'].'-'.$optTypeName.'-'.$strSet.'-'.$optFormatName;

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$filename);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
    }

}
