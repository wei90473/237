<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\ClassesRequirementsService;
use App\Models\Edu_classdemand; // 辦班需求確認檔_南投
use App\Models\Edu_classdemand_stopcook; //止伙明細
use App\Models\ClassWeek; //教師用餐確認檔
use PhpOffice\PhpSpreadsheet\Style\Border;
use DB ;

class FoodExpenseWriteoffSummary extends Controller
{
    public $paylist = array('108'=>array('breakfast'=>35,'lunch'=>70,'dinner'=>70)); //價格表
    public function __construct(ClassesRequirementsService $classesrequirementsService,User_groupService $user_groupService)
    {
        $this->classesrequirementsService = $classesrequirementsService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('food_expense_writeoff_summary', $user_group_auth)){
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
        return view('admin/food_expense_writeoff_summary/list',compact('result'));
    }

    public function export(Request $request)
    {
        $data = $request->all();
        if(!isset($data['yerly']) || !isset($data['month'])) return back()->with('result', 0)->with('message', '年度/月份錯誤!');  
       
        $year = '108';  //使用108年度的價格
        $results = $result = $total = $stoplist = array();
        $base = $this->classesrequirementsService->getFoodExpenseList($data);
        if(empty($base)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據'); 

        foreach ($base as $basekey => $basevalue) {
            $classdemand = Edu_classdemand::select(DB::RAW("edu_classdemand.*,B.max"))->leftJoin(DB::RAW("(
            SELECT class,term,date,MAX(etime) as max FROM `t06tb`
            WHERE `class` ='".$basevalue['class']."' AND term = '".$basevalue['term']."' GROUP BY `date`) as B"), function($join){
                $join->on('edu_classdemand.class', '=', 'B.class')
                     ->on('edu_classdemand.term',  '=', 'B.term')
                     ->on('edu_classdemand.date',  '=', 'B.date');
            })->where('edu_classdemand.class',$basevalue['class'])->where('edu_classdemand.term',$basevalue['term'])->orderby('edu_classdemand.date')->get()->toarray();
            if($basevalue['process']=='2'){
                $process = '2';
                $results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['enrollname']  = $basevalue['enrollname'];
            }else{
                $process = '1'; 
            }
            // 資料重組
            $results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['sdate'] = $basevalue['sdate'];
            $results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['edate'] = $basevalue['edate'];
            $results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['section'] = $basevalue['section'];
            $results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['name'] = $basevalue['name'];
            $results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['class'] = $basevalue['class'];
            $results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['term']  = $basevalue['term'];
            if(empty($classdemand)) {
                $results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['pay'] = 0; 
                continue;
            }
            $stopcook = edu_classdemand_stopcook::select()->where('class',$basevalue['class'])->where('term',$basevalue['term'])->orderby('stopdate')->get()->toarray();
            // 止伙
            foreach ($stopcook as $va) {
                if($va['cooktype']=='1'){
                    $stoplist[$va['stopdate']]['A']='1';
                }elseif($va['cooktype']=='2'){
                    $stoplist[$va['stopdate']]['B']='1';
                }elseif($va['cooktype']=='3'){
                    $stoplist[$va['stopdate']]['C']='1';
                }
            }
            // 講座
            $teacher = ClassWeek::select(DB::RAW("teacher_food.date,
            sum(IF(teacher_food.breakfast='N',0,1))as `breakfast`,
            sum(IF(teacher_food.lunch='N',0,1))as `lunch`,
            sum(IF(teacher_food.dinner='N',0,1))as `dinner` "))->join('teacher_food','class_weeks.id','=','teacher_food.class_weeks_id')->where('class_weeks.class',$basevalue['class'])->where('class_weeks.term',$basevalue['term'])->groupby('teacher_food.date')->get()->toarray();
            foreach ($teacher as $value) {
                $stop_b = $stop_l = $stop_d = 1;  //止伙基數
                if(isset($stoplist[$value['date']]['A'])){
                    $stop_b = 0;
                }elseif(isset($stoplist[$value['date']]['B'])){
                    $stop_l = 0;
                }elseif(isset($stoplist[$value['date']]['C'])){
                    $stop_d = 0;
                }
                $result[$value['date']]['teacher_b'] = $value['breakfast']*$stop_b;
                $result[$value['date']]['teacher_l'] = $value['lunch']*$stop_l;
                $result[$value['date']]['teacher_d'] = $value['dinner']*$stop_d;
                $total['teacher_b'] = isset($total['teacher_b'])? $total['teacher_b']+$result[$value['date']]['teacher_b'] : 0+$result[$value['date']]['teacher_b'];
                $total['teacher_l'] = isset($total['teacher_l'])? $total['teacher_l']+$result[$value['date']]['teacher_l'] : 0+$result[$value['date']]['teacher_l'];
                $total['teacher_d'] = isset($total['teacher_d'])? $total['teacher_d']+$result[$value['date']]['teacher_d'] : 0+$result[$value['date']]['teacher_d'];
            }
            if(empty($teacher)){
                $total['teacher_b'] = 0;
                $total['teacher_l'] = 0;
                $total['teacher_d'] = 0;
            }
            $i = 1;
            $days = count($classdemand); //上課天數
            foreach ($classdemand as $key => $value) {
                if(is_null($value['max'])) { // 排除異常課程
                    continue;
                }
                $stop_b = $stop_l = $stop_d = 1;  //止伙基數
                if(isset($stoplist[$value['date']]['A'])){
                    $stop_b = 0;
                }elseif(isset($stoplist[$value['date']]['B'])){
                    $stop_l = 0;
                }elseif(isset($stoplist[$value['date']]['C'])){
                    $stop_d = 0;
                }
                // 早餐：第一天早餐抓提前住宿人數，第二天以後抓前一天已報到人數。
                if($i==1){ 
                    $result[$value['date']]['student_b'] = $stop_b*$value['earlystaycnt'];
                }else{
                    $result[$value['date']]['student_b'] = $stop_b*$checkincnt;
                }
                $result[$value['date']]['counselor_b'] = $stop_b*($value['counselorcnt']+$value['counselorvegan']);
                $checkincnt = $value['checkincnt']+$value['checkinvegan']; // 已報到人數。
                $result[$value['date']]['sponsor'] = $stop_l; // 班務1人 吃午餐
                if($days!= $i || $value['max'] >= 1700){ // 晚餐：上課時間到下午17:00才有或隔天還有相同班別。
                    $result[$value['date']]['student_l'] = $stop_l*$checkincnt;
                    $result[$value['date']]['student_d'] = $stop_d*($checkincnt - $value['nodincnt']);
                    $result[$value['date']]['counselor_l'] = $stop_l*($value['counselorcnt']+$value['counselorvegan']);
                    $result[$value['date']]['counselor_d'] = $stop_d*($value['counselorcnt']+$value['counselorvegan']);
                }elseif($value['max'] >= 1200){// 午餐：上課時間到中午12:00才有或隔天還有相同班別。
                    $result[$value['date']]['student_l'] = $stop_l*$checkincnt;
                    $result[$value['date']]['student_d'] = 0;
                    $result[$value['date']]['counselor_l'] = $stop_l*($value['counselorcnt']+$value['counselorvegan']);
                    $result[$value['date']]['counselor_d'] = 0;
                }else{
                    $result[$value['date']]['student_l'] = 0;
                    $result[$value['date']]['student_d'] = 0;
                    $result[$value['date']]['counselor_l'] = 0;
                    $result[$value['date']]['counselor_d'] = 0;
                    $result[$value['date']]['sponsor'] = 0;
                }
                $result[$value['date']]['totla'] = array_sum($result[$value['date']]);
                $total['student_b'] = isset($total['student_b'])? $total['student_b']+$result[$value['date']]['student_b'] : $result[$value['date']]['student_b'];
                $total['student_l'] = isset($total['student_l'])? $total['student_l']+$result[$value['date']]['student_l'] : $result[$value['date']]['student_l'];
                $total['student_d'] = isset($total['student_d'])? $total['student_d']+$result[$value['date']]['student_d'] : $result[$value['date']]['student_d'];
                $total['sponsor'] = isset($total['sponsor'])? $total['sponsor']+$result[$value['date']]['sponsor'] : $result[$value['date']]['sponsor'];
                $total['counselor_b'] = isset($total['counselor_b'])? $total['counselor_b']+$result[$value['date']]['counselor_b'] : $result[$value['date']]['counselor_b'];
                $total['counselor_l'] = isset($total['counselor_l'])? $total['counselor_l']+$result[$value['date']]['counselor_l'] : $result[$value['date']]['counselor_l'];
                $total['counselor_d'] = isset($total['counselor_d'])? $total['counselor_d']+$result[$value['date']]['counselor_d'] : $result[$value['date']]['counselor_d'];
                $i++;
            }
            $pay = array();
            $pay['student_b']   = $this->paylist[$year]['breakfast']*$total['student_b'];
            $pay['student_l']   = $this->paylist[$year]['lunch']*    $total['student_l'];
            $pay['student_d']   = $this->paylist[$year]['dinner']*   $total['student_d'];
            $pay['teacher_b']   = $this->paylist[$year]['breakfast']*$total['teacher_b'];
            $pay['teacher_l']   = $this->paylist[$year]['lunch']*    $total['teacher_l'];
            $pay['teacher_d']   = $this->paylist[$year]['dinner']*   $total['teacher_d'];
            $pay['sponsor']     = $this->paylist[$year]['lunch']*    $total['sponsor'];
            $pay['counselor_b'] = $this->paylist[$year]['breakfast']*$total['counselor_b'];
            $pay['counselor_l'] = $this->paylist[$year]['lunch']*    $total['counselor_l'];
            $pay['counselor_d'] = $this->paylist[$year]['dinner']*   $total['counselor_d'];
            $results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['pay'] = array_sum($pay);
            $results[$process][$basevalue['accname']]['paytotal'] = isset($results[$process][$basevalue['accname']]['paytotal'])?$results[$process][$basevalue['accname']]['paytotal']+$results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['pay']:$results[$process][$basevalue['accname']][$basevalue['class'].$basevalue['term']]['pay'] ;
        }
        // var_dump($results);exit();
        // 檔案名稱
        $fileName = 'N18';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle($data['month'].'月總表');
        //  fill values
        // 基本資料
        $objActSheet->setCellValue('A1',$data['yerly'].'年'.$data['month'].'月南投院區學員伙食費核銷總表(自辦班)');
        $i = 3;
        $all = 0;
        if(isset($results[1])){
            foreach ($results[1] as $kind => $classarray) {
                $j=0;
                foreach ($classarray as $classterm => $v) {
                    if($classterm=='paytotal') {
                        continue;
                    }
                    $objActSheet->setCellValue('B'.($i+$j),$v['section']); 
                    $objActSheet->setCellValue('C'.($i+$j),$v['name']);
                    $objActSheet->getCell('D'.($i+$j))->setValue('起'.$v['sdate']. PHP_EOL .'迄'.$v['edate']);
                    $objActSheet->getStyle('D'.($i+$j))->getAlignment()->setWrapText(true);
                    // $objActSheet->setCellValue('D'.($i+$j),'起'.$v['sdate'].'\n迄'.$v['edate']);
                    $objActSheet->setCellValue('E'.($i+$j),number_format($v['pay']));
                    $j++;  
                }
                $paytotal = isset($classarray['paytotal'])?$classarray['paytotal']:0;
                $objActSheet->mergeCells('A'.$i.':A'.($i+$j-1));
                $objActSheet->setCellValue('A'.$i,$kind);
                $objActSheet->mergeCells('F'.$i.':F'.($i+$j-1));
                $objActSheet->setCellValue('F'.$i,number_format($paytotal));
                $i = $i+$j;
                $all = $all + $paytotal;
            }
            $objActSheet->setCellValue('A'.$i,'合計'); 
            $objActSheet->setCellValue('F'.$i,number_format($all));
            $objActSheet->setCellValue('A'.($i+1),'玉美公司：');
        }
        // apply borders
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $objActSheet->getStyle('A3:F'.($i+1))->applyFromArray($styleArray);
        // 委訓班
        $z = $i = $i+6;
        $all = $paytotal = 0;
        // 基本資料
        $objActSheet->mergeCells('A'.($i-2).':E'.($i-2));
        $objActSheet->setCellValue('A'.($i-2),$data['yerly'].'年'.$data['month'].'月南投院區學員伙食費核銷總表(委訓班)');
        $objActSheet->setCellValue('A'.($i-1),'開支科目');
        $objActSheet->setCellValue('B'.($i-1),'委訓機關');
        $objActSheet->setCellValue('C'.($i-1),'班別名稱');
        $objActSheet->setCellValue('D'.($i-1),'起迄時間');
        $objActSheet->setCellValue('E'.($i-1),'金額');
        if(isset($results[2])){
            foreach ($results[2] as $kind => $classarray) {
                $j=0;
                foreach ($classarray as $classterm => $v) {
                    if($classterm=='paytotal') {
                        continue;
                    }
                    $objActSheet->setCellValue('A'.($i+$j),$kind);
                    $objActSheet->setCellValue('B'.($i+$j),$v['enrollname']); 
                    $objActSheet->setCellValue('C'.($i+$j),$v['name']);
                    $objActSheet->getCell('D'.($i+$j))->setValue('起'.$v['sdate']. PHP_EOL .'迄'.$v['edate']);
                    $objActSheet->getStyle('D'.($i+$j))->getAlignment()->setWrapText(true);
                    $objActSheet->setCellValue('E'.($i+$j),number_format($v['pay']));
                    $j++;  
                }
                $paytotal = isset($classarray['paytotal'])?$classarray['paytotal']:0;
                $i = $i+$j;
                $all = $all + $paytotal;
            }
            $objActSheet->setCellValue('A'.$i,'合計'); 
            $objActSheet->setCellValue('F'.$i,number_format($all));
            $objActSheet->setCellValue('A'.($i+1),'玉美公司：');
        }
        // apply borders
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $objActSheet->getStyle('A'.($z-2).':E'.($i+1))->applyFromArray($styleArray);
        // style
        $styleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $objActSheet->getStyle('A'.($z-2))->applyFromArray($styleArray);
        $objActSheet->getStyle('A'.($z-1).':E'.($z-1))->applyFromArray($styleArray);
        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="伙食費核銷總表.xlsx"');
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
