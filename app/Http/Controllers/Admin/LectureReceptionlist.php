<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\LectureReceptionListService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LectureReceptionList extends Controller
{
    public function __construct(User_groupService $user_groupService, LectureReceptionListService $lectureReceptionListService)
    {
        $this->user_groupService = $user_groupService;
        $this->lectureReceptionListService = $lectureReceptionListService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_reception_list', $user_group_auth)){
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
        return view('admin/lecture_reception_list/list',compact('result'));
    }

    public function export(Request $request)
    {
        $queryData['sdatetw'] = str_replace('-', '', $request->input('sdatetw'));
        $queryData['type'] = $request->input('type');

        if($queryData['type'] == '1'){
        	//一般性
        	$data = $this->lectureReceptionListService->getLectureReceptionList1($queryData);
        }else{
        	//機敏性
        	$data = $this->lectureReceptionListService->getLectureReceptionList2($queryData);
        }

        // dd($data);
        if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/lecture_reception_list/list',compact('result'));
        }

         // 檔案名稱
         $fileName = 'H20';
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

            $rowcnt = 7;

            $weekday  = date('w', strtotime($queryData['sdatetw']+19110000));
            $weeklist = array('日', '一', '二', '三', '四', '五', '六');
            $week = '(星期' . $weeklist[$weekday].')';


            $objActSheet->setCellValue('H4',trim(substr($queryData['sdatetw'], 0, 3).'/'.substr($queryData['sdatetw'], 3, 2).'/'.substr($queryData['sdatetw'], 5, 2)));
            $objActSheet->setCellValue('J4',trim($week));

            foreach($data as $row){
                $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':A'.strval($rowcnt+2));
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($row['name']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('B'.strval($rowcnt).':B'.strval($rowcnt+2));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim($row['position']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('C'.strval($rowcnt).':C'.strval($rowcnt+2));
                $objActSheet->setCellValue('C'.strval($rowcnt),trim($row['class_name']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('D'.strval($rowcnt).':D'.strval($rowcnt+2));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim($row['breakfast']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('E'.strval($rowcnt).':E'.strval($rowcnt+2));
                $objActSheet->setCellValue('E'.strval($rowcnt),trim($row['lunch']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('F'.strval($rowcnt).':F'.strval($rowcnt+2));
                $objActSheet->setCellValue('F'.strval($rowcnt),trim($row['dinner']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('G'.strval($rowcnt).':G'.strval($rowcnt+2));
                $objActSheet->setCellValue('G'.strval($rowcnt),trim($row['stay']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('H'.strval($rowcnt).':H'.strval($rowcnt+2));
                $objActSheet->setCellValue('H'.strval($rowcnt),trim($row['room']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('I'.strval($rowcnt).':I'.strval($rowcnt+2));
                $objActSheet->setCellValue('I'.strval($rowcnt),trim($row['clas_time']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('J'.strval($rowcnt).':L'.strval($rowcnt));
                $objActSheet->setCellValue('J'.strval($rowcnt),trim($row['car_type_1']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('J'.strval($rowcnt+1).':L'.strval($rowcnt+1));
                $objActSheet->setCellValue('J'.strval($rowcnt+1),trim($row['mobiltel']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('J'.strval($rowcnt+2).':L'.strval($rowcnt+2));
                $objActSheet->setCellValue('J'.strval($rowcnt+2),trim($row['car_type_2']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('M'.strval($rowcnt).':M'.strval($rowcnt));
                $objActSheet->setCellValue('M'.strval($rowcnt),trim($row['end']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('M'.strval($rowcnt+1).':M'.strval($rowcnt+1));
                $objPHPExcel->getActiveSheet(0)->mergeCells('M'.strval($rowcnt+2).':M'.strval($rowcnt+2));
                $objActSheet->setCellValue('M'.strval($rowcnt+2),trim($row['start']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('N'.strval($rowcnt).':N'.strval($rowcnt));
                $objActSheet->setCellValue('N'.strval($rowcnt),trim($row['car1']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('N'.strval($rowcnt+1).':N'.strval($rowcnt+1));
                $objPHPExcel->getActiveSheet(0)->mergeCells('N'.strval($rowcnt+2).':N'.strval($rowcnt+2));
                $objActSheet->setCellValue('N'.strval($rowcnt+2),trim($row['car2']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('O'.strval($rowcnt).':O'.strval($rowcnt));
                $objActSheet->setCellValue('O'.strval($rowcnt),trim($row['license_plate1']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('O'.strval($rowcnt+1).':O'.strval($rowcnt+1));
                $objPHPExcel->getActiveSheet(0)->mergeCells('O'.strval($rowcnt+2).':O'.strval($rowcnt+2));
                $objActSheet->setCellValue('O'.strval($rowcnt+2),trim($row['license_plate2']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('P'.strval($rowcnt).':P'.strval($rowcnt));
                $objActSheet->setCellValue('P'.strval($rowcnt),trim($row['remark1']));
                $objPHPExcel->getActiveSheet(0)->mergeCells('P'.strval($rowcnt+1).':P'.strval($rowcnt+1));
                $objPHPExcel->getActiveSheet(0)->mergeCells('P'.strval($rowcnt+2).':P'.strval($rowcnt+2));
                $objActSheet->setCellValue('P'.strval($rowcnt+2),trim($row['remark2']));

                $rowcnt = $rowcnt+3;
            }
            //製表人：綜合規劃組張○○
            $name = auth()->user()->username;
            if($queryData['type'] == '1'){
                $name = mb_substr($name, 0, 1, "UTF-8").'○○';
            }
            $objActSheet->setCellValue('C'.strval($rowcnt+1),trim('製表人：'.auth()->user()->section.$name));

            $objActSheet->getStyle('A7:P'.strval($rowcnt-1))->getAlignment()->setWrapText(true);
            $objActSheet->getStyle('A7:P'.strval($rowcnt-1))->applyFromArray($styleArray);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="講座接待一覽表.xlsx"');
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
