<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\LectureBedroomUsageService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LectureBedroomUsage extends Controller
{
    public function __construct(User_groupService $user_groupService, LectureBedroomUsageService $lectureBedroomUsageService)
    {
        $this->user_groupService = $user_groupService;
        $this->lectureBedroomUsageService = $lectureBedroomUsageService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_bedroom_usage', $user_group_auth)){
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
        return view('admin/lecture_bedroom_usage/list',compact('result'));
    }

    public function export(Request $request)
    {
    	$selectYear = $request->input('selectYear');
    	$selectMonth = $request->input('selectMonth');

		$sdate = ($selectYear+1911).str_pad($selectMonth, 2, "0", STR_PAD_LEFT).'01';
		$queryData['sdate'] = $sdate-19110000;
		$Monthdays = date('t', strtotime($sdate));
		$queryData['edate'] = (($selectYear+1911).str_pad($selectMonth, 2, "0", STR_PAD_LEFT).$Monthdays)-19110000;

		$data = $this->lectureBedroomUsageService->getLectureBedroomUsage($queryData);
		// dd($data);
		if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/lecture_bedroom_usage/list',compact('result'));
        }

         // 檔案名稱
         $fileName = 'H23';
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
            $objPHPExcel->getDefaultStyle()->getFont()->setName('標楷體');

            $rowcnt = 4;

            $month = date('m', strtotime($sdate));

            for( $i=1 ; $i<=$Monthdays ; $i++ ) {

            	$month_day = $month.'月'.str_pad($i, 2, "0", STR_PAD_LEFT).'日';
            	$objActSheet->setCellValue('A'.strval($rowcnt),trim($month_day));

            	$room_day = '802_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('B'.strval($rowcnt),trim($data[$room_day]));
            	}
                $room_day = '803_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('C'.strval($rowcnt),trim($data[$room_day]));
            	}
                $room_day = '804_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('D'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '805_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('E'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '806_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('F'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '807_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('G'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '808_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('H'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '813_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('I'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '814_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('J'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '815_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('K'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '817_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('L'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '818_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('M'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '819_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('N'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '809_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('O'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '810_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('P'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '811_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('Q'.strval($rowcnt),trim($data[$room_day]));
            	}
            	$room_day = '812_'.str_pad($i, 2, "0", STR_PAD_LEFT);
            	if(isset($data[$room_day])){
            		$objActSheet->setCellValue('R'.strval($rowcnt),trim($data[$room_day]));
            	}


                $rowcnt++;
            }

            $objActSheet->getStyle('A4:R'.strval($rowcnt-1))->getAlignment()->setWrapText(true);
            $objActSheet->getStyle('A4:R'.strval($rowcnt-1))->applyFromArray($styleArray);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="講座寢室使用情形一覽表.xlsx"');
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
