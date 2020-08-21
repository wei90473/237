<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\StayDistributionByClassService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class StayDistributionByClass extends Controller
{
    public function __construct(User_groupService $user_groupService, StayDistributionByClassService $stayDistributionByClassService)
    {
        $this->user_groupService = $user_groupService;
        $this->stayDistributionByClassService = $stayDistributionByClassService;

        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('stay_distribution_byclass', $user_group_auth)){
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
        return view('admin/stay_distribution_byclass/list',compact('result', 'queryData'));
    }

    public function export(Request $request)
    {

    	$queryData['sdate'] = str_replace('-', '', $request->input('sdatetw'));

        $queryData['sdatetw'] = $request->input('sdatetw');

        if(empty($queryData['sdatetw']) ){
            $result ="開訓日期請勿空白";
            return view('admin/stay_distribution_byclass/list',compact('result', 'queryData'));
        }

        $data = $this->stayDistributionByClassService->getStayDistributionByClass($queryData);

        if($data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/stay_distribution_byclass/list',compact('result', 'queryData'));
        }

         // 檔案名稱
         $fileName = 'N22';
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
            $title1 = '行政院人事行政總處公務人力發展學院(南投院區)';
            $title2 = '學員住宿分配表';
            $title3 = substr($queryData['sdate'], 0, 3)."/".substr($queryData['sdate'], 3, 2)."/".substr($queryData['sdate'], 5, 2);

            foreach($data as $class_row){

                $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':J'.strval($rowcnt));
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($title1));
                $rowcnt++;
                $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':J'.strval($rowcnt));
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($title2));
                $rowcnt++;
                $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':J'.strval($rowcnt));
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($title3));
                $rowcnt++;
                $title4 = $class_row['name']." 第".$class_row['term']."期";
                $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':J'.strval($rowcnt));
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($title4));
                $rowcnt++;

                $rowcnt_A = $rowcnt;
                $objActSheet->setCellValue('A'.strval($rowcnt),trim('姓名'));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim('學號'));
                $objActSheet->setCellValue('C'.strval($rowcnt),trim('性別'));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim('住宿寢室'));
                $objActSheet->setCellValue('E'.strval($rowcnt),trim('提前住宿簽名欄'));
                $objActSheet->setCellValue('F'.strval($rowcnt),trim('姓名'));
                $objActSheet->setCellValue('G'.strval($rowcnt),trim('學號'));
                $objActSheet->setCellValue('H'.strval($rowcnt),trim('性別'));
                $objActSheet->setCellValue('I'.strval($rowcnt),trim('住宿寢室'));
                $objActSheet->setCellValue('J'.strval($rowcnt),trim('提前住宿簽名欄'));
                $rowcnt++;
                $M_rowcnt = $rowcnt;
                $F_rowcnt = $rowcnt;

                if(!empty($class_row['M_data'])){
                	foreach($class_row['M_data'] as $M_data){
                		$objActSheet->setCellValue('A'.strval($M_rowcnt),trim($M_data['cname']));
	                    $objActSheet->setCellValue('B'.strval($M_rowcnt),trim($M_data['no']));
	                    $objActSheet->setCellValue('C'.strval($M_rowcnt),trim('男'));
	                    $objActSheet->setCellValue('D'.strval($M_rowcnt),trim($M_data['floorname']."\n".$M_data['roomname']));
	                    $objActSheet->setCellValue('E'.strval($M_rowcnt),'');
	                    $M_rowcnt++;
                	}
                }

                if(!empty($class_row['F_data'])){
                	foreach($class_row['F_data'] as $F_data){
                		$objActSheet->setCellValue('F'.strval($F_rowcnt),trim($F_data['cname']));
	                    $objActSheet->setCellValue('G'.strval($F_rowcnt),trim($F_data['no']));
	                    $objActSheet->setCellValue('H'.strval($F_rowcnt),trim('女'));
	                    $objActSheet->setCellValue('I'.strval($F_rowcnt),trim($F_data['floorname']."\n".$F_data['roomname']));
	                    $objActSheet->setCellValue('J'.strval($F_rowcnt),'');
	                    $F_rowcnt++;
                	}
                }

                if($M_rowcnt < $F_rowcnt){
                	$rowcnt = $F_rowcnt;
                }else if($M_rowcnt > $F_rowcnt){
                	$rowcnt = $M_rowcnt;
                }else{
                	$rowcnt = $M_rowcnt;
                }

                $objActSheet->getStyle('A'.strval($rowcnt_A).':J'.strval($rowcnt-1))->applyFromArray($styleArray);
            }

            $objActSheet->getStyle('A1:J'.strval($rowcnt-1))->getAlignment()->setWrapText(true);
            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="學員住宿分配一覽表(分班).xlsx"');
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
