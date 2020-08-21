<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\MethodService;
use App\Models\method;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TeachWaySatisfaction extends Controller
{
    public function __construct(MethodService $methodService,User_groupService $user_groupService)
    {
        $this->methodService = $methodService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teach_way_satisfaction', $user_group_auth)){
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
        return view('admin/teach_way_satisfaction/list',compact('queryData'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        // 數據
        $SatisfactionList = $this->methodService->getSatisfactionList($data);
        if(empty($SatisfactionList)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據');  

        // var_dump($SatisfactionList);exit();
        $base = array('total'=>array('count'=>0));
        $typearray = array('19','23','25'); 
        $count = count($SatisfactionList);
        foreach ($SatisfactionList as $key => $value) {
            if (!in_array($value->type,$typearray )) $value->type = 'other';

            if($value->method1!=''){
                $base[$value->method1]['count'] = isset($base[$value->method1]['count'])? $base[$value->method1]['count']+1 : 1;
                $base[$value->method1][$value->type]['count'] = isset($base[$value->method1][$value->type]['count'])? $base[$value->method1][$value->type]['count']+1 : 1;
                // $base[$value->method1][$value->type]['satis'] = isset($base[$value->method1][$value->type]['satis'])? $base[$value->method1][$value->type]['satis']+$value->ans1avg : $value->ans1avg;
                $base['total']['count'] = $base['total']['count']+1;

            }
            if($value->method2!=''){
                $base[$value->method2]['count'] = isset($base[$value->method2]['count'])? $base[$value->method2]['count']+1 : 1;
                $base[$value->method2][$value->type]['count'] = isset($base[$value->method2][$value->type]['count'])? $base[$value->method2][$value->type]['count']+1 : 1;
                // $base[$value->method2][$value->type]['satis'] = isset($base[$value->method2][$value->type]['satis'])? $base[$value->method2][$value->type]['satis']+$value->ans1avg : $value->ans1avg;
                $base['total']['count'] = $base['total']['count']+1;
            }
            if($value->method3!=''){
                $base[$value->method3]['count'] = isset($base[$value->method3]['count'])? $base[$value->method3]['count']+1 : 1;
                $base[$value->method3][$value->type]['count'] = isset($base[$value->method3][$value->type]['count'])? $base[$value->method3][$value->type]['count']+1 : 1;
                // $base[$value->method3][$value->type]['satis'] = isset($base[$value->method3][$value->type]['satis'])? $base[$value->method3][$value->type]['satis']+$value->ans1avg : $value->ans1avg;
                $base['total']['count'] = $base['total']['count']+1; 
            }
            $base[$value->type]['satis'] = isset($base[$value->type]['satis'])? $base[$value->type]['satis']+$value->ans1avg : $value->ans1avg;
            $base[$value->type]['count'] = isset($base[$value->type]['count'])? $base[$value->type]['count']+1 : 1;
        }
        
        // 教學教法清單
        $methodlist = method::where('yerly',substr($SatisfactionList['0']->class, 0,3))->where('mode','1')->get()->toarray();
        $typearray[] = 'other';
        foreach ($methodlist as $k => $v) {
            $result[$v['method']]['name'] = $v['name'];
            if(!isset($base[$v['method']]['count'])){
                $result[$v['method']][$typearray[0]]['ratio'] = 0;   // 政策性訓練
                $result[$v['method']][$typearray[1]]['ratio'] = 0;   // 領導力發展
                $result[$v['method']][$typearray[2]]['ratio'] = 0;   // 部會業務知能訓練
                $result[$v['method']][$typearray[3]]['ratio'] = 0;   // 自我成長及其他
            }else{
                for($i=0;$i<4;$i++){
                    if(isset($base[$v['method']][$typearray[$i]])){
                        $result[$v['method']][$typearray[$i]]['ratio'] = round($base[$v['method']][$typearray[$i]]['count'] / $base['total']['count']* 100,2).'%'; 
                        // $result[$v['method']][$typearray[$i]]['satis'] = round($base[$v['method']][$typearray[$i]]['satis'] / $base[$v['method']][$typearray[$i]]['count'],2).'%'; 
                    }else{
                        $result[$v['method']][$typearray[$i]]['ratio'] = 0;
                        // $result[$v['method']][$typearray[$i]]['satis'] = 0;
                    }
                }
            }
        }
        $result['totalsatis']['name'] = '教學方法滿意度';
        for($i=0;$i<4;$i++){
           if(isset($base[$typearray[$i]])){
                $result['totalsatis'][$typearray[$i]]['ratio'] = round($base[$typearray[$i]]['satis'] / $base[$typearray[$i]]['count'],2).'%'; 
            }else{
                $result['totalsatis'][$typearray[$i]]['ratio'] = 0;
            } 
        }
        // 檔案名稱
        $fileName = 'F19';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        // 讀取excel
        $objPHPExcel = IOFactory::load($filePath);
        //  fill values
        $objActSheet = $objPHPExcel->getActiveSheet();
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $objActSheet->getHeaderFooter()->setOddHeader( '班別性質教法運用滿意度統計表('.$data['sdate'].'~'.$data['edate'].')');
        $objActSheet->setCellValue('B2', '起迄日期：'.substr($data['sdate'], 0, 3)."/".substr($data['sdate'], 3, 2)."/".substr($data['sdate'], 5, 2)."至".substr($data['edate'], 0, 3)."/".substr($data['edate'], 3, 2)."/".substr($data['edate'], 5, 2));
        $columns = array();
        $i=2;
        // 列表
        $list = range('A', 'Z');
        if(sizeof($result)>25){ // 再增加26欄 A-AZ
            foreach ($list as $letter) {
                $column = 'A'.$letter;
                $list[] = $column;
                if ($column == 'AZ'){
                    break;
                }
            }
        }
        
        foreach ($result as $key => $value) {
            $objActSheet->setCellValue($list[$i].'3', $value['name']);
            $objActSheet->setCellValue($list[$i].'4', $value['19']['ratio']);
            $objActSheet->setCellValue($list[$i].'5', $value['23']['ratio']);
            $objActSheet->setCellValue($list[$i].'6', $value['25']['ratio']);
            $objActSheet->setCellValue($list[$i].'7', $value['other']['ratio']);
            $i++;
        }
        // apply borders
        $objActSheet->getStyle('C3:'.$list[($i-1)].'7')->applyFromArray($styleArray);
        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="班別性質教法運用滿意度統計表.xlsx"');
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
