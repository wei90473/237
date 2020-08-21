<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Mail;
use App\Mail\H17;
use App\Services\User_groupService;

class HourlyfeeNoticeController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('hourlyfee_notice', $user_group_auth)){
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

                $temp=$RptBasic->gettdate();
                $tdateArr=$temp;

                $result = '';
                return view('admin/hourlyfee_notice/list',compact('classArr','termArr' ,'result','tdateArr'));
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
        $class=$request->input('classes');
        $term=$request->input('term');
        $weekpicker=$request->input('weekpicker');

        $sdate="";
        $edate="";
        // Validate date value.
        $tflag="";
        if($weekpicker!=""){
            try {
                $ttemp=explode(" ",$weekpicker);
                $sdatetmp=explode("/",$ttemp[0]);
                $edatetmp=explode("/",$ttemp[2]);
                $sdate=$sdatetmp[0].$sdatetmp[1].$sdatetmp[2];
                $edate=$edatetmp[0].$edatetmp[1].$edatetmp[2];
                $tflag="1";

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
                return view('admin/hourlyfee_notice/list',compact('classArr','termArr' ,'result'));
            }
        }

        // 正確邏輯包含t93tb
        $sql="
        SELECT
        RTRIM(cname) AS 講座姓名,
        class_name AS 上課班期,
        course_name AS 課程名稱,
        CONCAT(SUBSTRING(course_date,1,3),'年',
        SUBSTRING(course_date,4,2),'月',
        SUBSTRING(course_date,6,2),'日',
        '(共計',REPLACE(CAST(lecthr as CHAR),'.0',''),'小時)') AS 授課時間,
        CAST(FORMAT(CAST(lectamt AS int),0) AS CHAR) AS 鐘點費,
        (
         CASE
          WHEN noteamt = 0 THEN ''
          ELSE CAST(FORMAT(CAST(noteamt AS int),0) AS CHAR)
         END
        ) AS 稿費,
        (
         CASE
          WHEN speakamt = 0 THEN ''
          ELSE FORMAT(CAST(speakamt AS int),0)
         END
        ) AS 講演費,
        FORMAT(CAST(tratot AS int),0)AS 交通費,
        FORMAT(CAST(otheramt AS int),0)AS 住宿費,
        FORMAT(CAST(review_total AS int),0)AS 評閱費,
        FORMAT(CAST(other_salary AS int),0)AS 其他薪資所得,
        (
         CASE
          WHEN insuretot = 0 THEN ''
          ELSE FORMAT(CAST(insuretot AS int),0)
         END
        ) AS 扣取補充保險費,
        (
         CASE
          WHEN deductamt = 0 THEN ''
          ELSE FORMAT(CAST(deductamt AS int),0)
         END
        ) AS 扣繳稅額,
        CONCAT('劃撥郵局或金融機構代理：',post_bank) AS 劃撥郵局或金融機構代理,
        #'匯款日期：'+
        CONCAT(SUBSTRING(paidday,1,3),'年',SUBSTRING(paidday,4,2),'月', SUBSTRING(paidday,6,2),'日') AS 匯款日期
        FROM
        (
        SELECT
        (@sno := @sno+1) as serno,
        B.idno,      /* 身分證字號 */
        C.cname,     /* 講座姓名 */
        CONCAT(RTRIM(D.name),'第',CAST(CAST(A.term AS int) AS CHAR),'期') AS class_name, /* 上課班期 */
        RTRIM(A.name) AS course_name, /* 課程名稱 */
        A.date AS course_date,        /* 授課日期 */
        B.lecthr,    /* 授課時間 */
        B.lectamt,   /* 鐘點費 */
        B.noteamt,   /* 稿酬 */
        B.speakamt,  /* 講演費 */
        B.tratot,    /* 交通費 */
        B.insuretot, /* 扣取補充保險費 */
        B.deductamt, /* 扣繳稅額 */
        B.paidday,    /* 匯款日期 */
        B.otheramt,    /* 住宿費 */
        B.review_total,    /* 評閱費 */
        B.other_salary,    /* 其他薪資所得 */
        (
            CASE WHEN B.paidday<>'' and E.transfor='1' THEN RTRIM(E.post) /* 郵局 */
                WHEN B.paidday<>'' and E.transfor='2' THEN RTRIM(E.bank) /* 金融機構 */
                WHEN B.paidday<>'' and E.transfor='' THEN RTRIM(F.transname)
                WHEN B.paidday<>'' and E.transfor='' and C.transfor='1' THEN RTRIM(C.post) /* 郵局 */
                WHEN B.paidday<>'' and E.transfor='' and C.transfor='2' THEN RTRIM(C.bank) /* 金融機構 */
                END
        ) AS post_bank
        FROM t06tb A
        INNER JOIN t09tb B
        ON A.class = B.class
        AND A.term = B.term
        AND A.course = B.course
        INNER JOIN m01tb C
        ON B.idno = C.idno
        INNER JOIN t01tb D
        ON A.class = D.class
        INNER JOIN t11tb E
        ON B.idno = E.idno AND B.paidday = E.date
        left JOIN t93tb F
        ON B.idno = F.idno AND B.paidday = F.date
        , (select @sno:=0) AS S
        WHERE A.class = '".$class."'
        AND A.term = '".$term."'
        AND A.date<>''
        AND B.totalpay>0
        AND 1 = (
            CASE
            WHEN '".$sdate."' = '' THEN 1
            WHEN A.date BETWEEN '".$sdate."' AND '".$edate."' THEN 1
            END
        ) GROUP BY
            A.course,
            E.`date`,
            E.idno 
          ORDER BY A.date
        )as tmp_data ORDER BY serno ";


        //不包含 t93tb 測試用
        // $sql="
        // SELECT
        // RTRIM(cname) AS 講座姓名,
        // class_name AS 上課班期,
        // course_name AS 課程名稱,
        // CONCAT(SUBSTRING(course_date,1,3),'年',
        // SUBSTRING(course_date,4,2),'月',
        // SUBSTRING(course_date,6,2),'日',
        // '(共計',REPLACE(CAST(lecthr as CHAR),'.0',''),'小時)') AS 授課時間,
        // CAST(FORMAT(CAST(lectamt AS int),0) AS CHAR) AS 鐘點費,
        // (
        //  CASE
        //   WHEN noteamt = 0 THEN ''
        //   ELSE CAST(FORMAT(CAST(noteamt AS int),0) AS CHAR)
        //  END
        // ) AS 稿費,
        // (
        //  CASE
        //   WHEN speakamt = 0 THEN ''
        //   ELSE FORMAT(CAST(speakamt AS int),0)
        //  END
        // ) AS 講演費,
        // FORMAT(CAST(tratot AS int),0)AS 交通費,
        // (
        //  CASE
        //   WHEN insuretot = 0 THEN ''
        //   ELSE FORMAT(CAST(insuretot AS int),0)
        //  END
        // ) AS 扣取補充保險費,
        // (
        //  CASE
        //   WHEN deductamt = 0 THEN ''
        //   ELSE FORMAT(CAST(deductamt AS int),0)
        //  END
        // ) AS 扣繳稅額,
        // CONCAT('劃撥郵局或金融機構代理：',post_bank) AS 劃撥郵局或金融機構代理,
        // #'匯款日期：'+
        // CONCAT(SUBSTRING(paidday,1,3),'年',SUBSTRING(paidday,4,2),'月', SUBSTRING(paidday,6,2),'日') AS 匯款日期
        // FROM
        // (
        // SELECT
        // (@sno := @sno+1) as serno,
        // B.idno,      /* 身分證字號 */
        // C.cname,     /* 講座姓名 */
        // CONCAT(RTRIM(D.name),'第',CAST(CAST(A.term AS int) AS CHAR),'期') AS class_name, /* 上課班期 */
        // RTRIM(A.name) AS course_name, /* 課程名稱 */
        // A.date AS course_date,        /* 授課日期 */
        // B.lecthr,    /* 授課時間 */
        // B.lectamt,   /* 鐘點費 */
        // B.noteamt,   /* 稿酬 */
        // B.speakamt,  /* 講演費 */
        // B.tratot,    /* 交通費 */
        // B.insuretot, /* 扣取補充保險費 */
        // B.deductamt, /* 扣繳稅額 */
        // B.paidday,    /* 匯款日期 */
        // (
        //     CASE WHEN B.paidday<>'' and E.transfor='1' THEN RTRIM(E.post) /* 郵局 */
        //         WHEN B.paidday<>'' and E.transfor='2' THEN RTRIM(E.bank) /* 金融機構 */
        //         #WHEN B.paidday<>'' and E.transfor='' THEN RTRIM(F.transname)
        //         #WHEN B.paidday<>'' and E.transfor='' and F.transname='' and G.transfor='1' THEN RTRIM(G.post) /* 郵局 */
        //         #WHEN B.paidday<>'' and E.transfor='' and F.transname='' and G.transfor='2' THEN RTRIM(G.bank) /* 金融機構 */
        //         END
        // ) AS post_bank
        // FROM t06tb A
        // INNER JOIN t09tb B
        // ON A.class = B.class
        // AND A.term = B.term
        // AND A.course = B.course
        // INNER JOIN m01tb C
        // ON B.idno = C.idno
        // INNER JOIN t01tb D
        // ON A.class = D.class
        // INNER JOIN t11tb E
        // ON B.idno = E.idno AND B.paidday = E.date
        // #INNER JOIN t93tb F
        // #ON B.idno = F.idno AND B.paidday = F.date
        // INNER JOIN m01tb G ON B.idno = G.idno
        // , (select @sno:=0) AS S
        // WHERE A.class = '".$class."'
        // AND A.term = '".$term."'
        // AND A.date<>''
        // AND B.totalpay>0
        // AND 1 = (
        //     CASE
        //     WHEN '".$sdate."' = '' THEN 1
        //     WHEN A.date BETWEEN '".$sdate."' AND '".$edate."' THEN 1
        //     END
        // ) ORDER BY A.date
        // )as tmp_data ORDER BY serno ";

        $temp=DB::select($sql);

        if($temp==[]){
            $result = "查無資料，請重新查詢。";
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getclass();
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
            $termArr=$temp;

            $temp=$RptBasic->gettdate();
            $tdateArr=$temp;
            return view('admin/hourlyfee_notice/list',compact('classArr','termArr' ,'result','tdateArr'));
        }

        $data=json_decode(json_encode($temp), true);

        // 讀檔案
        $filename="H17";
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', $filename).'.docx');
        //set variables
        $outputfile="鐘點費入帳通知書";
        $part="";
        ini_set('pcre.backtrack_limit', 999999999);
        $templateProcessor->cloneBlock('b',sizeof($data), true, true);
        $listcnt=5;
        $ny=strval((INT)date('Y')-1911)."年".strval(date('m'))."月".strval(date('d'))."日";

        //fill values
        for($i=0;$i<sizeof($data);$i++){
            $part = '';
            $listcnt=5;
            if(!($data[$i]["鐘點費"]==""||(int)$data[$i]["鐘點費"]==0)){
                $part.=$listcnt.". 鐘點費：".$data[$i]["鐘點費"]." 元<w:br />";
                $listcnt++;
            }
            // if(!($data[$i]["稿費"]==""||(int)$data[$i]["稿費"]==0)){
            //     $part.=$listcnt.". 稿費：".$data[$i]["稿費"]." 元<w:br />";
            //     $listcnt++;
            // }
            // if(!($data[$i]["講演費"]==""||(int)$data[$i]["講演費"]==0)){
            //     $part.=$listcnt.". 講演費：".$data[$i]["講演費"]." 元<w:br />";
            //     $listcnt++;
            // }
            
            $part.=$listcnt.". 交通費：".$data[$i]["交通費"]." 元<w:br />";
            $listcnt++;

            if(!($data[$i]["住宿費"]==""||(int)$data[$i]["住宿費"]==0)){
                $part.=$listcnt.". 住宿費：".$data[$i]["住宿費"]." 元<w:br />";
                $listcnt++;
            }

            if(!($data[$i]["評閱費"]==""||(int)$data[$i]["評閱費"]==0)){
                $part.=$listcnt.". 評閱費：".$data[$i]["評閱費"]." 元<w:br />";
                $listcnt++;
            }

            if(!($data[$i]["其他薪資所得"]==""||(int)$data[$i]["其他薪資所得"]==0)){
                $part.=$listcnt.". 其他薪資所得：".$data[$i]["其他薪資所得"]." 元<w:br />";
                $listcnt++;
            }
            
            if(!($data[$i]["扣取補充保險費"]==""||(int)$data[$i]["扣取補充保險費"]==0)){
                $part.=$listcnt.". 扣取補充保險費：".$data[$i]["扣取補充保險費"]." 元<w:br />";
                $listcnt++;
            }
            if(!($data[$i]["扣繳稅額"]==""||(int)$data[$i]["扣繳稅額"]==0)){
                $part.=$listcnt.". 扣繳稅額：".$data[$i]["扣繳稅額"]." 元<w:br />";
                $listcnt++;
            }

            $part.=$listcnt.$data[$i]["劃撥郵局或金融機構代理"]."<w:br />";
            $listcnt++;
            $part.=$listcnt.". 匯款日期：".$data[$i]["匯款日期"]."<w:br />";
            $listcnt++;

            $templateProcessor->setValue('name#'.strval($i+1),$data[$i]["講座姓名"]);
            $templateProcessor->setValue('classterm#'.strval($i+1),$data[$i]["上課班期"]);
            $templateProcessor->setValue('course#'.strval($i+1),$data[$i]["課程名稱"]);
            $templateProcessor->setValue('cy#'.strval($i+1),$data[$i]["授課時間"]);
            $templateProcessor->setValue('part#'.strval($i+1),$part);
            $templateProcessor->setValue('ny#'.strval($i+1),$ny);

        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",$request->input('doctype'),$outputfile);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 


    }

    //send mail
    public function send($tdate)
    {

        //t93tb無資料，先拿掉
        $sql="SELECT
        (@sno := @sno+1) as serno,
        AA.idno,           /* 身分證字號 */
        GG.lectamt,   /* 鐘點費 */
        GG.noteamt,   /* 稿酬 */
        GG.speakamt,  /* 講演費 */
        GG.tratot,    /* 交通費合計 */
        GG.insuretot, /* 補充保費合計 */
        GG.deductamt, /* 扣繳稅額合計 */
        GG.otheramt,  /* 住宿費合計 */
        GG.review_total, /* 評閱費合計 */
        GG.other_salary, /* 其他薪資所得合計 */
        GG.totalpay,   /* 實付總計 */
        EE.lecthr,
        EE.period,
        BB.cname,           /* 中文姓名 */
        RTRIM(BB.email) AS email,    /* 電子信箱 */
        RTRIM(BB.mobiltel) AS mobiltel, /* 行動電話 */
        BB.notify,
        (
            CASE
                WHEN CC.transfor='1' THEN RTRIM(CC.post) /* 郵局 */
                WHEN CC.transfor='2' THEN RTRIM(CC.bank) /* 金融機構 */
                #WHEN CC.transfor='' THEN RTRIM(DD.transname)
            END
        )  AS post_bank,
        CONCAT(RTRIM(FF.name),'第',AA.term,'期') AS class_name_term
        FROM
        (
        SELECT
        A.class,     /* 班號 */
        A.term,      /* 期別 */
        A.course,    /* 課程編號 */
        A.idno      /* 身分證字號 */
        FROM t09tb A /* 【t09tb 講座任課資料檔】 */
        INNER JOIN t06tb B /* 【t06tb 課程表資料檔】 */
        ON A.class = B.class
        AND A.term = B.term
        AND A.course = B.course
        WHERE A.paidday = '".$tdate."'  #paidday
        ORDER BY A.idno,B.date
        ) AS AA
        INNER JOIN m01tb BB ON AA.idno = BB.idno
        INNER JOIN t11tb CC ON AA.idno = CC.idno AND CC.date = '".$tdate."'  #paidday
        #INNER JOIN t93tb DD ON AA.idno = DD.idno AND DD.date = '".$tdate."' #paidday
        INNER JOIN
        (
        SELECT
        Y.idno,
        Y.lecthr,
        Y.period
        FROM
        (
        select
        X.idno,
        X.lecthr,
        CONCAT(SUBSTRING(min_date,1,3),'年',
        SUBSTRING(min_date,4,2),'月',
        SUBSTRING(min_date,6,2),'日至',
        SUBSTRING(max_date,1,3),'年',
        SUBSTRING(max_date,4,2),'月',
        SUBSTRING(max_date,6,2),'日') as period
        FROM  (SELECT
         A.idno,
         MIN(B.date) AS min_date,
         MAX(B.date) AS max_date,
         CONCAT('(共',REPLACE(CAST(SUM(A.lecthr) AS CHAR),'.0',''),'小時)') as lecthr,
         B.date
         FROM t09tb A
         INNER JOIN t06tb B
        ON A.class = B.class
        AND A.term = B.term
        AND A.course = B.course
        WHERE A.paidday = '".$tdate."'
         GROUP BY A.idno) AS X
         ) AS Y group by Y.idno,Y.period
        ) AS EE ON EE.idno=AA.idno
        INNER JOIN t01tb FF ON AA.class = FF.class
        INNER JOIN
        (
        SELECT
        idno,           /* 身分證字號 */
        SUM(A.lectamt) AS lectamt,   /* 鐘點費 */
        SUM(A.noteamt) AS noteamt,   /* 稿酬 */
        SUM(A.speakamt) AS speakamt,  /* 講演費 */
        SUM(A.tratot) AS tratot,    /* 交通費合計 */
        SUM(A.insuretot) AS insuretot, /* 補充保費合計 */
        SUM(A.deductamt) AS deductamt, /* 扣繳稅額合計 */
        SUM(A.totalpay) AS totalpay,   /* 實付總計 */
        SUM( A.otheramt ) as otheramt,    /* 住宿費 */
        SUM( A.review_total ) as review_total,    /* 評閱費 */
        SUM( A.other_salary ) as other_salary   /* 其他薪資所得 */
        FROM t09tb A /* 【t09tb 講座任課資料檔】 */
        INNER JOIN t06tb B /* 【t06tb 課程表資料檔】 */
        ON A.class = B.class
        AND A.term = B.term
        AND A.course = B.course
        WHERE A.paidday = '".$tdate."'
        GROUP BY idno ORDER BY idno
        ) AS GG ON AA.idno=GG.idno
        ,(select @sno:=0) AS S
        GROUP BY idno ORDER BY idno ";

        $temp = json_decode(json_encode(DB::select($sql)), true);

        if($temp==[])
        {
            $result = "查無資料，請重新查詢。";
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getclass();
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
            $termArr=$temp;

            $temp=$RptBasic->gettdate();
            $tdateArr=$temp;
            return view('admin/hourlyfee_notice/list',compact('classArr','termArr' ,'result','tdateArr'));

        }

        $maildata=$temp;
        $showdate=strval((int)substr($tdate,0,3))."/".substr($tdate,3,2)."/".substr($tdate,5,2);
        $nowdate=strval((int)date("Y")-1911).strval(date("/m/d"));

        //review msg content
        // $data=array(
        //     "cname"=>$maildata[0]["cname"],
        //     "class_name_term"=>$maildata[0]["class_name_term"],
        //     "period"=>$maildata[0]["period"],
        //     "lecthr"=>$maildata[0]["lecthr"],
        //     "lectamt"=>$maildata[0]["lectamt"],
        //     "noteamt"=>$maildata[0]["noteamt"],
        //     "speakamt"=>$maildata[0]["speakamt"],
        //     "tratot"=>$maildata[0]["tratot"],
        //     "insuretot"=>$maildata[0]["insuretot"],
        //     "deductamt"=>$maildata[0]["deductamt"],
        //     "totalpay"=>$maildata[0]["totalpay"],
        //     "tdate"=>$showdate,
        //     "ndate"=>$nowdate
        // );
        // return view('/email/H17',compact('data'));
        //review msg content

        $msg="";
        for($i=0;$i<sizeof($maildata);$i++){

            if(!empty($maildata[$i]["email"])){
                $emailsto=array($maildata[$i]["email"]);
            } else {
                continue;
            }
            
            //send to test account
            $emailsto=array("csditest3322@gmail.com");

            $data=array(
                "cname"=>$maildata[$i]["cname"],
                "class_name_term"=>$maildata[$i]["class_name_term"],
                "period"=>$maildata[$i]["period"],
                "lecthr"=>$maildata[$i]["lecthr"],
                "lectamt"=>$maildata[$i]["lectamt"],
                "noteamt"=>$maildata[$i]["noteamt"],
                "speakamt"=>$maildata[$i]["speakamt"],
                "otheramt"=>$maildata[$i]["otheramt"],
                "review_total"=>$maildata[$i]["review_total"],
                "other_salary"=>$maildata[$i]["other_salary"],
                "tratot"=>$maildata[$i]["tratot"],
                "insuretot"=>$maildata[$i]["insuretot"],
                "deductamt"=>$maildata[$i]["deductamt"],
                "totalpay"=>$maildata[$i]["totalpay"],
                "post_bank"=>$maildata[$i]["post_bank"],
                "tdate"=>$showdate,
                "ndate"=>$nowdate
            );
            //without error handle
            Mail::to($emailsto)->send(new H17($data));

            //with error handle
            try{
                Mail::to($emailsto)->send(new H17($data));
            } catch (\Exception $e) {
                $msg.=$maildata[$i]["cname"].",";
            }

        }

        if($msg==""){
            $result = 'Mail已發送成功。';
        }else{
            $result = $msg.'Mail發送失敗，若持續發生請詢問系統工程師。';
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
        $termArr=$temp;
        $temp=$RptBasic->gettdate();
        $tdateArr=$temp;
        return view('admin/hourlyfee_notice/list',compact('classArr','termArr' ,'result','tdateArr'));

    }
}
