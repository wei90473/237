<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\StayDistributionDutyListService;

class StayDistributionDutyList extends Controller
{
    public function __construct(User_groupService $user_groupService,StayDistributionDutyListService $stayDistributionDutyListService)
    {
        $this->user_groupService = $user_groupService;
        $this->stayDistributionDutyListService = $stayDistributionDutyListService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('stay_distribution_dutylist', $user_group_auth)){
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
        return view('admin/stay_distribution_dutylist/list',compact('result'));
    }

    public function export(Request $request)
    {
        $sdate = $request->only(['sdatetw']);
        $edate = $request->only(['edatetw']);

        // 檔案名稱
        $fileName = 'PPrptSub8';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
       
        $syear = substr($sdate['sdatetw'], 0,3); 
        $sM = substr($sdate['sdatetw'], 3,2);
        $sD = substr($sdate['sdatetw'], 5,2);
        $eyear = substr($edate['edatetw'], 0,3); 
        $eM = substr($edate['edatetw'], 3,2);
        $eD = substr($edate['edatetw'], 5,2);
        $dateRank = $syear.'/'.$sM.'/'.$sD.'至'.$eyear.$eM.'/'.$eD;

        $spreadsheet->getActiveSheet()->getCell('K2')->setValue($dateRank);

        $data = $this->stayDistributionDutyListService->getrptPPrptSub8($sdate['sdatetw'],$edate['edatetw']);

        $row = 5;
        $CTotal = 0;
        $ITotal = 0;
        $JTotal = 0;
        $LTotal = 0;
        foreach ($data as $key => $value) {
            $CTotal += intval($value->rosternum);
            $ITotal += intval($value->stayreqcount);
            $JTotal += intval($value->staymreqcount);
            $LTotal += intval($value->stayfreqcount);

            $spreadsheet->getActiveSheet()->getCell('A'.$row)->setValue($value->classname);
            $spreadsheet->getActiveSheet()->getCell('B'.$row)->setValue($value->period);
            $spreadsheet->getActiveSheet()->getCell('C'.$row)->setValue($value->rosternum);
            $spreadsheet->getActiveSheet()->getCell('E'.$row)->setValue($value->startdate);
            $spreadsheet->getActiveSheet()->getCell('F'.$row)->setValue($value->enddate);
            $spreadsheet->getActiveSheet()->getCell('G'.$row)->setValue($value->trainingday);
            $spreadsheet->getActiveSheet()->getCell('I'.$row)->setValue($value->stayreqcount);
            $spreadsheet->getActiveSheet()->getCell('J'.$row)->setValue($value->staymreqcount);
            $spreadsheet->getActiveSheet()->getCell('K'.$row)->setValue($value->mbed);
            $spreadsheet->getActiveSheet()->getCell('L'.$row)->setValue($value->stayfreqcount);
            $spreadsheet->getActiveSheet()->getCell('M'.$row)->setValue($value->fbed);

            $spreadsheet->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('C'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('F'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('G'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('I'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('J'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('K'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('L'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('M'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);

            $spreadsheet->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('C'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('F'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('G'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('I'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('J'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('L'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(50);

            $row++;
        }

        $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(50);
        $spreadsheet->getActiveSheet()->getRowDimension(($row+1))->setRowHeight(50);
        $spreadsheet->getActiveSheet()->getRowDimension(($row+2))->setRowHeight(50);
        $spreadsheet->getActiveSheet()->getRowDimension(($row+3))->setRowHeight(50);
        $spreadsheet->getActiveSheet()->getRowDimension(($row+4))->setRowHeight(50);

        $spreadsheet->getActiveSheet()->getCell('A'.($row+3))->setValue('行動不便住宿');
        $spreadsheet->getActiveSheet()->getCell('K'.($row+3))->setValue('名人巷 名102');
        $spreadsheet->getActiveSheet()->getStyle('A'.($row+3))->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A'.($row+3))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('K'.($row+3))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);

        $spreadsheet->getActiveSheet()->getCell('A'.($row+4))->setValue('合計');
        $spreadsheet->getActiveSheet()->getCell('C'.($row+4))->setValue($CTotal);
        $spreadsheet->getActiveSheet()->getCell('I'.($row+4))->setValue($ITotal);
        $spreadsheet->getActiveSheet()->getCell('J'.($row+4))->setValue($JTotal);
        $spreadsheet->getActiveSheet()->getCell('L'.($row+4))->setValue($LTotal);

        $spreadsheet->getActiveSheet()->getStyle('A'.($row+4))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('A'.($row+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('C'.($row+4))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('I'.($row+4))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('J'.($row+4))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('L'.($row+4))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C'.($row+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('I'.($row+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('J'.($row+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('L'.($row+4))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getCell('A'.($row+5))->setValue('備註');
        $spreadsheet->getActiveSheet()->getStyle('A'.($row+5))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $remark = '◎記號為備用寢室，僅提供新增或遠途未登記住宿學員使用。'.chr(10).
                    '1.為便於整理及準備新班學員進住：'.chr(10).chr(32).
                     '(1)結訓日課程上午結束班，請班務人員宣導結訓日早上離開寢室時即將個人行李整理攜出，'.chr(10).chr(32).chr(32).chr(32).chr(32).
                        '房卡繳回。'.chr(10).chr(32).
                     '(2)結訓日課程下午結束班，請班務人員宣導結訓日中午午休後離開寢室時即將個人行李整理'.chr(10).chr(32).chr(32).chr(32).chr(32).
                        '攜出，房卡繳回。'.chr(10).
                    '2.本週住宿服務人員：電話分機7825；服務專線：2392133。'.chr(10).chr(32).chr(32).'上午班（07:00－15:00）：許燕玉小姐（0910-463273）'.chr(10).chr(32).chr(32).'下午班（13:00－21:00）：陳德興先生（0937-200856）'.chr(10).
                    '3.水電及設備維修：分機7836'.chr(10).chr(32).chr(32).
                      '服務人員：林信安（0963-175511）　李應昇（0933-547756）。';
        $spreadsheet->getActiveSheet()->mergeCells('B'.($row+5).':'.'M'.($row+5));
        $spreadsheet->getActiveSheet()->getCell('B'.($row+5))->setValue($remark);
        $spreadsheet->getActiveSheet()->getStyle('B'.($row+5))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);
        $spreadsheet->getActiveSheet()->getRowDimension(($row+5))->setRowHeight(200);
        $styles = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A5:M'.($row+5))->applyFromArray($styles);

        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="學員住宿分配暨輔導員執勤表.xlsx"');
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
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $objWriter->save('php://output');
        exit;

    }
}
