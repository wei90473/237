<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\ClassStatusService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ClassStatus extends Controller
{
    public function __construct(User_groupService $user_groupService, ClassStatusService $classStatusService)
    {
        $this->user_groupService = $user_groupService;
        $this->classStatusService = $classStatusService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_status', $user_group_auth)){
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
        return view('admin/class_status/list',compact('result', 'queryData'));
    }

    public function export(Request $request)
    {
        $queryData['sdate'] = str_replace('-', '', $request->input('sdatetw'));
        $queryData['edate'] = str_replace('-', '', $request->input('edatetw'));

        $queryData['sdatetw'] = $request->input('sdatetw');
        $queryData['edatetw'] = $request->input('edatetw');

        if(empty($queryData['sdatetw']) || empty($queryData['edatetw'])){
            $result ="起始日期或結束日期請勿空白";
            return view('admin/class_status/list',compact('result', 'queryData'));
        }
        if($queryData['sdate'] > $queryData['edate']){
            $result ="起始日期請勿大於結束日期";
            return view('admin/class_status/list',compact('result', 'queryData'));
        }
        $data = $this->classStatusService->getClassStatus($queryData);

        if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/class_status/list',compact('result', 'queryData'));
        }
         // 檔案名稱
         $fileName = 'N24';
         //範本位置
         $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
         //讀取excel

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

            $rowcnt = 6;
            $no = 1;

            $title_date = substr($queryData['sdate'], 0, 3)." / ".substr($queryData['sdate'], 3, 2)." / ".substr($queryData['sdate'], 5, 2)."  ~  ".substr($queryData['edate'], 0, 3)." / ".substr($queryData['edate'], 3, 2)." / ".substr($queryData['edate'], 5, 2)."  開辦班次概況表";

            $objActSheet->setCellValue('A2',trim($title_date));
            $objActSheet->setCellValue('A3',trim("列印日期：".(date('Y')-1911)."/".date('m')."/".date('d')));

            $process_1 = '0';
            $process_2 = '0';
            $process_3 = '0';
            $process_4 = '0';
            $process_5 = '0';

            $sum_j = '0';
            $sum_k = '0';
            $sum_l = '0';
            $sum_m = '0';
            $sum_n = '0';
            $sum_o = '0';
            $sum_p = '0';

            foreach($data as $row){
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($no));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim($row['name']));
                $objActSheet->setCellValue('C'.strval($rowcnt),trim($row['branchname']));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim($row['term']));
                $objActSheet->setCellValue('E'.strval($rowcnt),trim($row['process_name']));
                $objActSheet->setCellValue('F'.strval($rowcnt),trim($row['branch_name']));
                $objActSheet->setCellValue('G'.strval($rowcnt),trim($row['sdate'].'-'.$row['edate']));
                $objActSheet->setCellValue('H'.strval($rowcnt),trim(floor($row['trainday'])));
                $objActSheet->setCellValue('I'.strval($rowcnt),trim(round($row['trainhour'], 1)));
                $objActSheet->setCellValue('J'.strval($rowcnt),trim($row['all_count']));
                $objActSheet->setCellValue('K'.strval($rowcnt),trim($row['M']));
                $objActSheet->setCellValue('L'.strval($rowcnt),trim($row['F']));
                $objActSheet->setCellValue('M'.strval($rowcnt),trim($row['register_M']));
                $objActSheet->setCellValue('N'.strval($rowcnt),trim($row['register_F']));
                $objActSheet->setCellValue('O'.strval($rowcnt),trim($row['dorm_M']));
                $objActSheet->setCellValue('P'.strval($rowcnt),trim($row['dorm_F']));
                $objActSheet->setCellValue('Q'.strval($rowcnt),trim($row['client']));
                $objActSheet->setCellValue('R'.strval($rowcnt),trim($row['remark']));

                if($row['process'] == '1'){
                    $process_1 += '1';
                }
                if($row['process'] == '2'){
                    $process_2 += '1';
                }
                if($row['process'] == '3'){
                    $process_3 += '1';
                }
                if($row['process'] == '4'){
                    $process_4 += '1';
                }
                if($row['process'] == '5'){
                    $process_5 += '1';
                }

                $sum_j += $row['all_count'];
                $sum_k += $row['M'];
                $sum_l += $row['F'];
                $sum_m += $row['register_M'];
                $sum_n += $row['register_F'];
                $sum_o += $row['dorm_M'];
                $sum_p += $row['dorm_F'];

                $rowcnt++;
                $no++;
            }
            $objActSheet->setCellValue('A'.strval($rowcnt),trim('合計'));
            $objPHPExcel->getActiveSheet(0)->mergeCells('B'.strval($rowcnt).':G'.strval($rowcnt));
            $B_string = "自辦班(".$process_1."班)  委訓班(".$process_2."班)  合作辦理(".$process_3."班)  外地班(".$process_4."班)  巡迴研習(".$process_5."班)";
            $objActSheet->setCellValue('B'.strval($rowcnt),trim($B_string));

            $objActSheet->setCellValue('J'.strval($rowcnt),trim($sum_j));
            $objActSheet->setCellValue('K'.strval($rowcnt),trim($sum_k));
            $objActSheet->setCellValue('L'.strval($rowcnt),trim($sum_l));
            $objActSheet->setCellValue('M'.strval($rowcnt),trim($sum_m));
            $objActSheet->setCellValue('N'.strval($rowcnt),trim($sum_n));
            $objActSheet->setCellValue('O'.strval($rowcnt),trim($sum_o));
            $objActSheet->setCellValue('P'.strval($rowcnt),trim($sum_p));

            $objActSheet->getStyle('A6:R'.strval($rowcnt))->getAlignment()->setWrapText(true);
            $objActSheet->getStyle('A6:R'.strval($rowcnt))->applyFromArray($styleArray);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="開辦班次概況表(含住宿).xlsx"');
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
