<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\ClassesService;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SiteUsingMaintainAll extends Controller
{
    public function __construct(User_groupService $user_groupService,ClassesService $classesService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_using_maintain_all', $user_group_auth)){
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
        return view('admin/site_using_maintain_all/list',compact('result'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        if($data['sdate']=='' || $data['edate']=='')  return back()->with('result', 0)->with('message', '匯出失敗，請輸入日期'); 
        $base = $this->classesService->getSiteData($data,'2');
        $result = array();
        $applyno = '';
        // var_dump($base);exit();
        if(empty($base)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據');   

        $result['total']['name'] = '合計';
        foreach ($base as $value) {
            $htimes = $ntimes = $hnum = $nnum = 0;
            $result[$value['croomclsno']]['name'] = $value['croomclsname'];
            $result[$value['croomclsno']]['nday'] = isset($result[$value['croomclsno']]['nday'])? $result[$value['croomclsno']]['nday']+$value['nday']:$value['nday'];
            $result[$value['croomclsno']]['hday'] = isset($result[$value['croomclsno']]['hday'])? $result[$value['croomclsno']]['hday']+$value['hday']:$value['hday'];
            $result[$value['croomclsno']]['Allday'] = isset($result[$value['croomclsno']]['Allday'])? $result[$value['croomclsno']]['Allday']+$value['nday']+$value['hday']:$value['nday']+$value['hday'];
            if($value['nday']!=0){
                $ntimes = 1;
                $nnum = $value['num'];
            }elseif($value['hday']!=0){
                $htimes = 1;
                $hnum = $value['num'];
            }
            $result[$value['croomclsno']]['ntimes'] = isset($result[$value['croomclsno']]['ntimes'])? $result[$value['croomclsno']]['ntimes']+$ntimes:$ntimes;
            $result[$value['croomclsno']]['htimes'] = isset($result[$value['croomclsno']]['htimes'])? $result[$value['croomclsno']]['htimes']+$htimes:$htimes;
            $result[$value['croomclsno']]['Alltimes'] = isset($result[$value['croomclsno']]['Alltimes'])? $result[$value['croomclsno']]['Alltimes']+$htimes+$ntimes:$htimes+$ntimes;
            $result[$value['croomclsno']]['nnum'] = isset($result[$value['croomclsno']]['nnum'])? $result[$value['croomclsno']]['nnum']+$nnum:$nnum;
            $result[$value['croomclsno']]['hnum'] = isset($result[$value['croomclsno']]['hnum'])? $result[$value['croomclsno']]['hnum']+$hnum:$hnum;
            $result[$value['croomclsno']]['Allnum'] = isset($result[$value['croomclsno']]['Allnum'])? $result[$value['croomclsno']]['Allnum']+$value['num']:$value['num'];
            $result['total']['ntimes'] = isset($result['total']['ntimes'])? $result['total']['ntimes']+$ntimes:$ntimes;
            $result['total']['htimes'] = isset($result['total']['htimes'])? $result['total']['htimes']+$htimes:$htimes;
            $result['total']['Alltimes'] = isset($result['total']['Alltimes'])? $result['total']['Alltimes']+$htimes+$ntimes:$htimes+$ntimes;
            $result['total']['nnum'] = isset($result['total']['nnum'])? $result['total']['nnum']+$nnum:$nnum;
            $result['total']['hnum'] = isset($result['total']['hnum'])? $result['total']['hnum']+$hnum:$hnum;
            $result['total']['Allnum'] = isset($result['total']['Allnum'])? $result['total']['Allnum']+$value['num']:$value['num'];
            $result[$value['croomclsno']]['nfee'] = isset($result[$value['croomclsno']]['nfee'])? $result[$value['croomclsno']]['nfee']+$value['nfee']:$value['nfee'];
            $result[$value['croomclsno']]['hfee'] = isset($result[$value['croomclsno']]['hfee'])? $result[$value['croomclsno']]['hfee']+$value['hfee']:$value['hfee'];
            $result[$value['croomclsno']]['Allfee'] = isset($result[$value['croomclsno']]['Allfee'])? $result[$value['croomclsno']]['Allfee']+$value['nfee']+$value['hfee']:$value['nfee']+$value['hfee'];

            $result['total']['nday'] = isset($result['total']['nday'])? $result['total']['nday']+$value['nday']:$value['nday'];
            $result['total']['hday'] = isset($result['total']['hday'])? $result['total']['hday']+$value['hday']:$value['hday'];
            $result['total']['Allday'] = isset($result['total']['Allday'])? $result['total']['Allday']+$value['nday']+$value['hday']:$value['nday']+$value['hday'];
            $result['total']['nfee'] = isset($result['total']['nfee'])? $result['total']['nfee']+$value['nfee']:$value['nfee'];
            $result['total']['hfee'] = isset($result['total']['hfee'])? $result['total']['hfee']+$value['hfee']:$value['hfee'];
            $result['total']['Allfee'] = isset($result['total']['Allfee'])? $result['total']['Allfee']+$value['nfee']+$value['hfee']:$value['nfee']+$value['hfee'];
        }
        // var_dump($result);exit();
        // 檔案名稱
        $fileName = 'N35';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        //  fill values
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setCellValue('A3','總計期間自：'.substr($data['sdate'],0,3).'/'.substr($data['sdate'],3,2).'/'.substr($data['sdate'],-2).'至'.substr($data['edate'],0,3).'/'.substr($data['edate'],3,2).'/'.substr($data['edate'],-2) );
        $objActSheet->setCellValue('I3','列印日期：'.(date('Y', strtotime('now'))-1911).date('/m/d', strtotime('now')).' 頁次：1');
        $objActSheet->getStyle('I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $columns = array();
        $i=6;
        foreach ($result as $site => $value) {
            if($site=='total'){
                continue;
            }
            $objActSheet->setCellValue('A'.$i, $value['name']);
            $objActSheet->setCellValue('B'.$i, $value['nday']);
            $objActSheet->setCellValue('C'.$i, $value['hday']);
            $objActSheet->setCellValue('D'.$i, $value['Allday']);
            $objActSheet->setCellValue('E'.$i, $value['ntimes']);
            $objActSheet->setCellValue('F'.$i, $value['htimes']);
            $objActSheet->setCellValue('G'.$i, $value['Alltimes']);
            $objActSheet->setCellValue('H'.$i, $value['nfee']);
            $objActSheet->setCellValue('I'.$i, $value['hfee']);
            $objActSheet->setCellValue('J'.$i, $value['Allfee']);
            $objActSheet->setCellValue('K'.$i, $value['nnum']);
            $objActSheet->setCellValue('L'.$i, $value['hnum']);
            $objActSheet->setCellValue('M'.$i, $value['Allnum']);
            $i++;
        }
        $objActSheet->setCellValue('A'.$i, $result['total']['name']);
        $objActSheet->setCellValue('B'.$i, $result['total']['nday']);
        $objActSheet->setCellValue('C'.$i, $result['total']['hday']);
        $objActSheet->setCellValue('D'.$i, $result['total']['Allday']);
        $objActSheet->setCellValue('E'.$i, $result['total']['ntimes']);
        $objActSheet->setCellValue('F'.$i, $result['total']['htimes']);
        $objActSheet->setCellValue('G'.$i, $result['total']['Alltimes']);
        $objActSheet->setCellValue('H'.$i, $result['total']['nfee']);
        $objActSheet->setCellValue('I'.$i, $result['total']['hfee']);
        $objActSheet->setCellValue('J'.$i, $result['total']['Allfee']);
        $objActSheet->setCellValue('K'.$i, $result['total']['nnum']);
        $objActSheet->setCellValue('L'.$i, $result['total']['hnum']);
        $objActSheet->setCellValue('M'.$i, $result['total']['Allnum']);
        $objActSheet->getStyle('A6:A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        // apply borders
        $objActSheet->getStyle('A6:N'.$i)->applyFromArray($styleArray);
        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="各場地借用情形及維護費收入統計表.xlsx"');
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
