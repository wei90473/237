<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\PickupLocationTimesService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PickupLocationTimes extends Controller
{
    public function __construct(User_groupService $user_groupService, PickupLocationTimesService $pickupLocationTimesService)
    {
        $this->user_groupService = $user_groupService;
        $this->pickupLocationTimesService = $pickupLocationTimesService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('pickup_location_times', $user_group_auth)){
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
        $asasa = 'Z';
        $asasa++;
        $asasa++;
        // dd($asasa);
        $result="";
        return view('admin/pickup_location_times/list',compact('result'));
    }

    public function export(Request $request)
    {

        $queryData['sdate'] = str_replace('-', '', $request->input('sdatetw'));
        $queryData['edate'] = str_replace('-', '', $request->input('edatetw'));
        // dd($queryData);
        if(empty($queryData['sdate']) || empty($queryData['edate'])){
            $result ="起始日期或結束日期請勿空白";
            return view('admin/pickup_location_times/list',compact('result'));
        }
        if($queryData['sdate'] > $queryData['edate']){
            $result ="起始日期請勿大於結束日期";
            return view('admin/pickup_location_times/list',compact('result'));
        }
        $data = $this->pickupLocationTimesService->getPickupLocationTimes($queryData);

        if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/pickup_location_times/list',compact('result'));
        }

         // 檔案名稱
         $fileName = 'H24';
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

            $rowcnt = 2;
            $rowA = 'B';
            $allcount = '0';
            $alltotal = '0';

            $title_date = "計程車講座接送地點及次數一覽表\n";
            $title_date .= "自  ".substr($queryData['sdate'], 0, 3)."  年 ".substr($queryData['sdate'], 3, 2)." 月 ".substr($queryData['sdate'], 5, 2)." 日  起 至  ".substr($queryData['edate'], 0, 3)."  年 ".substr($queryData['edate'], 3, 2)." 月 ".substr($queryData['edate'], 5, 2)." 日";

            $objActSheet->setCellValue('A1',trim($title_date));

            $objActSheet->setCellValue('A'.strval($rowcnt),trim('接送地點'));
            $objActSheet->setCellValue('A'.strval($rowcnt+1),trim('次數'));
            $objActSheet->setCellValue('A'.strval($rowcnt+2),trim('金額合計'));

            foreach($data as $row){

                $objActSheet->setCellValue($rowA.strval($rowcnt),trim($row['location']));
                $objActSheet->setCellValue($rowA.strval($rowcnt+1),trim($row['count']));
                $objActSheet->setCellValue($rowA.strval($rowcnt+2),trim($row['total']));

                $allcount += $row['count'];
                $alltotal += $row['total'];

                $rowA++;
            }

            $objActSheet->setCellValue($rowA.strval($rowcnt),trim('合計'));
            $objActSheet->setCellValue($rowA.strval($rowcnt+1),trim($allcount));
            $objActSheet->setCellValue($rowA.strval($rowcnt+2),trim($alltotal));

            $objActSheet->getStyle('A2:'.$rowA.strval($rowcnt+2))->getAlignment()->setWrapText(true);
            $objActSheet->getStyle('A2:'.$rowA.strval($rowcnt+2))->applyFromArray($styleArray);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="接送地點及次數一覽表.xlsx"');
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
