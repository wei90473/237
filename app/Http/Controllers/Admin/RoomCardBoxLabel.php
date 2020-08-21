<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\StayDistributionDutyListService;
use PhpOffice\PhpSpreadsheet\Style\Border;


class RoomCardBoxLabel extends Controller
{
    public function __construct(User_groupService $user_groupService,StayDistributionDutyListService $stayDistributionDutyListService)
    {
        $this->user_groupService = $user_groupService;
        $this->stayDistributionDutyListService = $stayDistributionDutyListService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('room_card_box_label', $user_group_auth)){
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
        return view('admin/room_card_box_label/list',compact('result'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        $base = $this->stayDistributionDutyListService->getrptPPrptSub8($data['sdate'],$data['edate']);
        if(empty($base)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據');
        //  檔案名稱
        $fileName = 'N28';
        //  範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //  讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        //  fill values
        $row = 1;
        $column = 0;
        $columnBase = array('A','C','E');
        $valueBase = array('B','D','F');
        foreach ($base as $key => $value) {
            $column = $column %3;
            $selectColumn = $columnBase[$column];
            $valueColumn  = $valueBase[$column];
            $objActSheet->getCell($selectColumn.$row)->setValue('班別');
            $objActSheet->mergeCells($selectColumn.($row+1).':'.$selectColumn.($row+2));
            $objActSheet->getCell($selectColumn.($row+1))->setValue('日期');
            $objActSheet->getStyle($selectColumn.($row+1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $objActSheet->getCell($selectColumn.($row+3))->setValue('天');
            $objActSheet->getCell($selectColumn.($row+4))->setValue('輔導員');
            $objActSheet->getCell($selectColumn.($row+5))->setValue('男學員');
            $objActSheet->getCell($selectColumn.($row+6))->setValue('女學員');
            $objActSheet->getCell($valueColumn.$row)->setValue($value->classname.'第'.$value->period.'期');
            $objActSheet->getCell($valueColumn.($row+1))->setValue($value->startdate);
            $objActSheet->getCell($valueColumn.($row+2))->setValue($value->enddate);
            $objActSheet->getCell($valueColumn.($row+3))->setValue($value->trainingday);
            $objActSheet->getCell($valueColumn.($row+4))->setValue($value->counselorname);
            $objActSheet->getCell($valueColumn.($row+5))->setValue($value->mbed);
            $objActSheet->getCell($valueColumn.($row+6))->setValue($value->fbed);
            $column++;
            $row = $column == 3 ? $row+7 : $row;
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
        $objActSheet->getStyle('A1:F'.($row+6))->applyFromArray($styleArray);

        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="房卡盒標示紙.xlsx"');
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
