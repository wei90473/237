<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\ClassesService;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class SiteUsingMaintainDatail extends Controller
{
    public function __construct(User_groupService $user_groupService,ClassesService $classesService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_using_maintain_datail', $user_group_auth)){
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
        return view('admin/site_using_maintain_datail/list',compact('result'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        if($data['sdate']=='' || $data['edate']=='')  return back()->with('result', 0)->with('message', '匯出失敗，請輸入日期'); 

        $base = $this->classesService->getSiteData($data,'2');
        if(empty($base)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據');  
        // var_dump($base);exit;
        $result = array();
        $applyno = '';
        foreach ($base as  $value) {
            $result[$value['applyno']]['applydate'] = $value['applydate'];
            $result[$value['applyno']]['orgname'] = $value['orgname'];
            if(isset($result[$value['applyno']]['site'])){
                $result[$value['applyno']]['site'][] = array('site'=>$value['croomclsname'],'startdate'=>$value['startdate'],'enddate'=>$value['enddate']);
            }else{
                $result[$value['applyno']]['site'][0] = array('site'=>$value['croomclsname'],'startdate'=>$value['startdate'],'enddate'=>$value['enddate']);
            }
            $result[$value['applyno']]['fee'] = isset($result[$value['applyno']]['fee'])? $result[$value['applyno']]['fee'] + $value['fee'] : $value['fee'];
            $result[$value['applyno']]['paydate'] = $value['paydate'];
        }
        // var_dump($result);exit;
        // 檔案名稱
        $fileName = 'N33';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        //  fill values
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setCellValue('A3','借用期間：'.substr($data['sdate'],0,3).'/'.substr($data['sdate'],3,2).'/'.substr($data['sdate'],-2).'至'.substr($data['edate'],0,3).'/'.substr($data['edate'],3,2).'/'.substr($data['edate'],-2) );
        $i = 6;
        $total = 0;
        foreach ($result as $no => $value) {
            $startdate ='';
            $site = '';
            $objActSheet->setCellValue('A'.$i,$value['applydate']);
            $objActSheet->setCellValue('B'.$i,$no);
            $objActSheet->setCellValue('C'.$i,$value['orgname']);
            for($j=0;$j<sizeof($value['site']);$j++){
                $startdate .= $value['site'][$j]['startdate'].'~'.$value['site'][$j]['enddate'].PHP_EOL;
                $site .= $value['site'][$j]['site'].PHP_EOL;
            }
            $objActSheet->setCellValue('D'.$i,$startdate);
            $objActSheet->getStyle('D'.$i)->getAlignment()->setWrapText(true);
            $objActSheet->setCellValue('E'.$i,$site);
            $objActSheet->getStyle('E'.$i)->getAlignment()->setWrapText(true);
            $value['fee'] = $value['fee']==''?0:$value['fee'];
            $objActSheet->setCellValue('F'.$i,$value['fee']);
            $objActSheet->setCellValue('G'.$i,$value['paydate']);
            $total = $total + $value['fee'];
            $i++;
        }
        $objActSheet->mergeCells('A'.$i.':B'.$i);
        $objActSheet->setCellValue('A'.$i,'合計');
        $objActSheet->setCellValue('F'.$i,$total);
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        // apply borders
        $objActSheet->getStyle('A6:H'.$i)->applyFromArray($styleArray);
        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="場地借用維護費收入明細統計表.xlsx"');
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
