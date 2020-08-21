<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\LoanBeddingLaundryStaticsService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LoanBeddingLaundryStatics extends Controller
{
    public function __construct(User_groupService $user_groupService, LoanBeddingLaundryStaticsService $loanBeddingLaundryStaticsService)
    {
        $this->user_groupService = $user_groupService;
        $this->loanBeddingLaundryStaticsService = $loanBeddingLaundryStaticsService;

        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('loan_bedding_laundry_statics', $user_group_auth)){
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
        $queryData['sdatetw'] = '';
        $queryData['edatetw'] = '';
        return view('admin/loan_bedding_laundry_statics/list',compact('result', 'queryData'));
    }

    public function export(Request $request)
    {
        $queryData['sdate'] = str_replace('-', '', $request->input('sdatetw'));
        $queryData['edate'] = str_replace('-', '', $request->input('edatetw'));

        $queryData['sdatetw'] = $request->input('sdatetw');
        $queryData['edatetw'] = $request->input('edatetw');

        if(empty($queryData['sdatetw']) || empty($queryData['edatetw'])){
            $result ="起始日期或結束日期請勿空白";
            return view('admin/loan_bedding_laundry_statics/list',compact('result', 'queryData'));
        }
        if($queryData['sdate'] > $queryData['edate']){
            $result ="起始日期請勿大於結束日期";
            return view('admin/loan_bedding_laundry_statics/list',compact('result', 'queryData'));
        }
        $data = $this->loanBeddingLaundryStaticsService->getLoanBeddingLaundryStatics($queryData);

        if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/loan_bedding_laundry_statics/list',compact('result', 'queryData'));
        }
        // dd($data);
         // 檔案名稱
         $fileName = 'N30';
         //範本位置
         $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
         //讀取excel

            $styleArray = [
                'borders' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $objActSheet = $objPHPExcel->getActiveSheet();

            $rowcnt = 5;

            $title_date = "借用日期：".substr($queryData['sdate'], 0, 3)."/".substr($queryData['sdate'], 3, 2)."/".substr($queryData['sdate'], 5, 2)."至".substr($queryData['edate'], 0, 3)."/".substr($queryData['edate'], 3, 2)."/".substr($queryData['edate'], 5, 2);

            $objActSheet->setCellValue('A3',trim($title_date));
            $objActSheet->setCellValue('E3',trim("列印日期：".(date('Y')-1911)."/".date('m')."/".date('d')));

            $sum_f = '0';
            $sum_g = '0';
            $weekarray=array("日","一","二","三","四","五","六");

            foreach($data as $row){
                $stay_sum = ($row['mstay']+$row['fstay']);
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($row['applyuser']));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim($stay_sum));
                $floor_room_count = '0';
                $floor_room_sum = '0';
                $floor_room = "";
                foreach($row['floor_data'] as $floor_data){
                    $floor_room_sum += $floor_data['room_count'];
                    if($floor_room_count > '0'){
                        $floor_room .= "\n".$floor_data['croomclsname'].$floor_data['room'];
                    }else{
                        $floor_room .= $floor_data['croomclsname'].$floor_data['room'];
                    }
                    $floor_room_count++;
                }
                $objActSheet->setCellValue('C'.strval($rowcnt),trim($floor_room));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim($floor_room_sum));
                $week1 = '';
                $week2 = '';
                if($row['startdate'] != '0'){
                    $week1 = $weekarray[date("w",strtotime($row['startdate']+19110000))];
                }
                if($row['enddate'] != '0'){
                    $week2 = $weekarray[date("w",strtotime($row['enddate']+19110000))];
                }

                $date_string = $row['startdate'].'('.$week1.')~'.$row['enddate'].'('.$week2.')';
                $objActSheet->setCellValue('E'.strval($rowcnt),trim($date_string));
                $objActSheet->setCellValue('F'.strval($rowcnt),trim($stay_sum));
                $objActSheet->setCellValue('G'.strval($rowcnt),trim($stay_sum * $row['washingfare']));

                $sum_f += $stay_sum;
                $sum_g += ($stay_sum * $row['washingfare']);

                $rowcnt++;

            }

            $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':E'.strval($rowcnt));
            $objActSheet->setCellValue('A'.strval($rowcnt),trim('合計'));

            $objActSheet->setCellValue('F'.strval($rowcnt),trim($sum_f));
            $objActSheet->setCellValue('G'.strval($rowcnt),trim($sum_g));

            $objActSheet->getStyle('A5:G'.strval($rowcnt))->getAlignment()->setWrapText(true);
            $objActSheet->getStyle('A5:G'.strval($rowcnt))->applyFromArray($styleArray);

            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="借住寢室寢具洗滌數量統計表.xlsx"');
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
