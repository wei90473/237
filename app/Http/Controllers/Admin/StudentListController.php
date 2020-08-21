<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Services\User_groupService;

class StudentListController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('student_list', $user_group_auth)){
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
                $temp=DB::select("SELECT DISTINCT class,RTRIM(name) as name FROM t01tb WHERE EXISTS (
                    SELECT * FROM t04tb WHERE class=t01tb.class) ORDER BY class DESC ");
                $classArr=$temp;
                $temp=json_decode(json_encode($temp), true);
                $arraykeys=array_keys((array)$temp[0]);
                $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$temp[0][$arraykeys[0]]."'");
                $termArr=$temp;
                $result = '';
                return view('admin/student_list/list',compact('classArr','termArr' ,'result'));
    }

    //取得期別
    public function getTerms(Request $request)
    {
        $temp=DB::select("SELECT DISTINCT term FROM t04tb WHERE class='".$request->input('classes')."' ORDER BY term");
        $termArr=$temp;
        return $termArr;
    }


    /*
    學員名冊 CSDIR2050
    參考Tables:
    使用範本:J2.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //1:參訓人員名冊(已序號), 2:參訓人員名冊(未含序號), 3:結訓人員名冊 , 4:最新學員名冊
        $outputtype = $request->input('outputtype');
        //班別
        $classes = $request->input('classes');
        //期別
        $term = $request->input('term');
        //1:組別選項 checked value:1
        $checkteam = $request->input('checkteam');
        //是否依組別分頁: Y是, N否
        $grouptype = $request->input('grouptype');
        //2:學歷  checked value:1
        $checkedu = $request->input('checkedu');
        //3:出生日期 checked value:1
        $checkbirth = $request->input('checkbirth');

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
                        B.cname,
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

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
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

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),'學員名冊-'.$filenameC);
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }
}
