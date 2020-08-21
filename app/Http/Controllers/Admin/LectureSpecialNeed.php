<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\Teacher_relatedService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LectureSpecialNeed extends Controller
{
    public function __construct(Teacher_relatedService $teacher_relatedService, User_groupService $user_groupService)
    {
        $this->teacher_relatedService = $teacher_relatedService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_special_need', $user_group_auth)){
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
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=$temp;
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
        $termArr=$temp;
        $result="";
        return view('admin/lecture_special_need/list',compact('classArr','termArr' ,'result'));

    }

    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }

    public function export(Request $request)
    {
            $queryData['class'] = $request->input('classes');
            $queryData['term'] = $request->input('terms');
            $class_name = '';
            $class_data = $this->teacher_relatedService->getClassName($queryData);
            if(!empty($class_data)){
                $class_name = $class_data[0]['yerly'].'年  '.$class_data[0]['name'].'  第'.$queryData['term'].'期';
            }

            $data = $this->teacher_relatedService->getSpecialNeed($queryData);
            // dd($data);
            if($data==[]){
                $result ="此條件查無資料，請重新查詢";
                //return view('admin/lecture_special_need/list',compact('classArr','termArr' ,'result'));
                return back()->with('result', '0')->with('message', $result);
            }

            // 檔案名稱
            $fileName = 'H19';
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

            $rowcnt = 4;

            $objActSheet->setCellValue('A3',trim($class_name));

            foreach($data as $row){
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($row['name'].'('.$row['date'].')'));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim($row['demand']));
                $rowcnt++;
            }
            $objActSheet->getStyle('A3:B'.strval($rowcnt-1))->getAlignment()->setWrapText(true);
            $objActSheet->getStyle('A3:B'.strval($rowcnt-1))->applyFromArray($styleArray);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="講座特殊需求一覽表.xlsx"');

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
