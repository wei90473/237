<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\StayDistributionDutyListService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RoomCardReceipt extends Controller
{
    public function __construct(User_groupService $user_groupService,StayDistributionDutyListService $stayDistributionDutyListService)
    {
        $this->user_groupService = $user_groupService;
        $this->stayDistributionDutyListService = $stayDistributionDutyListService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('room_card_receipt', $user_group_auth)){
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
        return view('admin/room_card_receipt/list',compact('result'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        $syear = substr($data['sdate'], 0,3); 
        $sM = substr($data['sdate'], 3,2);
        $sD = substr($data['sdate'], 5,2);
        $eyear = substr($data['edate'], 0,3); 
        $eM = substr($data['edate'], 3,2);
        $eD = substr($data['edate'], 5,2);
        $dateRank = $syear.'/'.$sM.'/'.$sD.'至'.$eyear.'/'.$eM.'/'.$eD;
        $base = $this->stayDistributionDutyListService->getrptPPrptSub8($data['sdate'],$data['edate']);
        if(empty($base)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據');
        //  檔案名稱
        $fileName = 'N29';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        //  fill values
        $row = 4;
        $objActSheet->getCell('A2')->setValue('週別:'.$dateRank);
        foreach ($base as $key => $value) {
            $objActSheet->getCell('A'.$row)->setValue($value->classname.'第'.$value->period.'期');
            $objActSheet->getCell('B'.$row)->setValue($value->startdate);
            $objActSheet->getCell('C'.$row)->setValue($value->enddate);
            $objActSheet->getCell('D'.$row)->setValue($value->mbed);
            $objActSheet->getCell('E'.$row)->setValue($value->fbed);
            $row++;
        }
        $styleArray =[
            'borders' =>[
                    'allBorders'=>[
                    'borderStyle' => Border::BORDER_THIN,
                    'color' =>['rgb' => '000000'],
                ],
            ],
        ];
        // apply borders
        $objActSheet->getStyle('A4:G'.($row-1))->applyFromArray($styleArray);

        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="房卡簽收單.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        //匯出
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $objWriter->save('php://output');
        exit;

    }
}
