<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\FieldService;
use App\Services\StayListByFloorService;
use PhpOffice\PhpSpreadsheet\Style\Border;


class StayListByFloor extends Controller
{
    public function __construct(User_groupService $user_groupService, FieldService $fieldService, StayListByFloorService $stayListByFloorService)
    {
        $this->user_groupService = $user_groupService;
        $this->fieldService = $fieldService;
        $this->stayListByFloorService = $stayListByFloorService;

        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('staylist_byfloor', $user_group_auth)){
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
        $AllFloors = $this->fieldService->getAllFloors()->toArray();
        // dd($AllFloors);
        $result="";
        $queryData['sdate'] = '';
        $queryData['edate'] = '';
        $queryData['timeperiod'] = '';
        $queryData['floorno'] = '';

        return view('admin/staylist_byfloor/list',compact('result', 'AllFloors','queryData'));
    }

    public function export(Request $request)
    {
        $timeperiod = array(
            '1' => '早',
            '2' => '中',
            '3' => '晚',
        );

        $queryData2['sdate'] = str_replace('-', '', $request->input('sdatetw'));
        $queryData2['edate'] = str_replace('-', '', $request->input('edatetw'));
        $queryData2['timeperiod'] = $request->input('timeperiod');
        $queryData2['floorno'] = $request->input('floorno');

        $AllFloors = $this->fieldService->getAllFloors()->toArray();
        $Floors_name = array();
        foreach($AllFloors as $AllFloors_row){
            $Floors_name[$AllFloors_row['floorno']] = $AllFloors_row['floorname'];
        }

        $queryData['sdate'] = $request->input('sdatetw');
        $queryData['edate'] = $request->input('edatetw');
        $queryData['timeperiod'] = $request->input('timeperiod');
        $queryData['floorno'] = $request->input('floorno');
        if(empty($queryData2['sdate']) || empty($queryData2['edate'])){
            $result ="起始日期或結束日期請勿空白";
            return view('admin/staylist_byfloor/list',compact('result', 'AllFloors','queryData'));
        }
        if($queryData2['sdate'] > $queryData2['edate']){
            $result ="起始日期請勿大於結束日期";
            return view('admin/staylist_byfloor/list',compact('result', 'AllFloors','queryData'));
        }
        $data = $this->stayListByFloorService->getStayListByFloor($queryData2);

        if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/staylist_byfloor/list',compact('result', 'AllFloors','queryData'));
        }

         // 檔案名稱
         $fileName = 'N20';
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
            $title = '住宿人員一覽表';
            $page = '1';

            foreach($data as $key => $row){
                $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':C'.strval($rowcnt));
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($title));
                $rowcnt++;
                $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':C'.strval($rowcnt));
                $title_date = substr($key, 0, 3)."/".substr($key, 3, 2)."/".substr($key, 5, 2)."(".$timeperiod[$queryData2['timeperiod']].")";
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($title_date));
                $rowcnt++;
                $floor = "樓別：".$Floors_name[$queryData['floorno']];
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($floor));
                $objPHPExcel->getActiveSheet(0)->mergeCells('B'.strval($rowcnt).':C'.strval($rowcnt));
                $print_day = "列印日期：".(date('Y')-1911)."/".date('m')."/".date('d')."  頁次：".$page;
                $objActSheet->setCellValue('B'.strval($rowcnt),trim($print_day));
                $rowcnt++;
                $rowcnt_A = $rowcnt;
                $objActSheet->setCellValue('A'.strval($rowcnt),trim('寢室床位'));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim('班別'));
                $objActSheet->setCellValue('C'.strval($rowcnt),trim('姓名'));
                $rowcnt++;
                foreach($row as $room_data){
                    $n= substr($room_data['bedno'],-1);
                    $objActSheet->setCellValue('A'.strval($rowcnt),trim($room_data['roomname'].'-'.$n));
                    $objActSheet->setCellValue('B'.strval($rowcnt),trim($room_data['name'].'第'.$room_data['term'].'期'));
                    $objActSheet->setCellValue('C'.strval($rowcnt),trim($room_data['cname']));
                    $rowcnt++;
                }

                $objActSheet->getStyle('A'.strval($rowcnt_A).':C'.strval($rowcnt-1))->applyFromArray($styleArray);
                $page++;
            }
            // $objActSheet->getStyle('A3:B'.strval($rowcnt-1))->getAlignment()->setWrapText(true);
            // $objActSheet->getStyle('A3:B'.strval($rowcnt-1))->applyFromArray($styleArray);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="各樓住宿班次人員一覽表.xlsx"');
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
