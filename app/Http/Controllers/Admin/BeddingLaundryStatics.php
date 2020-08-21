<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\BeddingLaundryStaticsService;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Edu_unitset;
use App\Models\T04tb;
use config;

class BeddingLaundryStatics extends Controller
{
    public function __construct(User_groupService $user_groupService, BeddingLaundryStaticsService $BeddingLaundryStaticsService)
    {
        $this->user_groupService = $user_groupService;
        $this->bls = $BeddingLaundryStaticsService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('bedding_laundry_statics', $user_group_auth)){
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
        $queryData['sdate'] = '';
        $queryData['edate'] = '';
        return view('admin/bedding_laundry_statics/list',compact('result','queryData'));
    }

    public function export(Request $request)
    {
        $queryData['sdate'] = $request->input('sdate');
        $queryData['edate'] = $request->input('edate');
        if(empty($queryData['sdate']) || empty($queryData['edate'])){
            $result ="起始日期或結束日期請勿空白";
            return view('admin/bedding_laundry_statics/list',compact('result', 'queryData'));
        }
        if($queryData['sdate'] > $queryData['edate']){
            $result ="起始日期請勿大於結束日期";
            return view('admin/bedding_laundry_statics/list',compact('result', 'queryData'));
        }
        $data = $this->bls->getBeddingLaundryStatics($queryData);
        if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/bedding_laundry_statics/list',compact('result', 'queryData'));
        }
        $result = array();
        foreach ($data as $va) {
            $result[$va['process']][$va['section']][] = array(  'class' =>$va['class'],
                                                                'term'  =>$va['term'],
                                                                'name'  =>$va['name'],
                                                                'sdate' =>$va['sdate'],
                                                                'edate' =>$va['edate'], 
                                                                'count' =>$va['count'],
                                                                'washingfare' =>$va['washingfare']);
        }
        // 檔案名稱
        $fileName = 'N25';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        
        $styleArrayBorders =[
            'borders' =>[
                    'allBorders'=>[
                    'borderStyle' => Border::BORDER_THIN,
                    'color' =>['rgb' => '000000']
                ]
            ]
        ];
        $styleArrayColor =[
            'font' =>[
                'color' =>['rgb' => 'FF0000']
            ]
        ];
        $i=1;
        //  fill values
        foreach ($result as $key => $section) {
            $objActSheet->mergeCells('A'.$i.':F'.$i);
            $objActSheet->setCellValue('A'.$i, '行政院人事行政總處公務人力發展學院(南投院區)');
            $objActSheet->getStyle('A'.$i)->getFont()->setSize(16);
            $objActSheet->getStyle('A'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $objActSheet->mergeCells('A'.($i+1).':F'.($i+1));
            $objActSheet->setCellValue('A'.($i+1), '寢具洗滌數量統計表');
            $objActSheet->getStyle('A'.($i+1))->getFont()->setSize(14);
            $objActSheet->getStyle('A'.($i+1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $objActSheet->mergeCells('A'.($i+2).':F'.($i+2));
            $objActSheet->setCellValue('A'.($i+2),substr($queryData['sdate'], 0, 3)."/".substr($queryData['sdate'], 3, 2)."/".substr($queryData['sdate'], 5, 2)."至".substr($queryData['edate'], 0, 3)."/".substr($queryData['edate'], 3, 2)."/".substr($queryData['edate'], 5, 2));
                $objActSheet->getStyle('A'.($i+2))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $i = $i+3;
            $process = config('app.process.'.$key);
            foreach ($section as $k => $v) {
                $objActSheet->setCellValue('A'.$i, $process.'-'.$k);
                $i++;
                $j=0;
                $total = 0;
                $totalwashingfare = 0;
                foreach ($v as $class) {
                    if($j==0){
                        $objActSheet->setCellValue('A'.$i, '班名');
                        $objActSheet->setCellValue('B'.$i, '開訓');
                        $objActSheet->setCellValue('C'.$i, '結訓');
                        $objActSheet->setCellValue('D'.$i, '學員人數');
                        $objActSheet->setCellValue('E'.$i, '洗滌費用');
                        $objActSheet->setCellValue('F'.$i, '備註');
                        $j=$i;
                        $i++;
                    }
                    $objActSheet->setCellValue('A'.$i, $class['name'].'第'.$class['term'].'期');
                    $objActSheet->setCellValue('B'.$i, substr($class['sdate'], 0, 3)."/".substr($class['sdate'], 3, 2)."/".substr($class['sdate'], 5, 2));
                    $objActSheet->setCellValue('C'.$i, substr($class['edate'], 0, 3)."/".substr($class['edate'], 3, 2)."/".substr($class['edate'], 5, 2));
                    $objActSheet->setCellValue('D'.$i, $class['count']);
                    $objActSheet->setCellValue('E'.$i, $class['washingfare']);
                    $total = $total + $class['count'];
                    $totalwashingfare = $totalwashingfare + $class['washingfare'];
                    $i++;
                }
                $objActSheet->setCellValue('A'.$i, '合計');
                $objActSheet->setCellValue('D'.$i, $total);
                $objActSheet->setCellValue('E'.$i, $totalwashingfare);
                $objActSheet->getStyle('D'.$i.':E'.$i)->applyFromArray($styleArrayColor);
                $objActSheet->getStyle('A'.$j.':F'.$i)->applyFromArray($styleArrayBorders);
                $i = $i+2;
            }
        }

        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="寢具洗滌數量統計表.xlsx"');
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
