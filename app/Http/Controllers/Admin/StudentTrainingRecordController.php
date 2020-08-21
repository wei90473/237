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

class StudentTrainingRecordController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_training_record', $user_group_auth)){
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
        $temp=DB::select("select idno,cname, dept,position from m02tb where cname='".$request->input('sname')."'");
        $idnoArr=$temp;
        $result="";
        return view('admin/student_training_record/list',compact('result','idnoArr'));
        //$classArr = $this->sname();
        //$result = '';
        //return view('admin/student_training_record/list', compact('classArr','result'));
    }

    //取得學員idno
    public function getidno(Request $request){

        $temp=DB::select("select idno,cname, dept,position from m02tb where cname='".$request->input('sname')."'");
        $idnoArr=$temp;
        return $idnoArr;

    }

    // 搜尋下拉
    public function sname() {
        $sql = "SELECT T.cname, T.idno, T.dept, T.position
                FROM (
                        SELECT
                                (CASE WHEN B.cname IS NOT NULL THEN CONCAT(A.cname,' (',A.idno,') ', A.dept,'-', A.position )
                                    ELSE A.cname END) AS cname,
                                A.idno, A.dept, A.position
                        FROM m02tb A LEFT OUTER JOIN
                                (  SELECT cname
                                    FROM m02tb
                                    GROUP BY cname
                                    HAVING count(cname) > 1) B ON A.cname = B.cname
                            GROUP BY A.cname, A.idno, A.dept, A.position, B.cname
                        ) T
                ";
        $classArr = DB::select($sql);
        return $classArr;
    }

    /*
    學員歷次受訓紀錄 CSDIR4100
    參考Tables:
    使用範本:J13.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {

        //學員idno
        $idno = $request->input('idno');

        //取得學員姓名
        $sql = " SELECT DISTINCT cname FROM m02tb
                 WHERE idno = '".$idno."'
                ";
        $reportlist = DB::select($sql);
        $dataArr = json_decode(json_encode($reportlist), true);

        //取得 學員歷次受訓紀錄
        $sql2 = "SELECT A.class,
                        RTRIM(C.name) AS class_name,
                        CASE A.term WHEN '01' THEN '1'
                                    WHEN '02' THEN '2'
                                            WHEN '03' THEN '3'
                                            WHEN '04' THEN '4'
                                            WHEN '05' THEN '5'
                                        WHEN '06' THEN '6'
                                        WHEN '07' THEN '7'
                                                WHEN '08' THEN '8'
                                    WHEN '09' THEN '9'
                                    ELSE A.term
                        END AS term,
                        CONCAT(SUBSTRING(B.sdate,1,3),'.',SUBSTRING(B.sdate,4,2),'.',SUBSTRING(B.sdate,6,2)) AS sdate,
                        CONCAT(SUBSTRING(B.edate,1,3),'.',SUBSTRING(B.edate,4,2),'.',SUBSTRING(B.edate,6,2)) AS edate,
                        RTRIM(A.dept) AS dept,
                        RTRIM(A.position) AS position,
                        CASE WHEN A.rank ='01' THEN '委任第1職等'
                            WHEN A.rank ='02' THEN '委任第2職等'
                            WHEN A.rank ='03' THEN '委任第3職等'
                            WHEN A.rank ='04' THEN '委任第4職等'
                            WHEN A.rank ='05' THEN '委任第5職等'
                                WHEN A.rank ='06' THEN '薦任第6職等'
                                WHEN A.rank ='07' THEN '薦任第7職等'
                                WHEN A.rank ='08' THEN '薦任第8職等'
                                WHEN A.rank ='09' THEN '薦任第9職等'
                                WHEN A.rank ='10' THEN '簡任第10職等'
                                WHEN A.rank ='11' THEN '簡任第11職等'
                                WHEN A.rank ='12' THEN '簡任第12職等'
                                WHEN A.rank ='13' THEN '簡任第13職等'
                                WHEN A.rank ='14' THEN '簡任第14職等'
                            WHEN A.rank ='15' THEN '特任'
                            WHEN A.rank ='16' THEN '依法任派用人員'
                            WHEN A.rank ='17' THEN '聘僱人員'
                                WHEN A.rank ='18' THEN '約僱人員'
                                WHEN A.rank ='19' THEN '軍職人員'
                                WHEN A.rank ='20' THEN '其他'
                                ELSE ''
                        END	AS rank_name,
                        (CASE A.status
                            WHEN '1' THEN '報到'
                            WHEN '2' THEN '未報到'
                            WHEN '3' THEN '退訓'
                            END ) AS status_name
                FROM t13tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
                        INNER JOIN t01tb C ON A.class = C.class
                WHERE A.idno = '".$idno."'
                ORDER BY B.sdate
                            ";

        $reportlist2 = DB::select($sql2);
        //取出全部項目
        if(sizeof($reportlist2) != 0) {
            $arraykeys2=array_keys((array)$reportlist2[0]);
        }

        // 檔案名稱
        $fileName = 'J13';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $reportlist2 = json_decode(json_encode($reportlist2), true);

        //TITLE
        $objActSheet->setCellValue('A1', ''.$dataArr[0]['cname'].'學員歷次受訓紀錄');

        $objActSheet->setCellValue('H2', '日期:'.(date('Y')-'1911').'年'.date('m').'月'.date('d').'日');

        //
        if(sizeof($reportlist2) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys2); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist2); $j++) {
                    //3開始
                    $objActSheet->setCellValue($NameFromNumber.($j+4), $reportlist2[$j][$arraykeys2[$i]]);
                    //高 40
                    //$objActSheet->getRowDimension($j+3)->setRowHeight(40);
                }
            }
            $arraykeys2 = [
                'borders' => [
            //只有外框           'outline' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];
            $objActSheet->getStyle('A4:J'.($j+3))->applyFromArray($arraykeys2);
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"學員歷次受訓紀錄");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 
       
    }
}
