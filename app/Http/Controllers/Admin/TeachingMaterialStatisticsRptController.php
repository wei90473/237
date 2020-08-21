<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TeachingMaterialStatisticsRptController extends Controller
{
    public function __construct()
    {

    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin/teaching_material_statistics_rpt/list_report');
    }

    /*
    教材交印統計表列印 CSDIR7120
    參考Tables:
        【t04tb 開班資料檔】
        【t07tb 經費概(結)算資料檔】
        【t38tb 會議基本資料檔】
        【t49tb 教材交印主檔】
    使用範本:P1.xlsx
    'History:
    '2010/06/19 Update
    '排序由t49.kind,t49.class,t49.term,t49.applicant,t49.serno改為t49.kind,t49.serno


    '2008/02/19 Update
    '產製出的報表頁首改為「公務人力發展學院00年00月份A4教材資料印製統計表」

    '2007/05/24 Update
    '修正使其能顯示多期別之資訊

    '2005/07/22 Update
    '
    '2005/03/03 Update
    '將系統中【開支科目】的代碼定義移至【s06tb 開支科目代碼檔】中
    '由modKind.bas統一控制

    '2003/08/20 Update
    '引用modKind.bas -->For 開支科目
    '將開支科目改成11項
    '【t04tb 開班資料檔】
    '【t07tb 經費概(結)算資料檔】
    '【t38tb 會議基本資料檔】
    '【t49tb 教材交印主檔】
    'kind 開支科目 char 2 (‘’)
    '01  在職訓練短期研習班
    '02  在職訓練中期研習班
    '03  在職訓練長期研習班
    '04  國家策略及女性領導者研究班
    '05  游於藝講堂
    '06  訓練輔導研究行政維持
    '07  在職進修專業課程
    '08  人力資源研究發展
    '09  一般行政 (基本行政工作維持)
    '10 代收款
    '11 其他
    */    
    /**
     * 列印檔案
     * 
     */
    public function export(Request $request)
    {   
        //年
        $startYear = $request->input('startYear');
        //月
        $startMonth = $request->input('startMonth');

        /*
        if($startMonth<10){
            $startMonth='0'.$startMonth;
        }
        */

        //取得教材交印統計表
        $sql="  SELECT T.serno, T.NAME, T.material, T.total, T.accname
                FROM ( SELECT t49.serno,
                                            CASE WHEN t01.NAME IS NULL THEN IFNULL(m09.section,'')
                                                        ELSE CONCAT(t01.NAME,'第',t49.term,'期')
                                            END AS NAME,
                                            t49.material,
                                            t49.total,
                                            s06.accname,
                                            t49.kind
                                FROM t49tb t49
                                    LEFT OUTER JOIN t01tb t01 ON t01.class = t49.class
                                    LEFT OUTER JOIN m09tb m09 ON m09.userid = t49.applicant 
                                    LEFT OUTER JOIN s06tb s06 ON s06.acccode = t49.kind AND s06.yerly = '108' 
                                WHERE t49.paiddate = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0')) 
                                UNION ALL
                                SELECT '小計' AS serno,
                                            NULL AS NAME,
                                            NULL AS material,
                                            SUM(t49.total) AS total,
                                            NULL AS accname,
                                            t49.kind
                                FROM t49tb t49
                                    LEFT OUTER JOIN t01tb t01 ON t01.class = t49.class
                                    LEFT OUTER JOIN m09tb m09 ON m09.userid = t49.applicant 
                                    LEFT OUTER JOIN s06tb s06 ON s06.acccode = t49.kind AND s06.yerly = '108' 
                                WHERE t49.paiddate = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0')) 
                                GROUP BY t49.kind
                                UNION ALL
                                SELECT '合計' AS serno,
                                            NULL AS NAME,
                                            NULL AS material,
                                            SUM(t49.total) AS total,
                                            NULL AS accname,
                                            'ZZZ' AS kind
                                FROM t49tb t49
                                    LEFT OUTER JOIN t01tb t01 ON t01.class = t49.class
                                    LEFT OUTER JOIN m09tb m09 ON m09.userid = t49.applicant 
                                    LEFT OUTER JOIN s06tb s06 ON s06.acccode = t49.kind AND s06.yerly = '108' 
                                WHERE t49.paiddate = CONCAT(LPAD('".$startYear."',3,'0'),LPAD('".$startMonth."',2,'0')) 	
                ) T
                ORDER BY T.Kind, T.serno, 2 ";

        $reportlist = DB::select($sql);        
        //$dataArr=json_decode(json_encode(DB::select($sql)), true);  
        //取出全部項目
        if(sizeof($reportlist) != 0) {
            $arraykeys=array_keys((array)$reportlist[0]);  
        }           

        //檔案名稱
        $fileName = 'P1';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        
        $objPHPExcel = IOFactory::load($filePath);               
        $excelReader = IOFactory::createReaderForFile($filePath);
        $excelReader->setReadDataOnly(false);
        $objPHPExcel = $excelReader->load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();        
        $objActSheet->getHeaderFooter()->setOddHeader('&"標楷體,標準"&14'.'行政院人事行政總處公務人力發展學院'.substr($startYear,0,3).'年'.$startMonth.'月份A4教材資料印製統計表 &P/&N');
        $reportlist = json_decode(json_encode($reportlist), true);

        if(sizeof($reportlist) != 0) {
            //項目數量迴圈
            for ($i=0; $i < sizeof($arraykeys); $i++) {
                //excel 欄位 1 == A, etc
                $NameFromNumber=$this->getNameFromNumber($i+1); //A
                //資料by班別迴圈
                for ($j=0; $j < sizeof($reportlist); $j++) {
                    //C2開始
                    $objActSheet->setCellValue($NameFromNumber.($j+2), $reportlist[$j][$arraykeys[$i]]);
                    //高 34
                    $objActSheet->getRowDimension($j+2)->setRowHeight(34);
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

            $objActSheet->getStyle('A2:'.$NameFromNumber.($j+1))->applyFromArray($styleArray); 
        }

        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"教材交印統計表");
        //$obj: entity of file
        //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
        //$doctype:1.ooxml 2.odf
        //$filename:filename 

    }     
}
