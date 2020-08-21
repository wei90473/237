<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\Stay_registrationService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class StayRegistration extends Controller
{
    public function __construct(User_groupService $user_groupService, Stay_registrationService $stay_registrationService)
    {
        $this->user_groupService = $user_groupService;
        $this->stay_registrationService = $stay_registrationService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('stay_registration', $user_group_auth)){
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
        return view('admin/stay_registration/list',compact('result'));
    }

    public function export(Request $request)
    {
        $queryData['sdate'] = str_replace('-', '', $request->input('sdatetw'));
        $queryData['edate'] = str_replace('-', '', $request->input('edatetw'));
        if(empty($queryData['sdate']) || empty($queryData['edate'])){
            $result ="起始日期或結束日期請勿空白";
            return view('admin/stay_registration/list',compact('result'));
        }
        if($queryData['sdate'] > $queryData['edate']){
            $result ="起始日期請勿大於結束日期";
            return view('admin/stay_registration/list',compact('result'));
        }
        $data = $this->stay_registrationService->getStay_registration($queryData);

        if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/stay_registration/list',compact('result'));
        }

         // 檔案名稱
         $fileName = 'N19';
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

            $rowcnt = 6;
            $no = 1;

            $title_date = substr($queryData['sdate'], 0, 3)." / ".substr($queryData['sdate'], 3, 2)." / ".substr($queryData['sdate'], 5, 2)."  至  ".substr($queryData['edate'], 0, 3)." / ".substr($queryData['edate'], 3, 2)." / ".substr($queryData['edate'], 5, 2);

            $objActSheet->setCellValue('A2',trim($title_date));
            $objActSheet->setCellValue('A3',trim("列印日期：".(date('Y')-1911)."/".date('m')."/".date('d')));

            $sum_g = '0';
            $sum_h = '0';
            $sum_i = '0';
            $sum_j = '0';
            $sum_k = '0';
            $sum_l = '0';

            foreach($data as $row){
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($no));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim($row['name']));
                $objActSheet->setCellValue('C'.strval($rowcnt),trim($row['branchname']));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim($row['term']));
                $objActSheet->setCellValue('E'.strval($rowcnt),trim($row['sdate'].' - '.$row['edate']));
                $objActSheet->setCellValue('F'.strval($rowcnt),trim(floor($row['trainday'])));
                $objActSheet->setCellValue('G'.strval($rowcnt),trim($row['all_count']));
                $objActSheet->setCellValue('H'.strval($rowcnt),trim($row['M']));
                $objActSheet->setCellValue('I'.strval($rowcnt),trim($row['F']));
                $objActSheet->setCellValue('J'.strval($rowcnt),trim(($row['dorm_M']+$row['dorm_F'])));
                $objActSheet->setCellValue('K'.strval($rowcnt),trim($row['dorm_M']));
                $objActSheet->setCellValue('L'.strval($rowcnt),trim($row['dorm_F']));
                $objActSheet->setCellValue('M'.strval($rowcnt),trim($row['username']));
                $objActSheet->setCellValue('N'.strval($rowcnt),trim($row['remark']));

                $sum_g += $row['all_count'];
                $sum_h += $row['M'];
                $sum_i += $row['F'];
                $sum_j += ($row['dorm_M']+$row['dorm_F']);
                $sum_k += $row['dorm_M'];
                $sum_l += $row['dorm_F'];

                $rowcnt++;
                $no++;
            }
            $objActSheet->setCellValue('B'.strval($rowcnt),trim('合計'));
            $objActSheet->setCellValue('G'.strval($rowcnt),trim($sum_g));
            $objActSheet->setCellValue('H'.strval($rowcnt),trim($sum_h));
            $objActSheet->setCellValue('I'.strval($rowcnt),trim($sum_i));
            $objActSheet->setCellValue('J'.strval($rowcnt),trim($sum_j));
            $objActSheet->setCellValue('K'.strval($rowcnt),trim($sum_k));
            $objActSheet->setCellValue('L'.strval($rowcnt),trim($sum_l));

            $objActSheet->getStyle('A6:N'.strval($rowcnt))->getAlignment()->setWrapText(true);
            $objActSheet->getStyle('A6:N'.strval($rowcnt))->applyFromArray($styleArray);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="住宿登記概況表.xlsx"');
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
