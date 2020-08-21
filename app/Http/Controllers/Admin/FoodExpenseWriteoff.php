<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassesRequirementsService;
use App\Services\User_groupService;
use App\Models\Edu_classdemand; // 辦班需求確認檔_南投
use App\Models\Edu_classdemand_stopcook; //止伙明細
use App\Models\ClassWeek; //教師用餐確認檔
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\T01tb;
use DB ;

class FoodExpenseWriteoff extends Controller
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
            if(in_array('food_expense_writeoff', $user_group_auth)){
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
        $RptBasic = new \App\Rptlib\RptBasic();
        $temp=$RptBasic->getclass();
        $classArr=T01tb::select('class','name')->where('branch','2')->get();
        $temp=json_decode(json_encode($temp), true);
        $arraykeys=array_keys((array)$temp[0]);
        $temp=$RptBasic->getTerms($temp[0][$arraykeys[0]]);
        $termArr=$temp;
        $result="";
        return view('admin/food_expense_writeoff/list',compact('classArr','termArr' ,'result'));

    }

    public function getTerms(Request $request)
    {
        $RptBasic = new \App\Rptlib\RptBasic();
        $termArr=$RptBasic->getTerms($request->input('classes'));
        return $termArr;
    }

    public function export(Request $request)
    {
        $data = $request->all();
        if(!isset($data['classes']) || !isset($data['terms'])) return back()->with('result', 0)->with('message', '班別/期別錯誤!');  

        $class = $data['classes'];
        $term = $data['terms'];
        $year = '108';  //使用108年度的價格
        $result = array();
        $total = array();
        $base = $this->classesrequirementsService->getEditList(array('class'=>$class,'term'=>$term));
        if(empty($base)) return back()->with('result', 0)->with('message', '匯出失敗，查無相關數據'); 

        $classdemand = Edu_classdemand::select(DB::RAW("edu_classdemand.*,B.max"))->leftJoin(DB::RAW("(
        SELECT class,term,date,MAX(etime) as max FROM `t06tb`
        WHERE `class` ='".$class."' AND term = '".$term."' GROUP BY `date`) as B"), function($join){
            $join->on('edu_classdemand.class', '=', 'B.class')
                 ->on('edu_classdemand.term',  '=', 'B.term')
                 ->on('edu_classdemand.date',  '=', 'B.date');
        })->where('edu_classdemand.class',$class)->where('edu_classdemand.term',$term)->orderby('edu_classdemand.date')->get()->toarray();
        if(empty($classdemand)) return back()->with('result', 0)->with('message', '匯出失敗，查無伙食費數據'); 

        $stopcook = edu_classdemand_stopcook::select()->where('class',$class)->where('term',$term)->orderby('stopdate')->get()->toarray();
        $stoplist= array(); //止伙
        foreach ($stopcook as $va) {
            if($va['cooktype']=='1'){
                $stoplist[$va['stopdate']]['A']='1';
            }elseif($va['cooktype']=='2'){
                $stoplist[$va['stopdate']]['B']='1';
            }elseif($va['cooktype']=='3'){
                $stoplist[$va['stopdate']]['C']='1';
            }
        }
        $teacher = ClassWeek::select(DB::RAW("teacher_food.date,
            sum(IF(teacher_food.breakfast='N',0,1))as `breakfast`,
            sum(IF(teacher_food.lunch='N',0,1))as `lunch`,
            sum(IF(teacher_food.dinner='N',0,1))as `dinner` "))->join('teacher_food','class_weeks.id','=','teacher_food.class_weeks_id')->where('class_weeks.class',$class)->where('class_weeks.term',$term)->groupby('teacher_food.date')->get()->toarray();
        $total['teacher_d'] = $total['teacher_l'] = $total['teacher_b'] = 0;
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
        $pay['total'] = array_sum($pay);
        // var_dump($result);exit();
        // 檔案名稱
        $fileName = 'N17';
        //範本位置
        $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
        //讀取excel
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('數位組');
        $i=0;
        $count = sizeof($result)+1;
        $list = range('D', 'Z');
        if($days>20){ // 再增加26欄 A-AZ
            foreach ($list as $letter) {
                $column = 'A'.$letter;
                $list[] = $column;
                if ($column == 'AZ'){
                    break;
                }
            }
        }
        //  fill values
        foreach ($result as $key => $value) {
            $objActSheet->setCellValue($list[$i].'5',  substr($key, -4,2).'月'.substr($key, -2).'日');
            $objActSheet->setCellValue($list[$i].'6',  $value['student_b']);
            $objActSheet->setCellValue($list[$i].'7',  $value['student_l']);
            $objActSheet->setCellValue($list[$i].'8',  $value['student_d']);
            $objActSheet->setCellValue($list[$i].'9',  isset($value['teacher_b'])?$value['teacher_b']:0);
            $objActSheet->setCellValue($list[$i].'10', isset($value['teacher_l'])?$value['teacher_l']:0);
            $objActSheet->setCellValue($list[$i].'11', isset($value['teacher_d'])?$value['teacher_d']:0);
            $objActSheet->setCellValue($list[$i].'12', $value['sponsor']);
            $objActSheet->setCellValue($list[$i].'13', $value['counselor_b']);
            $objActSheet->setCellValue($list[$i].'14', $value['counselor_l']);
            $objActSheet->setCellValue($list[$i].'15', $value['counselor_d']);
            $i++;
        }
        // 補空白資料
        if($days<6){
            for($j = $days;$j<6 ;$j++){
                $i++;
            }
        }
        // 基本資料
        $objActSheet->mergeCells('A1:'.$list[($i+2)].'1');
        $objActSheet->setCellValue('A1','行政院人事行政總處公務人力發展學院南投院區伙食費核銷明細表');
        $objActSheet->mergeCells('A2:'.$list[($i+2)].'2');
        $objActSheet->setCellValue('A2','班名：'.$base['name']);
        $objActSheet->mergeCells('A3:'.$list[($i+2)].'3');
        $objActSheet->setCellValue('A3','訊期：'.substr($base['sdate'],0,3).'年'.substr($base['sdate'],3,2).'月'.substr($base['sdate'],-2).'日至'.substr($base['edate'],0,3).'年'.substr($base['edate'],3,2).'月'.substr($base['edate'],-2).'日');
        $objActSheet->mergeCells('D4:'.$list[($i-1)].'4');
        $objActSheet->setCellValue('D4','用餐日');
        // 數量
        $objActSheet->mergeCells($list[$i].'4:'.$list[$i].'5');
        $objActSheet->setCellValue($list[$i].'4','數量');
        $objActSheet->setCellValue($list[$i].'6', $total['student_b']);
        $objActSheet->setCellValue($list[$i].'7', $total['student_l']);
        $objActSheet->setCellValue($list[$i].'8', $total['student_d']);
        $objActSheet->setCellValue($list[$i].'9', $total['teacher_b']);
        $objActSheet->setCellValue($list[$i].'10', $total['teacher_l']);
        $objActSheet->setCellValue($list[$i].'11', $total['teacher_d']);
        $objActSheet->setCellValue($list[$i].'12', $total['sponsor']);
        $objActSheet->setCellValue($list[$i].'13', $total['counselor_b']);
        $objActSheet->setCellValue($list[$i].'14', $total['counselor_l']);
        $objActSheet->setCellValue($list[$i].'15', $total['counselor_d']);
        $i++;
        // 餐費
        $objActSheet->mergeCells($list[$i].'4:'.$list[$i].'5');
        $objActSheet->setCellValue($list[$i].'4','餐費');
        $objActSheet->setCellValue($list[$i].'6', $this->paylist[$year]['breakfast']);
        $objActSheet->setCellValue($list[$i].'7', $this->paylist[$year]['lunch']);
        $objActSheet->setCellValue($list[$i].'8', $this->paylist[$year]['dinner']);
        $objActSheet->setCellValue($list[$i].'9', $this->paylist[$year]['breakfast']);
        $objActSheet->setCellValue($list[$i].'10', $this->paylist[$year]['lunch']);
        $objActSheet->setCellValue($list[$i].'11', $this->paylist[$year]['dinner']);
        $objActSheet->setCellValue($list[$i].'12', $this->paylist[$year]['lunch']);
        $objActSheet->setCellValue($list[$i].'13', $this->paylist[$year]['breakfast']);
        $objActSheet->setCellValue($list[$i].'14', $this->paylist[$year]['lunch']);
        $objActSheet->setCellValue($list[$i].'15', $this->paylist[$year]['dinner']);
        $i++;
        // 合計
        $objActSheet->mergeCells($list[$i].'4:'.$list[$i].'5');
        $objActSheet->setCellValue($list[$i].'4','合計');
        $objActSheet->setCellValue($list[$i].'6', number_format($pay['student_b']));
        $objActSheet->setCellValue($list[$i].'7', number_format($pay['student_l']));
        $objActSheet->setCellValue($list[$i].'8', number_format($pay['student_d']));
        $objActSheet->setCellValue($list[$i].'9', number_format($pay['teacher_b']));
        $objActSheet->setCellValue($list[$i].'10', number_format($pay['teacher_l']));
        $objActSheet->setCellValue($list[$i].'11', number_format($pay['teacher_d']));
        $objActSheet->setCellValue($list[$i].'12', number_format($pay['sponsor']));
        $objActSheet->setCellValue($list[$i].'13', number_format($pay['counselor_b']));
        $objActSheet->setCellValue($list[$i].'14', number_format($pay['counselor_l']));
        $objActSheet->setCellValue($list[$i].'15', number_format($pay['counselor_d']));
        $objActSheet->setCellValue($list[$i].'16', number_format($pay['total']));
        // style
        $styleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $objActSheet->getStyle('A1')->applyFromArray($styleArray);
        $objActSheet->getStyle('D4')->applyFromArray($styleArray);
        $objActSheet->getStyle($list[$i].'4')->applyFromArray($styleArray);
        $objActSheet->getStyle($list[$i-1].'4')->applyFromArray($styleArray);
        $objActSheet->getStyle($list[$i-2].'4')->applyFromArray($styleArray);
        // apply borders
        $styleArray = [
            'borders' => [
                    'allBorders'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $objActSheet->getStyle('A1:'.$list[$i].'16')->applyFromArray($styleArray);
        $styleArray = [
            'borders' => [
                'outline'=> [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $objActSheet->getStyle('A17:'.$list[$i].'20')->applyFromArray($styleArray);
        //export excel
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // 設定下載 Excel 的檔案名稱
        header('Content-Disposition: attachment;filename="伙食費核銷明細表.xlsx"');

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
