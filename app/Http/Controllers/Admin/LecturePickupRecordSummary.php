<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\LecturePickupRecordSummaryService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LecturePickupRecordSummary extends Controller
{
    public function __construct(User_groupService $user_groupService, LecturePickupRecordSummaryService $lecturePickupRecordSummaryService)
    {
        $this->user_groupService = $user_groupService;
        $this->lecturePickupRecordSummaryService = $lecturePickupRecordSummaryService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_pickup_record_summary', $user_group_auth)){
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
        return view('admin/lecture_pickup_record_summary/list',compact('result'));
    }

    public function export(Request $request)
    {

        $queryData['sdate'] = str_replace('-', '', $request->input('sdatetw'));
        $queryData['edate'] = str_replace('-', '', $request->input('edatetw'));
        if(empty($queryData['sdate']) || empty($queryData['edate'])){
            $result ="起始日期或結束日期請勿空白";
            return view('admin/lecture_pickup_record_summary/list',compact('result'));
        }
        if($queryData['sdate'] > $queryData['edate']){
            $result ="起始日期請勿大於結束日期";
            return view('admin/lecture_pickup_record_summary/list',compact('result'));
        }
        $data = $this->lecturePickupRecordSummaryService->getLecturePickupRecordSummary($queryData);

        if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/lecture_pickup_record_summary/list',compact('result'));
        }
        // dd($data);
         // 檔案名稱
         $fileName = 'H22';
         //範本位置
         $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
         //讀取excel

         $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

         $styleArray = [
                'borders' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $objActSheet = $objPHPExcel->getActiveSheet();

            $rowcnt = 3;

            $title_date = "自  ".substr($queryData['sdate'], 0, 3)."  年 ".substr($queryData['sdate'], 3, 2)." 月 ".substr($queryData['sdate'], 5, 2)." 日  起 至  ".substr($queryData['edate'], 0, 3)."  年 ".substr($queryData['edate'], 3, 2)." 月 ".substr($queryData['edate'], 5, 2)." 日";



            $objActSheet->setCellValue('A2',trim($title_date));
            $total = '0';
            $group_total1 = '0';
            $group_total2 = '0';
            $group_total3 = '0';
            $group_total4 = '0';
            $group_total5 = '0';
            $group_total6 = '0';
            $group_total7 = '0';

            $group_total8 = '0';

            $nums = '1';
            foreach($data as $row){

                $objActSheet->setCellValue('A'.strval($rowcnt),trim($nums));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim($row['process_name']));
                $objActSheet->setCellValue('C'.strval($rowcnt),trim($row['class']));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim($row['name']));
                $objActSheet->setCellValue('E'.strval($rowcnt),trim($row['term'].'期'));
                $objActSheet->setCellValue('F'.strval($rowcnt),trim($row['total']));
                $objActSheet->setCellValue('G'.strval($rowcnt),trim($row['section']));

                $total += $row['total'];

                if($row['process'] == '1'){
                    if($row['section_id'] == '1'){
                        $group_total1 += $row['total'];
                    }
                    if($row['section_id'] == '2'){
                        $group_total2 += $row['total'];
                    }
                    if($row['section_id'] == '3'){
                        $group_total3 += $row['total'];
                    }
                    if($row['section_id'] == '4'){
                        $group_total4 += $row['total'];
                    }
                    if($row['section_id'] == '5'){
                        $group_total5 += $row['total'];
                    }
                    if($row['section_id'] == '6'){
                        $group_total6 += $row['total'];
                    }
                    if($row['section_id'] == '7'){
                        $group_total7 += $row['total'];
                    }
                }else{
                    $group_total8 += $row['total'];
                }

                $nums++;
                $rowcnt++;
            }

            $objActSheet->setCellValue('D'.strval($rowcnt),trim("以下空白"));
            $rowcnt++;
            $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':D'.strval($rowcnt));
            $objActSheet->setCellValue('A'.strval($rowcnt),trim("總計"));
            $objPHPExcel->getActiveSheet(0)->mergeCells('E'.strval($rowcnt).':E'.strval($rowcnt));
            $objPHPExcel->getActiveSheet(0)->mergeCells('F'.strval($rowcnt).':F'.strval($rowcnt));
            $objPHPExcel->getActiveSheet(0)->mergeCells('G'.strval($rowcnt).':G'.strval($rowcnt));
            $objActSheet->setCellValue('F'.strval($rowcnt),trim($total));
            $rowcnt++;
            $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':G'.strval($rowcnt));
            $all_total = "培育發展組：".$group_total1."元；專業訓練組".$group_total2."元；數位學習組".$group_total3."元；綜合規劃組".$group_total4."元；秘書室".$group_total5."元；人事室".$group_total6."元；主計室".$group_total7."元；其他委訓".$group_total8."元。";
            $objActSheet->setCellValue('A'.strval($rowcnt),trim($all_total));
            $objPHPExcel->getActiveSheet(0)->getRowDimension($rowcnt)->setRowHeight(38);

            $objActSheet->getStyle('A3:G'.strval($rowcnt))->getAlignment()->setWrapText(true);
            $objActSheet->getStyle('A3:G'.strval($rowcnt))->applyFromArray($styleArray);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="接送講座紀錄結算總表.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            //匯出
            //old code
            //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
            $objWriter->save('php://output');
            exit;

    }
}
