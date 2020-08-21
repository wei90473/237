<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Class_weeks;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\Teacher_car;
use App\Services\User_groupService;
use App\Services\LecturePickupRecordService;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LecturePickupRecord extends Controller
{
    public function __construct(User_groupService $user_groupService, LecturePickupRecordService $lecturePickupRecordService)
    {
        $this->user_groupService = $user_groupService;
        $this->lecturePickupRecordService = $lecturePickupRecordService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('lecture_pickup_record', $user_group_auth)){
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

        return view('admin/lecture_pickup_record/list',compact('result'));
    }

    public function num2str($num){
        $string = '';
        $numc ="零,壹,貳,參,肆,伍,陸,柒,捌,玖";
        $unic = ",拾,佰,仟";
        $unic1 = "元,萬,億,兆,京";
        $numc_arr = explode(",", $numc);
        $unic_arr = explode(",", $unic);
        $unic1_arr = explode(",", $unic1);
        $i = str_replace(",", "", $num);
        $c0 = 0;
        $str = array();
        do{
            $aa = 0;
            $c1 = 0;
            $s = "";
            $lan = (strlen($i) >= 4) ? 4 : strlen($i);
            $j = substr($i, -$lan);
            while($j > 0){
                $k = $j % 10;
                if($k > 0) {
                    $aa = 1;
                    $s = $numc_arr[$k].$unic_arr[$c1].$s;
                }elseif($k == 0) {
                    if($aa == 1) $s = "0".$s;
                }
                $j = intval($j / 10);
                $c1 += 1;
            }
            $str[$c0] = ($s == '') ? '' : $s.$unic1_arr[$c0];
            $count_len = strlen($i) - 4;
            $i = ($count_len > 0) ? substr($i, 0, $count_len) : '';
            $c0 += 1;
        }while($i != '');
        if(isset($str[0]) && stristr($str[0], '仟') == false){
            $str[0] = '0'.$str[0];
        }

        foreach($str as $v){
            $string .= array_pop($str);
        }

        $string = preg_replace('/0+/', '零', $string);
        return $string;
    }

    public function export(Request $request)
    {

        $queryData['type'] = $request->input('type');

        if($queryData['type'] == '1'){
            $queryData['sdate'] = str_replace('-', '', $request->input('sdatetw'));
            $queryData['edate'] = str_replace('-', '', $request->input('edatetw'));
            if(empty($queryData['sdate']) || empty($queryData['edate'])){
                $result ="起始日期或結束日期請勿空白";
                return view('admin/lecture_pickup_record/list',compact('result'));
            }
            if($queryData['sdate'] > $queryData['edate']){
                $result ="起始日期請勿大於結束日期";
                return view('admin/lecture_pickup_record/list',compact('result'));
            }
            $data = $this->lecturePickupRecordService->getLecturePickupRecord($queryData);
        }else{
            $class = $request->input('class');
            if(empty($class)){
                $result ="班期請勿空白";
                return view('admin/lecture_pickup_record/list',compact('result'));
            }
            $class = explode('_', $class);
            $queryData['class'] = $class[0];
            $queryData['term'] = $class[1];

            $data = $this->lecturePickupRecordService->getLecturePickupRecord2($queryData);
        }

        // dd($data);
        if(empty($data)){
            $result ="此條件查無資料，請重新查詢";
            return view('admin/lecture_pickup_record/list',compact('result'));
        }

         // 檔案名稱
         $fileName = 'H21';
         //範本位置
         $filePath = '../example/'.iconv('UTF-8', 'GBK', $fileName).'.xlsx';
         //讀取excel

         $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

         $styleArray = [
                'borders' => [
                        'allBorders'=> [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $objActSheet = $objPHPExcel->getActiveSheet();

            $rowcnt = 5;

            if($queryData['type'] == '1'){
                $title_date = "自  ".substr($queryData['sdate'], 0, 3)."  年 ".substr($queryData['sdate'], 3, 2)." 月 ".substr($queryData['sdate'], 5, 2)." 日  起 至  ".substr($queryData['edate'], 0, 3)."  年 ".substr($queryData['edate'], 3, 2)." 月 ".substr($queryData['edate'], 5, 2)." 日";
            }else{

                $T04tb_data = T04tb::select('t04tb.sdate', 't04tb.edate')->where('t04tb.class', $queryData['class'])->where('t04tb.term', $queryData['term'])->first();

                $title_date = "自  ".substr($T04tb_data->sdate, 0, 3)."  年 ".substr($T04tb_data->sdate, 3, 2)." 月 ".substr($T04tb_data->sdate, 5, 2)." 日  起 至  ".substr($T04tb_data->edate, 0, 3)."  年 ".substr($T04tb_data->edate, 3, 2)." 月 ".substr($T04tb_data->edate, 5, 2)." 日";
            }


            $objActSheet->setCellValue('A2',trim($title_date));
            $total = '0';
            foreach($data as $row){

                $objActSheet->setCellValue('A'.strval($rowcnt),trim(substr($row['date'], 3, 2)." 月 ".substr($row['date'], 5, 2)." 日"));
                $objActSheet->setCellValue('B'.strval($rowcnt),trim($row['cname']));
                $objActSheet->setCellValue('C'.strval($rowcnt),trim($row['class']));
                $objActSheet->setCellValue('D'.strval($rowcnt),trim($row['name']));
                $objActSheet->setCellValue('E'.strval($rowcnt),trim($row['term'].'期'));
                $objActSheet->setCellValue('F'.strval($rowcnt),trim($row['start']));
                $objActSheet->setCellValue('G'.strval($rowcnt),trim($row['end']));
                $objActSheet->setCellValue('H'.strval($rowcnt),trim($row['price']));
                $objActSheet->setCellValue('I'.strval($rowcnt),trim($row['call']));
                $objActSheet->setCellValue('J'.strval($rowcnt),trim($row['car_name']));
                $objActSheet->setCellValue('K'.strval($rowcnt),trim($row['process']));
                $objActSheet->setCellValue('L'.strval($rowcnt),trim($row['remark']));
                $total += $row['price'];

                $rowcnt++;
            }

            $objPHPExcel->getActiveSheet(0)->mergeCells('A'.strval($rowcnt).':J'.strval($rowcnt));
            $objActSheet->setCellValue('A'.strval($rowcnt),trim("合計：".$this->num2str($total)));
            $objPHPExcel->getActiveSheet(0)->mergeCells('K'.strval($rowcnt).':L'.strval($rowcnt));
            $objActSheet->setCellValue('K'.strval($rowcnt),trim($total.'元'));

            $objActSheet->getStyle('A5:L'.strval($rowcnt))->getAlignment()->setWrapText(true);
            $objActSheet->getStyle('A5:L'.strval($rowcnt))->applyFromArray($styleArray);

            //export excel
            ob_end_clean();
            ob_start();

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // 設定下載 Excel 的檔案名稱
            header('Content-Disposition: attachment;filename="接送講座紀錄結算表.xlsx"');
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

    function getClass(Request $request)
    {
        $keyword = $request->get('search');

        $query = Class_weeks::select('class_weeks.class', 't01tb.name', 'class_weeks.term');

        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 'class_weeks.class');
        });

        $query->where(function ($query) use ($keyword) {
            $query->where('t01tb.name', 'like', '%'.$keyword.'%')
                ->orwhere('class_weeks.class', 'like', '%'.$keyword.'%')
                ->orwhere('class_weeks.term', 'like', '%'.$keyword.'%');
        });

        $data = $query->distinct()->get(10);

        $result = array();

        foreach ($data as $va) {

            $newData = array();
            $newData['id'] = $va->class.'_'.$va->term;
            $newData['text'] = $va->class . $va->name;
            $newData['text'] .= $va->term.'期';

            $result[] = $newData;
        }

       return response()->json($result);
    }
}
