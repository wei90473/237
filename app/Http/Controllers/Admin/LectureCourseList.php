<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\SatisfactionService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LectureCourseList extends Controller
{
    public function __construct(User_groupService $user_groupService, SatisfactionService $satisfactionService)
    {
        $this->satisfactionService = $satisfactionService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_course_list', $user_group_auth)){
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
        return view('admin/lecture_course_list/list',compact('result'));
    }

    public function export(Request $request)
    {

        $queryData['sdatetw'] = str_replace("-","",$request->input('sdatetw'));
        $queryData['edatetw'] = str_replace("-","",$request->input('edatetw'));

        $class_data = $this->satisfactionService->getExport($queryData);
        // dd($class_data);
        if($class_data==[]){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/lecture_course_list/list',compact('result'));
        }
         // 檔案名稱
         $fileName = 'F10';
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

            foreach($class_data as $row){
                $objActSheet->setCellValue('A'.strval($rowcnt),trim($rowcnt-3));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim($row['cname']));
                $objActSheet->setCellValue('C'.strval($rowcnt),trim($row['class_name']));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim($row['hour']));
                $objActSheet->setCellValue('E'.strval($rowcnt),trim($row['okrate']));
                $objActSheet->setCellValue('F'.strval($rowcnt),trim($row['name']));
                $objActSheet->setCellValue('G'.strval($rowcnt),trim($row['date']));
                $objActSheet->setCellValue('H'.strval($rowcnt),trim($row['username']));
                $objActSheet->setCellValue('I'.strval($rowcnt),'');
                $rowcnt++;
            }

            $objActSheet->getStyle('A3:I'.strval($rowcnt-1))->applyFromArray($styleArray);

            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($objPHPExcel,"2",$request->input('doctype'),"講座期間授課課表");
            //$obj: entity of file
            //$objtype:1.PhpWord 2.PhpSpreadsheet 3.PhpExcel 
            //$doctype:1.ooxml 2.odf
            //$filename:filename 

    }

}
