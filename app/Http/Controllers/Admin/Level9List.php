<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Services\User_groupService;

class Level9List extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('level9_list', $user_group_auth)){
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
        return view('admin/level9_list/list',compact('result'));
    }

    public function export(Request $request)
    {

        //起始年月日
        $sdatetw = $request->input('sdatetw');
        //結束年月日
        $edatetw = $request->input('edatetw');

        /*
        班別學員資料檔	t13tb
            dorm 受訓期間住宿	Y:住宿 N:不住宿
            rank 職等
                01~05:委任第一職等~第五職等
                06~09:薦任第六職等~第九職等
                10~14:簡任第十職等~第十四職等
                15:特任
                16:依法任派用人員
                17:聘僱人員
                18:約僱人員
                19:軍職人員
                20:其他
        開班資料檔	t04tb
            class	班號
            term	期別
            sdate	開課日期
            edate	結束日期
            sponsor	辦班人員	辦班人員userid
        班別基本資料檔	t01tb
        學員基本資料檔	m02tb
        使用者基本資料檔	m09tb
        */
        /*
        $sql="SELECT    A.NO,
                        CASE WHEN B.sdate = B.edate THEN B.sdate ELSE CONCAT(B.sdate,'~',B.edate) END AS sedate,
                        CONCAT(A.class,'-' ,C.name,'(第', A.term,'期)') AS classname,
                        A.dept,
                        ifnull(D.cname, D.ename) CNAME,
                        A.position,
                        CASE WHEN D.sex = 'M' THEN '男' WHEN D.sex = 'F' THEN '女' ELSE D.sex END AS SEX,
                        A.rank,
                        CONCAT(E.username,'(' ,B.sponsor, ')') AS sponsor
                FROM t13tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term

                             INNER JOIN t01tb C ON A.class = C.class
                             INNER JOIN m02tb D ON A.idno = D.idno
                             INNER JOIN m09tb E ON B.sponsor = E.userid
                WHERE A.rank IN ('9','10','11','12','13','14','15') AND A.dorm = 'Y'
                  AND (B.sdate BETWEEN REPLACE('".$sdatetw."','/','') AND REPLACE('".$edatetw."','/','') OR
                       B.edate BETWEEN REPLACE('".$sdatetw."','/','') AND REPLACE('".$edatetw."','/','')   )
                ORDER BY 3,2,1
                ";
                */
        /* 
        06/18 新需求：
        1、產製出來的報表，只抓南投院區班期(t01tb.branch=2)的學員。
        2、職等不要顯示代碼，應顯示其名稱(例：職等=10，請顯示簡任第十職等)。
        3、辦班人員顯示為 曾文怡(GLADY)，姓名後面不要顯示括號還有內容。
        */                
        $sql="SELECT    A.NO,
                        CASE WHEN B.sdate = B.edate THEN B.sdate ELSE CONCAT(B.sdate,'~',B.edate) END AS sedate,
                        CONCAT(A.class,'-' ,C.name,'(第', A.term,'期)') AS classname,
                        A.dept,
                        ifnull(D.cname, D.ename) CNAME,
                        A.position,
                        CASE WHEN D.sex = 'M' THEN '男' WHEN D.sex = 'F' THEN '女' ELSE D.sex END AS SEX,
                        CASE A.rank 
                             WHEN '9' THEN '薦任第九職等'
                             WHEN '10' THEN '簡任第十職等'
                             WHEN '11' THEN '簡任第十一職等'
                             WHEN '12' THEN '簡任第十二職等'
                             WHEN '13' THEN '簡任第十三職等'
                             WHEN '14' THEN '簡任第十四職等'
                             WHEN '15' THEN '第十五職等'
                             ELSE A.rank
                        END AS rankname,
                        E.username AS sponsor
                FROM t13tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
                            INNER JOIN t01tb C ON A.class = C.class
                            INNER JOIN m02tb D ON A.idno = D.idno
                            INNER JOIN m09tb E ON B.sponsor = E.userid
                WHERE A.rank IN ('9','10','11','12','13','14','15') AND A.dorm = 'Y'
                AND (B.sdate BETWEEN REPLACE('".$sdatetw."','/','') AND REPLACE('".$edatetw."','/','') OR
                    B.edate BETWEEN REPLACE('".$sdatetw."','/','') AND REPLACE('".$edatetw."','/','')   )
                AND C.branch='2'
                ORDER BY 3,2,1
                ";                
        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);

        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

         // 檔案名稱
         $fileName = 'N27';
         //範本位置
         $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
         //讀取excel

         $objPHPExcel = IOFactory::load($filePath);
         $excelReader = IOFactory::createReaderForFile($filePath);
         $excelReader->setReadDataOnly(false);
         $objPHPExcel = $excelReader->load($filePath);
         $objActSheet = $objPHPExcel->getActiveSheet();
         //$objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

         $reportlist = json_decode(json_encode($reportlist), true);

         if(sizeof($reportlist) != 0) {
             //項目數量迴圈
             for ($i=0; $i < sizeof($arraykeys); $i++) {
                 //excel 欄位 1 == A, etc
                 $NameFromNumber=$this->getNameFromNumber($i+1);
                 //資料by班別迴圈
                 for ($j=0; $j < sizeof($reportlist); $j++) {
                     //A2開始
                     $objActSheet->setCellValue($NameFromNumber.($j+3), $reportlist[$j][$arraykeys[$i]]);
                 }
             }
             $styleArray = [
                 'borders' => [
             //只有外框           'outline' => [
                         'allBorders'=> [
                         'borderStyle' => Border::BORDER_THIN,
                         'color' => ['rgb' => '000000'],
                     ],
                 ],
             ];

             $objActSheet->getStyle('A2:'.$NameFromNumber.($j+2))->applyFromArray($styleArray);
         }

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"薦任9職等主管名單");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 


    }
}
