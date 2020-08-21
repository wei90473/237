<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use \PhpOffice\PhpWord\PhpWord;
use \PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\User_groupService;

class StudentSeatListSouthController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_seat_list_south', $user_group_auth)){
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

                $result = '';
                return view('admin/student_seat_list_south/list',compact('classArr','termArr','result'));
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

    public function export(Request $request)
    {
        //班別
        $classes = $request->input('classes');
        //期別
        $term = $request->input('term');
        $site = $request->input('site'); //教室
        $blockarr =explode(",", $request->input('blocks'));
        //'座位類型-->A (標準型);B (馬蹄型);C (T型);D (工型)
        $optType = $request->input('optType'); //座位類型
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
        $sitename="";
        $templatefile='';
        $filename='';

        if($classes=="0"||$term=="0"||$site=="0"){
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getclass();
            $classArr=$temp;
            $ctemp=json_decode(json_encode($temp), true);
            $carraykeys=array_keys((array)$ctemp[0]);

            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$ctemp[0]["class"]."'");
            $termArr=$temp;
            $ttemp=json_decode(json_encode($temp), true);

            $result = '請選擇班期別與教室。';
            return view('admin/student_seat_list_south/list',compact('classArr','termArr','result'));
        }

        //'取得報表資料t13tb+m02tb
        $sqlPAMA='';
        if($optSet=='1'){
            /*依組別*/
            $strSet = '依組別';
            $sqlPAMA=' ORDER BY sort, A.groupno, A.no ';
        }else{
            /*依學號*/
            $strSet = '依學號';
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

        //班別, 教室
        $sqlTITLE ="SELECT DISTINCT RTRIM(A.name) AS CLASSNAME
                    FROM t01tb A WHERE A.class = '".$classes."'";
        $reportlistTitle = DB::select($sqlTITLE);
        $dataArrTitle = json_decode(json_encode($reportlistTitle), true);

        if($site=="901"){

            $sitename="國際會議廳";

            //範本位置
            $filePath = '../example/'.iconv('UTF-8', 'GBK', 'J6S001').'.xlsx';
            //讀取excel
            $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $objActSheet = $objPHPExcel->getActiveSheet();

            $colstart=2;
            $rowstart=5;
            $colend=29;
            $colindex=2;
            $rowindex=5;
            $rowblock=array(10,16);
            $colblock=array(9,22);
            $blocks=array("B5","AC5");

            foreach( $blockarr as $b ){
                array_push($blocks,$b);
            }

            for($i=0;$i<362;$i++){
                $pos="";
                while($pos==""){
                    if(in_array($rowindex,$rowblock)){
                        $rowindex++;
                        continue;
                    }
                    if(in_array($colindex,$colblock)){
                        $colindex++;
                        continue;
                    }
                    if(in_array($this->getNameFromNumber($colindex).$rowindex,$blocks)){
                        $objActSheet->setCellValue($this->getNameFromNumber($colindex).$rowindex,"");
                        $colindex++;
                        continue;
                    }
                    if($colindex==30){
                        $colindex=2;
                        $rowindex++;
                        continue;
                    }
                    $pos=$this->getNameFromNumber($colindex).$rowindex;
                    $colindex++;
                }
                
                if($pos=="AD20"||$rowindex==21)
                    break;

                $objActSheet->setCellValue($pos, isset($dataArr[$i])?$dataArr[$i]['NO']."\n".$dataArr[$i]['CNAME']:"");

            }
            if($optFormat=='0'){
                $optFormatName='學員';
            }elseif($optFormat=='1'){
                $strTemp = $strTemp.'T';
                $optFormatName='講座';
            }

            $filename=$site.'_'.$dataArrTitle[0]['CLASSNAME'].'--'.$strSet.'-'.$optFormatName;

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"$filename");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 

        }else{

            switch ($site) {
                case "001":
                    $sitename="電腦教室";
                    if($optFormat=='0'){
                        $optFormatName='學員';
                        $templatefile="J6SA40S";
                    }elseif($optFormat=='1'){
                        $strTemp = $strTemp.'T';
                        $optFormatName='講座';
                        $templatefile="J6SA40T";
                    }

                  break;
                case "701":
                    $sitename="701教室";
                    if($optFormat=='0'){
                        $optFormatName='學員';
                        $templatefile="J6S701S";
                    }elseif($optFormat=='1'){
                        $strTemp = $strTemp.'T';
                        $optFormatName='講座';
                        $templatefile="J6S701T";
                    }

                  break;
                case "702":
                    $sitename="702教室";
                    if($optFormat=='0'){
                        $optFormatName='學員';
                        $templatefile="J6S702S";
                    }elseif($optFormat=='1'){
                        $strTemp = $strTemp.'T';
                        $optFormatName='講座';
                        $templatefile="J6S702T";
                    }

                  break;
                case "703":
                    $sitename="703教室";
                    if($optFormat=='0'){
                        $optFormatName='學員';
                        $templatefile="J6S703S";
                    }elseif($optFormat=='1'){
                        $strTemp = $strTemp.'T';
                        $optFormatName='講座';
                        $templatefile="J6S703T";
                    }

                  break;
                case "801":
                    $sitename="研討室";
                    if($optFormat=='0'){
                        $optFormatName='學員';
                        $templatefile="J6SBS";
                    }elseif($optFormat=='1'){
                        $strTemp = $strTemp.'T';
                        $optFormatName='講座';
                        $templatefile="J6SBT";
                    }

                  break;

                default: //"501"||"503"||"504"||"601"||"602"||"603"||"604"

                    if($site=="501"){
                        $sitename="501教室";
                    }elseif($site=="503"){
                        $sitename="503教室";
                    }elseif($site=="504"){
                        $sitename="504教室";
                    }elseif($site=="601"){
                        $sitename="601教室";
                    }elseif($site=="602"){
                        $sitename="602教室";
                    }elseif($site=="603"){
                        $sitename="603教室";
                    }elseif($site=="604"){
                        $sitename="604教室";
                    }

                    if($optFormat=='0'){
                        $optFormatName='學員';
                        switch($optType){
                            case "A":
                                $optTypeName = '標準型';
                                $templatefile="J6SA40S";
                                break;
                            case "B":
                                $optTypeName ='-T型';
                                $templatefile="J6SCS";
                                break;
                            case "C":
                                $optTypeName ='-工型';
                                $templatefile="J6SDS";
                                break;
                        }
                
                    }elseif($optFormat=='1'){
                        $strTemp = $strTemp.'T';
                        $optFormatName='講座';
                        switch($optType){
                            case "A":
                                $optTypeName = '標準型';
                                $templatefile="J6SA40T";
                                break;
                            case "B":
                                $optTypeName ='-T型';
                                $templatefile="J6SCT";
                                break;
                            case "C":
                                $optTypeName ='-工型';
                                $templatefile="J6SDT";
                                break;
                        }
                    }
                  
              }
              $strRoomType = $strTemp;

            // if($site!='303'){
            //     //'座位類型-->A (標準型);B (馬蹄型);C (T型);D (工型)
            //     $strTemp = $optType;
            //     if($optType=='A'){
            //         $optTypeName = '標準型';
            //     }elseif($optType=='B'){
            //         $optTypeName ='馬蹄型';
            //     }elseif($optType=='C'){
            //         $optTypeName ='-T型';
            //     }elseif($optType=='D'){
            //         $optTypeName ='-工型';
            //     }
            // }else{
            //     if($seat=='1'){
            //         $str303='A';
            //     }elseif($seat=='2'){
            //         $str303='B';
            //     }
            // }

            //列印格式
            //'排列方式-->'0:學生; 1:老師




            //取得m14tb.seat, m14tb.door的資料
            //'seat  座位類型 char  1  ('1') 1:變動座位(30人)2:變動座位(40人)3:固定座位
            //'door  教室門口 char  1  ('1') 1:右側  2:左側
            // $sqlSD = "SELECT seat, door FROM m14tb WHERE site='".$site."'
            //                 ";
            // $reportlistSD = DB::select($sqlSD);
            // $dataArrSD = json_decode(json_encode($reportlistSD), true);

            /*
            'm14tb 場地基本資料檔
            'seat  座位類型 char  1  ('1')
            '1 變動座位(30人)
            '2 變動座位(40人)
            '3 固定座位
            '4 變動座位(80人)
            '5 變動座位(90人)
            */

            // if($dataArrSD[0]['seat']=='1'){
            //     $strTemp = '30';
            //     $optTypeName = '30人';

            // }elseif($dataArrSD[0]['seat']=='2'){
            //     $strTemp = '40';
            //     $optTypeName = '40人';
            // }
            // $strSeat=$strTemp;

            //'教室門口－－＞1:右側；2:左側
            // if($optFormat=='0'){
            //     /*學生*/
            //     if($dataArrSD[0]['door']=='1'){
            //         $strDoor='R';
            //     }elseif($dataArrSD[0]['door']=='2'){
            //         $strDoor='L';
            //     }
            // }elseif($optFormat=='1'){
            //     /*老師*/
            //     if($dataArrSD[0]['door']=='1'){
            //         $strDoor='L';
            //     }elseif($dataArrSD[0]['door']=='2'){
            //         $strDoor='R';
            //     }
            // }

            //dd($dataArrSD[0]['door']);
            /*
            '範本名稱
            '種型-->A (標準型);B (馬蹄型);C (T型);D (工型)
            'm14tb 場地基本資料檔
            'seat  座位類型 char  1  ('1')
            '1 變動座位(30人)
            '2 變動座位(40人)
            '3 固定座位
            */
            // if($site=='404' OR $site=='405'){
            //     $strDotName='4XX'.$strRoomType;
            //     $strFormat='000';
            //     $strDoor='L';
            // }elseif($site=='C14'){
            //     $strDotName='C14'.$strRoomType;
            //     $strFormat='000';
            //     $strDoor='R';
            // }elseif($site=='303'){
            //     $strDotName='303'.$str303.$strRoomType;
            //     $strFormat='000';
            //     $strDoor='R';
            // }else{
            //     $strDotName=$strRoomType.$strSeat.$strDoor;
            //     $strFormat='00';
            // }

            

            // A (標準型)、B (馬蹄型)、C (T型)、D (工型)
            // T (講座方向)、S (學員方向)
            // ===
            // 電腦教室
            // 座位類型：固定座位A40S/A40T
            // 501教室
            // 座位類型：標準A40S/A40T、T型CS/CT、工型DS/DT
            // 503教室
            // 座位類型：標準A40S/A40T、T型CS/CT、工型DS/DT
            // 504教室
            // 座位類型：標準A40S/A40T、T型CS/CT、工型DS/DT
            // 601教室
            // 座位類型：標準A40S/A40T、T型CS/CT、工型DS/DT
            // 602教室
            // 座位類型：標準A40S/A40T、T型CS/CT、工型DS/DT
            // 603教室
            // 座位類型：標準A40S/A40T、T型CS/CT、工型DS/DT
            // 604教室
            // 座位類型：標準A40S/A40T、T型CS/CT、工型DS/DT
            // 701教室
            // 座位類型：六角桌座位701S、701T
            // 702教室
            // 座位類型：固定座位702S、702T
            // 703教室
            // 座位類型：固定座位703S、703T
            // 研討室
            // 座位類型：馬蹄型座位BS、BT
            // 國際會議廳
            // 	座位類型：固定座位001STAGE

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $templatefile).'.docx');
            //取大可坐位數
            $MaxNum=108;
                    // if($strDotName=='4XXA' OR $strDotName=='4XXAT'){
                    //     $MaxNum=112;
                    // }elseif($strDotName=='4XXB' OR $strDotName=='4XXBT'){
                    //     $MaxNum=78;
                    // }elseif($strDotName=='4XXC' OR $strDotName=='4XXCT'){
                    //     $MaxNum=60;
                    // }elseif($strDotName=='4XXD' OR $strDotName=='4XXDT'){
                    //     $MaxNum=96;
                    // }elseif($strDotName=='303A' OR $strDotName=='303AT'){
                    //     $MaxNum=30;
                    // }elseif($strDotName=='303B' OR $strDotName=='303BT'){
                    //     $MaxNum=40;
                    // }elseif($strDotName=='C14A' OR $strDotName=='C14AT'){
                    //     $MaxNum=80;
                    // }elseif($strDotName=='C14B' OR $strDotName=='C14BT'){
                    //     $MaxNum=144;
                    // }elseif($strDotName=='C14C' OR $strDotName=='C14CT'){
                    //     $MaxNum=90;
                    // }elseif($strDotName=='C14D' OR $strDotName=='C14DT'){
                    //     $MaxNum=112;
                    // }else{
                    //     $MaxNum=48;
                    // }
                    //班別：簡報表達技巧基礎研習班
                    $templateProcessor->setValue('CLASSNAME', $dataArrTitle[0]['CLASSNAME']);
                    //教室：201教室
                    $templateProcessor->setValue('SITENAME',$sitename);

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

}
