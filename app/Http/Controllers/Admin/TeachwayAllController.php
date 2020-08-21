<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\User_groupService;

class TeachwayAllController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teachway_all', $user_group_auth)){
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
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclasstypek();
        $class=$temp;
        $temp = DB::select("SELECT distinct method,name FROM method order by method");
        $twaysArr=$temp;
        $result="";

        return view('admin/teachway_all/list',compact('result','class','twaysArr'));
    }

    public function export(Request $request){
        $sdatetw=$request->input('sdatetw');
        $edatetw=$request->input('edatetw');
        $classtype=$request->input('classtype');
        $tways=$request->input('tways');
        $sdate=str_replace('-','',$sdatetw);
        $edate=str_replace('-','',$edatetw);
        $tsdate=str_replace('-','/',$sdatetw);
        $tedate=str_replace('-','/',$edatetw);

        $sql="SELECT
        AA.classprop AS col_01,  /* 班別性質 */
        AA.class AS col_02,      /* 班號 */
        AA.classname AS col_03,  /* 班別 */
        AA.term AS col_04,       /* 期別 */
        AA.coursename AS col_05, /* 課程 */
        AA.cname AS col_06 ,      /* 講座 */
        (
            CASE
                WHEN AA.method1 <> '' THEN AA.method1
                ELSE AA.other1
            END
        ) AS col_07,         /* 教學方法 1 */
        (
            CASE
                WHEN AA.method2 <> '' THEN AA.method2
                ELSE AA.other2
            END
        ) AS col_08,         /* 教學方法 2 */
        (
            CASE
                WHEN AA.method3 <> '' THEN AA.method3
                ELSE AA.other3
            END
        ) AS col_09,         /* 教學方法 3 */
        BB.ans1avg AS col_10,    /* 教學技法 */
        BB.ans2avg AS col_11,    /* 教學內容 */
        BB.ans3avg AS col_12     /* 教學態度 */
        FROM
        (
        SELECT
        (@sno := @sno+1) as rn,
        CONCAT(RTRIM(C.code),RTRIM(C.name)) AS classprop , /* 班別性質 */
        D.class,
        RTRIM(B.name) AS classname,
        D.term,
        D.course,
        RTRIM(D.name) AS coursename,
        F.idno,
        RTRIM(F.cname) AS cname,
        IFNULL(M_1.name,'') AS method1,
        IFNULL(M_2.name,'') AS method2,
        IFNULL(M_3.name,'') AS method3,
        IFNULL(G.other1,'') AS other1,
        IFNULL(G.other2,'') AS other2,
        IFNULL(G.other3,'') AS other3
        FROM t04tb A
        INNER JOIN t01tb B
        ON A.class = B.class
        AND B.type <> '13'
        AND B.teaching = 'Y'
        INNER JOIN s01tb C
        ON B.type = RTRIM(C.code)
        AND C.type = 'K'
        INNER JOIN t06tb D
        ON A.class = D.class
        AND A.term = D.term
        INNER JOIN t08tb E
        ON D.class = E.class
        AND D.term = E.term
        AND D.course = E.course
        AND E.hire = 'Y'
        INNER JOIN m01tb F
        ON E.idno = F.idno
        LEFT JOIN t98tb G
        ON D.class = G.class
        AND D.term = G.term
        AND D.course = G.course
        AND F.idno = G.idno
        LEFT JOIN method M_1
        ON G.method1 = M_1.method AND left(G.class,3)= M_1.yerly
        LEFT JOIN method M_2 
        ON G.method2 = M_2.method AND left(G.class,3)= M_2.yerly
        LEFT JOIN method M_3
        ON G.method3 = M_3.method AND left(G.class,3)= M_3.yerly ,(select @sno:=0) AS S
        WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
        AND 1 = (
                 CASE /* 納入調查 或 【t98tb 講座教學教法資料檔】無資料 */
                  WHEN G.mark = ''    THEN 1 /* 【不納入調查】 空白:未勾選 Y :勾選 */
                  WHEN G.mark IS NULL THEN 1
                 END
                )
        AND '".$classtype."' = (CASE WHEN '".$classtype."'='0' THEN '0' ELSE B.type END)
        AND 1 = (/* 教學方法 */
                 CASE
                  WHEN '".$tways."' = ''                    THEN 1
                  WHEN '".$tways."' <> '' AND (instr('".$tways."',G.method1) > 0
                                           OR instr('".$tways."',G.method2) > 0
                                           OR instr('".$tways."',G.method3) > 0) THEN 1
                 END
                )
        ORDER BY
        RTRIM(C.code), /* 班別性質 */
        D.class,       /* 班別代碼 */
        D.term,        /* 期別 */
        D.date,      /* 日期 */
        D.stime,       /* 開始時間 */
        D.etime,       /* 結束時間 */
        D.course       /* 課程代碼 */
        ) AS AA
        INNER JOIN
        (
        SELECT
        xx.class,
        xx.term,
        xx.course,
        xx.idno,
        (
            CASE
                WHEN SUM(ans1cnt)=0 THEN NULL
                ELSE SUM(ans1)/CAST(SUM(ans1cnt) AS float)
            END
        ) AS ans1avg , /* 教學技法 */
        (
            CASE
                WHEN SUM(ans2cnt)=0 THEN NULL
                ELSE SUM(ans2)/CAST(SUM(ans2cnt) AS float)
            END
        ) AS ans2avg, /* 教學內容 */
        (
            CASE
                WHEN SUM(ans3cnt)=0 THEN NULL
                ELSE SUM(ans3)/cast(SUM(ans3cnt) AS float)
            END
        ) AS ans3avg /* 教學態度 */
        FROM
        (
        SELECT
        A.class,   /* 班級編號 */
        A.term,    /* 期別 */
        A.course,  /* 課程編號 */
        A.idno,    /* 身分證字號  */
        (
                  CASE A.ans1
                   WHEN 5 THEN 100
                   WHEN 4 THEN 80
                   WHEN 3 THEN 60
                   WHEN 2 THEN 40
                   WHEN 1 THEN 20
                   ELSE 0
                  END
                 ) AS ans1,      /* 教學技法 */
        (CASE WHEN A.ans1>0 THEN 1 ELSE 0 END) AS ans1cnt, /* 教學技法分母 */
        (
                  CASE A.ans2
                   WHEN 5 THEN 100
                   WHEN 4 THEN 80
                   WHEN 3 THEN 60
                   WHEN 2 THEN 40
                   WHEN 1 THEN 20
                   ELSE 0
                  END
                 ) AS ans2,      /* 教學內容 */
        (CASE WHEN A.ans2>0 THEN 1 ELSE 0 END) AS ans2cnt, /* 教學內容分母 */
        (
                  CASE A.ans3
                   WHEN 5 THEN 100
                   WHEN 4 THEN 80
                   WHEN 3 THEN 60
                   WHEN 2 THEN 40
                   WHEN 1 THEN 20
                   ELSE 0
                  END
                 ) AS ans3,      /* 教學態度 */
        (CASE WHEN A.ans3>0 THEN 1 ELSE 0 END) AS ans3cnt /* 教學態度分母 */
        FROM t56tb A
        INNER JOIN
        (
        SELECT
        CONCAT(RTRIM(C.code),RTRIM(C.name)) AS classprop , /* 班別性質 */
        D.class,
        RTRIM(B.name) AS classname,
        D.term,
        D.course,
        RTRIM(D.name) AS coursename,
        F.idno,
        RTRIM(F.cname) AS cname,
        IFNULL(M_1.name,'') AS method1,
        IFNULL(M_2.name,'') AS method2,
        IFNULL(M_3.name,'') AS method3,
        IFNULL(G.other1,'') AS other1,
        IFNULL(G.other2,'') AS other2,
        IFNULL(G.other3,'') AS other3
        FROM t04tb A
        INNER JOIN t01tb B
        ON A.class = B.class
        AND B.type <> '13'
        AND B.teaching = 'Y'
        INNER JOIN s01tb C
        ON B.type = RTRIM(C.code)
        AND C.type = 'K'
        INNER JOIN t06tb D
        ON A.class = D.class
        AND A.term = D.term
        INNER JOIN t08tb E
        ON D.class = E.class
        AND D.term = E.term
        AND D.course = E.course
        AND E.hire = 'Y'
        INNER JOIN m01tb F
        ON E.idno = F.idno
        LEFT JOIN t98tb G
        ON D.class = G.class
        AND D.term = G.term
        AND D.course = G.course
        AND F.idno = G.idno
        LEFT JOIN method M_1
        ON G.method1 = M_1.method AND substring(G.class,0,3)= M_1.yerly
        LEFT JOIN method M_2 
        ON G.method2 = M_2.method AND substring(G.class,0,3)= M_2.yerly
        LEFT JOIN method M_3
        ON G.method3 = M_3.method AND substring(G.class,0,3)= M_3.yerly ,(select @sno:=0) AS S
        WHERE A.edate BETWEEN '".$sdate."' AND '".$edate."'
        AND 1 = (
                 CASE /* 納入調查 或 【t98tb 講座教學教法資料檔】無資料 */
                  WHEN G.mark = ''    THEN 1 /* 【不納入調查】 空白:未勾選 Y :勾選 */
                  WHEN G.mark IS NULL THEN 1
                 END
                )
        AND '".$classtype."' = (CASE WHEN '".$classtype."'='0' THEN '0' ELSE B.type END)
        AND 1 = (/* 教學方法 */
                 CASE
                  WHEN '".$tways."' = ''                    THEN 1
                  WHEN '".$tways."' <> '' AND (instr('".$tways."',G.method1) > 0
                                           OR instr('".$tways."',G.method2) > 0
                                           OR instr('".$tways."',G.method3) > 0) THEN 1
                 END
                )
        ORDER BY
        RTRIM(C.code), /* 班別性質 */
        D.class,       /* 班別代碼 */
        D.term,        /* 期別 */
        D.date,      /* 日期 */
        D.stime,       /* 開始時間 */
        D.etime,       /* 結束時間 */
        D.course       /* 課程代碼 */
        ) B
        ON A.class = B.class
        AND A.term = B.term
        AND A.course = B.course
        AND A.idno = B.idno
        ) AS xx
        GROUP BY xx.class,xx.term,xx.course,xx.idno
        )AS BB ON AA.class = BB.class AND AA.term = BB.term AND AA.course = BB.course AND AA.idno = BB.idno
        ORDER BY AA.rn";

        $temp = json_decode(json_encode(DB::select($sql)), true);
        if ($temp==[])
        {
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getclasstypek();
            $class=$temp;
            $temp = DB::select("SELECT distinct method,name FROM method order by method");
            $twaysArr=$temp;
            $result="查無資料，請重新查詢";
            return view('admin/teachway_all/list',compact('result','class','twaysArr'));
        }
        $data = $temp;
        $datakeys=array_keys((array)$data[0]);
        // 檔案名稱
        $fileName = 'F17';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel，
        $objPHPExcel = IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];


        $objActSheet->getHeaderFooter()->setOddHeader( '&L&G&C&H 行政院人事行政總處公務人力發展學院自辦班期課程教學方法運用彙總表('.$tsdate.'~'.$tedate.')');
        //行政院人事行政總處公務人力發展學院自辦班期課程教學方法運用彙總表(108/03/01~108/03/31)
        $mergefrom=0;
        $mergeto=0;
        $tmpclass="";
        $tmpterm="";
        //fill values
        for($i=0;$i<sizeof($data);$i++){
            for($j=0;$j<sizeof($datakeys);$j++){
                $objActSheet->setCellValue($this->getNameFromNumber($j+1).strval($i+2),$data[$i][$datakeys[$j]]);
            }
            if($data[$i][$datakeys[1]]==$tmpclass && $data[$i][$datakeys[3]]==$tmpterm){
                $mergeto++;
            }else{
                if($i!=0){
                    $objActSheet->mergeCells('A'.$mergefrom.':A'.$mergeto);
                    $objActSheet->mergeCells('B'.$mergefrom.':B'.$mergeto);
                    $objActSheet->mergeCells('C'.$mergefrom.':C'.$mergeto);
                    $objActSheet->mergeCells('D'.$mergefrom.':D'.$mergeto);
                }
                $tmpclass=$data[$i][$datakeys[1]];
                $tmpterm=$data[$i][$datakeys[3]];
                $mergefrom=$i+2;
                $mergeto=$i+2;
            }
        }

        $objActSheet->mergeCells('A'.$mergefrom.':A'.$mergeto);
        $objActSheet->mergeCells('B'.$mergefrom.':B'.$mergeto);
        $objActSheet->mergeCells('C'.$mergefrom.':C'.$mergeto);
        $objActSheet->mergeCells('D'.$mergefrom.':D'.$mergeto);

        //apply borders
        $objActSheet->getStyle('A1:M'.(sizeof($data)+1))->applyFromArray($styleArray);

        //export excel
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"行政院人事行政總處公務人力發展學院自辦班期課程教學方法運用彙總表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 


        // ob_end_clean();
        // ob_start();

        // // Redirect output to a client’s web browser (Excel2007)
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // // 設定下載 Excel 的檔案名稱
        // header('Content-Disposition: attachment;filename="行政院人事行政總處公務人力發展學院自辦班期課程教學方法運用彙總表.xlsx"');
        // header('Cache-Control: max-age=0');
        // // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');

        // // If you're serving to IE over SSL, then the following may be needed
        // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header ('Pragma: public'); // HTTP/1.0

        // //匯出
        // $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        // $objWriter->save('php://output');
        // exit;
    }

    public function tways(){ //get teach ways
        $temp = DB::select("SELECT distinct method,name FROM method order by method");
        $twaysArr=$temp;
        return $tways;
    }

}
