<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Mail;
use App\Mail\H3;
use App\Services\User_groupService;
//use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Rptlib\OfficeConverterTool;
use App\Services\EmployService;

use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\M09tb;
// use App\Models\Setting;
// use Config;


class LectureMailController extends Controller
{
    public function __construct(EmployService $employService,User_groupService $user_groupService)
    {
        $this->employService = $employService;
        $this->user_groupService = $user_groupService;
      //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_mail', $user_group_auth)){
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
                return view('admin/lecture_mail/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }



    public function detail($class,$term)
    {

  
        $queryData['class'] = $class;
        $queryData['term'] = $term;
        $class_mail_data = array();
        $class_mail_data['title'] = '講座聘函';
        $class_mail_data['content'] = '
        
        <p style="margin-left:32px; text-align:center"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">行政院人事行政總處公務人力發展學院□講座聘請通知</span></span></span></p>

        <p style="margin-left:38px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">敬愛的 {講座姓名} 講座，您好：</span></span></span></p>

        <p style="margin-left:38px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">素仰貴講座學驗俱豐，碩望卓著，特敦聘您擔任講座，相關資訊如下：</span></span></span></p>

        <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="font-family:&quot;新細明體&quot;,serif">            {課程資訊}   </span></span></span></p>

        <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">三、</span> <strong><span style="font-family:&quot;新細明體&quot;,serif">為尊重多元價值，並維繫文官體系行政中立之基本分際，請於授課或演</span></strong><strong><span style="font-family:&quot;新細明體&quot;,serif">講時避免有影響行政中立之論述。</span></strong></span></span></p>

        <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">四、</span> <strong><span style="font-family:&quot;新細明體&quot;,serif">為提升學習成效，務請貴講座視課程需要運用多元教學方法授課，並協</span></strong><strong><span style="font-family:&quot;新細明體&quot;,serif">助填復教學教法調查表(如附件)。</span></strong></span></span></p>

        <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">五、</span> <span style="font-family:&quot;新細明體&quot;,serif">有關講義教材敬請於<strong>授課10天前</strong>以電子郵件傳送或逕寄班務人員，俾彙整編印，教材將由本學院妥為收存。</span></span></span></p>

        <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">六、</span> <span style="font-family:&quot;新細明體&quot;,serif">研習課程表、參訓人員名冊、○○院區交通位置圖等相關資訊，請至本學院全球資訊網(網址：</span><a href="http://www.hrd.gov.tw" style="color:#0563c1; text-decoration:underline"><span style="font-family:&quot;新細明體&quot;,serif">http://www.hrd.gov.tw</span></a><span style="font-family:&quot;新細明體&quot;,serif">)</span><span style="font-family:&quot;新細明體&quot;,serif">點選「講座」身分後登入查閱。</span></span></span></p>

        <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">七、</span> <span style="font-family:&quot;新細明體&quot;,serif">有關教學資源及其他設施服務資訊，請按此<u>連結</u>逕行參閱。</span></span></span></p>

        <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">八、</span> <span style="font-family:&quot;新細明體&quot;,serif">本學院聯絡方式如下：</br> {承辦人員聯絡資訊} </span></span></span></p>

       
    
        
        
        ';


        
        // <p style="margin-left:32px; text-align:center"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">行政院人事行政總處公務人力發展學院□講座聘請通知</span></span></span></p>

        // <p style="margin-left:38px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">敬愛的 {講座姓名} 講座，您好：</span></span></span></p>

        // <p style="margin-left:38px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">素仰貴講座學驗俱豐，碩望卓著，特敦聘您擔任講座，相關資訊如下：</span></span></span></p>

        // <p style="margin-left:38px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">一、</span> <span style="font-family:&quot;新細明體&quot;,serif"><span style="color:red">○○年度○○○○○○○○第○○期，共計○○人參訓，授課時間如下：</span></span></span></span></p>

        // <p style="margin-left:38px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif"><span style="color:red">(</span></span><span style="font-family:&quot;新細明體&quot;,serif"><span style="color:red">一) (課程名稱)：○○年○○月○○日○○時○○分至○○時○○分</span></span></span></span></p>

        // <p style="margin-left:38px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif"><span style="color:red">(</span></span><span style="font-family:&quot;新細明體&quot;,serif"><span style="color:red">二) (課程名稱)：○○年○○月○○日○○時○○分至○○時○○分</span></span></span></span></p>

        // <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">二、</span> <span style="font-family:&quot;新細明體&quot;,serif">研習地點：本學院 (○○院區) ＜棟別＞○○樓○○教室</span></span></span></p>

        // <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">&nbsp;&nbsp;&nbsp;&nbsp; (</span><span style="font-family:&quot;新細明體&quot;,serif">○○○○○○○○○號)。</span></span></span></p>

        // <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">三、</span> <strong><span style="font-family:&quot;新細明體&quot;,serif">為尊重多元價值，並維繫文官體系行政中立之基本分際，請於授課或演</span></strong><strong><span style="font-family:&quot;新細明體&quot;,serif">講時避免有影響行政中立之論述。</span></strong></span></span></p>

        // <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">四、</span> <strong><span style="font-family:&quot;新細明體&quot;,serif">為提升學習成效，務請貴講座視課程需要運用多元教學方法授課，並協</span></strong><strong><span style="font-family:&quot;新細明體&quot;,serif">助填復教學教法調查表(如附件)。</span></strong></span></span></p>

        // <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">五、</span> <span style="font-family:&quot;新細明體&quot;,serif">有關講義教材敬請於<strong>授課10天前</strong>以電子郵件傳送或逕寄班務人員，俾彙整編印，教材將由本學院妥為收存。</span></span></span></p>

        // <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">六、</span> <span style="font-family:&quot;新細明體&quot;,serif">研習課程表、參訓人員名冊、○○院區交通位置圖等相關資訊，請至本學院全球資訊網(網址：</span><a href="http://www.hrd.gov.tw" style="color:#0563c1; text-decoration:underline"><span style="font-family:&quot;新細明體&quot;,serif">http://www.hrd.gov.tw</span></a><span style="font-family:&quot;新細明體&quot;,serif">)</span><span style="font-family:&quot;新細明體&quot;,serif">點選「講座」身分後登入查閱。</span></span></span></p>

        // <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">七、</span> <span style="font-family:&quot;新細明體&quot;,serif">有關教學資源及其他設施服務資訊，請按此<u>連結</u>逕行參閱。</span></span></span></p>

        // <p style="margin-left:38px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">八、</span> <span style="font-family:&quot;新細明體&quot;,serif">本學院聯絡方式如下：</span></span></span></p>

        // <p style="margin-left:32px; margin-right:6px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (</span><span style="font-family:&quot;新細明體&quot;,serif">一)</span> <span style="font-family:&quot;新細明體&quot;,serif">聯絡人：○○○組○○○，電話：( ○○○ ) ○○○○○○</span> <span style="font-family:&quot;新細明體&quot;,serif">轉</span></span></span></p>

        // <p style="margin-left:32px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">○○○</span></span></span></p>

        // <p style="margin-left:32px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif"><span style="font-family:&quot;新細明體&quot;,serif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (</span><span style="font-family:&quot;新細明體&quot;,serif">二) E-mail：○○○@hrd.gov.tw</span></span></span></p>

        $class_data =  $queryData;

  
        $sql="SELECT   B.cname, B.email, A.idno
        FROM t08tb A   INNER JOIN m01tb B   ON A.idno=B.idno
        WHERE
        A.class='". $queryData['class']."'
        AND  A.term='".$queryData['term']."'
        AND A.hire='Y'
        AND B.email<>''
        GROUP BY B.cname,B.email, A.idno
        ORDER BY A.idno ";
        $teacher_list = DB::select($sql);

  
        // $class_data = $this->noticeEmailService->getClass($queryData);
        // $mail_data = $this->noticeEmailService->getTTLMailData($queryData);
        // if(!empty($mail_data)){
        //     if(!empty($mail_data['title'])){
        //         $class_mail_data['title'] = $mail_data['title'];
        //     }
        //     if(!empty($mail_data['content'])){
        //         $class_mail_data['content'] = $mail_data['content'];
        //     }
        //     if(!empty($mail_data['date'])){
        //         $class_mail_data['date'] = (date("Y",strtotime($mail_data['date']))-1911).date("/m/d H:i",strtotime($mail_data['date']));
        //     }
        // }

        return view('admin/lecture_mail/detail', compact( 'class_mail_data','class_data','teacher_list'));
    }



    /* 學員名冊 */

    public function student_list($classes,$term){

        //1:參訓人員名冊(已序號), 2:參訓人員名冊(未含序號), 3:結訓人員名冊 , 4:最新學員名冊
        $outputtype = 4;

        //1:組別選項 checked value:1
        $checkteam = '1';
        //是否依組別分頁: Y是, N否
        $grouptype = 'N';
        //2:學歷  checked value:1
        $checkedu = 1;
        //3:出生日期 checked value:1
        $checkbirth = 1;
        
        //取得Title
        $sqlTitle=" SELECT DISTINCT A.class,
                            CONCAT(RTRIM(A.name), '第',
                                (CASE B.term
                                            WHEN '01' THEN '1'
                                            WHEN '02' THEN '2'
                                            WHEN '03' THEN '3'
                                            WHEN '04' THEN '4'
                                            WHEN '05' THEN '5'
                                            WHEN '06' THEN '6'
                                            WHEN '07' THEN '7'
                                            WHEN '08' THEN '8'
                                            WHEN '09' THEN '9'
                                ELSE B.term END),'期') AS classname,
                            B.sdate, B.edate , A.type, A.special
                    FROM t01tb A LEFT JOIN t04tb B ON A.class = B.class
                    WHERE EXISTS (SELECT * FROM t04tb C WHERE A.class=C.class)
                    AND B.class= '".$classes."'
                    AND B.term= '".$term."'
                    ";
        $reportlistTitle = DB::select($sqlTitle);
        $dataArrTitle=json_decode(json_encode(DB::select($sqlTitle)), true);
        //取出全部項目
        //if(sizeof($reportlistTitle) != 0) {
        //    $arraykeysTitle=array_keys((array)$reportlistTitle[0]);
        //}

        $sqlPara="";
        if($outputtype=='1'){
            $sqlPara=$sqlPara." AND A.no<>'' ";
        }
        if($outputtype=='3' or $outputtype=='4'){
            $sqlPara=$sqlPara." AND A.status='1' ";
        }
        if($checkteam=='1' && $grouptype=='Y'){
            $sqlPara=$sqlPara." ORDER BY A.groupno,A.no ";
        }else{
            $sqlPara=$sqlPara." ORDER BY A.no ";
        }

        //取得 學員名冊
        $sql="SELECT A.no,
                        B.dept,
                        CONCAT(SUBSTRING(B.cname,1,1),'O',SUBSTRING(B.cname,-1)) AS cname,
                        B.position,
                            CONCAT(SUBSTRING(B.birth,1,3),'/',SUBSTRING(B.birth,4,2),'/',SUBSTRING(B.birth,6,2)) AS birth,
                            (CASE B.sex WHEN 'F' THEN '女' WHEN 'M' THEN '男' ELSE '' END) AS sex,
                        B.education,
                            CONCAT((CASE A.extradorm WHEN 'Y' THEN '*' WHEN 'N' THEN '' ELSE '' END),
                                        (CASE A.dorm WHEN 'Y' THEN '是' WHEN 'N' THEN ''  ELSE '' END)) AS dorm,
                        (CASE vegan WHEN 'Y' THEN '是' WHEN 'N' THEN '' ELSE '' END) AS vegan,
                            (CASE C.mboard WHEN '1' THEN '三餐' WHEN '2' THEN '三餐' WHEN '3' THEN '午餐' ELSE '' END) AS mboard,
                        A.groupno,
                            (CASE race WHEN '1' THEN '現職' WHEN '2' THEN '退休' WHEN '3' THEN '里民' ELSE '' END) AS race
                FROM t13tb A LEFT JOIN m02tb B ON A.idno=B.idno
                            LEFT JOIN t40tb C ON A.class=C.class AND A.term=C.term AND A.idno=C.idno
                WHERE A.class= '".$classes."'
                    AND A.term= '".$term."'
                    ".$sqlPara;
        $reportlist = DB::select($sql);
        $dataArr=json_decode(json_encode(DB::select($sql)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'J2';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet = $objPHPExcel->getSheet(0);

        $reportlist = json_decode(json_encode($reportlist), true);
        $reportlistTitle = json_decode(json_encode($reportlistTitle), true);

        $filenameC ='';
        if($outputtype=='1'){
            $filenameC ='參訓人員名冊';
        }elseif($outputtype=='2'){
            $filenameC ='參訓人員名冊';
        }elseif($outputtype=='3'){
            $filenameC ='結訓人員名冊';
        }elseif($outputtype=='4'){
            $filenameC ='最新學員名冊';
        }

        //分頁分組顯示
        if($checkteam=='1' && $grouptype=='Y'){
            $groupno='';
            //分頁
            $pageline=0;
            //$startborder=3;

            if(sizeof($reportlist) != 0) {
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    if($j==0){
                        $groupno=$reportlist[$j][$arraykeys[10]];
                        //簡報表達技巧基礎研習班第1期參訓人員名冊
                        $objActSheet->setCellValue('A1', $dataArrTitle[0]['classname'].$filenameC);
                        $objActSheet->mergeCells('A1:L1');
                        //107.05.09 至 107.05.09　(第2組)
                        if($reportlist[$j][$arraykeys[10]]<>''){
                            $objActSheet->setCellValue('A2', substr($dataArrTitle[0]['sdate'],0,3).'.'.substr($dataArrTitle[0]['sdate'],3,2).'.'.substr($dataArrTitle[0]['sdate'],5,2).' 至 '.substr($dataArrTitle[0]['edate'],0,3).'.'.substr($dataArrTitle[0]['edate'],3,2).'.'.substr($dataArrTitle[0]['edate'],5,2).'　(第'.$reportlist[$j][$arraykeys[10]].'組)');
                        }else{
                            $objActSheet->setCellValue('A2', substr($dataArrTitle[0]['sdate'],0,3).'.'.substr($dataArrTitle[0]['sdate'],3,2).'.'.substr($dataArrTitle[0]['sdate'],5,2).' 至 '.substr($dataArrTitle[0]['edate'],0,3).'.'.substr($dataArrTitle[0]['edate'],3,2).'.'.substr($dataArrTitle[0]['edate'],5,2));
                        }
                        $objActSheet->mergeCells('A2:L2');
                        $startborder=3;
                    }
                    if($reportlist[$j][$arraykeys[10]]<>$groupno && $reportlist[$j][$arraykeys[10]]<>''){
                        //簡報表達技巧基礎研習班第1期參訓人員名冊
                        $objActSheet->setCellValue('A'.($j+4+$pageline), $dataArrTitle[0]['classname'].$filenameC);
                        $objActSheet->mergeCells('A'.($j+4+$pageline).':L'.($j+4+$pageline));
                        $objActSheet->getRowDimension($j+4+$pageline)->setRowHeight(30);  //高 30
                        $objActSheet->getStyle('A'.($j+4+$pageline).':L'.($j+4+$pageline))->getFont()->setSize(14);
                        //107.05.09 至 107.05.09　(第2組)
                        if($reportlist[$j][$arraykeys[10]]<>''){
                            $objActSheet->setCellValue('A'.($j+4+$pageline+1), substr($dataArrTitle[0]['sdate'],0,3).'.'.substr($dataArrTitle[0]['sdate'],3,2).'.'.substr($dataArrTitle[0]['sdate'],5,2).' 至 '.substr($dataArrTitle[0]['edate'],0,3).'.'.substr($dataArrTitle[0]['edate'],3,2).'.'.substr($dataArrTitle[0]['edate'],5,2).'　(第'.$reportlist[$j][$arraykeys[10]].'組)');
                        }else{
                            $objActSheet->setCellValue('A'.($j+4+$pageline+1), substr($dataArrTitle[0]['sdate'],0,3).'.'.substr($dataArrTitle[0]['sdate'],3,2).'.'.substr($dataArrTitle[0]['sdate'],5,2).' 至 '.substr($dataArrTitle[0]['edate'],0,3).'.'.substr($dataArrTitle[0]['edate'],3,2).'.'.substr($dataArrTitle[0]['edate'],5,2));
                        }
                        $objActSheet->mergeCells('A'.($j+4+$pageline+1).':L'.($j+4+$pageline+1));
                        $objActSheet->getRowDimension($j+4+$pageline+1)->setRowHeight(20); //高 20
                        $objActSheet->getStyle('A'.($j+4+$pageline+1).':L'.($j+4+$pageline+1))->getFont()->setSize(12);
                        //項目列
                        $objActSheet->setCellValue('A'.($j+4+$pageline+2),'學號');
                        $objActSheet->setCellValue('B'.($j+4+$pageline+2),'服　務　機　關');
                        $objActSheet->setCellValue('C'.($j+4+$pageline+2),'姓名');
                        $objActSheet->setCellValue('D'.($j+4+$pageline+2),'職稱');
                        $objActSheet->setCellValue('E'.($j+4+$pageline+2),'出生日期');
                        $objActSheet->setCellValue('F'.($j+4+$pageline+2),'性別');
                        $objActSheet->setCellValue('G'.($j+4+$pageline+2),'學        歷');
                        $objActSheet->setCellValue('H'.($j+4+$pageline+2),'住宿');
                        $objActSheet->setCellValue('I'.($j+4+$pageline+2),'素食');
                        $objActSheet->setCellValue('J'.($j+4+$pageline+2),'膳食');
                        $objActSheet->setCellValue('K'.($j+4+$pageline+2),'組別');
                        $objActSheet->setCellValue('L'.($j+4+$pageline+2),'學員分類');
                        $styleCenter = array(
                            'alignment' => array(
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            )
                        );
                        $objActSheet->getStyle('A'.($j+4+$pageline+2).':L'.($j+4+$pageline+2))->applyFromArray($styleCenter);
                        $arrayStyle = [
                            'borders' => [
                        //只有外框           'outline' => [
                                    'allBorders'=> [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ];
                        $objActSheet->getStyle('A'.($j+4+$pageline+2).':L'.($j+4+$pageline+2))->applyFromArray($arrayStyle);
                        //$startborder=($j+4+$pageline+2);
                        $pageline=$pageline+3;
                    }
                    for ($i=0; $i < sizeof($arraykeys); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+1);
                        if($i==4){
                            $objActSheet->setCellValue($NameFromNumber.($j+4+$pageline), ltrim($reportlist[$j][$arraykeys[$i]],'0'));
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+4+$pageline), $reportlist[$j][$arraykeys[$i]]);
                        }
                        $arrayStyle = [
                            'borders' => [
                        //只有外框           'outline' => [
                                    'allBorders'=> [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ];
                        $objActSheet->getStyle($NameFromNumber.($j+4+$pageline))->applyFromArray($arrayStyle);
                    }

                    $groupno=$reportlist[$j][$arraykeys[10]];
                }
            }
        }else{
            if(sizeof($reportlist) != 0) {
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    for ($i=0; $i < sizeof($arraykeys); $i++) {
                        //excel 欄位 1 == A, etc
                        $NameFromNumber=$this->getNameFromNumber($i+1);
                        if($i==4){
                            $objActSheet->setCellValue($NameFromNumber.($j+4), ltrim($reportlist[$j][$arraykeys[$i]],'0'));
                        }else{
                            $objActSheet->setCellValue($NameFromNumber.($j+4), $reportlist[$j][$arraykeys[$i]]);
                        }
                    }
                }
                $arrayStyle = [
                    'borders' => [
                //只有外框           'outline' => [
                            'allBorders'=> [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];
                $objActSheet->getStyle('A4:L'.($j+3))->applyFromArray($arrayStyle);
            }
        }

        /*
        '訓練班別與委辦班別(t01tb. special= Y)與
        '游於藝講堂班別(t01tb.type <> 13)之格式不同。
        'PS: 游於藝講堂班別不可加印「組別」。
        */
        if($dataArrTitle[0]['special']=='Y'){
            $objActSheet->getColumnDimension('L')->setVisible(false); //學員分類
            //if($checkteam!='1'){
                $objActSheet->getColumnDimension('K')->setVisible(false); //組別
            // }
        }elseif($dataArrTitle[0]['special']=='13'){
            $objActSheet->getColumnDimension('H')->setVisible(false); //住宿
            $objActSheet->getColumnDimension('I')->setVisible(false); //素食
            $objActSheet->getColumnDimension('J')->setVisible(false); //膳食
            //if($checkteam!='1'){
                $objActSheet->getColumnDimension('K')->setVisible(false); //組別
            //}
        }else{
            $objActSheet->getColumnDimension('J')->setVisible(false); //膳食
            $objActSheet->getColumnDimension('L')->setVisible(false); //學員分類
            if($checkteam!='1'){
                $objActSheet->getColumnDimension('K')->setVisible(false); //組別
            }elseif($grouptype=='Y'){
                $objActSheet->getColumnDimension('K')->setVisible(false); //組別
            }
        }

        if($checkbirth!='1'){
            $objActSheet->getColumnDimension('E')->setVisible(false); //出生日期
        }

        if($checkedu!='1'){
            $objActSheet->getColumnDimension('G')->setVisible(false); //學歷
        }

        if(!($checkteam=='1' && $grouptype=='Y')){
            //簡報表達技巧基礎研習班第1期參訓人員名冊
            $objActSheet->setCellValue('A1', $dataArrTitle[0]['classname'].$filenameC);
            $objActSheet->mergeCells('A1:L1');
            //107.01.17 至 107.01.19
            $objActSheet->setCellValue('A2', substr($dataArrTitle[0]['sdate'],0,3).'.'.substr($dataArrTitle[0]['sdate'],3,2).'.'.substr($dataArrTitle[0]['sdate'],5,2).' 至 '.substr($dataArrTitle[0]['edate'],0,3).'.'.substr($dataArrTitle[0]['edate'],3,2).'.'.substr($dataArrTitle[0]['edate'],5,2));
            $objActSheet->mergeCells('A2:L2');
        }

        // 設定下載 Excel 的檔案名稱

        if($checkteam=='1'){
            $filenameC =$filenameC.'-含組別';
        }
        if($checkteam=='1' && $grouptype=='Y'){
            $filenameC =$filenameC.'-依組別分頁';
        }

        if($checkteam=='1' && $checkedu=='1'){
            $filenameC =$filenameC.'、學歷';
        }elseif($checkedu=='1'){
            $filenameC =$filenameC.'-學歷';
        }

        if($checkteam=='1' && $checkedu=='1' && $checkbirth=='1'){
            $filenameC =$filenameC.'、出生日期';
        }elseif($checkteam=='1' && $checkbirth=='1'){
            $filenameC =$filenameC.'、出生日期';
        }elseif($checkedu=='1' && $checkbirth=='1'){
            $filenameC =$filenameC.'、出生日期';
        }elseif($checkbirth=='1'){
            $filenameC =$filenameC.'-出生日期';
        }

      
    
        
        $officeConverterTool = new OfficeConverterTool();
        $today_filepath  =  $officeConverterTool->today_filepath();

        $r_filename = $today_filepath.DS.time().'.xlsx';
        $outfilename =''; //不設定新名稱則沿用原來擋案名稱
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $objWriter->save($r_filename); //先把檔案存起來
        $new_file = $officeConverterTool->Converter2OtherFileType($r_filename,$outfilename,'pdf');
        return $new_file;
    }



    /* 課程表 */
    public function course_schedule_list($class,$term){
        {
    
            // $weekpicker=$request->input('weekpicker');
            // $cardselect=$request->input('cardselect');
            $cardselect=1;
            $weektype=1;
            $area=3;
            //$outputname="課程表";依班期-單週 雙週  依整週 台北院區 南投院區 全部
            $outputfile="課程表";
            $weekarray=array("日","一","二","三","四","五","六");
            $tdate="";
            $tcnt=0;
            $sdate="";
            $edate="";
    
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
                // $result="查無資料";
                // $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                // FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                // ORDER BY t04tb.class DESC");
                // $classArr=$temp;
                // $temp=json_decode(json_encode($temp), true);
                // $arraykeys=array_keys((array)$temp[0]);
                // $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                // $termArr=$temp;
    
                // return '';
            }
    
            $temp=json_decode(json_encode($temp), true);
            $roomname=$temp[0]["roomname"];
    
            if($cardselect=="1"){   //依班期
    
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
                    order by AA.date,AA.stime";
    
                    $temp = DB::select($sql);
    
                    if($temp==[])
                    {
                        // $result="查無資料";
                        // $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                        // FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                        // ORDER BY t04tb.class DESC");
                        // $classArr=$temp;
                        // $temp=json_decode(json_encode($temp), true);
                        // $arraykeys=array_keys((array)$temp[0]);
                        // $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                        // $termArr=$temp;
    
                        // return '';
                    }
    
                    $temp=json_decode(json_encode($temp), true);
                    $lineup=$temp[0]["lineup"];
                    $data=$temp;
    
                    if($lineup=="1"){//單一講師 F7C1.docx
    
                    // 讀檔案
                    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F7C1').'.docx');
                    ini_set('pcre.backtrack_limit', 999999999);
                    $templateProcessor->setValue('classname',$data[0]["classname"]);
                    $templateProcessor->setValue('object',$data[0]["object"]);
                    $templateProcessor->setValue('target',$data[0]["target"]);
                    $templateProcessor->setValue('quota',$data[0]["quota"]);
                    $templateProcessor->setValue('fee',$data[0]["fee"]);
                    $templateProcessor->setValue('classroom',$data[0]["classroom"]);
                    $templateProcessor->setValue('stime',strval((int)substr($data[0]["stime"],0,2)).':'.substr($data[0]["stime"],2,2));
                    $templateProcessor->setValue('etime',strval((int)substr($data[0]["etime"],0,2)).':'.substr($data[0]["etime"],2,2));
                    $templateProcessor->setValue('teacher',$data[0]["teacher"]);
                    $templateProcessor->cloneRow('c', sizeof($data));
    
    
                    for($i=0;$i<sizeof($data);$i++){
                        $dnow=strval((int)substr($data[$i]["date"],0,3)+1911)."-".substr($data[$i]["date"],3,2)."-".substr($data[$i]["date"],5,2);
                        $dnowc=substr($data[$i]["date"],0,3)."-".substr($data[$i]["date"],3,2)."-".substr($data[$i]["date"],5,2);
                        if($tdate!=$data[$i]["date"]){
                            $tdate=$data[$i]["date"];
                            $tcnt=1;
                        }else{
                            $tcnt++;
                        }
                        $templateProcessor->setValue('c#'.strval($i+1),$tcnt);
                        $templateProcessor->setValue('date#'.strval($i+1),$dnowc);
                        $templateProcessor->setValue('w#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
                        $templateProcessor->setValue('course#'.strval($i+1),$data[$i]["coursename"]);
                        $templateProcessor->setValue('matter#'.strval($i+1),$data[$i]["matter"]);
                    }
    
                    }else{//多講師 F7C2.docx
                        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F7C2').'.docx');
                        $templateProcessor->setValue('classname',$data[0]["classname"]);
                        $templateProcessor->setValue('object',$data[0]["object"]);
                        $templateProcessor->setValue('target',$data[0]["target"]);
                        $templateProcessor->setValue('quota',$data[0]["quota"]);
                        $templateProcessor->setValue('fee',$data[0]["fee"]);
                        $templateProcessor->setValue('classroom',$data[0]["classroom"]);
                        $templateProcessor->setValue('stime',strval((int)substr($data[0]["stime"],0,2)).':'.substr($data[0]["stime"],2,2));
                        $templateProcessor->setValue('etime',strval((int)substr($data[0]["etime"],0,2)).':'.substr($data[0]["etime"],2,2));
                        $templateProcessor->cloneRow('c', sizeof($data));
    
                        for($i=0;$i<sizeof($data);$i++){
                            $dnow=strval((int)substr($data[$i]["date"],0,3)+1911)."-".substr($data[$i]["date"],3,2)."-".substr($data[$i]["date"],5,2);
                            $dnowc=substr($data[$i]["date"],0,3)."-".substr($data[$i]["date"],3,2)."-".substr($data[$i]["date"],5,2);
                            if($tdate!=$data[$i]["date"]){
                                $tdate=$data[$i]["date"];
                                $tcnt=1;
                            }else{
                                $tcnt++;
                            }
                            $templateProcessor->setValue('c#'.strval($i+1),$tcnt);
                            $templateProcessor->setValue('date#'.strval($i+1),$dnowc);
                            $templateProcessor->setValue('w#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
                            $templateProcessor->setValue('course#'.strval($i+1),$data[$i]["coursename"]);
                            $templateProcessor->setValue('matter#'.strval($i+1),$data[$i]["matter"]);
                            $templateProcessor->setValue('teacher#'.strval($i+1),$data[$i]["teacher"]);
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
                    CONCAT((CASE A.stime WHEN '' THEN '' ELSE CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2)) END),
                           (CASE A.etime WHEN '' THEN '' ELSE CONCAT('-' ,SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) END)) AS classtime ,
                    A.course,
                    IFNULL(A.name,'') AS classname,
                    '' AS name,
                    B.remark ,
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
                    ) ORDER BY date,course,type";
    
                    $temp = DB::select($sql);
    
                    if($temp==[])
                    {
                        // $result="查無資料";
                        // $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                        // FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                        // ORDER BY t04tb.class DESC");
                        // $classArr=$temp;
                        // $temp=json_decode(json_encode($temp), true);
                        // $arraykeys=array_keys((array)$temp[0]);
                        // $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                        // $termArr=$temp;
                        // $result = '';
                        // return '';
                    }
    
                    $temp=json_decode(json_encode($temp), true);
                    $data=$temp;
    
                    $temp=DB::select("SELECT name FROM  t01tb WHERE class='".$class."'");
                    $temp=json_decode(json_encode($temp), true);
                    $classnamet=$temp[0]["name"];
    
                    if($weektype=="1"){ //單週
                        $outputfile.="-依班期-單週";
                        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F7B').'.docx');
                        $templateProcessor->setValue('class',$class);
                        $templateProcessor->setValue('classname',$classnamet);
                        $templateProcessor->setValue('term',strval((int)$term));
                        $templateProcessor->setValue('roomname',$roomname);
    
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
    
                        $templateProcessor->cloneBlock('b',sizeof($cntarr), true, true);
    
                        for($i=0;$i<sizeof($cntarr);$i++){
                            $templateProcessor->cloneRow('date#'.strval($i+1), (int)$cntarr[$i]["rcnt"]);
                            if($i<(sizeof($cntarr)-1))
                                $templateProcessor->setValue('pagebreak#'.strval($i+1), '</w:t></w:r>'.'<w:r><w:br w:type="page"/></w:r>'.'<w:r><w:t>');
                            else
                                $templateProcessor->setValue('pagebreak#'.strval($i+1), '');
                        }
                        $wpos=0;
                        $rpos=0;
                        $cntarrcnt=0;
                        $tmpname="";
                        $ctmp="";
                        $cname="";
                        if(sizeof($cntarr)==1){
                            $i=0;
                            foreach($data as $v){
    
                                    if($i==0)
                                        $templateProcessor->setValue('remark#'.strval($i+1),$v["remark"]);
    
                                    $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                    $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";
                                    $templateProcessor->setValue('date#1#'.strval($i+1),$dnowc);
                                    $templateProcessor->setValue('wdate#1#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
                                    $templateProcessor->setValue('time#1#'.strval($i+1),$v["classtime"]);
                                    $templateProcessor->setValue('course#1#'.strval($i+1),$v["classname"]);
                                    $templateProcessor->setValue('lec#1#'.strval($i+1),$v["name"]);
    
                                $i++;
                            }
    
                        }else{
    
                            foreach($data as $v){
                                if($wpos==0)
                                    $templateProcessor->setValue('remark#'.strval($wpos+1),$v["remark"]);
                                if($rpos<$cntarr[$wpos]["rcnt"])
                                {
                                    $rpos++;
                                }else{
    
                                    $wpos++;
                                    $rpos=1;
                                    $templateProcessor->setValue('remark#'.strval($wpos+1),$v["remark"]);
                                }
    
                                $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                $dnowc=substr($v["date"],0,3)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                $templateProcessor->setValue('date#'.strval($wpos+1).'#'.$rpos,$dnow);
                                $templateProcessor->setValue('wdate#'.strval($wpos+1).'#'.$rpos,$weekarray[date("w",strtotime($dnow))]);
                                $templateProcessor->setValue('time#'.strval($wpos+1).'#'.$rpos,$v["classtime"]);
                                $templateProcessor->setValue('course#'.strval($wpos+1).'#'.$rpos,$v["classname"]);
                                $templateProcessor->setValue('lec#'.strval($wpos+1).'#'.$rpos,$v["name"]);
    
                            }
                        }
    
                    }else{ //雙週
                        $outputfile.="-依班期-雙週";
                        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F7A').'.docx');
                        $templateProcessor->setValue('class',$class);
                        $templateProcessor->setValue('classname',$classnamet);
                        $templateProcessor->setValue('term',strval((int)$term));
                        $templateProcessor->setValue('roomname',$roomname);
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
    
                        $templateProcessor->cloneBlock('b',sizeof($cntarr), true, true);
    
                        for($i=0;$i<sizeof($cntarr);$i++){
                            $templateProcessor->cloneRow('w#'.strval($i+1), (int)$cntarr[$i]["rcnt"]);
                            $templateProcessor->setValue('titlew#'.strval($i+1),strval($i*2+1)."、".strval($i*2+2));
                            if($i<(sizeof($cntarr)-1))
                                $templateProcessor->setValue('pagebreak#'.strval($i+1), '</w:t></w:r>'.'<w:r><w:br w:type="page"/></w:r>'.'<w:r><w:t>');
                            else
                                $templateProcessor->setValue('pagebreak#'.strval($i+1), '');
                        }
                        $wpos=0;
                        $rpos=0;
                        $cntarrcnt=0;
                        $tmpname="";
                        $ctmp="";
                        $cname="";
                        $tmpdate="";
                        if(sizeof($cntarr)==1){
                            $i=0;
                            foreach($data as $v){
                                    $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                    $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";
                                    if($i==0){
                                        $templateProcessor->setValue('remark#'.strval($i+1),$v["remark"]);
                                        $templateProcessor->setValue('w#1#'.strval($i+1),"第".strval($wpos+1)."週");
                                    }else{
                                        $templateProcessor->setValue('w#1#'.strval($i+1),"");
                                    }
    
                                        $templateProcessor->setValue('date#1#'.strval($i+1),$dnowc);
                                        $templateProcessor->setValue('wdate#1#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
    
                                    $templateProcessor->setValue('time#1#'.strval($i+1),$v["classtime"]);
                                    $templateProcessor->setValue('course#1#'.strval($i+1),$v["classname"]);
                                    $templateProcessor->setValue('lec#1#'.strval($i+1), $v["name"]);
    
                                $i++;
                            }
                        }else{
                            $tmpwpos=-1;
                            $rpos=0;
                            $wpos=0;
                            foreach($data as $v){
    
                                if($rpos<$cntarr[$wpos]["rcnt"])
                                {
                                    $rpos++;
                                }else{
                                    $wpos++;
                                    $rpos=1;
                                }
    
                                $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                $dnowc=substr($v["date"],0,3)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
    
                                if($tmpwpos!=$wpos){
                                    $templateProcessor->setValue('remark#'.strval($wpos+1),$v["remark"]);
                                    $templateProcessor->setValue('w#'.strval($wpos+1).'#'.$rpos,"第".strval($wpos+1)."週");
                                    $tmpwpos=$wpos;
                                }
                                else{
                                    $templateProcessor->setValue('w#'.strval($wpos+1).'#'.$rpos,"");
                                }
                                $templateProcessor->setValue('date#'.strval($wpos+1).'#'.$rpos,$dnowc);
                                $templateProcessor->setValue('wdate#'.strval($wpos+1).'#'.$rpos,$weekarray[date("w",strtotime($dnow))]);
                                $templateProcessor->setValue('time#'.strval($wpos+1).'#'.$rpos,$v["classtime"]);
                                $templateProcessor->setValue('course#'.strval($wpos+1).'#'.$rpos,$v["classname"]);
                                $templateProcessor->setValue('lec#'.strval($wpos+1).'#'.$rpos,$v["name"]);
    
                            }
    
                        }
                    }
    
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
                        // $result="查無資料";
                        // $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                        // FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                        // ORDER BY t04tb.class DESC");
                        // $classArr=$temp;
                        // $temp=json_decode(json_encode($temp), true);
                        // $arraykeys=array_keys((array)$temp[0]);
                        // $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                        // $termArr=$temp;
                        // $result = '';
                        // return '';
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
                        ORDER BY date,course,type";
    
                        if($i==sizeof($weekdata)-1)
                            $wsql.="SELECT * FROM ( ".$sql." ) AS A".$i;
                        else
                            $wsql.="SELECT * FROM ( ".$sql." ) AS A".$i." UNION ";
    
                    }
    
                    $temp = DB::select($wsql);
    
                    if($temp==[])
                    {
                        // $result="查無資料";
                        // $temp=DB::select("SELECT DISTINCT t04tb.class, t01tb.name
                        // FROM t04tb INNER JOIN t01tb ON t04tb.class = t01tb.class
                        // ORDER BY t04tb.class DESC");
                        // $classArr=$temp;
                        // $temp=json_decode(json_encode($temp), true);
                        // $arraykeys=array_keys((array)$temp[0]);
                        // $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                        // $termArr=$temp;
                        // $result = '';
                        // return '';
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
    
                    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'F7B').'.docx');
                    $templateProcessor->setValue('class', '');
                    $templateProcessor->cloneBlock('b',sizeof($cntarr), true, true);
    
                    for($i=0;$i<sizeof($cntarr);$i++){
                        $templateProcessor->cloneRow('date#'.strval($i+1), (int)$cntarr[$i]["rcnt"]);
                        if($i<(sizeof($cntarr)-1))
                            $templateProcessor->setValue('pagebreak#'.strval($i+1), '</w:t></w:r>'.'<w:r><w:br w:type="page"/></w:r>'.'<w:r><w:t>');
                        else
                            $templateProcessor->setValue('pagebreak#'.strval($i+1), '');
                    }
                    $ctpos=0;
                    $rpos=0;
                    $cntarrcnt=0;
                    $tmpname="";
                    $ctmp="";
                    $cname="";
                    if(sizeof($cntarr)==1){
                        $i=0;
                        foreach($data as $v){
    
                                if($i=0){
                                    $templateProcessor->setValue('remark#'.strval($i+1),$v["remark"]);
                                    $templateProcessor->setValue('class#'.strval($i+1),$v["class"]);
                                    $templateProcessor->setValue('classname#'.strval($i+1),$v["classtermname"]);
                                    $templateProcessor->setValue('term#'.strval($i+1),strval((int)$v["term"]));
                                    $templateProcessor->setValue('roomname#'.strval($i+1),$v["roomname"]);
    
                                }
                                $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                                $dnowc=strval((int)substr($v["date"],3,2))."月".strval((int)substr($v["date"],5,2))."日";
                                $templateProcessor->setValue('date#1#'.strval($i+1),$dnowc);
                                $templateProcessor->setValue('wdate#1#'.strval($i+1),$weekarray[date("w",strtotime($dnow))]);
                                $templateProcessor->setValue('time#1#'.strval($i+1),$v["classtime"]);
                                $templateProcessor->setValue('course#1#'.strval($i+1),$v["classname"]);
                                $templateProcessor->setValue('lec#1#'.strval($i+1),$v["name"]);
    
                            $i++;
                        }
    
                    }else{
    
                        foreach($data as $v){
                            if($ctpos==0)
                                $templateProcessor->setValue('remark#'.strval($ctpos+1),$v["remark"]);
                                $templateProcessor->setValue('class#'.strval($ctpos+1),$class);
                                $templateProcessor->setValue('classname#'.strval($ctpos+1),$v["classtermname"]);
                                $templateProcessor->setValue('term#'.strval($ctpos+1),strval((int)$v["term"]));
                                $templateProcessor->setValue('roomname#'.strval($ctpos+1),$v["roomname"]);
                            if($rpos<$cntarr[$ctpos]["rcnt"])
                            {
                                $rpos++;
                            }else{
    
                                $ctpos++;
                                $rpos=1;
                                $templateProcessor->setValue('remark#'.strval($ctpos+1),$v["remark"]);
                            }
                            $dnow=strval((int)substr($v["date"],0,3)+1911)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                            $dnowc=substr($v["date"],0,3)."-".substr($v["date"],3,2)."-".substr($v["date"],5,2);
                            $templateProcessor->setValue('date#'.strval($ctpos+1).'#'.$rpos,$dnow);
                            $templateProcessor->setValue('wdate#'.strval($ctpos+1).'#'.$rpos,$weekarray[date("w",strtotime($dnow))]);
                            $templateProcessor->setValue('time#'.strval($ctpos+1).'#'.$rpos,$v["classtime"]);
                            $templateProcessor->setValue('course#'.strval($ctpos+1).'#'.$rpos,$v["classname"]);
                            $templateProcessor->setValue('lec#'.strval($ctpos+1).'#'.$rpos,$v["name"]);
                        }
                    }
            }
    

            $officeConverterTool = new OfficeConverterTool();
            $today_filepath  =  $officeConverterTool->today_filepath();
            $r_filename = $today_filepath.DS.$class.$term.time().'.xlsx';
            $outfilename =''; //不設定新名稱則沿用原來擋案名稱
            $templateProcessor->saveAs($r_filename); //先把檔案存起來
            $new_file = $officeConverterTool->Converter2OtherFileType($r_filename,$outfilename,'pdf');
            return $new_file;


        }
    }


    public function teacherinfo($classes,$term,$filetype,$teacher_name){

        //班別：1, 講座姓名：2, 空白表格：3, 個資授權書:4
        $type = $filetype;
        $lname = $teacher_name;
        //1:講座, 2:助理
        $formtype = 1;

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
                    $templateProcessor->setValue('USERNAME', '');
                    $templateProcessor->setValue('TEL', '');
                    $templateProcessor->setValue('FAX', '');
                    $templateProcessor->setValue('EMAIL', '');
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
                $templateProcessor->setValue('USERNAME', '');
                $templateProcessor->setValue('TEL', '');
                $templateProcessor->setValue('FAX', '');
                $templateProcessor->setValue('EMAIL', '');
            }
        }

        $outputfile= '';

        if($type=='1'){
            $outputfile="講師基本資料表-依班期.docx";
        }elseif($type=='2'){
            $outputfile="講師基本資料表-依班期講師姓名.docx";
        }elseif($type=='3'){
            //1:講座, 2:助理
            if($formtype=='1'){
                $outputfile="講師基本資料表-空白表格-講座.docx";
            }else{
                $outputfile="講師基本資料表-空白表格-助理.docx";
            }
        }elseif($type=='4'){
            $outputfile="講師基本資料表-空白表格-個資授權書.docx";
        }


        $officeConverterTool = new OfficeConverterTool();
        $today_filepath  =  $officeConverterTool->today_filepath();
        $r_filename = $today_filepath.DS.$classes.$term.time().'2.xlsx';
        $outfilename =''; //不設定新名稱則沿用原來擋案名稱
        $templateProcessor->saveAs($r_filename); //先把檔案存起來
        $new_file = $officeConverterTool->Converter2OtherFileType($r_filename,$outfilename,'pdf');
        return $new_file;

   }

   public function authorizepaper($class,$term){
  

       $sql="SELECT
       B.branch, /* 上課地點 1:臺北院區 2:南投院區 */
       LEFT(A.class,3) AS yerly,
       CONCAT(RTRIM(B.name),'第',CAST(CAST(A.term AS int) AS char),'期') AS class_name_term,
       /* 訓期 */
       CONCAT(SUBSTRING(A.sdate,1,3),'年',
                      CAST(CAST(SUBSTRING(A.sdate,4,2) AS int) AS char),'月',
                      CAST(CAST(SUBSTRING(A.sdate,6,2) AS int) AS char),'日至',
                      CAST(CAST(SUBSTRING(A.edate,4,2) AS int) AS char),'月',
                      CAST(CAST(SUBSTRING(A.edate,6,2) AS int) AS char),'日') AS sate_edate,
       /* 參訓人數 */
           CAST(A.quota as char) AS quota,
       /* 上課地點 */
           (
                 CASE
                  WHEN B.branch = '1' THEN CONCAT('本學院(臺北院區)',
                                           (
                                            CASE
                                             WHEN D.site IN ('C01','C02') THEN '集會棟' /* 前瞻廳、卓越堂 */
                                             WHEN D.site = 'C14'          THEN ''
                                             WHEN D.site = '406'          THEN '住宿棟'
                                             ELSE '教學棟'                                /* 教學棟各樓層教室 */
                                            END
                                           ),
                                           (
                                            CASE
                                             WHEN D.site = 'C14' THEN ''
                                             WHEN CAST(SUBSTRING(D.site,1,1) AS INT) = 1 THEN SUBSTRING(D.site,1,1) + '樓'
                                             ELSE ''
                                             END
                                           ),
                                           RTRIM(D.name),
                                           '（臺北市大安區新生南路3段30號）')
                  WHEN B.branch = '2' AND E.site LIKE '9%' THEN RTRIM(E.name)
                  WHEN B.branch = '2' THEN CONCAT('本學院(南投院區)',
                                           (
                                            CASE
                                             WHEN E.site IN ('111','212') THEN '詠晴園' /* Zoom I、Zoom II */
                                             ELSE '文教大樓'
                                            END
                                           ),
                                           (
                                            CASE
                                             WHEN E.site = '001' THEN '1樓' /* 國際會議廳 */
                                             WHEN CAST(SUBSTRING(E.site,1,1) AS INT) = 1 THEN SUBSTRING(E.site,1,1) + '樓'
                                             ELSE ''
                                            END
                                           ),
                                           RTRIM(E.name),
                                           '（南投市光明路1號）')
                  ELSE ''
                 END
                ) AS site,
       CONCAT(RTRIM(C.section),RTRIM(C.username)) AS user_name, /* 承辦人 */
           (
                CASE B.branch
                 WHEN '1' THEN '（02）83691399轉'+RTRIM(C.ext)
                 WHEN '2' THEN '（049）2332131轉'+RTRIM(C.ext)
                 ELSE ''
                END
               ) AS tel1,
           RTRIM(IFNULL(C.email,'')) AS email,
           (
                CASE B.branch
                 WHEN '1' THEN '（02）83691399轉'+C.ext
                 WHEN '2' THEN '（049）2332131轉'+C.ext
                 ELSE ''
                END
               ) AS tel2,
           (
                CASE B.branch
                 WHEN '1' THEN (
                                CASE C.section
                                 WHEN '綜合規劃組' THEN '（02）83695616'
                                 WHEN '培育發展組' THEN '（02）83695611'
                                 WHEN '專業訓練組' THEN '（02）83695615'
                                 WHEN '秘書室'     THEN '（02）83695613'
                                 WHEN '人事室'     THEN '（02）83695618'
                                 WHEN '主計室'     THEN '（02）83695619'
                                 ELSE '（02）83695616'
                                END
                               )
                 WHEN '2' THEN (
                                CASE C.section
                                 WHEN '綜合規劃組' THEN '（049）2332723'
                                 WHEN '培育發展組' THEN '（049）2370962'
                                 WHEN '專業訓練組' THEN '（049）2332724'
                                 WHEN '數位學習組' THEN '（049）2352979'
                                 WHEN '秘書室'     THEN '（049）2351627'
                                 WHEN '人事室'     THEN '（049）2359871'
                                 WHEN '主計室'     THEN '（049）2359871'
                                 ELSE '（049）2339030'
                                END
                               )
                 ELSE ''
                END
               ) AS fax
       FROM t04tb A
       INNER JOIN t01tb B ON A.class = B.class
       LEFT JOIN m09tb C ON A.sponsor = C.userid
       LEFT JOIN m14tb D ON A.site = D.site
       LEFT JOIN m25tb E ON A.site = E.site
       WHERE A.class = '".$class."'
       AND A.term = '".$term."'";
       $temp=DB::select($sql);

       $data=json_decode(json_encode($temp), true);
       // 讀檔案
       $filename="H3B";
       $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $filename).'.docx');
       $outputfile="教材著作授權使用同意書.docx";

       $templateProcessor->setValue('y',$data[0]["yerly"]);
       $templateProcessor->setValue('classname',$data[0]["class_name_term"]);

       

       $officeConverterTool = new OfficeConverterTool();
       $today_filepath  =  $officeConverterTool->today_filepath();
       $r_filename = $today_filepath.DS.$class.$term.time().'3.xlsx';
       $outfilename =''; //不設定新名稱則沿用原來擋案名稱
       $templateProcessor->saveAs($r_filename); //先把檔案存起來
       $new_file = $officeConverterTool->Converter2OtherFileType($r_filename,$outfilename,'pdf');
       return $new_file;



   }

    public function save_mail(Request $request){


 
        $request->merge(['file_1' => '','file_2' => '','file_3' => '','file_4' => '','file_5' => '']);
        $data = $request->all();
        $queryData['class'] = $data['class'];
        $queryData['term'] = $data['term'];
       

  
       


        $replace_class_info = '';


        /*取得班級資料 */
        $s_query = T01tb::select('t01tb.class', 't01tb.name', 't01tb.branch', 't01tb.branchname', 't01tb.process');
        $s_results = $s_query->where('class', $queryData['class'])->get()->toArray();
        $class_data = $s_results[0];
        $s_query = T04tb::select('t04tb.term', 't04tb.sdate', 't04tb.edate', 't04tb.sponsor');
        $s_results = $s_query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
        $class_data['term']    = $s_results[0]['term'];
        $class_data['sdate']   = $s_results[0]['sdate'];
        $class_data['edate']   = $s_results[0]['edate'];
        $class_data['sponsor'] = $s_results[0]['sponsor'];
        if(!empty($class_data['sponsor'])){
            $s_query = M09tb::select('*');
            $s_results = $s_query->where('userid', $class_data['sponsor'])->get()->toArray();
            $class_data['sponsor_info'] = $s_results;
        }

        $replace_class_info .= ('<p>'.$class_data['class'].$class_data['name'].'第'.$class_data['term'].'期，授課時間如下</p>');

        
        $replace_officer = '
        <p> 一)聯絡人：[office] [name]，電話：(02)8369-1399 轉 [ext]</p>
        <p>二) E-mail：[email]</p>
        ';
        $replace_officer = str_replace("[office]",$class_data['sponsor_info'][0]['section'],$replace_officer);
        $replace_officer = str_replace("[name]",$class_data['sponsor_info'][0]['username'],$replace_officer);
        $replace_officer = str_replace("[ext]",$class_data['sponsor_info'][0]['ext'],$replace_officer);
        $replace_officer = str_replace("[email]",$class_data['sponsor_info'][0]['email'],$replace_officer);


        // ○○年度○○○○○○○○第○○期，共計○○人參訓，授課時間如下：
        // (課程名稱)：○○年○○月○○日○○時○○分至○○時○○分
        // 研習地點：本學院 (○○院區) ＜棟別＞○○樓○○教室


        if(empty($data['teacher_mail'])){
            return back()->with('result', '0')->with('message', '尚未選擇收件者!');
        }

        $maillist = $data['teacher_mail'];   

        foreach ($maillist as $mailer){
            $mailinfo = explode("~",$mailer);
        
            /*
            array:6 [▼
                0 => array:13 [▼
                    "id" => 58069
                    "course" => "01"
                    "idno" => "L102585702"
                    "type" => "1"
                    "lecthr" => 2.0
                    "lectamt" => 3200
                    "teachtot" => 3200
                    "tratot" => 0
                    "deductamt" => 0
                    "totalpay" => 3200
                    "name" => "成功簡報的基本要素"
                    "date" => "01/17"
                    "cname" => "吳約西"
                ]
                1 => array:13 [▶]
                2 => array:13 [▶]
                3 => array:13 [▶]
                4 => array:13 [▶]
                5 => array:13 [▶]
                ]
            */
            $teacher_list = $this->employService->getDetailList($queryData);
            foreach ($teacher_list as $teacherInfoData){
                if($teacherInfoData['cname']==$mailinfo[0]){
                    $replace_class_info .= ('<p>'.$teacherInfoData['name'].'：'.$teacherInfoData['date'].'</p>'); 
                }
            }



            //取代講座姓名
            $data['content'] = str_replace("{講座姓名}",$mailinfo[0],$data['content']);
            //取代這個講座在這一班的資料
            $data['content'] = str_replace("{課程資訊}",$replace_class_info,$data['content']);
            //取代寄送的承辦人資料
            $data['content'] = str_replace("{承辦人員聯絡資訊}",$replace_officer,$data['content']);
         
            $mail = array('hws0106@gmail.com','clairec4305@gmail.com');

            if(isset($data['attached'])){
         
                foreach($data['attached'] as $attacedid){
                    switch ($attacedid) {
                        case 1://學員名冊
                            $data['file_1'] = $this->student_list($queryData['class'],$queryData['term']);
                            break;
                        case 2://課程表
                            $data['file_2'] = $this->course_schedule_list($queryData['class'],$queryData['term']);
                            break;
                        case 3://個人資料表
                           $data['file_3'] = $this->teacherinfo($queryData['class'],$queryData['term'],2,$mailinfo[0]);
                            break;
                        case 4://個資授權書
                            $data['file_4'] = $this->teacherinfo($queryData['class'],$queryData['term'],4,$mailinfo[0]);
                            break;
                        case 5://數位材授權書
                            $data['file_5'] = $this->authorizepaper($queryData['class'],$queryData['term']);
                        break;
                    }
                }
                
            }

            Mail::send("admin/lecture_mail/send", $data, function ($message) use ($mail,$data){
                $message->from('fet@hrd.gov.tw', 'CSDI自動寄信通知');
                $message->subject($data['title']);
                $message->to($mail);
                if($data['file_1']!=''){
                    $message->attach($data['file_1'],['as'=>'學員名冊.pdf']);
                }
                if($data['file_2']!=''){
                    $message->attach($data['file_2'],['as'=>'課程表.pdf']);
                }
                if($data['file_3']!=''){
                    $message->attach($data['file_3'],['as'=>'個人資料表.pdf']);
                }
                if($data['file_4']!=''){
                    $message->attach($data['file_4'],['as'=>'個資授權書.pdf']);
                }
                if($data['file_5']!=''){
                    $message->attach($data['file_5'],['as'=>'數位材授權書.pdf']);
                }
               
            });
           
        }
        return back()->with('result', '1')->with('message', '寄送成功!');



    }


    public function Mail($class,$term)
    {

        $emailsto=[];

        $sql="SELECT   B.cname, B.email, A.idno
        FROM t08tb A   INNER JOIN m01tb B   ON A.idno=B.idno
        WHERE
        A.class='".$class."'
        AND  A.term='".$term."'
        AND A.hire='Y'
        AND B.email<>''
        GROUP BY B.cname,B.email, A.idno
        ORDER BY A.idno ";
        $temp = json_decode(json_encode(DB::select($sql)), true);

        if($temp==[])
        {
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getclass();
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
            $termArr=$temp;
            $result = '查無講座email資料';
            return view('admin/lecture_mail/list',compact('classArr','termArr' ,'result'));

        }

        $maildata = $temp;

        foreach($maildata as $v) {
            array_push($emailsto, $v["email"]);
        }

        $sql="SELECT
        B.branch, /* 上課地點 1:臺北院區 2:南投院區 */
        LEFT(A.class,3) AS yerly,
        CONCAT(RTRIM(B.name),'第',CAST(CAST(A.term AS int) AS char),'期') AS class_name_term,
        /* 訓期 */
        CONCAT(SUBSTRING(A.sdate,1,3),'年',
                    CAST(CAST(SUBSTRING(A.sdate,4,2) AS int) AS char),'月',
                    CAST(CAST(SUBSTRING(A.sdate,6,2) AS int) AS char),'日至',
                    CAST(CAST(SUBSTRING(A.edate,4,2) AS int) AS char),'月',
                    CAST(CAST(SUBSTRING(A.edate,6,2) AS int) AS char),'日') AS sate_edate,
        /* 參訓人數 */
            CAST(A.quota as char) AS quota,
        /* 上課地點 */
            (
                CASE
                WHEN B.branch = '1' THEN CONCAT('本學院(臺北院區)',
                                            (
                                            CASE
                                            WHEN D.site IN ('C01','C02') THEN '集會棟' /* 前瞻廳、卓越堂 */
                                            WHEN D.site = 'C14'          THEN ''
                                            WHEN D.site = '406'          THEN '住宿棟'
                                            ELSE '教學棟'                                /* 教學棟各樓層教室 */
                                            END
                                            ),
                                            (
                                            CASE
                                            WHEN D.site = 'C14' THEN ''
                                            WHEN CAST(SUBSTRING(D.site,1,1) AS INT) = 1 THEN CONCAT(SUBSTRING(D.site,1,1),'樓')
                                            ELSE ''
                                            END
                                            ),
                                            RTRIM(D.name),
                                            '（臺北市大安區新生南路3段30號）')
                WHEN B.branch = '2' AND E.site LIKE '9%' THEN RTRIM(E.name)
                WHEN B.branch = '2' THEN CONCAT('本學院(南投院區)',
                                            (
                                            CASE
                                            WHEN E.site IN ('111','212') THEN '詠晴園' /* Zoom I、Zoom II */
                                            ELSE '文教大樓'
                                            END
                                            ),
                                            (
                                            CASE
                                            WHEN E.site = '001' THEN '1樓' /* 國際會議廳 */
                                            WHEN CAST(SUBSTRING(E.site,1,1) AS INT) = 1 THEN CONCAT(SUBSTRING(E.site,1,1) , '樓')
                                            ELSE ''
                                            END
                                            ),
                                            RTRIM(E.name),
                                            '（南投市光明路1號）')
                ELSE ''
                END
                ) AS site,
        CONCAT(RTRIM(C.section),RTRIM(C.username)) AS user_name, /* 承辦人 */
            (
                CASE B.branch
                WHEN '1' THEN CONCAT('（02）83691399轉',RTRIM(C.ext))
                WHEN '2' THEN CONCAT('（049）2332131轉',RTRIM(C.ext))
                ELSE ''
                END
                ) AS tel1,
            RTRIM(IFNULL(C.email,'')) AS email,
            (
                CASE B.branch
                WHEN '1' THEN CONCAT('（02）83691399轉',C.ext)
                WHEN '2' THEN CONCAT('（049）2332131轉',C.ext)
                ELSE ''
                END
                ) AS tel2,
            (
                CASE B.branch
                WHEN '1' THEN (
                                CASE C.section
                                WHEN '綜合規劃組' THEN '（02）83695616'
                                WHEN '培育發展組' THEN '（02）83695611'
                                WHEN '專業訓練組' THEN '（02）83695615'
                                WHEN '秘書室'     THEN '（02）83695613'
                                WHEN '人事室'     THEN '（02）83695618'
                                WHEN '主計室'     THEN '（02）83695619'
                                ELSE '（02）83695616'
                                END
                                )
                WHEN '2' THEN (
                                CASE C.section
                                WHEN '綜合規劃組' THEN '（049）2332723'
                                WHEN '培育發展組' THEN '（049）2370962'
                                WHEN '專業訓練組' THEN '（049）2332724'
                                WHEN '數位學習組' THEN '（049）2352979'
                                WHEN '秘書室'     THEN '（049）2351627'
                                WHEN '人事室'     THEN '（049）2359871'
                                WHEN '主計室'     THEN '（049）2359871'
                                ELSE '（049）2339030'
                                END
                                )
                ELSE ''
                END
                ) AS fax
        FROM t04tb A
        INNER JOIN t01tb B ON A.class = B.class
        LEFT JOIN m09tb C ON A.sponsor = C.userid
        LEFT JOIN m14tb D ON A.site = D.site
        LEFT JOIN m25tb E ON A.site = E.site
        WHERE A.class = '".$class."'
        AND A.term = '".$term."'";

        $temp = json_decode(json_encode(DB::select($sql)), true);

        if($temp==[])
        {
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getclass();
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
            $termArr=$temp;
            $result = '查無資料';
            return view('admin/lecture_mail/list',compact('classArr','termArr' ,'result'));

        }

        $maildata=$temp;
        $branch="";
        $url_pdf="";
        if($maildata[0]["branch"]==1){
            $branch="臺北院區";
            $url_pdf="http://mediab.hrd.gov.tw/courses/tmp/講座聘請通知參閱資料臺北院區.pdf";
        }
        elseif($maildata[0]["branch"]==1){
            $branch="南投院區";
            $url_pdf="http://mediab.hrd.gov.tw/courses/tmp/講座聘請通知參閱資料南投院區.pdf";
        }
        $data=array(
            "yerly"=>$maildata[0]["yerly"],
            "class_name_term"=>$maildata[0]["class_name_term"],
            "sate_edate"=>$maildata[0]["sate_edate"],
            "quota"=>$maildata[0]["quota"],
            "site"=>$maildata[0]["site"],
            "branch_name"=>$branch,
            "user_name"=>$maildata[0]["user_name"],
            "tel"=>$maildata[0]["tel1"],
            "email"=>$maildata[0]["email"],
            "url_pdf"=>$url_pdf
        );

        // 讀檔案
        $filename="H3E";
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $filename).'.docx');
        $outputfile="教學方法調查表".$maildata[0]["class_name_term"];

        $templateProcessor->setValue('classname',$maildata[0]["class_name_term"]);

        $templateProcessor->saveAs('../public/backend/attachments/'.$outputfile.'.docx');

        //send to test account
        $emailsto=array("csditest3322@gmail.com");
        $path="../public/backend/attachments/".$outputfile.".docx";

        $mflag=0;

        // without error handle
        // Mail::to($emailsto)->send(new H3($data,$path));

        // with error handle
        try{
            Mail::to($emailsto)->send(new H3($data,$path));
        } catch (\Exception $e) {
            $mflag=1;
        }

        if($mflag==0){
            $result = 'Mail已發送成功。';
        }else{
            $result = 'Mail發送失敗，若持續發生請詢問系統工程師。';
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;

        return view('admin/lecture_mail/list',compact('classArr','termArr' ,'result'));

    }




    public function mail_to_me(Request $request)
    {
        $emailsto = auth()->user()->email;
        $class=$request->input('class');
        $term=$request->input('term');

        $sql="SELECT
        B.branch, /* 上課地點 1:臺北院區 2:南投院區 */
        LEFT(A.class,3) AS yerly,
        CONCAT(RTRIM(B.name),'第',CAST(CAST(A.term AS int) AS char),'期') AS class_name_term,
        /* 訓期 */
        CONCAT(SUBSTRING(A.sdate,1,3),'年',
                    CAST(CAST(SUBSTRING(A.sdate,4,2) AS int) AS char),'月',
                    CAST(CAST(SUBSTRING(A.sdate,6,2) AS int) AS char),'日至',
                    CAST(CAST(SUBSTRING(A.edate,4,2) AS int) AS char),'月',
                    CAST(CAST(SUBSTRING(A.edate,6,2) AS int) AS char),'日') AS sate_edate,
        /* 參訓人數 */
            CAST(A.quota as char) AS quota,
        /* 上課地點 */
            (
                CASE
                WHEN B.branch = '1' THEN CONCAT('本學院(臺北院區)',
                                            (
                                            CASE
                                            WHEN D.site IN ('C01','C02') THEN '集會棟' /* 前瞻廳、卓越堂 */
                                            WHEN D.site = 'C14'          THEN ''
                                            WHEN D.site = '406'          THEN '住宿棟'
                                            ELSE '教學棟'                                /* 教學棟各樓層教室 */
                                            END
                                            ),
                                            (
                                            CASE
                                            WHEN D.site = 'C14' THEN ''
                                            WHEN CAST(SUBSTRING(D.site,1,1) AS INT) = 1 THEN CONCAT(SUBSTRING(D.site,1,1),'樓')
                                            ELSE ''
                                            END
                                            ),
                                            RTRIM(D.name),
                                            '（臺北市大安區新生南路3段30號）')
                WHEN B.branch = '2' AND E.site LIKE '9%' THEN RTRIM(E.name)
                WHEN B.branch = '2' THEN CONCAT('本學院(南投院區)',
                                            (
                                            CASE
                                            WHEN E.site IN ('111','212') THEN '詠晴園' /* Zoom I、Zoom II */
                                            ELSE '文教大樓'
                                            END
                                            ),
                                            (
                                            CASE
                                            WHEN E.site = '001' THEN '1樓' /* 國際會議廳 */
                                            WHEN CAST(SUBSTRING(E.site,1,1) AS INT) = 1 THEN CONCAT(SUBSTRING(E.site,1,1) , '樓')
                                            ELSE ''
                                            END
                                            ),
                                            RTRIM(E.name),
                                            '（南投市光明路1號）')
                ELSE ''
                END
                ) AS site,
        CONCAT(RTRIM(C.section),RTRIM(C.username)) AS user_name, /* 承辦人 */
            (
                CASE B.branch
                WHEN '1' THEN CONCAT('（02）83691399轉',RTRIM(C.ext))
                WHEN '2' THEN CONCAT('（049）2332131轉',RTRIM(C.ext))
                ELSE ''
                END
                ) AS tel1,
            RTRIM(IFNULL(C.email,'')) AS email,
            (
                CASE B.branch
                WHEN '1' THEN CONCAT('（02）83691399轉',C.ext)
                WHEN '2' THEN CONCAT('（049）2332131轉',C.ext)
                ELSE ''
                END
                ) AS tel2,
            (
                CASE B.branch
                WHEN '1' THEN (
                                CASE C.section
                                WHEN '綜合規劃組' THEN '（02）83695616'
                                WHEN '培育發展組' THEN '（02）83695611'
                                WHEN '專業訓練組' THEN '（02）83695615'
                                WHEN '秘書室'     THEN '（02）83695613'
                                WHEN '人事室'     THEN '（02）83695618'
                                WHEN '主計室'     THEN '（02）83695619'
                                ELSE '（02）83695616'
                                END
                                )
                WHEN '2' THEN (
                                CASE C.section
                                WHEN '綜合規劃組' THEN '（049）2332723'
                                WHEN '培育發展組' THEN '（049）2370962'
                                WHEN '專業訓練組' THEN '（049）2332724'
                                WHEN '數位學習組' THEN '（049）2352979'
                                WHEN '秘書室'     THEN '（049）2351627'
                                WHEN '人事室'     THEN '（049）2359871'
                                WHEN '主計室'     THEN '（049）2359871'
                                ELSE '（049）2339030'
                                END
                                )
                ELSE ''
                END
                ) AS fax
        FROM t04tb A
        INNER JOIN t01tb B ON A.class = B.class
        LEFT JOIN m09tb C ON A.sponsor = C.userid
        LEFT JOIN m14tb D ON A.site = D.site
        LEFT JOIN m25tb E ON A.site = E.site
        WHERE A.class = '".$class."'
        AND A.term = '".$term."'";

        $temp = json_decode(json_encode(DB::select($sql)), true);

        if($temp==[])
        {
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getclass();
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
            $termArr=$temp;
            $result = '查無資料';
            return view('admin/lecture_mail/list',compact('classArr','termArr' ,'result'));

        }

        $maildata=$temp;
        $branch="";
        $url_pdf="";
        if($maildata[0]["branch"]==1){
            $branch="臺北院區";
            $url_pdf="http://mediab.hrd.gov.tw/courses/tmp/講座聘請通知參閱資料臺北院區.pdf";
        }
        elseif($maildata[0]["branch"]==1){
            $branch="南投院區";
            $url_pdf="http://mediab.hrd.gov.tw/courses/tmp/講座聘請通知參閱資料南投院區.pdf";
        }
        $data=array(
            "yerly"=>$maildata[0]["yerly"],
            "class_name_term"=>$maildata[0]["class_name_term"],
            "sate_edate"=>$maildata[0]["sate_edate"],
            "quota"=>$maildata[0]["quota"],
            "site"=>$maildata[0]["site"],
            "branch_name"=>$branch,
            "user_name"=>$maildata[0]["user_name"],
            "tel"=>$maildata[0]["tel1"],
            "email"=>$maildata[0]["email"],
            "url_pdf"=>$url_pdf
        );

        // 讀檔案
        $filename="H3E";
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $filename).'.docx');
        $outputfile="教學方法調查表".$maildata[0]["class_name_term"];

        $templateProcessor->setValue('classname',$maildata[0]["class_name_term"]);

        $templateProcessor->saveAs('../public/backend/attachments/'.$outputfile.'.docx');

        //send to test account
        $emailsto=array("csditest3322@gmail.com");
        $path="../public/backend/attachments/".$outputfile.".docx";

        $mflag=0;

        // without error handle
        // Mail::to($emailsto)->send(new H3($data,$path));

        // with error handle
        try{
            Mail::to($emailsto)->send(new H3($data,$path));
        } catch (\Exception $e) {
            $mflag=1;
        }

        if($mflag==0){
            $result = 'Mail已發送成功。';
        }else{
            $result = 'Mail發送失敗，若持續發生請詢問系統工程師。';
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;

        return view('admin/lecture_mail/list',compact('classArr','termArr' ,'result'));


    }



    public function export(Request $request)
    {
        $class=$request->input('classes');
        $term=$request->input('term');

        $sql="SELECT
        B.branch, /* 上課地點 1:臺北院區 2:南投院區 */
        LEFT(A.class,3) AS yerly,
        CONCAT(RTRIM(B.name),'第',CAST(CAST(A.term AS int) AS char),'期') AS class_name_term,
        /* 訓期 */
        CONCAT(SUBSTRING(A.sdate,1,3),'年',
                       CAST(CAST(SUBSTRING(A.sdate,4,2) AS int) AS char),'月',
                       CAST(CAST(SUBSTRING(A.sdate,6,2) AS int) AS char),'日至',
                       CAST(CAST(SUBSTRING(A.edate,4,2) AS int) AS char),'月',
                       CAST(CAST(SUBSTRING(A.edate,6,2) AS int) AS char),'日') AS sate_edate,
        /* 參訓人數 */
            CAST(A.quota as char) AS quota,
        /* 上課地點 */
            (
                  CASE
                   WHEN B.branch = '1' THEN CONCAT('本學院(臺北院區)',
                                            (
                                             CASE
                                              WHEN D.site IN ('C01','C02') THEN '集會棟' /* 前瞻廳、卓越堂 */
                                              WHEN D.site = 'C14'          THEN ''
                                              WHEN D.site = '406'          THEN '住宿棟'
                                              ELSE '教學棟'                                /* 教學棟各樓層教室 */
                                             END
                                            ),
                                            (
                                             CASE
                                              WHEN D.site = 'C14' THEN ''
                                              WHEN CAST(SUBSTRING(D.site,1,1) AS INT) = 1 THEN SUBSTRING(D.site,1,1) + '樓'
                                              ELSE ''
                                              END
                                            ),
                                            RTRIM(D.name),
                                            '（臺北市大安區新生南路3段30號）')
                   WHEN B.branch = '2' AND E.site LIKE '9%' THEN RTRIM(E.name)
                   WHEN B.branch = '2' THEN CONCAT('本學院(南投院區)',
                                            (
                                             CASE
                                              WHEN E.site IN ('111','212') THEN '詠晴園' /* Zoom I、Zoom II */
                                              ELSE '文教大樓'
                                             END
                                            ),
                                            (
                                             CASE
                                              WHEN E.site = '001' THEN '1樓' /* 國際會議廳 */
                                              WHEN CAST(SUBSTRING(E.site,1,1) AS INT) = 1 THEN SUBSTRING(E.site,1,1) + '樓'
                                              ELSE ''
                                             END
                                            ),
                                            RTRIM(E.name),
                                            '（南投市光明路1號）')
                   ELSE ''
                  END
                 ) AS site,
        CONCAT(RTRIM(C.section),RTRIM(C.username)) AS user_name, /* 承辦人 */
            (
                 CASE B.branch
                  WHEN '1' THEN '（02）83691399轉'+RTRIM(C.ext)
                  WHEN '2' THEN '（049）2332131轉'+RTRIM(C.ext)
                  ELSE ''
                 END
                ) AS tel1,
            RTRIM(IFNULL(C.email,'')) AS email,
            (
                 CASE B.branch
                  WHEN '1' THEN '（02）83691399轉'+C.ext
                  WHEN '2' THEN '（049）2332131轉'+C.ext
                  ELSE ''
                 END
                ) AS tel2,
            (
                 CASE B.branch
                  WHEN '1' THEN (
                                 CASE C.section
                                  WHEN '綜合規劃組' THEN '（02）83695616'
                                  WHEN '培育發展組' THEN '（02）83695611'
                                  WHEN '專業訓練組' THEN '（02）83695615'
                                  WHEN '秘書室'     THEN '（02）83695613'
                                  WHEN '人事室'     THEN '（02）83695618'
                                  WHEN '主計室'     THEN '（02）83695619'
                                  ELSE '（02）83695616'
                                 END
                                )
                  WHEN '2' THEN (
                                 CASE C.section
                                  WHEN '綜合規劃組' THEN '（049）2332723'
                                  WHEN '培育發展組' THEN '（049）2370962'
                                  WHEN '專業訓練組' THEN '（049）2332724'
                                  WHEN '數位學習組' THEN '（049）2352979'
                                  WHEN '秘書室'     THEN '（049）2351627'
                                  WHEN '人事室'     THEN '（049）2359871'
                                  WHEN '主計室'     THEN '（049）2359871'
                                  ELSE '（049）2339030'
                                 END
                                )
                  ELSE ''
                 END
                ) AS fax
        FROM t04tb A
        INNER JOIN t01tb B ON A.class = B.class
        LEFT JOIN m09tb C ON A.sponsor = C.userid
        LEFT JOIN m14tb D ON A.site = D.site
        LEFT JOIN m25tb E ON A.site = E.site
        WHERE A.class = '".$class."'
        AND A.term = '".$term."'";
        $temp=DB::select($sql);

        if($temp==[]){
            $result ="此條件查無資料，請重新查詢";
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getclass();
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
            $termArr=$temp;
            return view('admin/lecture_mail/list',compact('classArr','termArr' ,'result'));
        }

        $data=json_decode(json_encode($temp), true);
        // 讀檔案
        $filename="H3B";
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $filename).'.docx');
        $outputfile="教材著作授權使用同意書";

        $templateProcessor->setValue('y',$data[0]["yerly"]);
        $templateProcessor->setValue('classname',$data[0]["class_name_term"]);

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputfile);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }

}
