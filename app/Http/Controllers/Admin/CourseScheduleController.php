<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
// use App\Models\T01tb;
use DB;

class CourseScheduleController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('course_schedule', $user_group_auth)){
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
        FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
        ORDER BY t04tb.class DESC");
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;
        $result = '';
        return view('admin/course_schedule/list',compact('classArr','termArr' ,'result'));
    }

    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }

    public function export(Request $request)
    {
        $class=$request->input('classes');
        $term=$request->input('terms');
        $weekpicker=$request->input('weekpicker');
        $cardselect=$request->input('cardselect');
        $weektype=$request->input('weektype');
        $area=$request->input('area');
        //$outputname="課程表";依班期-單週 雙週  依整週 台北院區 南投院區 全部
        $outputfile="課程表";
        $weekarray=array("日","一","二","三","四","五","六");
        $tdate="";
        $tcnt=0;
        $sdate="";
        $edate="";

        // //variables for phpword
      
        $cellRowSpan  = array('vMerge' => 'restart','valign' => 'center','align' => 'center'); //垂直合併
        $cellRowContinue = array('vMerge' => 'continue'); //略過
        $cellColSpan  = array('gridSpan' => 4); //水平合併
        $cellHCentered = array('align' => 'center');
        $cellVCentered = array('valign' => 'center');
        $styleTable = ['borderColor' => '000000', 'borderSize' => 6, 'cellMargin' => 80];
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('標楷體'); //設定預設字型
        $phpWord->setDefaultFontSize(12); //設定預設字型大小
        $sectionStyle = array( //上邊界 1.5cm*566.929134
            'marginTop'         => 850,
            'marginLeft'		=> 850,
            'marginRight'		=> 850,
            'marginBottom'		=> 1134);

        $section = $phpWord->addSection($sectionStyle); //建立一個區域

        if($cardselect=="1"){   //依班期

            $sql="SELECT
            (
                CASE B.branch
                WHEN '1' THEN CONCAT(IFNULL(RTRIM(C.name),''),'(臺北院區)')
                WHEN '2' THEN CONCAT(IFNULL(RTRIM(D.name),''),'(南投院區)')
                END
            ) as roomname
            FROM t04tb A
            INNER JOIN t01tb B ON A.class = B.class
            LEFT JOIN m14tb C  ON A.site = C.site
            LEFT JOIN m25tb D ON A.site = D.site
            WHERE A.class = '".$class."' AND A.term = '".$term."'";
            $temp=DB::select("$sql");

            if($temp==[])
            {
                $result="查無資料";
                $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                ORDER BY t04tb.class DESC");
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;

                return view('admin/course_schedule/list',compact('classArr','termArr' ,'result'));
            }

            $temp=json_decode(json_encode($temp), true);
            $roomname=$temp[0]["roomname"];

            $sql="select type from t01tb where class='".$class."'";
            $temp=DB::select("$sql");
            $temp=json_decode(json_encode($temp), true);
            $ctype=$temp[0]["type"];

            if($ctype=="13"){  //游於藝
                $outputfile.="-游於藝";
                $sql="select distinct * from (
                select IFNULL(t01tb.name,'') as classname,  #系列主題
                IFNULL(t01tb.object,'') as object,  #課程目標
                IFNULL(t01tb.target,'') as target,  #對象
                IFNULL(t01tb.quota,0) as quota,  #人數
                IFNULL(t04tb.fee,0) as fee,  #費用
                t06tb.course as course,  #課程編號
                t06tb.name as coursename,  #課程名稱
                t06tb.date as date,  #日期
                t06tb.stime as stime,  #開始時間
                t06tb.etime as etime,  #結束時間
                t06tb.matter as matter,  #課程內容
                CONCAT(IFNULL(RTRIM(m14tb.name),''),'(臺北院區)') as classroom,  #上課地點
                IFNULL(t04tb.lineup,'') as lineup,  #教師人數1.表一人2.表多人
                IFNULL(t04tb.remark,'') as remark,  #備註
                employ_sort.teacher_sort as teacher_sort,
                IFNULL(t08tb.cname,'') as teacher  #講座姓名
                from t06tb
                left outer join t01tb on t01tb.class=t06tb.class
                left outer join t04tb on t04tb.class=t06tb.class and t04tb.term=t06tb.term
                left outer join m14tb on m14tb.site=t04tb.site
                left outer join t08tb on t06tb.course=t08tb.course
                and t06tb.class=t08tb.class
                and t06tb.term=t08tb.term
                and t08tb.hire='Y'
                left outer join t09tb on t08tb.idno=t09tb.idno
                and t08tb.class=t09tb.class
                and t08tb.term=t09tb.term
                and t08tb.course=t09tb.course
                left outer join employ_sort on t09tb.class=employ_sort.class
                and t09tb.term=employ_sort.term
                and t09tb.idno=employ_sort.idno
                where t06tb.class='".$class."'
                and t06tb.term='".$term."'
                and t06tb.date<>''
                and t08tb.idkind<>'1'

                #英文姓名
                union all

                select IFNULL(t01tb.name,'') as classname,   #系列主題
                IFNULL(t01tb.object,'') as object,  #課程目標
                IFNULL(t01tb.target,'') as target,  #對象
                IFNULL(t01tb.quota,0) as quota,  #人數
                IFNULL(t04tb.fee,0) as fee,  #費用
                t06tb.course as course,  #課程編號
                t06tb.name as coursename,  #課程名稱
                t06tb.date as date,  #日期
                t06tb.stime as stime,  #開始時間
                t06tb.etime as etime,  #結束時間
                t06tb.matter as matter,  #課程內容
                CONCAT(IFNULL(RTRIM(m14tb.name),''),'(臺北院區)') as classroom,  #上課地點
                IFNULL(t04tb.lineup,'') as lineup,  #教師人數1.表一人2.表多人
                IFNULL(t04tb.remark,'') as remark,  #備註
                employ_sort.teacher_sort as teacher_sort,
                IFNULL(t08tb.ename,'') as teacher  #講座姓名
                from t06tb
                left outer join t01tb on t01tb.class=t06tb.class
                left outer join t04tb on t04tb.class=t06tb.class
                and t04tb.term=t06tb.term
                left outer join m14tb  on m14tb.site=t04tb.site
                left outer join t08tb on t06tb.course=t08tb.course
                and t06tb.class=t08tb.class
                and t06tb.term=t08tb.term
                and t08tb.hire='Y'
                and t08tb.idkind='1'
                left outer join t09tb on t08tb.idno=t09tb.idno
                and t08tb.class=t09tb.class
                and t08tb.term=t09tb.term
                and t08tb.course=t09tb.course
                left outer join employ_sort on t09tb.class=employ_sort.class
                and t09tb.term=employ_sort.term
                and t09tb.idno=employ_sort.idno
                where t06tb.class='".$class."'
                and t06tb.term='".$term."'
                and t06tb.date<>''
                and t08tb.idkind='1'

                #無聘任資料
                union all

                select IFNULL(t01tb.name,'') as classname,   #系列主題
                IFNULL(t01tb.object,'') as object,  #課程目標
                IFNULL(t01tb.target,'') as target,  #對象
                IFNULL(t01tb.quota,0) as quota,  #人數
                IFNULL(t04tb.fee,0) as fee,  #費用
                t06tb.course as course,  #課程編號
                t06tb.name as coursename,  #課程名稱
                t06tb.date as date,  #日期
                t06tb.stime as stime,  #開始時間
                t06tb.etime as etime,  #結束時間
                t06tb.matter as matter,  #課程內容
                CONCAT(IFNULL(RTRIM(m14tb.name),''),'(臺北院區)') as classroom,  #上課地點
                IFNULL(t04tb.lineup,'') as lineup,  #教師人數1.表一人2.表多人
                IFNULL(t04tb.remark,'') as remark,  #備註
                '' as teacher_sort,
                '' as teacher  #講座姓名
                from t06tb
                left outer join t01tb on t01tb.class=t06tb.class
                left outer join t04tb on t04tb.class=t06tb.class
                and t04tb.term=t06tb.term
                left outer join m14tb  on m14tb.site=t04tb.site
                where t06tb.class='".$class."'
                and t06tb.term='".$term."'
                and t06tb.date<>''
                and t06tb.course not in (select course from t08tb where class='".$class."' and term='".$term."')
                ) AS AA
                order by AA.date,AA.stime, ISNULL(AA.teacher_sort), AA.teacher_sort"; //20200716增加講師排序

                $temp = DB::select($sql);

                if($temp==[])
                {
                    $result="查無資料";
                    $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                    FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                    ORDER BY t04tb.class DESC");
                    $classArr=$temp;
                    $temp=json_decode(json_encode($temp), true);
                    $arraykeys=array_keys((array)$temp[0]);
                    $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                    $termArr=$temp;

                    return view('admin/course_schedule/list',compact('classArr','termArr' ,'result'));
                }

                $temp=json_decode(json_encode($temp), true);
                $lineup=$temp[0]["lineup"];
                $data=$temp;

                $section->addText('＊系列主題：'.$data[0]["classname"],array('bold' => false,'size'=>14), array('align' => 'left'));
                $section->addText('＊課程目標：'.$data[0]["object"],array('bold' => false,'size'=>14), array('align' => 'left'));
                $section->addText('＊對　　象：'.$data[0]["target"],array('bold' => false,'size'=>14), array('align' => 'left'));
                $section->addText('＊人　　數：'.$data[0]["quota"],array('bold' => false,'size'=>14), array('align' => 'left'));
                $section->addText('＊費　　用：'.$data[0]["fee"],array('bold' => false,'size'=>14), array('align' => 'left'));
                $section->addText('＊上課地點：'.$data[0]["classroom"],array('bold' => false,'size'=>14), array('align' => 'left'));
                $section->addText('＊上課時間：'.strval((int)substr($data[0]["stime"],0,2)).':'.substr($data[0]["stime"],2,2)."~".strval((int)substr($data[0]["etime"],0,2)).':'.substr($data[0]["etime"],2,2),array('bold' => false,'size'=>14), array('align' => 'left'));
             
                if($lineup=="1"){//單一講師 F7C1.docx

                    $section->addText('＊講　　座：'.$data[0]["teacher"],array('bold' => false,'size'=>14), array('align' => 'left'));
                    $section->addText('');

                    $phpWord->addTableStyle('myTable', $styleTable); //建立表格樣式
                    $table = $section->addTable('myTable'); //建立表格
                    
                    $table->addRow();
                    $table->addCell(400,$cellVCentered)->addText('節次',array('size'=>14),$cellHCentered);
                    $table->addCell(3200,$cellVCentered)->addText('日　　　期',array('size'=>14),$cellHCentered);
                    $table->addCell(900,$cellVCentered)->addText('星期',array('size'=>14),$cellHCentered);
                    $table->addCell(4500,$cellVCentered)->addText('課　　　程',array('size'=>14),$cellHCentered);
                    $table->addCell(5900,$cellVCentered)->addText('內　　　　容',array('size'=>14),$cellHCentered);
                    				
                    for($i=0;$i<sizeof($data);$i++){
                        $dnow=strval((int)substr($data[$i]["date"],0,3)+1911)."-".substr($data[$i]["date"],3,2)."-".substr($data[$i]["date"],5,2);
                        $dnowc=strval((int)substr($data[$i]["date"],3,2))."月".strval((int)substr($data[$i]["date"],5,2))."日";
                        
                        $table->addRow();

                        if($tdate!=$data[$i]["date"]){
                            $tdate=$data[$i]["date"];
                            $tcnt=1;

                            $table->addCell(400,$cellVCentered)->addText($tcnt,array('size'=>14),$cellHCentered);
                            $table->addCell(3200,$cellRowSpan)->addText($dnowc,array('size'=>14),$cellHCentered);
                            $table->addCell(900,$cellRowSpan)->addText($weekarray[date("w",strtotime($dnow))],array('size'=>14),$cellHCentered);

                        }else{
                            $tcnt++;

                            $table->addCell(400,$cellVCentered)->addText($tcnt,array('size'=>14),$cellHCentered);
                            $table->addCell(null,$cellRowContinue);
                            $table->addCell(null,$cellRowContinue);

                        }

                        $table->addCell(4500,$cellVCentered)->addText($data[$i]['coursename'],array('size'=>14));
                        $table->addCell(5900,$cellVCentered)->addText($data[$i]['matter'],array('size'=>14));

                    }

              

                }else{//多講師 F7C2.docx

                     $section->addText('');

                    $phpWord->addTableStyle('myTable', $styleTable); //建立表格樣式
                    $table = $section->addTable('myTable'); //建立表格

                    $table->addRow();

                    $table->addCell(400,$cellVCentered)->addText('節次',array('size'=>14),$cellHCentered);
                    $table->addCell(2600,$cellVCentered)->addText('日　　　期',array('size'=>14),$cellHCentered);
                    $table->addCell(900,$cellVCentered)->addText('星期',array('size'=>14),$cellHCentered);
                    $table->addCell(4000,$cellVCentered)->addText('課　　　程',array('size'=>14),$cellHCentered);
                    $table->addCell(5000,$cellVCentered)->addText('內　　　　容',array('size'=>14),$cellHCentered);
                    $table->addCell(2000,$cellVCentered)->addText('講　座',array('size'=>14),$cellHCentered);  
                    
                    				
                    for($i=0;$i<sizeof($data);$i++){
                        $dnow=strval((int)substr($data[$i]["date"],0,3)+1911)."-".substr($data[$i]["date"],3,2)."-".substr($data[$i]["date"],5,2);
                        $dnowc=strval((int)substr($data[$i]["date"],3,2))."月".strval((int)substr($data[$i]["date"],5,2))."日";
                       
                        $table->addRow();
                       
                        if($tdate!=$data[$i]["date"]){
                            $tdate=$data[$i]["date"];
                            $tcnt=1;

                            $table->addCell(400,$cellVCentered)->addText($tcnt,array('size'=>14),$cellHCentered);
                            $table->addCell(2600,$cellRowSpan)->addText($dnowc,array('size'=>14),$cellHCentered);
                            $table->addCell(900,$cellRowSpan)->addText($weekarray[date("w",strtotime($dnow))],array('size'=>14),$cellHCentered);

                        }else{
                            $tcnt++;

                            $table->addCell(400,$cellVCentered)->addText($tcnt,array('size'=>14),$cellHCentered);
                            $table->addCell(null,$cellRowContinue)->addText($dnowc,array('size'=>14));
                            $table->addCell(null,$cellRowContinue)->addText($weekarray[date("w",strtotime($dnow))],array('size'=>14));

                        }

                        $table->addCell(4000,$cellVCentered)->addText($data[$i]['coursename'],array('size'=>14));
                        $table->addCell(5000,$cellVCentered)->addText($data[$i]['matter'],array('size'=>14));
                        $table->addCell(2000,$cellVCentered)->addText($data[$i]['teacher'],array('size'=>14));

                    }


                }

            }else{//非游於藝
                $sdate=""; //取消日期條件
                $sql="SELECT
                (
                    CASE B.branch
                    WHEN '1' THEN CONCAT(IFNULL(RTRIM(C.name),''),'(臺北院區)')
                    WHEN '2' THEN CONCAT(IFNULL(RTRIM(D.name),''),'(南投院區)')
                    END
                ) as roomname
                FROM t04tb A
                INNER JOIN t01tb B ON A.class = B.class
                LEFT JOIN m14tb C  ON A.site = C.site
                LEFT JOIN m25tb D ON A.site = D.site
                WHERE A.class = '".$class."' AND A.term = '".$term."'";

                $temp = DB::select($sql);
                $temp=json_decode(json_encode($temp), true);
                $roomname=$temp[0]["roomname"];

                $sql="SELECT
                D.type,
                A.date,
                A.stime,
                CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':', SUBSTRING(A.stime,3,2)) END) ,
                       (CASE A.etime WHEN '' THEN '' ELSE  CONCAT('-',SUBSTRING(A.etime,1,2),':', SUBSTRING(A.etime,3,2)) END)) AS classtime,
                A.course as course,
                IFNULL(A.name,'') AS classname ,
                (CASE IFNULL(B.cname,'') WHEN '' THEN '' ELSE CONCAT(RTRIM(B.cname),'講座') END) AS name ,
                C.remark,
                F.teacher_sort,
                '".$roomname."' AS roomname
                FROM t06tb A
                LEFT JOIN t08tb B ON A.course = B.course AND A.class = B.class AND A.term = B.term
                LEFT JOIN t04tb C ON A.class = C.class AND A.term = C.term
                INNER JOIN t09tb D ON B.class = D.class AND B.term = D.term AND B.course = D.course AND B.idno = D.idno
                LEFT JOIN employ_sort F ON F.class = D.class and F.term = D.term and F.idno = D.idno
                WHERE A.class = '".$class."'
                AND A.term = '".$term."'
                AND B.idkind <> '1' /* 證號別 1：事業團體 */
                AND B.hire = 'Y'
                AND 1 = (
                    CASE
                    WHEN '".$sdate."' = '' THEN 1
                    WHEN A.date BETWEEN '".$sdate."' AND '".$edate."' THEN 1
                    END
                )
                UNION
                SELECT
                D.type,
                A.date,
                A.stime,
                CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2)) END),
                       (CASE A.etime WHEN '' THEN '' ELSE CONCAT('-',SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) END)) AS classtime ,
                A.course,
                IFNULL(A.name,'') AS classname,
                IFNULL(B.ename,'') AS name,
                C.remark ,
                F.teacher_sort,
                '".$roomname."' AS roomname
                FROM t06tb A LEFT JOIN t08tb B ON A.course=B.course AND A.class=B.class AND A.term=B.term
                LEFT JOIN t04tb C ON A.class=C.class AND A.term=C.term
                INNER JOIN t09tb D ON B.class = D.class AND B.term = D.term AND B.course = D.course AND B.idno = D.idno
                LEFT JOIN employ_sort F ON F.class = D.class and F.term = D.term and F.idno = D.idno
                WHERE A.class = '".$class."'
                AND A.term = '".$term."'
                AND B.idkind = '1' /* 證號別 1：事業團體 */
                AND B.hire='Y'
                AND 1 = (
                    CASE
                    WHEN '".$sdate."' = '' THEN 1
                    WHEN A.date BETWEEN '".$sdate."' AND '".$edate."' THEN 1
                    END
                )
                UNION
                SELECT
                '3' AS 'type',
                A.date,
                A.stime,
                CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2)) END),
                       (CASE A.etime WHEN '' THEN '' ELSE CONCAT('-' ,SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) END)) AS classtime ,
                A.course,
                IFNULL(A.name,'') AS classname,
                '' AS name,
                B.remark ,
                '' as teacher_sort,
                '".$roomname."' AS roomname
                FROM t06tb A
                LEFT JOIN t04tb B
                ON A.class=B.class
                AND A.term=B.term
                WHERE A.class = '".$class."'
                AND A.term='".$term."'
                AND 1 = (
                    CASE
                    WHEN '".$sdate."' = '' THEN 1
                    WHEN A.date BETWEEN '".$sdate."' AND '".$edate."' THEN 1
                    END
                ) ORDER BY date,stime,course,type, ISNULL(teacher_sort), teacher_sort"; //20200716增加講師排序

                $temp = DB::select($sql);

                if($temp==[])
                {
                    $result="查無資料";
                    $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                    FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                    ORDER BY t04tb.class DESC");
                    $classArr=$temp;
                    $temp=json_decode(json_encode($temp), true);
                    $arraykeys=array_keys((array)$temp[0]);
                    $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                    $termArr=$temp;
                    $result = '';
                    return view('admin/course_schedule/list',compact('classArr','termArr' ,'result'));
                }

                $temp=json_decode(json_encode($temp), true);
                $data=$temp;

                $temp=DB::select("SELECT name FROM  t01tb WHERE class='".$class."'");
                $temp=json_decode(json_encode($temp), true);
                $classnamet=$temp[0]["name"];

                if($weektype=="1"){ //單週
                    $outputfile.="-依班期-單週";
                    $cntarr=[];
                    $wcnt=-1;
                    $wtemp="";
                    $ctmp="";
                    $itemp="";
                    for($i=0;$i<sizeof($data);$i++){
                        if( $ctmp!=$data[$i]["course"]){ //過濾空值
                            $ctmp=$data[$i]["course"];
                            $itemp=$i ;
                        }elseif(isset($data[$i]["name"])){
                            if(trim($data[$i]["name"])!=""){
                                if($data[$i]["type"]=="2")
                                    $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />助教：".str_replace("講座", "", $data[$i]["name"]);
                                else
                                    $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />".$data[$i]["name"];
                            }
                        }
                    }

                    $ctmp="";
                    foreach($data as $k => $v){
                        if( $ctmp!=$v["course"]) //拿掉空值
                            $ctmp=$v["course"];
                        else
                            unset($data[$k]);
                    }

                    foreach($data as $v){//建立每週天數，以此為基礎填入多週報表值
                            $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                            if($wtemp!=date("W",strtotime($dnow))){
                                $wtemp=date("W",strtotime($dnow));

                                array_push($cntarr,array(
                                        "week"=>$wtemp,
                                        "rcnt"=>1
                                    ));

                                $wcnt++;
                            }else{
                                $cntarr[$wcnt]["rcnt"]++;
                            }
                    }
                    $section->addText($classnamet.'第'.strval((int)$term).'期課程表',array('bold' => false,'size'=>14), array('align' => 'center'));
                    $section->addText('教室：'.$roomname,array('bold' => false,'size'=>14), array('align' => 'right'));
                   
                    $phpWord->addTableStyle('myTable', $styleTable); //建立表格樣式
                    $table = $section->addTable('myTable'); //建立表格
                    
                    $table->addRow();

                    $table->addCell(2600,$cellVCentered)->addText('日期',array('size'=>14),$cellHCentered);
                    $table->addCell(1600,$cellVCentered)->addText('星期',array('size'=>14),$cellHCentered);
                    $table->addCell(2600,$cellVCentered)->addText('時間',array('size'=>14),$cellHCentered);
                    $table->addCell(5400,$cellVCentered)->addText('課程',array('size'=>14),$cellHCentered);
                    $table->addCell(3200,$cellVCentered)->addText('講座',array('size'=>14),$cellHCentered);

                    $wpos=0;
                    $rpos=0;
                    $cntarrcnt=0;
                    $tmpname="";
                    $ctmp="";
                    $cname="";
                    $tempdate="";

                    foreach($data as $k=>$v){
                        if($rpos<$cntarr[$wpos]["rcnt"])
                        {
                            $rpos++;
                        }else{
                            $table->addRow();
                            $table->addCell(2600,$cellRowSpan)->addText('備註',array('size'=>14),$cellHCentered);
                            $table->addCell(12800,$cellColSpan)->addText(str_replace("\n","<w:br />",$data[0]['remark']),array('size'=>12));

                            $section ->addText('');
                            $section ->addPageBreak(); //換頁

                            $section->addText($classnamet.'第'.strval((int)$term).'期課程表',array('bold' => false,'size'=>14), array('align' => 'center'));
                            $section->addText('教室：'.$roomname,array('bold' => false,'size'=>14), array('align' => 'right'));
                           
                            $phpWord->addTableStyle('myTable', $styleTable); //建立表格樣式
                            $table = $section->addTable('myTable'); //建立表格
                            
                            $table->addRow();
                            $table->addCell(2600,$cellVCentered)->addText('日期',array('size'=>14),$cellHCentered);
                            $table->addCell(1600,$cellVCentered)->addText('星期',array('size'=>14),$cellHCentered);
                            $table->addCell(2600,$cellVCentered)->addText('時間',array('size'=>14),$cellHCentered);
                            $table->addCell(5400,$cellVCentered)->addText('課程',array('size'=>14),$cellHCentered);
                            $table->addCell(3200,$cellVCentered)->addText('講座',array('size'=>14),$cellHCentered);

                            $wpos++;
                            $rpos=1;

                        }
                        $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                        $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";

                        $table->addRow();
                        if($tempdate==$dnowc){
                            $table->addCell(null,$cellRowContinue);
                            $table->addCell(null,$cellRowContinue);
                        }else{
                            $table->addCell(2600,$cellRowSpan)->addText($dnowc,array('size'=>14),$cellHCentered);
                            $table->addCell(1600,$cellRowSpan)->addText($weekarray[date("w",strtotime($dnow))],array('size'=>14),$cellHCentered);
                        }
                        $table->addCell(2600,$cellVCentered)->addText($v["classtime"],array('size'=>14),$cellHCentered);
                        $table->addCell(5400,$cellVCentered)->addText($v["classname"],array('size'=>14));
                        $table->addCell(3200,$cellVCentered)->addText($v["name"],array('size'=>14),$cellHCentered);

                        $tempdate=$dnowc;
                                       
                    }

                    $table->addRow();
              
                    $table->addCell(2600,$cellRowSpan)->addText('備註',array('size'=>14),$cellHCentered);
                    $table->addCell(12800,$cellColSpan)->addText(str_replace("\n","<w:br />",$data[0]['remark']),array('size'=>12));
                
                    $footer = $section->addFooter();
                    $footer->addText($class,array('bold' => false,'size'=>8), array('align' => 'left'));

                }else{ //雙週
                    $outputfile.="-依班期-雙週";
                    $cellColSpan  = array('gridSpan' => 5); //水平合併
                    $section->addText($classnamet.'第'.strval((int)$term).'期第1、2週課程表',array('bold' => false,'size'=>14), array('align' => 'center'));
                    $section->addText('教室：'.$roomname,array('bold' => false,'size'=>14), array('align' => 'right'));
                   
                    $phpWord->addTableStyle('myTable', $styleTable); //建立表格樣式
                    $table = $section->addTable('myTable'); //建立表格
                    
                    $table->addRow();

                    $table->addCell(800,$cellVCentered)->addText('週',array('size'=>14),$cellHCentered);
                    $table->addCell(2600,$cellVCentered)->addText('日期',array('size'=>14),$cellHCentered);
                    $table->addCell(1600,$cellVCentered)->addText('星期',array('size'=>14),$cellHCentered);
                    $table->addCell(2600,$cellVCentered)->addText('時間',array('size'=>14),$cellHCentered);
                    $table->addCell(5400,$cellVCentered)->addText('課程',array('size'=>14),$cellHCentered);
                    $table->addCell(3200,$cellVCentered)->addText('講座',array('size'=>14),$cellHCentered);

                    $titlew="";
                    $cntarr=[];
                    $wcnt=-1;
                    $wtemp="";
                    $ctmp="";
                    $itemp="";
                    for($i=0;$i<sizeof($data);$i++){
                        if( $ctmp!=$data[$i]["course"]){ //過濾空值
                            $ctmp=$data[$i]["course"];
                            $itemp=$i ;
                        }elseif(isset($data[$i]["name"])){
                            if(trim($data[$i]["name"])!=""){
                                if($data[$i]["type"]=="2")
                                    $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />助教：".str_replace("講座", "", $data[$i]["name"]);
                                else
                                    $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />".$data[$i]["name"];
                            }
                        }
                    }

                    $ctmp="";
                    foreach($data as $k => $v){
                        if( $ctmp!=$v["course"]) //拿掉空值
                            $ctmp=$v["course"];
                        else
                            unset($data[$k]);
                    }

                    foreach($data as $v){//建立每週天數，以此為基礎填入多週報表值
                        $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                        if($wtemp!=date("W",strtotime($dnow))){
                            $wtemp=date("W",strtotime($dnow));
                            $wcnt++;
                            array_push($cntarr,array(
                                    "week"=>$wcnt,
                                    "rcnt"=>1
                                ));
                        }else{
                            $cntarr[$wcnt]["rcnt"]++;
                        }
                    }

                    $wpos=0;
                    $rpos=0;
                    $cntarrcnt=0;
                    $tmpname="";
                    $ctmp="";
                    $cname="";
                    $tmpdate="";

                    $tmpwpos=-1;
                    $rpos=0;
                    $wpos=0;
                    $tempdate="";
                    foreach($data as $v){
                        if($rpos<$cntarr[$wpos]["rcnt"])
                        {
                            $rpos++;
                        }else{

                            $table->addRow();
                            $table->addCell(800,$cellRowSpan)->addText('備註',array('size'=>14),$cellHCentered);
                            $table->addCell(15400,$cellColSpan)->addText(str_replace("\n","<w:br />",$data[0]['remark']),array('size'=>12));

                            $section ->addText('');
                            $section ->addPageBreak(); //換頁

                            $wpos++;
                            $rpos=1;

                            $section->addText($classnamet.'第'.strval((int)$term).'期第'.strval($wpos*2+1)."、".strval($wpos*2+2).'週課程表',array('bold' => false,'size'=>14), array('align' => 'center'));
                            $section->addText('教室：'.$roomname,array('bold' => false,'size'=>14), array('align' => 'right'));
                           
                            $phpWord->addTableStyle('myTable', $styleTable); //建立表格樣式
                            $table = $section->addTable('myTable'); //建立表格
                            
                            $table->addRow();
        
                            $table->addCell(800,$cellVCentered)->addText('週',array('size'=>14),$cellHCentered);
                            $table->addCell(2600,$cellVCentered)->addText('日期',array('size'=>14),$cellHCentered);
                            $table->addCell(1600,$cellVCentered)->addText('星期',array('size'=>14),$cellHCentered);
                            $table->addCell(2600,$cellVCentered)->addText('時間',array('size'=>14),$cellHCentered);
                            $table->addCell(5400,$cellVCentered)->addText('課程',array('size'=>14),$cellHCentered);
                            $table->addCell(3200,$cellVCentered)->addText('講座',array('size'=>14),$cellHCentered);

                        }

                        $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                        $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";

                        $table->addRow();

                        if($tmpwpos!=$wpos){
                            $table->addCell(800,$cellRowSpan)->addText("第".strval($wpos+1)."週",array('size'=>14),$cellHCentered);
                            $tmpwpos=$wpos;
                        }else{
                            $table->addCell(null,$cellRowContinue);
                        }
                        
                        if($tempdate==$dnowc){
                            $table->addCell(null,$cellRowContinue);
                            $table->addCell(null,$cellRowContinue);
                        }else{
                            $table->addCell(2600,$cellRowSpan)->addText($dnowc,array('size'=>14),$cellHCentered);
                            $table->addCell(1600,$cellRowSpan)->addText($weekarray[date("w",strtotime($dnow))],array('size'=>14),$cellHCentered);
                        }
                        
                        $table->addCell(2600,$cellVCentered)->addText($v["classtime"],array('size'=>14),$cellHCentered);
                        $table->addCell(5400,$cellVCentered)->addText($v["classname"],array('size'=>14));
                        $table->addCell(3200,$cellVCentered)->addText($v["name"],array('size'=>14),$cellHCentered);

                        $tempdate=$dnowc;
                        
                    }

                    $table->addRow();
                    $table->addCell(800,$cellRowSpan)->addText('備註',array('size'=>14),$cellHCentered);
                    $table->addCell(15400,$cellColSpan)->addText(str_replace("\n","<w:br />",$data[0]['remark']),array('size'=>12));

                }
                $footer = $section->addFooter();
                $footer->addText($class,array('bold' => false,'size'=>8), array('align' => 'left'));
            }
        }else{  //依整週

                $classcnt=0;
                $weekpicker=$request->input('weekpicker');
                $tflag="";
                if($weekpicker!=""){
                    try {
                        $ttemp=explode(" ",$weekpicker);
                        $sdatetmp=explode("/",$ttemp[0]);
                        $edatetmp=explode("/",$ttemp[2]);
                        $sdate=$sdatetmp[0].$sdatetmp[1].$sdatetmp[2];
                        $edate=$edatetmp[0].$edatetmp[1].$edatetmp[2];
                        $tflag="1";
                        // Validate the value...
                    } catch (\Exception $e) {
                        $ttemp="error";
                }


                    if($ttemp=="error" || $sdate=="NaNundefinedundefined" )
                    {
                        $RptBasic = new \App\Rptlib\RptBasic();
                        $temp=$RptBasic->getclass();
                        $classArr=$temp;
                        $temp=json_decode(json_encode($temp), true);
                        $arraykeys=array_keys((array)$temp[0]);
                        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                        $termArr=$temp;
                        $result = "日期格式錯誤，請重新輸入。";
                        return view('admin/lecture_signature/list',compact('classArr','termArr' ,'result'));
                    }
                }


            if($area=="1"){         //台北院區
                $outputfile.="-依整週-台北院區";
            }elseif($area=="2"){    //南投院區
                $outputfile.="-依整週-南投院區";
            }else{                  //全部
                $area="";
                $outputfile.="-依整週-全部";
            }

            $sql="SELECT
                A.class,A.term,
                CONCAT(CAST(A.class AS char),RTRIM(B.name)) AS class_name
                FROM t06tb A
                INNER JOIN t01tb B
                ON A.class = B.class
                WHERE A.date BETWEEN '".$sdate."' AND '".$edate."'
                AND '".$area."' = (CASE WHEN '".$area."' = '' THEN '' ELSE B.branch END) /* @branch 上課地點 1:臺北院區 2:南投院區 */
                GROUP BY A.class,A.term,B.name
                ORDER BY A.class,A.term";

                $temp = DB::select($sql);

                if($temp==[])
                {
                    $result="查無資料";
                    $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                    FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                    ORDER BY t04tb.class DESC");
                    $classArr=$temp;
                    $temp=json_decode(json_encode($temp), true);
                    $arraykeys=array_keys((array)$temp[0]);
                    $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                    $termArr=$temp;
                    $result = '';
                    return view('admin/course_schedule/list',compact('classArr','termArr' ,'result'));
                }

                $temp=json_decode(json_encode($temp), true);
                $weekdata=$temp;
                $wsql="";
                for($i=0;$i<sizeof($weekdata);$i++){


                    $sql="SELECT
                    (
                        CASE B.branch
                        WHEN '1' THEN CONCAT(IFNULL(RTRIM(C.name),''),'(臺北院區)')
                        WHEN '2' THEN CONCAT(IFNULL(RTRIM(D.name),''),'(南投院區)')
                        END
                    ) as roomname
                    FROM t04tb A
                    INNER JOIN t01tb B ON A.class = B.class
                    LEFT JOIN m14tb C  ON A.site = C.site
                    LEFT JOIN m25tb D ON A.site = D.site
                    WHERE A.class = '".$weekdata[$i]["class"]."' AND A.term = '".$weekdata[$i]["term"]."'";

                    $temp = DB::select($sql);
                    if(sizeof($temp)==0)
                        continue;
                    $classcnt++;
                    $temp=json_decode(json_encode($temp), true);
                    $roomname=$temp[0]["roomname"];


                    //取消日期條件
                    $sql="SELECT
                    A.class,A.term,E.name AS classtermname,
                    D.type,
                    A.date,
                    A.stime,
                    CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':', SUBSTRING(A.stime,3,2)) END) ,
                        (CASE A.etime WHEN '' THEN '' ELSE  CONCAT('-',SUBSTRING(A.etime,1,2),':', SUBSTRING(A.etime,3,2)) END)) AS classtime,
                    A.course as course,
                    IFNULL(A.name,'') AS classname ,
                    (CASE IFNULL(B.cname,'') WHEN '' THEN '' ELSE CONCAT(RTRIM(B.cname),'講座') END) AS name ,
                    C.remark,
                    '".$roomname."' AS roomname
                    FROM t06tb A
                    LEFT JOIN t08tb B ON A.course = B.course AND A.class = B.class AND A.term = B.term
                    LEFT JOIN t04tb C ON A.class = C.class AND A.term = C.term
                    INNER JOIN t09tb D ON B.class = D.class AND B.term = D.term AND B.course = D.course AND B.idno = D.idno
                    LEFT JOIN t01tb E ON A.class = E.class
                    WHERE A.class = '".$weekdata[$i]["class"]."'
                    AND A.term = '".$weekdata[$i]["term"]."'
                    AND B.idkind <> '1' /* 證號別 1：事業團體 */
                    AND B.hire = 'Y'
                    UNION
                    SELECT
                    A.class,A.term,E.name AS classtermname,
                    D.type,
                    A.date,
                    A.stime,
                    CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2)) END),
                        (CASE A.etime WHEN '' THEN '' ELSE CONCAT('-',SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) END)) AS classtime ,
                    A.course,
                    IFNULL(A.name,'') AS classname,
                    IFNULL(B.ename,'') AS name,
                    C.remark ,
                    '".$roomname."' AS roomname
                    FROM t06tb A LEFT JOIN t08tb B ON A.course=B.course AND A.class=B.class AND A.term=B.term
                    LEFT JOIN t04tb C ON A.class=C.class AND A.term=C.term
                    INNER JOIN t09tb D ON B.class = D.class AND B.term = D.term AND B.course = D.course AND B.idno = D.idno
                    LEFT JOIN t01tb E ON A.class = E.class
                    WHERE A.class = '".$weekdata[$i]["class"]."'
                    AND A.term = '".$weekdata[$i]["term"]."'
                    AND B.idkind = '1' /* 證號別 1：事業團體 */
                    AND B.hire='Y'
                    UNION
                    SELECT
                    A.class,A.term,C.name AS classtermname,
                    '3' AS 'type',
                    A.date,
                    A.stime,
                    CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2)) END),
                        (CASE A.etime WHEN '' THEN '' ELSE CONCAT('-' ,SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) END)) AS classtime ,
                    A.course,
                    IFNULL(A.name,'') AS classname,
                    '' AS name,
                    B.remark ,
                    '".$roomname."' AS roomname
                    FROM t06tb A
                    LEFT JOIN t04tb B ON A.class=B.class AND A.term=B.term
                    LEFT JOIN t01tb C ON A.class = C.class
                    WHERE A.class = '".$weekdata[$i]["class"]."'
                    AND A.term='".$weekdata[$i]["term"]."'
                    ORDER BY date,stime,course,type"; //20200716增加講師排序

                    if($i==sizeof($weekdata)-1)
                        $wsql.="SELECT * FROM ( ".$sql." ) AS A".$i;
                    else
                        $wsql.="SELECT * FROM ( ".$sql." ) AS A".$i." UNION ";

                }

                $temp = DB::select($wsql);

                if($temp==[])
                {
                    $result="查無資料";
                    $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                    FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                    ORDER BY t04tb.class DESC");
                    $classArr=$temp;
                    $temp=json_decode(json_encode($temp), true);
                    $arraykeys=array_keys((array)$temp[0]);
                    $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                    $termArr=$temp;
                    $result = '';
                    return view('admin/course_schedule/list',compact('classArr','termArr' ,'result'));
                }

                $temp=json_decode(json_encode($temp), true);
                $data=$temp;


                $cntarr=[];
                $wcnt=-1;
                $wtemp="";
                $ctmp="";
                $classtmp="";
                $termtmp="";
                $itemp="";
                for($i=0;$i<sizeof($data);$i++){
                    if( $ctmp!=$data[$i]["course"]){ //過濾空值

                        if($i==0)
                        {
                            $classtmp=$data[$i]["class"];
                            $termtmp=$data[$i]["term"];
                            $ctmp=$data[$i]["course"];
                            $itemp=$i ;
                        }else{
                            if($classtmp==$data[$i]["class"] && $termtmp=$data[$i]["term"]){
                                $ctmp=$data[$i]["course"];
                                $itemp=$i ;
                            }else{
                                $classtmp=$data[$i]["class"];
                                $termtmp=$data[$i]["term"];
                                $ctmp=$data[$i]["course"];
                                $itemp=$i ;
                            }
                        }
                    }elseif(isset($data[$i]["name"]) && $classtmp==$data[$i]["class"] && $termtmp=$data[$i]["term"]){
                        if(trim($data[$i]["name"])!=""){
                            if($data[$i]["type"]=="2")
                                $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />助教：".str_replace("講座", "", $data[$i]["name"]);
                            else
                                $data[$itemp]["name"]=$data[$itemp]["name"]."<w:br />".$data[$i]["name"];
                        }
                    }
                }

                $ctmp="";
                $classtmp="";
                $termtmp="";
                foreach($data as $k => $v){
                    if($k==0)
                        {
                            $classtmp=$v["class"];
                            $termtmp=$v["term"];
                            $ctmp=$v["course"];
                        }else{
                            if($classtmp==$v["class"] && $termtmp=$v["term"]){
                                if( $ctmp!=$v["course"]) //拿掉空值
                                    $ctmp=$v["course"];
                                else
                                    unset($data[$k]);
                            }else{
                                $classtmp=$v["class"];
                                $termtmp=$v["term"];
                                $ctmp=$v["course"];
                            }
                        }
                }

                $cntarr=[];
                $ctcnt=-1;
                $classtmp="";
                $termtmp="";
                foreach($data as $v){//建立班期筆數，以此為基礎填入報表值
                        $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                        if( $classtmp!=$v["class"] || $termtmp!=$v["term"] ){
                            $classtmp=$v["class"];
                            $termtmp=$v["term"];
                            array_push($cntarr,array(
                                    "ct"=>$classtmp.$termtmp,
                                    "rcnt"=>1
                                ));

                            $ctcnt++;
                        }else{
                            $cntarr[$ctcnt]["rcnt"]++;
                        }
                }



                $wpos=0;
                $rpos=0;
                $cntarrcnt=0;
                $tmpname="";
                $ctmp="";
                $cname="";
                $tempdate="";

                foreach($data as $k=>$v){

                    if($wpos==0 && $rpos==0){
                        $section->addText($v["classtermname"].'第'.strval((int)$v["term"]).'期課程表',array('bold' => false,'size'=>14), array('align' => 'center'));
                        $section->addText('教室：'.$v["roomname"],array('bold' => false,'size'=>14), array('align' => 'right'));
                       
                        $phpWord->addTableStyle('myTable', $styleTable); //建立表格樣式
                        $table = $section->addTable('myTable'); //建立表格
                        
                        $table->addRow();
        
                        $table->addCell(2600,$cellVCentered)->addText('日期',array('size'=>14),$cellHCentered);
                        $table->addCell(1600,$cellVCentered)->addText('星期',array('size'=>14),$cellHCentered);
                        $table->addCell(2600,$cellVCentered)->addText('時間',array('size'=>14),$cellHCentered);
                        $table->addCell(5400,$cellVCentered)->addText('課程',array('size'=>14),$cellHCentered);
                        $table->addCell(3200,$cellVCentered)->addText('講座',array('size'=>14),$cellHCentered);
                    }


                    if($rpos<$cntarr[$wpos]["rcnt"])
                    {

                        $rpos++;
                    }else{
                        $table->addRow();
                        $table->addCell(2600,$cellRowSpan)->addText('備註',array('size'=>14),$cellHCentered);
                        $table->addCell(12800,$cellColSpan)->addText(str_replace("\n","<w:br />",$data[0]['remark']),array('size'=>12));

                        $section ->addText('');
                        $section ->addPageBreak(); //換頁

                        $section->addText($v["classtermname"].'第'.strval((int)$v["term"]).'期課程表',array('bold' => false,'size'=>14), array('align' => 'center'));
                        $section->addText('教室：'.$v["roomname"],array('bold' => false,'size'=>14), array('align' => 'right'));
                       
                        $phpWord->addTableStyle('myTable', $styleTable); //建立表格樣式
                        $table = $section->addTable('myTable'); //建立表格
                        
                        $table->addRow();
                        $table->addCell(2600,$cellVCentered)->addText('日期',array('size'=>14),$cellHCentered);
                        $table->addCell(1600,$cellVCentered)->addText('星期',array('size'=>14),$cellHCentered);
                        $table->addCell(2600,$cellVCentered)->addText('時間',array('size'=>14),$cellHCentered);
                        $table->addCell(5400,$cellVCentered)->addText('課程',array('size'=>14),$cellHCentered);
                        $table->addCell(3200,$cellVCentered)->addText('講座',array('size'=>14),$cellHCentered);

                        $wpos++;
                        $rpos=1;

                    }
                    $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                    $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";

                    $table->addRow();
                    if($tempdate==$dnowc){
                        $table->addCell(null,$cellRowContinue);
                        $table->addCell(null,$cellRowContinue);
                    }else{
                        $table->addCell(2600,$cellRowSpan)->addText($dnowc,array('size'=>14),$cellHCentered);
                        $table->addCell(1600,$cellRowSpan)->addText($weekarray[date("w",strtotime($dnow))],array('size'=>14),$cellHCentered);
                    }
                    $table->addCell(2600,$cellVCentered)->addText($v["classtime"],array('size'=>14),$cellHCentered);
                    $table->addCell(5400,$cellVCentered)->addText($v["classname"],array('size'=>14));
                    $table->addCell(3200,$cellVCentered)->addText($v["name"],array('size'=>14),$cellHCentered);

                    $tempdate=$dnowc;
                                   
                }

                $table->addRow();
                $table->addCell(2600,$cellRowSpan)->addText('備註',array('size'=>14),$cellHCentered);
                $table->addCell(12800,$cellColSpan)->addText(str_replace("\n","<w:br />",$data[0]['remark']),array('size'=>12));
            
                $footer = $section->addFooter();
                $footer->addText($class,array('bold' => false,'size'=>8), array('align' => 'left'));



                // if(sizeof($cntarr)==1){
                //     $i=0;
                //     foreach($data as $v){

                //             if($i=0){
                //                 $templateProcessor->setValue('remark#'.strval($i+1),$v["remark"]);
                //                 $templateProcessor->setValue('class#'.strval($i+1),$v["class"]);
                //                 $templateProcessor->setValue('classname#'.strval($i+1),$v["classtermname"]);
                //                 $templateProcessor->setValue('term#'.strval($i+1),strval((int)$v["term"]));
                //                 $templateProcessor->setValue('roomname#'.strval($i+1),$v["roomname"]);

                //             }
                //             $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                //             $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";
                //             $templateProcessor->setValue('date#1#'.strval($i+1),$dnowc);
                //             $templateProcessor->setValue('wdate#1#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
                //             $templateProcessor->setValue('time#1#'.strval($i+1),$v["classtime"]);
                //             $templateProcessor->setValue('course#1#'.strval($i+1),$v["classname"]);
                //             $templateProcessor->setValue('lec#1#'.strval($i+1),$v["name"]);

                //         $i++;
                //     }

                // }else{

                //     foreach($data as $v){
                //         if($ctpos==0)
                //             $templateProcessor->setValue('remark#'.strval($ctpos+1),$v["remark"]);
                //             $templateProcessor->setValue('class#'.strval($ctpos+1),$class);
                //             $templateProcessor->setValue('classname#'.strval($ctpos+1),$v["classtermname"]);
                //             $templateProcessor->setValue('term#'.strval($ctpos+1),strval((int)$v["term"]));
                //             $templateProcessor->setValue('roomname#'.strval($ctpos+1),$v["roomname"]);
                //         if($rpos<$cntarr[$ctpos]["rcnt"])
                //         {
                //             $rpos++;
                //         }else{

                //             $ctpos++;
                //             $rpos=1;
                //             $templateProcessor->setValue('remark#'.strval($ctpos+1),$v["remark"]);
                //         }
                //         $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                //         $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";
                //         $templateProcessor->setValue('date#'.strval($ctpos+1).'#'.$rpos,$dnowc);
                //         $templateProcessor->setValue('wdate#'.strval($ctpos+1).'#'.$rpos,$weekarray[date("w",strtotime($dnow))]);
                //         $templateProcessor->setValue('time#'.strval($ctpos+1).'#'.$rpos,$v["classtime"]);
                //         $templateProcessor->setValue('course#'.strval($ctpos+1).'#'.$rpos,$v["classname"]);
                //         $templateProcessor->setValue('lec#'.strval($ctpos+1).'#'.$rpos,$v["name"]);
                //     }
                // }
        }
        
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objWriter,"4",$request->input('doctype'),$outputfile);
        //$obj: entity of file
        //$objtype:1.templateProcessor 2.PhpSpreadsheet 3.PhpExcel 4.PhpWord
        //$doctype:1.ooxml 2.odf
        //$filename:filename

    }
}
