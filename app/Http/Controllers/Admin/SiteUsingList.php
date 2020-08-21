<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\ClassesService;


class SiteUsingList extends Controller
{
    public function __construct(User_groupService $user_groupService,ClassesService $classesService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_using_list', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        $this->classesService = $classesService;
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $result  ='';
        return view('admin/site_using_list/list',compact('result'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        $times = range('A','C');
        $base = $this->classesService->getSiteList($data);
        if(empty($base)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據');   

        $result = array();
        foreach ($base as $va) {
            if(in_array($va['time'], $times )){
                $result[$va['date']][$va['time']][] = array('site'=>$va['site'],'sitename'=>$va['roomname'],'class'=>$va['class'],'term'=>$va['term'],'name'=>$va['name']);
            }else{
                if($va['stime'] < 1200) {
                    $result[$va['date']]['A'][] = array('site'=>$va['site'],'sitename'=>$va['roomname'],'class'=>$va['class'],'term'=>$va['term'],'name'=>$va['name']);
                }
                if($va['etime'] > 1630){
                    $result[$va['date']]['C'][] = array('site'=>$va['site'],'sitename'=>$va['roomname'],'class'=>$va['class'],'term'=>$va['term'],'name'=>$va['name']);
                }
                if($va['stime'] < 1630 && $va['etime'] > 1200 && $va['stime'] < $va['etime'] ){
                    $result[$va['date']]['B'][] = array('site'=>$va['site'],'sitename'=>$va['roomname'],'class'=>$va['class'],'term'=>$va['term'],'name'=>$va['name']);
                }
            }
        }
        // 檔案名稱
        $fileName = 'N31';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        // fill values
        $objActSheet = $objPHPExcel->getActiveSheet();
        $title = '行政院人事行政總處公務人力發展學院'.$data['yerly'].'年'.$data['month'].'月場地借用行事曆';
        $objActSheet->setCellValue('A1', $title);
        $day = (str_pad($data['yerly'],3,'0',STR_PAD_LEFT)+1911).str_pad($data['month'],2,'0',STR_PAD_LEFT).'01';
        $week = date('w', strtotime($day) ); // 0-6
        $weekend = 0; //第幾週
        $weeklist = range('B','H');
        while (true) {
            $date = (date('Y', strtotime($day))-1911).date('md', strtotime($day)); //民國
            $sitecode = (5*$weekend+2);  // 5x+2
            $word = substr($day,-4,2).'月'.substr($day,-2).'日';
            $objActSheet->setCellValue($weeklist[$week].$sitecode,$word);
            for($i=0;$i<3;$i++){
                if(!isset($result[$date][$times[$i]])){
                    continue;
                }else{
                    $input = '';
                    for($j=0;$j<sizeof($result[$date][$times[$i]]);$j++){
                        $input .= $result[$date][$times[$i]][$j]['name']."第".$result[$date][$times[$i]][$j]['term']."期_".$result[$date][$times[$i]][$j]['sitename'].PHP_EOL;
                    }
                    $objActSheet->setCellValue($weeklist[$week].($sitecode+$i+2), $input);
                    $objActSheet->getStyle($weeklist[$week].($sitecode+$i+2))->getAlignment()->setWrapText(true);
                }
            }


            $day = date('Ymd',strtotime($day .' +1 Day'));
            $week = date('w', strtotime($day) );
            // 換週
            if($week==0){
                $weekend++;
            }
            // 換月跳出
            if(substr($day,-4,2)!= $data['month']){
                break;
            }
        }


        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="場地借用行事曆.xlsx"');
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
