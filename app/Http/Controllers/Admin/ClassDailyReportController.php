<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\User_groupService;

class ClassDailyReportController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_daily_report', $user_group_auth)){
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
        $result="";
        return view('admin/class_daily_report/list',compact('result'));
    }
    public function export(Request $request)
    {
        $weekpicker=$request->input('weekpicker');
        $sdate="";
        $edate="";
        $branch=$request->input('area');
        $A1="";
        $ttemp="";
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Validate date value.
        $tflag="";
        if($weekpicker!=""){
            try {
                $ttemp=explode(" ",$weekpicker);
                $sdatetmp=explode("/",$ttemp[0]);
                $edatetmp=explode("/",$ttemp[2]);
                $sdate=strval(intval($sdatetmp[0])+1911)."-".$sdatetmp[1]."-".$sdatetmp[2];
                $edate=$edatetmp[0].$edatetmp[1].$edatetmp[2];
                $tflag="1";

            } catch (\Exception $e) {
                    $ttemp="error";
            }

            if($ttemp=="error" || $sdate=="NaNundefinedundefined" )
            {
                $result = "日期格式錯誤，請重新輸入。";
                return view('admin/class_daily_report/list',compact('result'));
            }
        }

        // EXCEL範本檔案名稱
        $fileName = 'F8';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel，
        $objPHPExcel = IOFactory::load($filePath);
        $sheet_id = 0;

        for($d=0;$d<7;$d++){

            $datetmp=explode("-",date("Y-m-d",strtotime("+$d day",strtotime($sdate))));
            $date=str_pad(strval(intval($datetmp[0])-1911),3,"0",STR_PAD_LEFT).str_pad($datetmp[1],2,"0",STR_PAD_LEFT).str_pad($datetmp[2],2,"0",STR_PAD_LEFT);

            $sql="SELECT
            X.serno,
            X.row_no,
            X.class,
            X.term,
            ROW_NUMBER() OVER(PARTITION BY X.class,X.term,X.course ORDER BY X.course) AS course_no,
            X.course,
            X.site_name,
            X.class_name,
            X.course_name,
            X.stime_etime,
            X.cname
            FROM
            (
            SELECT (@rn1:=@rn1 + 1)  AS serno, AAA.* FROM
            (
            select * from
            (SELECT
            '' AS row_no,
            A.class,
            A.term,
            '' AS course_no,
            A.course,      /* 課程編號 */
            (/* 場地類型 1:教室 2:會議室 3:KTV  4:宿舍 5:簡報室 */
                            CASE
                            WHEN D.site = '4XX' THEN '404教室及405教室'
                            WHEN F.type = '1'   THEN D.site
                            ELSE RTRIM(IFNULL(F.name,''))
                            END
                        ) AS site_name,  /* 教室 */
            CONCAT(RTRIM(E.name),'第',CAST(CAST(A.term AS INT) AS CHAR),'期') AS class_name,  /* 班期 */
            RTRIM(A.name) AS course_name,                                     /* 課程 */
            CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2),'-',
                            SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) AS  stime_etime, /* 時間 */
            CONCAT(RTRIM(B.cname),
                    (/* 類型 1:講座 2:助理 */
                        CASE C.type
                        WHEN '1' THEN '講座'
                        WHEN '2' THEN '助理'
                        ELSE ''
                        END
                    )) AS cname, /* 講座 */
                    E.branch
            FROM t06tb A        /* 【t06tb 課程表資料檔】 */
            INNER JOIN t08tb B  /* 【t08tb 擬聘講座資料檔】 */
            ON A.class = B.class    /* 班號 */
            AND A.term = B.term     /* 期別 */
            AND A.course = B.course /* 課程編號 */
            INNER JOIN t09tb C  /* 【t09tb 講座任課資料檔】 */
            ON B.class = C.class    /* 班號 */
            AND B.term = C.term     /* 期別 */
            AND B.course = C.course /* 課程編號 */
            AND B.idno = C.idno     /* 身分證字號 */
            INNER JOIN t04tb D  /* 【t04tb 開班資料檔】 */
            ON A.class = D.class
            AND A.term = D.term
            INNER JOIN t01tb E  /* 【t01tb 班別基本資料檔】 */
            ON A.class = E.class
            LEFT JOIN m14tb F  /* 【m14tb 場地基本資料檔】 */
            ON D.site = F.site
            WHERE A.date = '".$date."' /* 日期 */
            AND B.hire = 'Y' /* 遴聘與否 Y:聘用 N:未遴聘 */
            AND B.idkind IN ('0','3','4','5','7') /* 【證號別】 0:本國個人 1:事業團體 3:國內無地址之外僑 4:國內有地址之外僑 5:大陸地區個人 6:大陸地區法人團體 7:非居住者 8:境內有分支機構 9:境內無分支機構 */
            AND D.site <> ''
            AND E.branch = '1' /* 上課地點 1:臺北院區 2:南投院區 */
            GROUP BY
            A.class,
            A.term,
            A.course, /* 課程編號 */
            A.name,
            A.stime,
            A.etime,
            B.cname,
            C.type,
            D.site,
            E.name,   /* 班別名稱 */
            F.type, /* 場地類型 1:教室 2:會議室 3:KTV  4:宿舍 5:簡報室 */
            F.name    /* 場地名稱 */
            ORDER BY
            (/* 場地類型 1:教室 2:會議室 3:KTV  4:宿舍 5:簡報室 */
            CASE
            WHEN F.type = '2'   THEN 1
            WHEN F.type = '1'   THEN 2
            WHEN D.site = '4XX' THEN 2
            ELSE 3
            END
            ),
            (/* 場地類型 1:教室 2:會議室 3:KTV  4:宿舍 5:簡報室 */
            CASE /*　教室由高樓層至低樓層 */
            WHEN F.type = '1'   THEN LEFT(D.site,1)
            WHEN D.site = '4XX' THEN LEFT(D.site,1)
            END
            ) DESC,
            (/* 場地類型 1:教室 2:會議室 3:KTV  4:宿舍 5:簡報室 */
            CASE /*　教室同樓層由小至大 */
            WHEN F.type = '1'   THEN D.site
            WHEN D.site = '4XX' THEN '404'
            END
            ),
            A.class,
            A.term,
            A.stime,
            C.type /* 類型 1:講座 2:助理 */
            ) AS AA
            UNION ALL
            select * from
            (
            SELECT
            '' AS row_no,
            A.class,
            A.term,
            '' AS course_no,
            A.course,      /* 課程編號 */
            RTRIM(IFNULL(F.name,'')) AS site_name, /* 教室 */
            CONCAT(RTRIM(E.name),'第',CAST(CAST(A.term AS INT) AS CHAR),'期') AS class_name,  /* 班期 */
            RTRIM(A.name) AS course_name,                                     /* 課程 */
            CONCAT(SUBSTRING(A.stime,1,2),':',SUBSTRING(A.stime,3,2),'-',
                            SUBSTRING(A.etime,1,2),':',SUBSTRING(A.etime,3,2)) AS  stime_etime, /* 時間 */
            CONCAT(RTRIM(B.cname),
                    (/* 類型 1:講座 2:助理 */
                        CASE C.type
                        WHEN '1' THEN '講座'
                        WHEN '2' THEN '助理'
                        ELSE ''
                        END
                    )) AS cname, /* 講座 */
                    E.branch
            FROM t06tb A        /* 【t06tb 課程表資料檔】 */
            INNER JOIN t08tb B  /* 【t08tb 擬聘講座資料檔】 */
            ON A.class = B.class    /* 班號 */
            AND A.term = B.term     /* 期別 */
            AND A.course = B.course /* 課程編號 */
            INNER JOIN t09tb C  /* 【t09tb 講座任課資料檔】 */
            ON B.class = C.class    /* 班號 */
            AND B.term = C.term     /* 期別 */
            AND B.course = C.course /* 課程編號 */
            AND B.idno = C.idno     /* 身分證字號 */
            INNER JOIN t04tb D  /* 【t04tb 開班資料檔】 */
            ON A.class = D.class
            AND A.term = D.term
            INNER JOIN t01tb E  /* 【t01tb 班別基本資料檔】 */
            ON A.class = E.class
            LEFT JOIN m25tb F  /* 【m25tb 南投院區場地代碼基本資料檔】 */
            ON D.site = F.site
            WHERE A.date = '".$date."' /* 日期 */
            AND B.hire = 'Y' /* 遴聘與否 Y:聘用 N:未遴聘 */
            AND B.idkind IN ('0','3','4','5','7') /* 【證號別】 0:本國個人 1:事業團體 3:國內無地址之外僑 4:國內有地址之外僑 5:大陸地區個人 6:大陸地區法人團體 7:非居住者 8:境內有分支機構 9:境內無分支機構 */
            AND D.site <> ''
            AND E.branch = '2' /* 上課地點 1:臺北院區 2:南投院區 */
            GROUP BY
            A.class,
            A.term,
            A.course, /* 課程編號 */
            A.name,
            A.stime,
            A.etime,
            B.cname,
            C.type,
            F.site,
            E.name,   /* 班別名稱 */
            F.name    /* 場地名稱 */
            ORDER BY
            (
            CASE F.site
            WHEN '501' THEN  1
            WHEN '502' THEN  2
            WHEN '503' THEN  3
            WHEN '504' THEN  4
            WHEN '601' THEN  5
            WHEN '602' THEN  6
            WHEN '603' THEN  7
            WHEN '604' THEN  8
            WHEN '701' THEN  9
            WHEN '702' THEN 10
            WHEN '703' THEN 11
            WHEN '704' THEN 12
            WHEN '303' THEN 13 /* 電腦教室三 */
            WHEN '111' THEN 14 /* Zoom I */
            WHEN '212' THEN 15 /* Zoom II */
            WHEN '001' THEN 16 /* 國際會議廳 */
            ELSE CAST(F.site AS INT) /* 外他班 */
            END
            ),
            A.class,
            A.term,
            A.stime,
            C.type /* 類型 1:講座 2:助理 */) AS BB) AS AAA, (SELECT @rn1 := 0) AS rnt1) AS X WHERE X.branch='".$branch."'
            ORDER BY X.serno,X.course_no ";

            $temp=DB::select($sql);
            if($temp==[]){
                continue;
            }
            $data=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$data[0]);

            $clonedWorksheet= clone $objPHPExcel->getSheet(0);
            $indexname=$date;
            $clonedWorksheet->setTitle($indexname);
            $objPHPExcel->addSheet($clonedWorksheet);
            $sheet_id++;
            $objgetSheet=$objPHPExcel->getSheet($sheet_id);

            // $objActSheet = $objPHPExcel->getActiveSheet();

            $A1="行政院人事行政總處公務人力發展學院".strval((int)$datetmp[0])."年".strval((int)$datetmp[1])."月".strval((int)$datetmp[2])."日班期日報表";

            $objgetSheet->setCellValue('A1',$A1);

            //set parameters
            $rowcnt=0;
            $tmpcname="";
            $tclass="";
            $tterm="";
            $tcourse="";
            $tsite="";
            $mergefrom=0;
            $mergeto=0;

            //fill values
            for($i=0;$i<sizeof($data);$i++){

                if($tclass==$data[$i]["class"] && $tterm==$data[$i]["term"]){
                    if($tcourse==$data[$i]["course"]){
                        $tmpcname.="\n".$data[$i]["cname"];
                        $objgetSheet->setCellValue('E'.strval($rowcnt+2),$tmpcname);
                    }else{
                        $objgetSheet->setCellValue('A'.strval($rowcnt+3),$data[$i]["site_name"]);
                        $objgetSheet->setCellValue('B'.strval($rowcnt+3),$data[$i]["class_name"]."(".$data[$i]["class"].")");
                        $objgetSheet->setCellValue('C'.strval($rowcnt+3),$data[$i]["course_name"]);
                        $objgetSheet->setCellValue('D'.strval($rowcnt+3),$data[$i]["stime_etime"]);
                        $objgetSheet->setCellValue('E'.strval($rowcnt+3),$data[$i]["cname"]);
                        $tmpcname=$data[$i]["cname"];
                        $tclass=$data[$i]["class"];
                        $tterm=$data[$i]["term"];
                        $tcourse=$data[$i]["course"];
                        $tsite=$data[$i]["site_name"];
                        $mergeto=$rowcnt+3;
                        $rowcnt++;
                    }
                }else{
                    $objgetSheet->setCellValue('A'.strval($rowcnt+3),$data[$i]["site_name"]);
                    $objgetSheet->setCellValue('B'.strval($rowcnt+3),$data[$i]["class_name"]."(".$data[$i]["class"].")");
                    $objgetSheet->setCellValue('C'.strval($rowcnt+3),$data[$i]["course_name"]);
                    $objgetSheet->setCellValue('D'.strval($rowcnt+3),$data[$i]["stime_etime"]);
                    $objgetSheet->setCellValue('E'.strval($rowcnt+3),$data[$i]["cname"]);
                    $tmpcname=$data[$i]["cname"];
                    $tclass=$data[$i]["class"];
                    $tterm=$data[$i]["term"];
                    $tcourse=$data[$i]["course"];
                    $tsite=$data[$i]["site_name"];
                    if($mergefrom!=0 && $mergeto!=0 && $mergefrom<$mergeto){
                        $objgetSheet->mergeCells('A'.$mergefrom.':A'.$mergeto);
                        $objgetSheet->mergeCells('B'.$mergefrom.':B'.$mergeto);
                    }
                    $mergefrom=$rowcnt+3;
                    $rowcnt++;
                }

            }
            if($mergefrom<$mergeto){
                $objgetSheet->mergeCells('A'.$mergefrom.':A'.$mergeto);
                $objgetSheet->mergeCells('B'.$mergefrom.':B'.$mergeto);
            }
            $objgetSheet->getStyle('A2:E'.strval($rowcnt+2))->applyFromArray($styleArray);

        }

        if($sheet_id>0)
            $objPHPExcel->removeSheetByIndex(0);
      

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"班期日報表");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename            

    }



}
