<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LectureSignatureController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_signature', $user_group_auth)){
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
        return view('admin/lecture_signature/list',compact('classArr','termArr' ,'result'));
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
        $check=$request->input('check');

        $tflag="";
        // Validate date value.
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
                return view('admin/lecture_signature/list',compact('classArr','termArr' ,'result'));
            }
        }

        //空白表單
        if($check=="1"){
            $outputname="講師簽名單-空白";
            // EXCEL範本檔案名稱
            $fileName = 'H4A';
            $sql="SELECT
            RTRIM(LTRIM(B.sdate)) AS sdate,
            RTRIM(LTRIM(B.edate)) AS edate,
            '' AS date,
            '' AS dname,
            '' AS cname,
            '' AS lecthr,
            '' AS lectamt,
            '' AS teachtot,
            '' AS idno,
            '' AS transfor,
            '' AS bankno,
            '' AS postno,
            '' AS regaddress,
            A.name AS aname,
            '' AS term,
            '' AS sort
            FROM
            t01tb A INNER JOIN t04tb B
            ON A.class = B.class
            WHERE
            B.class='".$class."'
            AND B.term='".$term."'";

        }else{
            //不為空白表單
            $outputname="講師簽名單";
            // EXCEL範本檔案名稱
            $fileName = 'H4A';
            $sql="SELECT
            RTRIM(LTRIM(C.sdate)) AS sdate,
            RTRIM(LTRIM(C.edate)) AS edate,
            RTRIM(LTRIM(D.date)) AS date,
            D.name as dname,
            E.cname,
            B.lecthr,
            B.lectamt,
            B.teachtot,
            E.idno,
            E.transfor,
            E.bankno,
            E.postno,
            E.regaddress,
            A.name as aname,
            B.term,
            CONCAT(B.course , B.type) AS sort,
            (
            CASE
            WHEN E.authority IS NULL then 'NULL'
            ELSE ''
            END
            ) AS authority,
            B.insuretot, /* 補充保險費 */
            B.deductamt,  /* 扣繳稅額合計 */
            ifnull(E.insurekind1,'') as insurekind1,
            F.teacher_sort
            FROM t01tb A
            INNER JOIN t09tb B
            ON A.class = B.class
            INNER JOIN t04tb C
            ON B.class = C.class
            AND B.term = C.term
            INNER JOIN t06tb D
            ON B.class = D.class
            AND B.term = D.term
            AND B.course = D.course
            INNER JOIN m01tb E
            ON B.idno = E.idno
            LEFT JOIN employ_sort F
            ON F.class = B.class and F.term = B.term and F.idno = B.idno
            WHERE
            (D.date <> '') AND (B.totalpay > 0)
            AND B.class='".$class."'
            AND B.term='".$term."'";
            //AND A.type='13'"  UI沒有類別，先取消

            if($tflag=="1")
            {
                $sql.="AND D.date between ".$sdate ." AND ".$edate." ORDER BY  D.date, D.stime, sort, ISNULL(teacher_sort), teacher_sort ";
            }else{
                $sql.=" ORDER BY  D.date, D.stime, sort, ISNULL(teacher_sort), teacher_sort ";
            }
            //20200716增加講師排序
        }
        $temp=DB::select($sql);

        if($temp==[]){
            $RptBasic = new \App\Rptlib\RptBasic();
            $temp=$RptBasic->getclass();
            $classArr=$temp;
            $temp=json_decode(json_encode($temp), true);
            $arraykeys=array_keys((array)$temp[0]);
            $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
            $termArr=$temp;
            $result ="此條件查無資料，請重新查詢";
            return view('admin/lecture_signature/list',compact('classArr','termArr' ,'result'));
        }

        $data=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$data[0]);
        //prepare values for fields
        $excelheader='&"標楷體"&16&B '.$data[0]["aname"].'第'.strval((int)$term).'期講師簽名單';
        $period='上課日期：'.substr($data[0]["sdate"],0,3).'年'.substr($data[0]["sdate"],3,2).'月'.substr($data[0]["sdate"],5,2).'日～'.substr($data[0]["edate"],0,3).'年'.substr($data[0]["edate"],3,2).'月'.substr($data[0]["edate"],5,2).'日';
        $note="註1：★表示無身分證字號、●表示無轉帳帳號、▲表示無戶籍地址、■表示無個資授權書、◆表示第2類被保險人　班號：".$class." \n註2:依「全民健康保險法」規定，每日課程講課酬勞，除50類薪資所得超過新臺幣20,008元(不含交通費)時，將扣取1.91%之補充保險費外，其他如執行業務所得9B類別稿費及演講鐘點費，則以達新台幣20,000元(不含交通費)時，扣取1.91%之補充保費，不便之處，尚祈見諒。";
        $contentH="□公務車\n□計程車單程(150元)  □計程車來回(300元)\n□火車______________\n□飛機高鐵__________\n交通費總計__________";
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel，
        $objPHPExcel = IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

        //fill values to fields
        $objActSheet->getHeaderFooter()->setOddHeader($excelheader);
        $objActSheet->setCellValue('A3',$period);

        $objActSheet->mergeCells('A12:L12');

        $objActSheet->setCellValue('A12',$note);

        if($check!="1"){

            $pagecnt=5;
            $rowcnt=5;
            $objActSheet->getRowDimension($pagecnt+7)->setRowHeight(35);
            $objActSheet->mergeCells('A'.strval($pagecnt+7).':L'.strval($pagecnt+7));
            for($i=0;$i<sizeof($data);$i++){
                if(((($i+1)%7)==1)&&($i!=0)){
                    $pagecnt+=12;
                    $rowcnt+=5;
                    $objActSheet->getRowDimension($pagecnt-2)->setRowHeight(18);
                    $objActSheet->getRowDimension($pagecnt-3)->setRowHeight(18);
                    $objActSheet->getRowDimension($pagecnt-4)->setRowHeight(18);
                    $objActSheet->getRowDimension($pagecnt-1)->setRowHeight(30);
                    $objActSheet->setCellValue('B'.strval($pagecnt-1),"月　日");
                    $objActSheet->setCellValue('C'.strval($pagecnt-1),"課　程　名　稱");
                    $objActSheet->setCellValue('D'.strval($pagecnt-1),"講　座");
                    $objActSheet->setCellValue('E'.strval($pagecnt-1),"時數");
                    $objActSheet->setCellValue('F'.strval($pagecnt-1),"單價");
                    $objActSheet->setCellValue('G'.strval($pagecnt-1),"講課\n酬勞");
                    $objActSheet->setCellValue('H'.strval($pagecnt-1),"交　　通　　工　　具");
                    $objActSheet->setCellValue('I'.strval($pagecnt-1),"補充\n保險費");
                    $objActSheet->setCellValue('J'.strval($pagecnt-1),"預扣\n所得稅");
                    $objActSheet->setCellValue('K'.strval($pagecnt-1),"請領費用\n實付總計");
                    $objActSheet->setCellValue('L'.strval($pagecnt-1),"簽　名");
                    $objActSheet->getStyle('A'.strval($pagecnt-1).':L'.strval($pagecnt-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $objActSheet->getRowDimension($pagecnt)->setRowHeight(66);
                    $objActSheet->getStyle('H'.strval($pagecnt))->getFont()->setSize(9);
                    $objActSheet->getStyle('H'.strval($pagecnt))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $objActSheet->getRowDimension($pagecnt+1)->setRowHeight(66);
                    $objActSheet->getStyle('H'.strval($pagecnt+1))->getFont()->setSize(9);
                    $objActSheet->getStyle('H'.strval($pagecnt+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $objActSheet->getRowDimension($pagecnt+2)->setRowHeight(66);
                    $objActSheet->getStyle('H'.strval($pagecnt+2))->getFont()->setSize(9);
                    $objActSheet->getStyle('H'.strval($pagecnt+2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $objActSheet->getRowDimension($pagecnt+3)->setRowHeight(66);
                    $objActSheet->getStyle('H'.strval($pagecnt+3))->getFont()->setSize(9);
                    $objActSheet->getStyle('H'.strval($pagecnt+3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $objActSheet->getRowDimension($pagecnt+4)->setRowHeight(66);
                    $objActSheet->getStyle('H'.strval($pagecnt+4))->getFont()->setSize(9);
                    $objActSheet->getStyle('H'.strval($pagecnt+4))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $objActSheet->getRowDimension($pagecnt+5)->setRowHeight(66);
                    $objActSheet->getStyle('H'.strval($pagecnt+5))->getFont()->setSize(9);
                    $objActSheet->getStyle('H'.strval($pagecnt+5))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $objActSheet->getRowDimension($pagecnt+6)->setRowHeight(66);
                    $objActSheet->getStyle('H'.strval($pagecnt+6))->getFont()->setSize(9);
                    $objActSheet->getStyle('H'.strval($pagecnt+6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $objActSheet->getRowDimension($pagecnt+7)->setRowHeight(35);
                    $objActSheet->mergeCells('A'.strval($pagecnt+7).':L'.strval($pagecnt+7));
                    $objActSheet->getStyle('A'.strval($pagecnt+7))->getFont()->setSize(9);
                    $objActSheet->getStyle('A'.strval($pagecnt+7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $objActSheet->setCellValue('A'.strval($pagecnt+7),$note);
                    $objActSheet->getStyle('A'.strval($pagecnt-1).':L'.strval($pagecnt+6))->applyFromArray($styleArray);

                }
                //fill columns from B-J
                $objActSheet->setCellValue('B'.strval($i+$rowcnt),substr($data[$i]["date"],3,2).".".substr($data[$i]["date"],5,2));
                $objActSheet->setCellValue('C'.strval($i+$rowcnt),$data[$i]["dname"]);
                $objActSheet->setCellValue('D'.strval($i+$rowcnt),$data[$i]["cname"]);
                $objActSheet->setCellValue('E'.strval($i+$rowcnt),$data[$i]["lecthr"]);
                $objActSheet->setCellValue('F'.strval($i+$rowcnt),floatval($data[$i]["lecthr"])==floatval(0)?'0':floatval($data[$i]["teachtot"])/floatval($data[$i]["lecthr"]));
                $objActSheet->setCellValue('G'.strval($i+$rowcnt),$data[$i]["teachtot"]);
                // $objActSheet->setCellValue('H'.strval($i+$rowcnt),$contentH);
                $objActSheet->setCellValue('I'.strval($i+$rowcnt),$data[$i]["insuretot"]);
                $objActSheet->setCellValue('J'.strval($i+$rowcnt),$data[$i]["deductamt"]);
            }
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),$outputname);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel
        //$doctype:1.ooxml 2.odf
        //$filename:filename

    }
}
