<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\MethodService;
use App\Models\method;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
class TeachWayCalssAnalyze extends Controller
{
    public function __construct(MethodService $methodService,User_groupService $user_groupService)
    {
        $this->methodService = $methodService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teach_way_calss_analyze', $user_group_auth)){
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
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');
        return view('admin/teach_way_calss_analyze/list',compact('queryData'));
    }

    public function export(Request $request)
    {

        $data = $request->all();
        // 數據
        $TeachWayList = $this->methodService->getTeachWayList($data);
        if(empty($TeachWayList)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據'); 

        $result = array('course'=>array());
        $typearray = array('19','23','25');  // 對應班別性質C A B
        foreach ($TeachWayList as $key => $value) {
            if (!in_array($value->type,$typearray )) $value->type = 'other';

            $count = 0;
            $result[$value->type]['total'] = isset($result[$value->type]['total'])? $result[$value->type]['total']+1 : 1;
            $result['course']['total'] = isset($result['course']['total'])? $result['course']['total']+1 : 1;
            $count = $value->method1==''? $count:$count+1;
            $count = $value->method2==''? $count:$count+1;
            $count = $value->method3==''? $count:$count+1;
            $result[$value->type]['count'.$count] = isset($result[$value->type]['count'.$count])? $result[$value->type]['count'.$count]+1 : 1;
            $result['course']['count'.$count] = isset($result['course']['count'.$count])? $result['course']['count'.$count]+1 : 1;
        }
        $typearray[] = 'other';
        for ($i=0;$i<sizeof($typearray) ;$i++) {
            $result[$typearray[$i]]['total'] = isset($result[$typearray[$i]]['total'])? $result[$typearray[$i]]['total']:0;
            $result[$typearray[$i]]['count1'] = isset($result[$typearray[$i]]['count1'])? $result[$typearray[$i]]['count1']:0;
            $result[$typearray[$i]]['count2'] = isset($result[$typearray[$i]]['count2'])? $result[$typearray[$i]]['count2']:0;
            $result[$typearray[$i]]['count3'] = isset($result[$typearray[$i]]['count3'])? $result[$typearray[$i]]['count3']:0;
            if($result[$typearray[$i]]['total'] !=0){
                $result[$typearray[$i]]['ratio1'] = round($result[$typearray[$i]]['count1'] / $result[$typearray[$i]]['total']* 100,2).'%'; 
                $result[$typearray[$i]]['ratio2'] = round($result[$typearray[$i]]['count2'] / $result[$typearray[$i]]['total']* 100,2).'%'; 
                $result[$typearray[$i]]['ratio3'] = round($result[$typearray[$i]]['count3'] / $result[$typearray[$i]]['total']* 100,2).'%'; 
            }else{
                $result[$typearray[$i]]['ratio1'] = 0;
                $result[$typearray[$i]]['ratio2'] = 0;
                $result[$typearray[$i]]['ratio3'] = 0;
            }
        }
        // 檔案名稱
        $fileName = 'F21';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        // 讀取excel
        $objPHPExcel = IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getHeaderFooter()->setOddHeader( '班別性質與教法數目分析表('.$data['sdate'].'~'.$data['edate'].')');
        $control = array('course'=>8,'19'=>6,'23'=>4,'25'=>5,'other'=>7);
        //  fill values
        $objActSheet->setCellValue('B2', '起迄日期：'.substr($data['sdate'], 0, 3)."/".substr($data['sdate'], 3, 2)."/".substr($data['sdate'], 5, 2)."至".substr($data['edate'], 0, 3)."/".substr($data['edate'], 3, 2)."/".substr($data['edate'], 5, 2));
        foreach ($result as $key => $value) {
            $objActSheet->setCellValue('C'.$control[$key], $value['total']);
            $objActSheet->setCellValue('D'.$control[$key], $value['count1']);
            $objActSheet->setCellValue('F'.$control[$key], $value['count2']);
            $objActSheet->setCellValue('H'.$control[$key], $value['count3']);
            if($key != 'course'){
                $objActSheet->setCellValue('E'.$control[$key], $value['ratio1']);
                $objActSheet->setCellValue('G'.$control[$key], $value['ratio2']);
                $objActSheet->setCellValue('I'.$control[$key], $value['ratio3']);
            }
        }
        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="班別性質與教法數目分析表.xlsx"');
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
