<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\ClassesService;
use App\Models\Edu_loanplacelst;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SiteApplication extends Controller
{
    public function __construct(User_groupService $user_groupService,ClassesService $classesService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_application', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        $this->classesService = $classesService;
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
        return view('admin/site_application/list',compact('result'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        if($data['sdate']=='')  return back()->with('result', 0)->with('message', '匯出失敗，請輸入日期'); 
        $base = Edu_loanplacelst::join('edu_loanplace','edu_loanplace.applyno','=','edu_loanplacelst.applyno')->where('edu_loanplace.applydate',$data['sdate'])->get()->toarray();
        $result = array();
        $applyno = '';
        // var_dump($base);exit();
        if(empty($base)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據'); 
        // 檔案名稱
        $fileName = 'N34';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        //  fill values
        $objActSheet = $objPHPExcel->getActiveSheet();
        
        $columns = array();
        $i=0;
        foreach ($base as $va) {
            $fee=0;
            if($i>0){
                // 複製格式
                $this->copyRange($objActSheet, 'A1:I25', 'A'.($i*25+1)); 
            }
            $objActSheet->setCellValue('G'.($i*25+1),'申請日期：'.substr($va['applydate'],0,3).'年'.substr($va['applydate'],3,2).'月'.substr($va['applydate'],-2).'日');
            $objActSheet->getStyle('G'.($i*25+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $objActSheet->setCellValue('A'.($i*25+3),$va['orgname']);
            $objActSheet->setCellValue('D'.($i*25+3),$va['title']);
            $objActSheet->setCellValue('D'.($i*25+4),$va['applyuser']);
            $objActSheet->setCellValue('E'.($i*25+3),$va['num']);
            $objActSheet->setCellValue('G'.($i*25+3),$va['mstay']);
            $objActSheet->setCellValue('G'.($i*25+4),$va['fstay']);
            $objActSheet->setCellValue('A'.($i*25+8),$va['reason']);
            $objActSheet->setCellValue('I'.($i*25+3),$va['tel']);
            $objActSheet->setCellValue('I'.($i*25+5),$va['fax']);
            $objActSheet->setCellValue('I'.($i*25+4),$va['cellphone']);
            $objActSheet->setCellValue('B'.($i*25+5),$va['email']);
            $objActSheet->setCellValue('B'.($i*25+6),$va['addr']);
            $sitebase = $this->classesService->getSiteData(array('applyno'=>$va['applyno']),'2');
            $site = '';
            for($j=0;$j<sizeof($sitebase);$j++){
                $Vdate = (substr($sitebase[$j]['applydate'],0,3)+1911 ). substr($sitebase[$j]['applydate'],3);
                $week = date('w', strtotime($Vdate))=='0'? '日' : date('w', strtotime($Vdate));
                $site .= substr($sitebase[$j]['applydate'],0,3).'/'.substr($sitebase[$j]['applydate'],3,2).'/'.substr($sitebase[$j]['applydate'],-2).'(星期'.$week.' '.substr($sitebase[$j]['timestart'],0,2).'：'.substr($sitebase[$j]['timestart'],2).'-'.substr($sitebase[$j]['timeend'],0,2).'：'.substr($sitebase[$j]['timeend'],2).') '.$sitebase[$j]['croomclsname'].$sitebase[$j]['placenum'].'間';
                $site .= (($j+1) == sizeof($sitebase))? '' : PHP_EOL;
                $fee = $sitebase[$j]['fee']+$fee;
            }
            $objActSheet->setCellValue('C'.($i*25+8),$site);
            $objActSheet->setCellValue('E'.($i*25+12),$fee.'元');
            $i++;
        }
        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="場地借用申請表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        //匯出
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $objWriter->save('php://output');
        exit;
    }

    function copyRange( Worksheet $sheet, $srcRange, $dstCell) {
        // Validate source range. Examples: A1:I23, A2:AB2, A27:B100
        if( !preg_match('/^([A-Z]+)(\d+):([A-Z]+)(\d+)$/', $srcRange, $srcRangeMatch) ) {
            // Wrong source range
            return;
        }
        // Validate destination cell. Examples: A26, AB3, A27
        if( !preg_match('/^([A-Z]+)(\d+)$/', $dstCell, $destCellMatch) ) {
            // Wrong destination cell
            return;
        }

        $srcColumnStart = $srcRangeMatch[1];
        $srcRowStart = $srcRangeMatch[2];
        $srcColumnEnd = $srcRangeMatch[3];
        $srcRowEnd = $srcRangeMatch[4];

        $destColumnStart = $destCellMatch[1];
        $destRowStart = $destCellMatch[2];

        // For looping purposes we need to convert the indexes instead
        // Note: We need to subtract 1 since column are 0-based and not 1-based like this method acts.

        $srcColumnStart = Coordinate::columnIndexFromString($srcColumnStart);
        $srcColumnEnd = Coordinate::columnIndexFromString($srcColumnEnd) ;
        $destColumnStart = Coordinate::columnIndexFromString($destColumnStart);

        $rowCount = 0;
        for ($row = $srcRowStart; $row <= $srcRowEnd; $row++) {
            $colCount = 0;
            for ($col = $srcColumnStart; $col <= $srcColumnEnd; $col++) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $style = $sheet->getStyleByColumnAndRow($col, $row);
                $dstCell = Coordinate::stringFromColumnIndex($destColumnStart + $colCount) . (string)($destRowStart + $rowCount);
                $sheet->setCellValue($dstCell, $cell->getValue());
                $sheet->duplicateStyle($style, $dstCell);

                // Set width of column, but only once per row
                if ($rowCount === 0) {
                    $w = $sheet->getColumnDimensionByColumn($col)->getWidth();
                    $sheet->getColumnDimensionByColumn ($destColumnStart + $colCount)->setAutoSize(false);
                    $sheet->getColumnDimensionByColumn ($destColumnStart + $colCount)->setWidth($w);
                }

                $colCount++;
            }

            $h = $sheet->getRowDimension($row)->getRowHeight();
            $sheet->getRowDimension($destRowStart + $rowCount)->setRowHeight($h);

            $rowCount++;
        }

        foreach ($sheet->getMergeCells() as $mergeCell) {
            $mc = explode(":", $mergeCell);
            $mergeColSrcStart = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[0]));
            $mergeColSrcEnd = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[1]));
            $mergeRowSrcStart = ((int)preg_replace("/[A-Z]*/", "", $mc[0]));
            $mergeRowSrcEnd = ((int)preg_replace("/[A-Z]*/", "", $mc[1]));

            $relativeColStart = $mergeColSrcStart - $srcColumnStart;
            $relativeColEnd = $mergeColSrcEnd - $srcColumnStart;
            $relativeRowStart = $mergeRowSrcStart - $srcRowStart;
            $relativeRowEnd = $mergeRowSrcEnd - $srcRowStart;

            if (0 <= $mergeRowSrcStart && $mergeRowSrcStart >= $srcRowStart && $mergeRowSrcEnd <= $srcRowEnd) {
                $targetColStart = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColStart);
                $targetColEnd = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColEnd);
                $targetRowStart = $destRowStart + $relativeRowStart;
                $targetRowEnd = $destRowStart + $relativeRowEnd;

                $merge = (string)$targetColStart . (string)($targetRowStart) . ":" . (string)$targetColEnd . (string)($targetRowEnd);
                //Merge target cells
                $sheet->mergeCells($merge);
            }
        }
    }

}
