<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\MethodService;
use App\Models\method;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class TeachWayStatics extends Controller
{
    public function __construct(MethodService $methodService,User_groupService $user_groupService)
    {
        $this->methodService = $methodService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teach_way_statics', $user_group_auth)){
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
        return view('admin/teach_way_statics/list',compact('queryData'));
    }

    public function export(Request $request)
    {   
        $data = $request->all();
        // 數據
        $TeachWayList = $this->methodService->getTeachWayList($data);
        if(empty($TeachWayList)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據');    
        
        // 教學教法清單
        $methodlist = method::where('yerly',substr($TeachWayList['0']->class, 0,3))->where('mode','1')->get()->toarray();
        // 分類
        $result = array('total'=>array('A'=>0,'B'=>0,'C'=>0,'D'=>0,'E'=>'100.00%','F'=>'100.00%','name'=>'總計'));
        $classcount = 0;
        $class= '';
        $term='';
        foreach ($TeachWayList as $key => $value) {
            $result[$value->method1]['A'] = isset($result[$value->method1]['A'])? $result[$value->method1]['A']+1 : 1;
            $result[$value->method2]['B'] = isset($result[$value->method2]['B'])? $result[$value->method2]['B']+1 : 1;
            $result[$value->method3]['C'] = isset($result[$value->method3]['C'])? $result[$value->method3]['C']+1 : 1;
            $result['total']['A'] = $value->method1==''?$result['total']['A'] : $result['total']['A']+1;
            $result['total']['B'] = $value->method2==''?$result['total']['B'] : $result['total']['B']+1;
            $result['total']['C'] = $value->method3==''?$result['total']['C'] : $result['total']['C']+1;
            if($class !=$value->class || $term !=$value->term ){
                $classcount = $classcount +1;
                $class = $value->class;
                $term = $value->term;
            }
        }
        foreach ($methodlist as $k => $v) {
            $result[$v['method']]['name'] = $v['name'];
            if(!isset($result[$v['method']]['A'])) $result[$v['method']]['A']=0;
            if(!isset($result[$v['method']]['B'])) $result[$v['method']]['B']=0;
            if(!isset($result[$v['method']]['C'])) $result[$v['method']]['C']=0;
            $result[$v['method']]['D'] = $result[$v['method']]['A'] + $result[$v['method']]['B'] + $result[$v['method']]['C'];
            $result['total']['D'] = $result[$v['method']]['D'] + $result['total']['D'];
            $result[$v['method']]['F'] = $result[$v['method']]['A'] / $result['total']['A'] ;
        }
        foreach ($result as $key => $value) {
            if($key=='total' || $key==''){
                continue;
            }else{
                $result[$key]['E'] = $result[$key]['D'] / $result['total']['D'] ;
            }
        }
       
        // 檔案名稱
        $fileName = 'F18';
        // 範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        // 讀取excel
        $objPHPExcel = IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $styleArray =[
            'borders' =>[
                    'allBorders'=>[
                    'borderStyle' => Border::BORDER_THIN,
                    'color' =>['rgb' => '000000'],
                ],
            ],
        ];
        $mergefrom=0;
        $mergeto=0;
        $i=2;
        $count = sizeof($result)+1;
        //  fill values
        foreach ($methodlist as $key => $value) {
            $objActSheet->setCellValue('A'.$i, $result[$value['method']]['name']);
            $objActSheet->setCellValue('B'.$i, $result[$value['method']]['A']);
            $objActSheet->setCellValue('C'.$i, $result[$value['method']]['B']);
            $objActSheet->setCellValue('D'.$i, $result[$value['method']]['C']);
            $objActSheet->setCellValue('E'.$i, $result[$value['method']]['D']);
            $objActSheet->setCellValue('F'.$i, $result[$value['method']]['E']);
            $objActSheet->setCellValue('G'.$i, $result[$value['method']]['F']);
            $i++;
        }
        $objActSheet->setCellValue('A'.$i, $result['total']['name']);
        $objActSheet->setCellValue('B'.$i, $result['total']['A']);
        $objActSheet->setCellValue('C'.$i, $result['total']['B']);
        $objActSheet->setCellValue('D'.$i, $result['total']['C']);
        $objActSheet->setCellValue('E'.$i, $result['total']['D']);
        $objActSheet->setCellValue('F'.$i, $result['total']['E']);
        $objActSheet->setCellValue('G'.$i, $result['total']['F']);
        $objActSheet->setCellValue('A'.($i+2), '共計'.$classcount.'個班，'.count($TeachWayList).'門課程');
        // apply borders
        $objActSheet->getStyle('A1:G'.$i)->applyFromArray($styleArray);

        // 畫圖 
        $dataSeriesLabels =[
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'objActSheet!$F$1', null, 1)  //X軸標題
        ];
        $xAxisTickValues =[
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'objActSheet!$A$2:$A$'.($i-1), null, ($i+1) ) // Y軸內容
        ];
        $dataSeriesValues =[
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'objActSheet!$F$2:$F$'.($i-1), null, ($i+1) ) // X軸數據
        ];
        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,    // plotType
            DataSeries::GROUPING_CLUSTERED, // plotGrouping
            range(0, count($dataSeriesValues) - 1),            // plotOrder
            $dataSeriesLabels,      // plotLabel
            $xAxisTickValues,       // plotCategory
            $dataSeriesValues       // plotValues
        ); 
        // Set additional dataseries parameters
        // Make it a horizontal bar rather than a vertical column graph
        $series->setPlotDirection(DataSeries::DIRECTION_BAR);
        // Set the series in the plot area
        $layout = new Layout();
        $layout->setShowval(true); // 資料標籤
        $plotArea = new PlotArea($layout,[$series]);
        // Set the chart legend
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);

        $title = new Title('教學教法運用統計表');
        // $yAxisLabel = new Title('Value ($k)');
        // Create the chart 圖表項目
        $chart = new Chart(
            'chart1', // name
            $title, // title 圖表標題
            $legend, // legend 圖例
            $plotArea, // plotArea 
            true,  // plotVisibleOnly
            'gap', // displayBlanksAs
            null,  // xAxisLabel
            null  //$yAxisLabel  // yAxisLabel
        );
        
        // Set the position where the chart should appear in the objActSheet
        $chart->setTopLeftPosition('J2');
        $chart->setBottomRightPosition('W32');
        // Add the chart to the objActSheet
        $objActSheet->addChart($chart);
        // 畫圖END
        // export excel
        ob_start();
        
        //匯出
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $objWriter->setIncludeCharts(true); //畫圖有錯 到這邊就會噴500 
        
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="教學教法運用統計表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // $post['term']always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        ob_end_clean();
        $objWriter->save('php://output');
        exit;

    }
}
