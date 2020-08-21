<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\ClassesService;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Edu_holiday;
class SiteUsageStatics extends Controller
{
    public function __construct(User_groupService $user_groupService,ClassesService $classesService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_usage_statics', $user_group_auth)){
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
        $result="";
        return view('admin/site_usage_statics/list',compact('result'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        $data['groupby'] = array('site','date');
        $base = $this->classesService->getSiteList($data);
        if(empty($base)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據');   

        // dd($base);
        $result = array();
        foreach ($base as $va) {
            $result[$va['site']]['count'] = isset($result[$va['site']])? $result[$va['site']]['count']+1 : 1;
            $result[$va['site']]['name'] = $va['roomname'];
        }
        // 上班日次
        $sdate = date_create( (substr($data['sdate'],0,3)+1911).substr($data['sdate'],3) );
        $edate = date_create( (substr($data['edate'],0,3)+1911).substr($data['edate'],3) );
        $diff=date_diff($sdate,$edate);
        // 假日
        $holiday = edu_holiday::wherebetween('holiday',array($data['sdate'],$data['edate']))->count();
        $result['total'] = $diff->format("%a")+1 - $holiday;
        // dd($result); 
        foreach ($result as $site => $value) {
            if($site=='total'){
                continue;
            }else{
                $result[$site]['ratio'] = round($value['count'] / $result['total']* 100,2).'%'; 
            }
        }
        // 檔案名稱
        $fileName = 'N32';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setCellValue('A3','列印區間：'.substr($data['sdate'],0,3).'/'.substr($data['sdate'],3,2).'/'.substr($data['sdate'],-2).'至'.substr($data['edate'],0,3).'/'.substr($data['edate'],3,2).'/'.substr($data['edate'],-2) );
        $objActSheet->setCellValue('C3','列印日期：'.(date('Y', strtotime('now'))-1911).date('/m/d', strtotime('now')).' 頁次：1');
        $objActSheet->getStyle('C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $i=5;
        // dd($result);
        foreach ($result as $site => $value) {
            if($site=='total'){
                continue;
            }else{
                $objActSheet->setCellValue('A'.$i,$value['name']);
                $objActSheet->setCellValue('B'.$i,$value['count']);
                $objActSheet->setCellValue('C'.$i,$result['total']);
                $objActSheet->setCellValue('D'.$i,$value['ratio']);
            }
            $i++;
        }
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        // apply borders
        $objActSheet->getStyle('A5:E'.($i-1))->applyFromArray($styleArray);
        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="場地使用成效統計表.xlsx"');
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
