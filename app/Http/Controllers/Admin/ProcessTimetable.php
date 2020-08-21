<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Term_processService;
use App\Services\User_groupService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProcessTimetable extends Controller
{
    public function __construct(Term_processService $term_processService, User_groupService $user_groupService)
    {
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('process_timetable', $user_group_auth)){
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
        $data=[];
        $this_yesr = date('Y') - 1911;

        if(null == $request->get('yerly')){
            $queryData['yerly'] = $this_yesr;
        }else{
            $queryData['yerly'] = $request->get('yerly');
        }

        $queryData['process_complete'] = $request->get('process_complete');
        $queryData['job_complete'] = $request->get('job_complete');

        $queryData['search'] = $request->get('search');
        // 測試用 NIMA
        // $queryData['sponsor'] = 'NIMA';
        $queryData['sponsor'] = auth()->user()->userid;

        if($queryData['search'] != 'search' ){

        }else{

            $data=$this->term_processService->getExport($queryData);

            $class_rooms = $this->term_processService->getClassRooms();

            if($data==[]){
                $result ="此條件查無資料，請重新查詢";
                return view('admin/process_timetable/list', compact('queryData'));
            }

            // 檔案名稱
            $fileName = 'C1';
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

            $rowcnt = 3;

            $objActSheet->setCellValue('B1',trim('使用者帳號：'.auth()->user()->username));
            $objActSheet->setCellValue('G1',trim('列印年度：'.$queryData['yerly']));

            foreach($data as $class_row){
                foreach($class_row['job_data'] as $process_row){
                    $class_room = '';
                    if($class_row['site_branch'] == '1'){
                        $class_room = $class_rooms['m14tb'][$class_row['site']];
                    }
                    if($class_row['site_branch'] == '2'){
                        $class_room = $class_rooms['m25tb'][$class_row['site']];
                    }
                    $objActSheet->setCellValue('A'.strval($rowcnt),trim($class_row['class']));
                    $objActSheet->setCellValue('B'.strval($rowcnt),trim($class_row['name']));
                    $objActSheet->setCellValue('C'.strval($rowcnt),trim($class_row['term']));
                    $objActSheet->setCellValue('D'.strval($rowcnt),trim($class_room));
                    $objActSheet->setCellValue('E'.strval($rowcnt),trim($class_row['sdate']));
                    $objActSheet->setCellValue('F'.strval($rowcnt),trim($class_row['edate']));
                    $objActSheet->setCellValue('G'.strval($rowcnt),trim($process_row['name']));
                    $objActSheet->setCellValue('H'.strval($rowcnt),trim($process_row['deadline']));
                    $objActSheet->setCellValue('I'.strval($rowcnt),trim($process_row['complete']));
                    $rowcnt++;
                }
            }
            $objActSheet->getStyle('A3:I'.strval($rowcnt-1))->getAlignment()->setWrapText(true);
            $objActSheet->getStyle('A3:I'.strval($rowcnt-1))->applyFromArray($styleArray);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="班務流程指引維護.xlsx"');

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
        return view('admin/process_timetable/list', compact('queryData'));
    }
}
