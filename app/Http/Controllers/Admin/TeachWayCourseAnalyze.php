<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\MethodService;
use App\Models\method;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TeachWayCourseAnalyze extends Controller
{
    public function __construct(MethodService $methodService,User_groupService $user_groupService)
    {
        $this->methodService = $methodService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teach_way_course_analyze', $user_group_auth)){
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
        // $RptBasic = new \App\Rptlib\RptBasic();
        // $class = $RptBasic->getclasstypek();
        // $result="";
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');
        $queryData['type'] = $request->get('type');
        return view('admin/teach_way_course_analyze/list',compact('queryData'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        // 數據
        $TeachWayList = $this->methodService->getTeachWayList($data);
        if(empty($TeachWayList)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據'); 

        $result = array();
        foreach ($TeachWayList as $key => $value) {
            $count = 0;
            $count = $value->method1==''? $count:$count+1;
            $count = $value->method2==''? $count:$count+1;
            $count = $value->method3==''? $count:$count+1;
            $result['count'.$count] = isset($result['count'.$count])? $result['count'.$count]+1 : 1;
        }
        $result['total'] = count($TeachWayList);
        $result['ratio1'] = round($result['count1'] / $result['total']* 100,2).'%'; 
        $result['ratio2'] = round($result['count2'] / $result['total']* 100,2).'%'; 
        $result['ratio3'] = round($result['count3'] / $result['total']* 100,2).'%';
        // 檔案名稱
        $fileName = 'F20';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        // 讀取excel
        $objPHPExcel = IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader( '課程教學教法數目分析('.$data['sdate'].'~'.$data['edate'].')');
        //  fill values
        $objActSheet->setCellValue('B2', '起迄日期：'.substr($data['sdate'], 0, 3)."/".substr($data['sdate'], 3, 2)."/".substr($data['sdate'], 5, 2)."至".substr($data['edate'], 0, 3)."/".substr($data['edate'], 3, 2)."/".substr($data['edate'], 5, 2));
        $objActSheet->setCellValue('C4', $result['count1']);
        $objActSheet->setCellValue('C5', $result['count2']);
        $objActSheet->setCellValue('C6', $result['count3']);
        $objActSheet->setCellValue('C7', $result['total']);
        $objActSheet->setCellValue('D4', $result['ratio1']);
        $objActSheet->setCellValue('D5', $result['ratio2']);
        $objActSheet->setCellValue('D6', $result['ratio3']);
        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="領導力發展課程教學教法數目分析.xlsx"');
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
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $objWriter->save('php://output');
        exit;

    }
}
