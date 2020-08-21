<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\StayStaticsAfterRegService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class StayStaticsAfterReg extends Controller
{
    public function __construct(User_groupService $user_groupService, StayStaticsAfterRegService $stayStaticsAfterRegService)
    {
        $this->user_groupService = $user_groupService;
        $this->stayStaticsAfterRegService = $stayStaticsAfterRegService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('stay_statics_after_reg', $user_group_auth)){
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
        return view('admin/stay_statics_after_reg/list',compact('result', 'queryData'));
    }

    public function export(Request $request)
    {
        $queryData['sdate'] = str_replace('-', '', $request->input('sdatetw'));
        $queryData['edate'] = str_replace('-', '', $request->input('edatetw'));

        $queryData['sdatetw'] = $request->input('sdatetw');
        $queryData['edatetw'] = $request->input('edatetw');

        if(empty($queryData['sdatetw']) || empty($queryData['edatetw'])){
            $result ="起始日期或結束日期請勿空白";
            return view('admin/stay_statics_after_reg/list',compact('result', 'queryData'));
        }
        if($queryData['sdate'] > $queryData['edate']){
            $result ="起始日期請勿大於結束日期";
            return view('admin/stay_statics_after_reg/list',compact('result', 'queryData'));
        }
        $data = $this->stayStaticsAfterRegService->getStayStaticsAfterReg($queryData);

        if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/stay_statics_after_reg/list',compact('result', 'queryData'));
        }

         // 檔案名稱
         $fileName = 'N26';
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

            $rowcnt = 1;
            $title1 = "行政院人事行政總處公務人力發展學院(南投院區)";
            $title2 = "住宿統計表";

            $title_date = substr($queryData['sdate'], 0, 3)." / ".substr($queryData['sdate'], 3, 2)." / ".substr($queryData['sdate'], 5, 2)." 至 ".substr($queryData['edate'], 0, 3)." / ".substr($queryData['edate'], 3, 2)." / ".substr($queryData['edate'], 5, 2);

            foreach($data as $row){
                $objPHPExcel->getActiveSheet()->getStyle('A'.strval($rowcnt).':F'.strval($rowcnt))->getFont()->setSize(16);
                $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':F'.strval($rowcnt));
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($title1));
                $rowcnt++;
                $objPHPExcel->getActiveSheet()->getStyle('A'.strval($rowcnt).':F'.strval($rowcnt))->getFont()->setSize(14);
                $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':F'.strval($rowcnt));
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($title2));
                $rowcnt++;
                $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':F'.strval($rowcnt));
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($title_date));
                $rowcnt++;
                // $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':B'.strval($rowcnt));
                $objPHPExcel->getActiveSheet(0)->mergeCells('D'.strval($rowcnt).':F'.strval($rowcnt));
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($row['floorname']));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim("列印日期：".(date('Y')-1911)."/".date('m')."/".date('d')));
                $rowcnt++;
                $rowcnt_A = $rowcnt;
                $objActSheet->setCellValue('A'.strval($rowcnt),trim('班名'));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim('開訓'));
                $objActSheet->setCellValue('C'.strval($rowcnt),trim('結訓'));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim('學員人數'));
                $objActSheet->setCellValue('E'.strval($rowcnt),trim('住宿天數'));
                $objActSheet->setCellValue('F'.strval($rowcnt),trim('住宿人天'));
                $rowcnt++;
                $sum_d = '0';
                $sum_e = '0';
                $sum_f = '0';
                foreach($row['class_data'] as $class_row){
                    $objActSheet->setCellValue('A'.strval($rowcnt),trim($class_row['name'].'  第'.$class_row['term'].'期'));
                    $objActSheet->setCellValue('B'.strval($rowcnt),trim($class_row['sdate']));
                    $objActSheet->setCellValue('C'.strval($rowcnt),trim($class_row['edate']));
                    $objActSheet->setCellValue('D'.strval($rowcnt),trim($class_row['dorm_count']));
                    $objActSheet->setCellValue('E'.strval($rowcnt),trim($class_row['day']));
                    $objActSheet->setCellValue('F'.strval($rowcnt),trim($class_row['total']));

                    $sum_d += $class_row['dorm_count'];
                    $sum_e += $class_row['day'];
                    $sum_f += $class_row['total'];
                    $rowcnt++;
                }

                $objActSheet->setCellValue('A'.strval($rowcnt),trim('合計'));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim($sum_d));
                $objActSheet->setCellValue('E'.strval($rowcnt),trim($sum_e));
                $objActSheet->setCellValue('F'.strval($rowcnt),trim($sum_f));

                $objActSheet->getStyle('A'.strval($rowcnt_A).':F'.strval($rowcnt))->applyFromArray($styleArray);

                $rowcnt++;
            }

            $objActSheet->getStyle('A1:F'.strval($rowcnt))->getAlignment()->setWrapText(true);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="住宿統計表(報到後).xlsx"');
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
