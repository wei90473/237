<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\ClassesService;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SiteUsingOverview extends Controller
{
    public function __construct(User_groupService $user_groupService,ClassesService $classesService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_using_overview', $user_group_auth)){
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
        return view('admin/site_using_overview/list',compact('result'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        if($data['year']=='' )  return back()->with('result', 0)->with('message', '匯出失敗，請輸入年度'); 

        $base = $this->classesService->getSiteData($data);
        if(empty($base)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據');  
        // var_dump($base);exit;
        $result = array();
        $applyno = '';
        foreach ($base as  $value) {
            $result[$value['applyno']]['applydate'] = $value['applydate'];
            $result[$value['applyno']]['orgname'] = $value['orgname'];
            $result[$value['applyno']]['title'] = $value['title'];
            $result[$value['applyno']]['applyuser'] = $value['applyuser'];
            $result[$value['applyno']]['reason'] = $value['reason'];
            $site = array(  'classroom'=>$value['classroom'],       'site'=>$value['croomclsname'],
                            'startdate'=>$value['startdate'],       'enddate'=>$value['enddate'],
                            'fullname'=>$value['fullname'],         'bedno'=>$value['bedno']);
            if(isset($result[$value['applyno']]['site'])){
                $result[$value['applyno']]['site'][] = $site;
            }else{
                $result[$value['applyno']]['site'][0] = $site;
            }
            if($value['classroom']=='1'){
                $result[$value['applyno']]['fee'] = isset($result[$value['applyno']]['fee'])? $result[$value['applyno']]['fee'] + $value['fee'] : $value['fee'];
                $result[$value['applyno']]['discount'] = isset($result[$value['applyno']]['discount'])? $result[$value['applyno']]['discount'] + $value['ndiscount'] + $value['hdiscount'] : $value['ndiscount'] + $value['hdiscount'];
            }else{
                $result[$value['applyno']]['fee'] = isset($result[$value['applyno']]['fee'])? $result[$value['applyno']]['fee'] : $value['fee'];
                $result[$value['applyno']]['discount'] = isset($result[$value['applyno']]['discount'])? $result[$value['applyno']]['discount'] : $value['ndiscount'] + $value['hdiscount']; 
            }
            $result[$value['applyno']]['realfee'] = $result[$value['applyno']]['fee'] - $result[$value['applyno']]['discount'] ; 
            $result[$value['applyno']]['paydate'] = $value['paydate'];
        }
        // var_dump($result['9944008']);exit();
        // 檔案名稱
        $fileName = 'N36';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        //  fill values
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setCellValue('A1',$data['year'].'年場地借用概況表');
        $i = 4;
        foreach ($result as $applyno => $va) {
            $objActSheet->setCellValue('A'.$i,$va['applydate']);
            $objActSheet->setCellValue('B'.$i,$va['orgname']);
            $objActSheet->setCellValue('C'.$i,$va['applyuser']);
            $objActSheet->setCellValue('D'.$i,$va['reason']);
            $date = '';
            $class = '';
            $bedroom = '';
            for($j=0;$j<sizeof($va['site']);$j++){
                $date .= $va['site'][$j]['startdate'].'~'.$va['site'][$j]['enddate'];
                if($va['site'][$j]['classroom']=='1'){
                    $class .= $va['site'][$j]['fullname'];
                    $bedroom .= '';
                }else{
                    $class .= '';
                    $bedroom .= $va['site'][$j]['site'].' '.$va['site'][$j]['bedno'];
                }
                
                if( ($j+1) != sizeof($va['site'])){
                    $date .= PHP_EOL;
                    $class .= PHP_EOL;
                    $bedroom .= PHP_EOL;
                }
            }
            $objActSheet->setCellValue('E'.$i,$date);
            $objActSheet->getStyle('E'.$i)->getAlignment()->setWrapText(true);
            $objActSheet->setCellValue('G'.$i,$applyno);
            $objActSheet->setCellValue('H'.$i,$class);
            $objActSheet->setCellValue('J'.$i,$class);
            $objActSheet->setCellValue('I'.$i,$bedroom);
            $objActSheet->setCellValue('K'.$i,$bedroom);
            $objActSheet->getStyle('H'.$i.':K'.$i)->getAlignment()->setWrapText(true);
            $objActSheet->setCellValue('L'.$i,$va['discount']);
            $objActSheet->setCellValue('M'.$i,$va['fee']);
            $objActSheet->setCellValue('N'.$i,$va['realfee']);
            $pay = $va['paydate']==''?'N':'Y';
            $objActSheet->setCellValue('O'.$i,$pay);
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
        $objActSheet->getStyle('A4:Q'.$i)->applyFromArray($styleArray);
        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="場地借用概況表.xlsx"');
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
