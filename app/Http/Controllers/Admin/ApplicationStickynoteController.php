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

class ApplicationStickynoteController extends Controller
{
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('application_stickynote', $user_group_auth)){
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
        return view('admin/application_stickynote/list');
    }

    /*
    申請表及黏存單 CSDIR3200
    參考Tables:
    使用範本:H8.xlsx
    */
    /**
     * 列印檔案
     *
     */
    public function export(Request $request)
    {
        //1:台北院區, 2:南投院區
        $area = $request->input('area');
        //"109/03/08 ~ 109/03/14"
        $weekpicker = $request->input('weekpicker');
        $sdate = str_replace('/','',substr( $weekpicker,0,9));
        $edate = str_replace('/','',substr( $weekpicker,12,9));

        //1:一般班, 2:代收款班, 3:全部
        $type = $request->input('type');

        //Get_Total_Data 取得【經費類合計數】金額
        //一般, 排除 讀取【代收款】的開支科目代碼 14
        $sqlTotal="SELECT SUM(A.teachtot+A.tratot) AS TOTAL,
                         SUM(A.totalpay) AS TOTALPAY,
                         SUM(A.deductamt) AS DEDUCTAMT,
                         SUM(A.insuretot) AS INSURETOR
                    FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                INNER JOIN t01tb D ON A.class=D.class
                                INNER JOIN m01tb E ON A.idno = E.idno
                    WHERE A.totalpay > 0
                    AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                    AND B.kind<>'14'
                    AND D.branch = '".$area."'
                    ";
        $reportlistTotal = DB::select($sqlTotal);
        $dataArrTotal=json_decode(json_encode(DB::select($sqlTotal)), true);

        //Get_Total_Data 取得【經費類合計數】金額
        //代收款
        $sqlTotal14="SELECT SUM(A.teachtot+A.tratot) AS TOTAL,
                         SUM(A.totalpay) AS TOTALPAY,
                         SUM(A.deductamt) AS DEDUCTAMT,
                         SUM(A.insuretot) AS INSURETOR
                    FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                INNER JOIN t01tb D ON A.class=D.class
                                INNER JOIN m01tb E ON A.idno = E.idno
                    WHERE A.totalpay > 0
                    AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                    AND B.kind='14'
                    AND D.branch = '".$area."'
                    ";
        $reportlistTotal14 = DB::select($sqlTotal14);
        $dataArrTotal14=json_decode(json_encode(DB::select($sqlTotal14)), true);

        //黏存單
        /* acccode 開支科目代碼 */
        /* accname 開支科目名稱 */
        $sql="SELECT    A.class,
                        A.term,
                        RTRIM(C.name) AS class_name,
                        B.kind AS acccode,
                        RTRIM(IFNULL(D.accname,'')) AS accname
                FROM t06tb A INNER JOIN t04tb B ON A.class = B.class AND A.term = B.term
                                INNER JOIN t01tb C ON A.class = C.class
                                LEFT JOIN s06tb D ON LEFT(A.class,3) = D.yerly AND B.kind = D.acccode
                WHERE 1 = (CASE WHEN A.date BETWEEN '".$sdate."' AND '".$edate."'
                                THEN 1 ELSE 0 END )
                 AND C.branch = '".$area."'
                GROUP BY A.class,A.term,B.kind,C.name,D.accname
                ORDER BY (CASE WHEN D.accname = '代收款' THEN 2 ELSE 1 END), A.class,A.term
                    ";
        $reportlist = DB::select($sql);
        //$dataArr=json_decode(json_encode(DB::select($reportlist)), true);
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);
        }

        // 檔案名稱
        $fileName = 'H8';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = IOFactory::load($filePath);
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();

        $reportlist = json_decode(json_encode($reportlist), true);

        $AreaName='';
        if($area=='1'){
            $AreaName='臺北院區';
        }else{
            $AreaName='南投院區';
        }

        $count=0;
        //1:一般班, 2:代收款班, 3:全部
        if($type=='1' || $type=='3'){
            $objActSheet = $objPHPExcel->getSheet(0);
            $objActSheet->setTitle('一般班');
            //經費支出申請表
            //行政院人事行政總處公務人力發展學院(臺北院區)經費支出申請表
            $objActSheet->setCellValue('A3', '行政院人事行政總處公務人力發展學院('.$AreaName.')經費支出申請表');
            //108/05/19-108/05/25講座鐘點費及交通費經費支用如附單據，共計新台幣211,939元，請准予報銷以清手續，可否？恭請  核示。
            $objActSheet->setCellValue('B4', str_replace(' ~ ','-',$weekpicker).'講座鐘點費及交通費經費支用如附單據，共計新台幣'.number_format($dataArrTotal[0]['TOTAL']).'元，請准予報銷以清手續，請  核示。');
            //訓練輔導及研究(211,939元)
            $objActSheet->setCellValue('B6', '訓練輔導及研究('.number_format($dataArrTotal[0]['TOTAL']).'元)');
            //新台幣211,939元整
            $objActSheet->setCellValue('B8', '新台幣'.number_format($dataArrTotal[0]['TOTAL']).'元整');
            //211,939
            $objActSheet->setCellValue('D15', number_format($dataArrTotal[0]['TOTAL']));
            $objActSheet->setCellValue('D19', number_format($dataArrTotal[0]['TOTAL']));
            $objActSheet->setCellValue('A16', '');
            $objActSheet->setCellValue('A17', '');
            $objActSheet->setCellValue('B17', '');
            $count++;
        }
        if($type=='3'){
            $objActSheet = $objPHPExcel->getSheet(0);
            //第二頁sheet開始，複製範本為new sheet
            $sheet = clone $objPHPExcel->getSheet(0);
            $sheet->setTitle("代收款班");
            $objPHPExcel->addSheet($sheet, 1);
            $objPHPExcel->setActiveSheetIndex((int)$count);
            $objActSheet = $objPHPExcel->getSheet(1);
            $count=1;
        }
        if($type=='2' || $type=='3'){
            $objActSheet = $objPHPExcel->getSheet($count);
            $objActSheet->setTitle('代收款班');
            //經費支出申請表
            //行政院人事行政總處公務人力發展學院(臺北院區)經費支出申請表
            $objActSheet->setCellValue('A3', '行政院人事行政總處公務人力發展學院('.$AreaName.')經費支出申請表');
            //108/05/19-108/05/25講座鐘點費及交通費經費支用如附單據，共計新台幣211,939元，請准予報銷以清手續，可否？恭請  核示。
            $objActSheet->setCellValue('B4', str_replace(' ~ ','-',$weekpicker).'講座鐘點費及交通費經費支用如附單據，共計新台幣'.number_format($dataArrTotal14[0]['TOTAL']).'元，請准予報銷以清手續，請  核示。');
            //代收款(211,939元)
            $objActSheet->setCellValue('B6', '代收款('.number_format($dataArrTotal14[0]['TOTAL']).'元)');
            //新台幣211,939元整
            $objActSheet->setCellValue('B8', '新台幣'.number_format($dataArrTotal14[0]['TOTAL']).'元整');
            //211,939
            $objActSheet->setCellValue('D15', number_format($dataArrTotal14[0]['TOTAL']));
            $objActSheet->setCellValue('D19', number_format($dataArrTotal14[0]['TOTAL']));
            $objActSheet->setCellValue('A16', '');
            $objActSheet->setCellValue('A17', '');
            $objActSheet->setCellValue('B17', '');
            $count++;
        }

        $copyIndex=$count;
        $objActSheet = $objPHPExcel->getSheet($count);

        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            //for ($i=0; $i < sizeof($arraykeys); $i++) {
                //資料by班別迴圈
                $k=0;
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    if($reportlist[$j][$arraykeys[3]]>='01'){
                        if(($type=='1' && $reportlist[$j][$arraykeys[4]]<>'代收款') ||
                           ($type=='2' && $reportlist[$j][$arraykeys[4]]=='代收款') ||
                           ($type=='3')){

                            //fee 用途
                            $sqlFee="SELECT CONCAT(RTRIM(D.name),
                                                        (
                                                        CASE
                                                        WHEN A.term IS NULL THEN ''
                                                        ELSE CONCAT('第',A.term,'期')
                                                        END
                                                        )) AS nameterm,
                                                    SUM(A.lectamt) AS lectamt,
                                                    SUM(A.motoramt) AS motoramt,
                                                    SUM(A.trainamt+A.planeamt+A.ship+A.mrtamt+A.otheramt) AS trainamt,
                                                    SUM(A.noteamt+A.speakamt) AS speakamt,
                                                    SUM(A.teachtot+A.tratot) AS total
                                                FROM t09tb A INNER JOIN t04tb B ON A.class=B.class AND A.term=B.term
                                                INNER JOIN t06tb C ON A.class=C.class AND A.term=C.term AND A.course=C.course
                                                INNER JOIN t01tb D ON A.class=D.class
                                                LEFT JOIN m09tb F ON B.sponsor=F.userid
                                                INNER JOIN m01tb G ON A.idno = G.idno
                                                WHERE A.totalpay > 0
                                                AND C.date BETWEEN '".$sdate."' AND '".$edate."'
                                                AND A.class = '".$reportlist[$j][$arraykeys[0]]."'
                                                AND A.term = '".$reportlist[$j][$arraykeys[1]]."'
                                                AND B.kind = '".$reportlist[$j][$arraykeys[3]]."'
                                                GROUP BY A.class,A.term,D.name,B.sdate,B.edate,F.username
                                                ORDER BY A.class,A.term
                                                ";
                            $reportlistFee = DB::select($sqlFee);
                            $dataArrFee=json_decode(json_encode(DB::select($sqlFee)), true);
                            //取出全部項目
                            //if(sizeof($reportlistFee) != 0) {
                            //    $arraykeysFee=array_keys((array)$reportlistFee[0]);
                            //}
                            //$reportlistFee = json_decode(json_encode($reportlistFee), true);

                            if($k==0){
                                //$objActSheet = $objPHPExcel->getActiveSheet();
                                $objActSheet = $objPHPExcel->getSheet($k+$copyIndex);
                                $objActSheet->setTitle($reportlist[$j][$arraykeys[0]].$reportlist[$j][$arraykeys[1]]);
                            }else{
                                $sheet = clone $objPHPExcel->getSheet($copyIndex);
                                $sheet->setTitle($reportlist[$j][$arraykeys[0]].$reportlist[$j][$arraykeys[1]]);
                                //$count++;
                                $objPHPExcel->addSheet($sheet, $k+$copyIndex);
                                //$objPHPExcel->setActiveSheetIndex((int)($k+$copyIndex));
                                $objActSheet = $objPHPExcel->getSheet($k+$copyIndex);
                            }

                            //黏存單
                            //行政院人事行政總處公務人力發展學院(臺北院區)
                            $objActSheet->setCellValue('A1', '行政院人事行政總處公務人力發展學院('.$AreaName.')');
                            //所屬年度：108年度
                            $objActSheet->setCellValue('A3', '所屬年度：'.substr($reportlist[$j][$arraykeys[0]],0,3).'年度');
                            //業務計畫-分支計畫：訓練輔導及研究-在職訓練研習課程(教務組)
                            //業務計畫-分支計畫：代收款-
                            if($reportlist[$j][$arraykeys[4]]<>'代收款'){
                                $objActSheet->setCellValue('B5','業務計畫-分支計畫：訓練輔導及研究-'.$reportlist[$j][$arraykeys[4]]);
                                //講座鐘點費0A$34,500
                                if(!empty($dataArrFee)){
                                    if($dataArrFee[0]['lectamt']!='0'){
                                        $objActSheet->setCellValue('M6','講座鐘點費0A$'.number_format($dataArrFee[0]['lectamt']));
                                    }else{
                                        $objActSheet->setCellValue('M6','');
                                    }
                                }

                            }else{
                                $objActSheet->setCellValue('B5','業務計畫-分支計畫：'.$reportlist[$j][$arraykeys[4]].'-');
                                //講座鐘點費$
                                if(!empty($dataArrFee)){
                                    if($dataArrFee[0]['lectamt']!='0'){
                                        $objActSheet->setCellValue('M6','講座鐘點費$'.number_format($dataArrFee[0]['lectamt']));
                                    }else{
                                        $objActSheet->setCellValue('M6','');
                                    }
                                }
                            }

                            if(!empty($dataArrFee)){
                                //短程車資$1,800
                                if($dataArrFee[0]['motoramt']!='0'){
                                    $objActSheet->setCellValue('M7','短程車資$'.number_format($dataArrFee[0]['motoramt']));
                                }else{
                                    $objActSheet->setCellValue('M7','');
                                }
                                //國內旅費$
                                if($dataArrFee[0]['trainamt']!='0'){
                                    $objActSheet->setCellValue('M8','國內旅費$'.number_format($dataArrFee[0]['trainamt']));
                                }else{
                                    $objActSheet->setCellValue('M8','');
                                }
                                //講演費$
                                if($dataArrFee[0]['speakamt']!='0'){
                                    $objActSheet->setCellValue('M9','講演費$'.number_format($dataArrFee[0]['speakamt']));
                                }else{
                                    $objActSheet->setCellValue('M9','');
                                }

                                //金額每個字折開填入
                                if($dataArrFee[0]['total']!='0'){
                                    for($m=0;$m<strlen($dataArrFee[0]['total']);$m++){
                                        //KJI...
                                        $NameFromNumber=$this->getNameFromNumber(11-$m);
                                        $objActSheet->setCellValue($NameFromNumber.'10',substr($dataArrFee[0]['total'],strlen($dataArrFee[0]['total'])-$m-1,1));
                                    }
                                    $NameFromNumber=$this->getNameFromNumber(11-$m);
                                    $objActSheet->setCellValue($NameFromNumber.'10','$');
                                }else{
                                    $objActSheet->setCellValue('B10','');
                                    $objActSheet->setCellValue('C10','');
                                    $objActSheet->setCellValue('D10','');
                                    $objActSheet->setCellValue('E10','');
                                    $objActSheet->setCellValue('F10','');
                                    $objActSheet->setCellValue('G10','');
                                    $objActSheet->setCellValue('H10','');
                                    $objActSheet->setCellValue('I10','');
                                    $objActSheet->setCellValue('J10','');
                                    $objActSheet->setCellValue('K10','');
                                }

                                //108054簡報表達技巧研習班第04期講座鐘點費及交通費
                                //$objActSheet->setCellValue('M10',$reportlist[$j][$arraykeys[0]].$dataArrFee[0]['nameterm'].'講座鐘點費及交通費');
                                $objActSheet->setCellValue('M10',$reportlist[$j][$arraykeys[0]].$reportlist[$j][$arraykeys[2]].'第'.$reportlist[$j][$arraykeys[1]].'期講座鐘點費及交通費');
                            }else{
                                $objActSheet->setCellValue('M6','');
                                $objActSheet->setCellValue('M7','');
                                $objActSheet->setCellValue('M8','');
                                $objActSheet->setCellValue('M9','');

                                $objActSheet->setCellValue('B10','');
                                $objActSheet->setCellValue('C10','');
                                $objActSheet->setCellValue('D10','');
                                $objActSheet->setCellValue('E10','');
                                $objActSheet->setCellValue('F10','');
                                $objActSheet->setCellValue('G10','');
                                $objActSheet->setCellValue('H10','');
                                $objActSheet->setCellValue('I10','');
                                $objActSheet->setCellValue('J10','');
                                $objActSheet->setCellValue('K10','');
                                $objActSheet->setCellValue('M10','');
                                $objPHPExcel->removeSheetByIndex($k+$copyIndex);
                            }
                            $k++;
                        }
                    }
                }
            //}


        }
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"申請表及黏存單");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel
        //$doctype:1.ooxml 2.odf
        //$filename:filename

    }
}
