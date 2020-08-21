<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ItineracyService;
use App\Services\User_groupService;
use App\Models\Itineracy;
use App\Models\itineracy_schedule;
use App\Models\itineracy_survey;
use App\Models\itineracy_course;
use App\Models\itineracy_annual;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Config;

class ItineracyScheduleController extends Controller
{
    /**
     * ItineracyController constructor.
     * @param ItineracyService $ItineracyService
     */
    public function __construct(ItineracyService $itineracyService, User_groupService $user_groupService)
    {
        $this->itineracyService = $itineracyService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('itineracy_schedule', $user_group_auth)){
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
    public function index(Request $request){
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;

        $data = $this->itineracyService->getAnnualList($queryData);
        return view('admin/itineracy_schedule/list', compact('data','queryData'));
    }

    /**
     * 列印
     * @param $type_yerly_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function print($type_yerly_term){
        if(!$type_yerly_term) return back()->with('result', '0')->with('message', '錯誤，請選擇列印項目');

        $type  = substr($type_yerly_term, 0,1);
        $queryData['yerly'] = substr($type_yerly_term, 1,3);
        $queryData['term']  = substr($type_yerly_term, 4);
        $name = itineracy::select('name')->where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->first();
        if(is_null($name['name'])) $name['name'] = '';
        $data[2] = $this->itineracyService->getSchedulePrintList($queryData);
        if($type == 'A'){ //彙總表列印
            $data[0] = [$queryData['yerly']."年度「".$name['name']."」巡迴研習(彙總表)"];
            $data[4] = ['列印日期：'.(date('Y')-1911).date('/m/d')];
            $data[1] = ['期別','縣市別','確定辦理日期','調訓人數','實施地點','課程名稱','期望議題'];
            // $data[2] = $this->itineracyService->getSchedulePrintList($queryData);
            Excel::create($data[0][0], function ($excel) use ($data) {//第一參數是檔案名稱
                $excel->sheet('data', function ($sheet) use ($data) {//第一個參數是sheet名稱
                    $sheet->setFontSize(12);
                    $sheet->setFontFamily('DFKai-sb'); //字型
                    $sheet->mergeCells('A1:G1');
                    $sheet->cell('A1', function($cell) use($data) {
                        $num=$data[0][0];
                        $cell->setValue($num);
                        $cell->setFont([                   
                            'size' => 14,
                        ]);
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                    });

                    $sheet->row(2,$data[4]);
                    $ascii=65;
                    for($a=0;$a<count($data[1]);$a++){ //欄寬
                        if($a==0){
                            $sheet->setWidth(chr($ascii),5);
                        }elseif($a==1 ||$a==3){
                            $sheet->setWidth(chr($ascii),10);
                        }elseif($a==2 ){
                            $sheet->setWidth(chr($ascii),15);
                        }elseif($a==4){
                            $sheet->setWidth(chr($ascii),20);
                        }else{
                            $sheet->setWidth(chr($ascii),33);
                        }
                        $ascii=$ascii+1;
                    }
                    $sheet->row(3,$data[1]);
                    $range = range('A','G');
                    for($i=0;$i<sizeof($range);$i++){
                        $sheet->cell($range[$i].'3', function($cell) use($data) {
                            $cell->setBorder('thin','thin','thin','thin');
                            $cell->setValignment('center');
                            $cell->setAlignment('center');
                        });
                    }

                    $margeRow = $row = 4;
                    $times=0;
                    $a=1;
                    $data['quotatotal'] = 0;
                    //插入資料
                    for($b=0;$b<count($data[2]);$b++){
                        if($b>0 && $data[2][$b]->city==$data[2][$b-$a]->city && $data[2][$b]->quota==$data[2][$b-$a]->quota){
                            $times=$times-1;
                            $a++;
                        }else{
                            if( ($row-$margeRow) >1){ //合併
                                $endRow = $row - 1 ;
                                $sheet->mergeCells('A'.$margeRow.':A'.$endRow);
                                $sheet->mergeCells('B'.$margeRow.':B'.$endRow);
                                $sheet->mergeCells('C'.$margeRow.':C'.$endRow);
                                $sheet->mergeCells('D'.$margeRow.':D'.$endRow);
                                $sheet->mergeCells('E'.$margeRow.':E'.$endRow);
                            }
                            $margeRow = $row;
                            $a=1;
                            $sheet->cell('A'.$row, function($cell) use($data,$b) {
                                $num=$data[2][$b]->term;
                                $cell->setValue($num);
                                $cell->setBorder('thin','thin','thin','thin');
                            });
                            $sheet->cell('B'.$row, function($cell) use($data,$b) {
                                $city=$data[2][$b]->city;
                                $num = config('app.city.'.$city);
                                $cell->setValue($num);
                                $cell->setBorder('thin','thin','thin','thin');
                            });

                            $sheet->cell('C'.$row, function($cell) use($data,$b) {
                                $num=$data[2][$b]->actualdate;
                                $cell->setValue($num);
                                $cell->setBorder('thin','thin','thin','thin');
                            });
                            $sheet->cell('D'.$row, function($cell) use($data,$b) {
                                $num=$data[2][$b]->quota;
                                $cell->setValue($num);
                                $cell->setBorder('thin','thin','thin','thin');
                            });
                            $sheet->cell('E'.$row, function($cell) use($data,$b) {
                                $num=$data[2][$b]->address;
                                $cell->setValue($num);
                                $cell->setBorder('thin','thin','thin','thin');
                            });
                            $sheet->getStyle('E'.$row)->getAlignment()->setWrapText(true);
                        }
                        $sheet->cell('F'.$row, function($cell) use($data,$b) {
                            $num=$data[2][$b]->name;
                            $cell->setValue($num);
                            $cell->setBorder('thin','thin','thin','thin');
                        });
                        $sheet->cell('G'.$row, function($cell) use($data,$b) {
                            $num=$data[2][$b]->remake;
                            $cell->setValue($num);
                            $cell->setBorder('thin','thin','thin','thin');
                        });
                        $data['quotatotal'] = $data['quotatotal'] + ($data[2][$b]->quota==''?0:$data[2][$b]->quota);
                        $times=$times+1;
                        $row++;
                    }
                    if( ($row-$margeRow) >1){ //合併最後一行
                        $endRow = $row - 1 ;
                        $sheet->mergeCells('A'.$margeRow.':A'.$endRow);
                        $sheet->mergeCells('B'.$margeRow.':B'.$endRow);
                        $sheet->mergeCells('C'.$margeRow.':C'.$endRow);
                        $sheet->mergeCells('D'.$margeRow.':D'.$endRow);
                        $sheet->mergeCells('E'.$margeRow.':E'.$endRow);
                    }
                    $data[3] =['合計','',$times.'場 0節',$data['quotatotal']];
                    $sheet->cell('A'.$row, function($cell) use($data,$b) {
                        $num=$data[3][0];
                        $cell->setValue($num);
                    });
                    $sheet->cell('C'.$row, function($cell) use($data,$b) {
                        $num=$data[3][2];
                        $cell->setValue($num);
                    });
                    $sheet->cell('D'.$row, function($cell) use($data,$b) {
                        $num=$data[3][3];
                        $cell->setValue($num);
                    });
                    $sheet->cell('A4:D'.$row, function($cell) use($data) {
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                    });
                });
            })->export('xls');
    /*
        $base = $this->itineracyService->getSchedulePrintList($queryData);
        $name = itineracy::where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->max('name');
        //設定瀏覽器讀取此份資料為不快取，與解讀行為是下載 CSV 檔案
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-type: application/csv");
        if($type == 'A'){ //彙總表列印
            //檔案名稱
            header("Content-Disposition: attachment; filename=".$queryData['yerly'].iconv("UTF-8","big-5","年度「".$name."」巡迴研習(彙總表) ").".csv");
            $csv_arr[] = array();
            $csv_arr[] = array($queryData['yerly']."年度「".$name."」巡迴研習(彙總表) ");
            $csv_arr[] = array('列印日期：'.(date('Y')-1911).date('/m/d'));
            $csv_arr[] = array('期別','縣市別','確定辦理日期','調訓人數','實施地點','課程名稱','期望議題');
            $class = '';
            $times = 0;
            $quotatotal = 0;

            foreach ($base as $key => $value) {
                if( $value->actualdate=="" || is_null($value->actualdate) ){
                    //跳過
                }else{
                    if($class==''){
                        $class = $value->class;
                        $times = 1;
                        $quotatotal = $value->quota;
                        $csv_arr[] = array($value->term,config('app.city.'.$value->city),$value->actualdate,$value->quota,$value->address,$value->name,$value->remake);
                    }elseif($class == $value->class){
                        $csv_arr[] = array('','','','','',$value->name,$value->remake);
                    }else{
                        $csv_arr[] = array($value->term,config('app.city.'.$value->city),$value->actualdate,$value->quota,$value->address,$value->name,$value->remake);
                        $class = $value->class;
                        $times++;
                        $quotatotal = $quotatotal+$value->quota;
                    }
                }
            }
            $csv_arr[] = array('合計','',$times.'場 0節',$quotatotal );
            //正式循環輸出陣列內容
            for ($j = 0; $j < count($csv_arr); $j++) {
                if ($j == 0) {
                    //檔案標頭如果沒補上 UTF-8 BOM 資訊的話，Excel 會解讀錯誤，偏向輸出給程式觀看的檔案
                    echo "\xEF\xBB\xBF";
                }
                //輸出符合規範的 CSV 字串以及斷行
                echo $this->csvstr($csv_arr[$j]) . PHP_EOL;
            }
    */
        }elseif($type == 'B'){  //空白日程表列印
            
            $data[0] = [$queryData['yerly']."年度「".$name['name']."」空白日程表 "];
            $data[1] = ['縣市別','確定辦理日期','調訓人數','工作人員','',''];
            // $data[2] = $this->itineracyService->getSchedulePrintList($queryData);
            $data[4] = ['列印日期：'.(date('Y')-1911).date('/m/d')];
            $data[3] = [$queryData['yerly']."年度 巡迴研習"];
            Excel::create($data[0][0], function ($excel) use ($data) { //第一參數是檔案名稱
                $excel->sheet('data', function ($sheet) use ($data) { //第一個參數是sheet名稱
                    $sheet->setFontFamily('DFKai-sb'); //字型
                    $sheet->mergeCells('A1:F1');
                    $sheet->cell('A1', function($cell) use($data) {
                        $num=$data[3][0];
                        $cell->setValue($num);
                        $cell->setFont([                    // 一次性設置
                            'size' => 14,
                            // 'bold' => false,
                            // 'name' => 'DFKai-sb',
                        ]);
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                    });
                    $sheet->setFontSize(12);
                    $sheet->row(2,$data[4]);
                    $ascii=65;
                    for($a=0;$a<count($data[1]);$a++){
                        $sheet->setWidth(chr($ascii),15);
                        $ascii=$ascii+1;
                    }
                    $sheet->row(3,$data[1]);
                    $range = range('A','F');
                    for($i=0;$i<sizeof($range);$i++){
                        $sheet->cell($range[$i].'3', function($cell) use($data) {
                            $cell->setBorder('thin','thin','thin','thin');
                            $cell->setValignment('center');
                            $cell->setAlignment('center');
                        });
                    }
                    $row=4;
                    $data['quotatotal'] = 0;
                    $data['stafftotal'] = 0;
                    //插入資料
                    for($b=0;$b<count($data[2]);$b++){
                        $sheet->cell('A'.$row, function($cell) use($data,$b) {
                            $city=$data[2][$b]->city;
                            $num = config('app.city.'.$city);
                            $cell->setValue($num);
                            $cell->setBorder('thin','thin','thin','thin');
                            $cell->setValignment('center');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('B'.$row, function($cell) use($data,$b) {
                            $num=$data[2][$b]->actualdate;
                            $cell->setValue($num);
                            $cell->setBorder('thin','thin','thin','thin');
                            $cell->setValignment('center');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('C'.$row, function($cell) use($data,$b) {
                            $num=$data[2][$b]->quota;
                            $cell->setValue($num);
                            $cell->setBorder('thin','thin','thin','thin');
                            $cell->setValignment('center');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('D'.$row, function($cell) use($data,$b) {
                            $num=$data[2][$b]->staff;
                            $cell->setValue($num);
                            $cell->setBorder('thin','thin','thin','thin');
                            $cell->setValignment('center');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('E'.$row, function($cell) use($data,$b) {

                            $cell->setBorder('thin','thin','thin','thin');
                            $cell->setValignment('center');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('F'.$row, function($cell) use($data,$b) {

                            $cell->setBorder('thin','thin','thin','thin');
                            $cell->setValignment('center');
                            $cell->setAlignment('center');
                        });
                        $data['quotatotal'] = $data['quotatotal'] + $data[2][$b]->quota;
                        $data['stafftotal'] = $data['stafftotal'] + $data[2][$b]->staff;
                        $row++;
                    }
                    $data[3] =['合計','',$data['stafftotal'],$data['quotatotal']];
                    $sheet->cell('A'.$row, function($cell) use($data,$b) {
                        $num=$data[3][0];
                        $cell->setValue($num);
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('C'.$row, function($cell) use($data,$b) {
                        $num=$data[3][3];
                        $cell->setValue($num);
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('D'.$row, function($cell) use($data,$b) {
                        $num=$data[3][2];
                        $cell->setValue($num);
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                    });
                });
            })->export('xls');

    /*        //檔案名稱
            header("Content-Disposition: attachment; filename=".$queryData['yerly'].iconv("UTF-8","big-5","年度「".$name."」空白日程表 ").".csv");
            $csv_arr[] = array();
            $csv_arr[] = array($queryData['yerly']."年度  巡迴研習 ");
            $csv_arr[] = array('列印日期：'.(date('Y')-1911).date('/m/d'));
            $csv_arr[] = array('縣市別','確定辦理日期','調訓人數','工作人員','','');
            $class = '';
            $times = 0;
            $quotatotal = 0;
            $stafftotal = 0;
            foreach ($base as $key => $value) {
                if( $value->actualdate=="" || is_null($value->actualdat) ){
                    //跳過
                }else{
                    if($class==''){
                        $class = $value->class;
                        $times = 1;
                        $quotatotal = $value->quota;
                        $stafftotal = $value->staff;
                        $csv_arr[] = array(config('app.city.'.$value->city),$value->actualdate,$value->quota,$value->staff);
                    }elseif($class == $value->class){
                        //跳過
                    }else{
                        $csv_arr[] = array(config('app.city.'.$value->city),$value->actualdate,$value->quota,$value->staff);
                        $class = $value->class;
                        $times++;
                        $quotatotal = $quotatotal+$value->quota;
                        $stafftotal = $stafftotal+$value->staff;
                    }
                }
            }
            $csv_arr[] = array('合計','',$times.'場 0節',$quotatotal,$stafftotal );
            //正式循環輸出陣列內容
            for ($j = 0; $j < count($csv_arr); $j++) {
                if ($j == 0) {
                    //檔案標頭如果沒補上 UTF-8 BOM 資訊的話，Excel 會解讀錯誤，偏向輸出給程式觀看的檔案
                    echo "\xEF\xBB\xBF";
                }
                //輸出符合規範的 CSV 字串以及斷行
                echo $this->csvstr($csv_arr[$j]) . PHP_EOL;
            }
    */
        }else{
            return back()->with('result', '0')->with('message', '錯誤，請選擇列印項目!');
        }
    }
    /**
     * 修改頁
     *
     * @param $yerly_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request ,$yerly_term){
        $queryData['yerly'] = substr($yerly_term, 0,3);
        $queryData['term']  = substr($yerly_term, 3);
        $data = $this->itineracyService->getList($queryData);
        $queryData['name'] = itineracy::where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->max('name');
        return view('admin/itineracy_schedule/form', compact('data','queryData'));
    }
    /**
     * 匯入需求調查
     *
     * @param Request $yerly_term_city
     * @return \Illuminate\Http\RedirectResponse
     */
    public function batchimport(Request $request, $yerly_term_city){
        $queryData['yerly'] = substr($yerly_term_city, 0,3);
        $queryData['term']  = substr($yerly_term_city, 3,1);
        $queryData['city']  = substr($yerly_term_city, -2);
        $ckeck = itineracy_survey::select('presetdate','day','actualdate','actualdays')->where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->where('city',$queryData['city'])->first();
        if(strlen($ckeck['actualdate'])>0 || $ckeck['actualdays'] >0 ) return back()->with('result', '0')->with('message', '已經有確認辦理日期，不能再匯入!');

        DB::beginTransaction();
        try{
            itineracy_survey::where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->where('city',$queryData['city'])
            ->update(array('actualdate' =>$ckeck['presetdate'],  'actualdays' =>$ckeck['day']));

            $olddata = itineracy_schedule::select('class','presetdate','actualdate')->where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->where('city',$queryData['city'])->get()->toArray();
            foreach ($olddata as $key => $value) {
                if(strlen($value['actualdate']>0)){
                    return false;
                }else{
                    itineracy_schedule::where('class',$value['class'])->update(array('actualdate'=>$value['presetdate']));
                }
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '匯入成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '已經有確認辦理日期，不能再匯入!');
        }

    }

    /**
     * 縣市別修改頁
     *
     * @param $class
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cityedit(Request $request, $yerly_term_city){
        $queryData['yerly'] = substr($yerly_term_city, 0,3);
        $queryData['term']  = substr($yerly_term_city, 3,1);
        $queryData['city']  = substr($yerly_term_city, -2);
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        $citydata = $this->itineracyService->getList($queryData);
        $data = $this->itineracyService->getScheduleList($queryData);
        $citydata = $citydata[0];
        // var_dump($citydata[0]);exit();
        $queryData['name'] = itineracy::where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->max('name');
        return view('admin/itineracy_schedule/form2', compact('data','citydata','queryData'));
    }
    /**
     * 縣市別新增
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function liststore(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try{
            itineracy_schedule::create(array('yerly'     =>$data['yerly'],  'term'      =>$data['term'],
                                                'city'   =>$data['city'],   'actualdate'=>$data['actualdate'],
                                                'quota'  =>$data['quota'],  'staff'     =>$data['staff'],
                                                'address'=>$data['address'],'fee'       =>$data['fee']));
            $this->itineracyService->updateSurvey($data);
            DB::commit();
            return redirect('admin/itineracy_schedule/edit/city/'.$data['yerly'].$data['term'].$data['city'])->with('result', '1')->with('message', '新增成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '新增失敗，請稍後再試!');
        }
    }

    /**
     * 縣市別更新
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function listupdate(Request $request, $class)    {
        if(empty($class))  return view('admin/errors/error');

        $data = $request->all();
        // var_dump($data);exit();
        if( $data['E_actualdate']=='' || strlen($data['E_actualdate'])!='7' ) return back()->with('result', '0')->with('message', '確認辦理日期錯誤!');

        DB::beginTransaction();
        try{
            itineracy_schedule::where('class',$class)->update(array('actualdate'=>$data['E_actualdate'],'quota'  =>$data['E_quota'],
                                                                    'staff'     =>$data['E_staff'],     'address'=>$data['E_address'],
                                                                    'fee'       =>$data['E_fee'] ));
            $this->itineracyService->updateSurvey($data);
            DB::commit();
            return back()->with('result', '1')->with('message', '更新成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '更新失敗，請稍後再試!');
        }
    }

    /**
     * 縣市別刪除處理
     *
     * @param $class
     * @return \Illuminate\Http\RedirectResponse
     */
    public function listdestroy($class) {
        if(empty($class)) return view('admin/errors/error');

        DB::beginTransaction();
        try{
            $data = itineracy_schedule::select('yerly','term','city')->where('class',$class)->first();
            itineracy_schedule::where('class',$class)->delete();
            itineracy_course::where('class',$class)->delete();
            $this->itineracyService->updateSurvey($data);

            DB::commit();
            return back()->with('result', '1')->with('message', '刪除成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '刪除失敗，請稍後再試!');
        }
    }
    /**
     * 設定課程頁
     *
     * @param $classes_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function settingclass($class){
        if(empty($class))  return view('admin/errors/error');

        $queryData = itineracy_schedule::select('yerly','term','city')->where('class',$class)->first();
        $queryData['class'] = $class;
        $data = $this->itineracyService->getAnnual($queryData);
        $itineracy = itineracy::select('name','topics')->where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->first();
        $queryData['name'] = $itineracy['name'];
        $queryData['topics'] = $itineracy['topics'];
        return view('admin/itineracy_schedule/setting', compact('data','queryData'));
    }
    /**
     * 設定課程修改
     *
     * @param $classes_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function classupdate(Request $request, $class)    {
        if(empty($class))  return view('admin/errors/error');

        $data = $request->all();
        DB::beginTransaction();
        try{
            itineracy_course::where('class',$class)->delete();
            $annual = itineracy_annual::select('id')->where('yerly',$data['yerly'])->where('term',$data['term'])->get()->toarray();
            //unset($data['_method'],$data['_token'],$data['yerly'],$data['term']);
            foreach ($annual as $key => $value) {
                if(isset($data[$value['id']])){
                    itineracy_course::create(array( 'class'  =>$class,       'yerly'    =>$data['yerly'], 'term'   =>$data['term'],
                                                    'city'   =>$data['city'],'annual_id'=>$value['id'],   'remake' =>$data['remake'.$value['id']]  ));
                }
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '更新成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '更新失敗，請稍後再試!');
        }
    }

    private function input_csv($handle) {
        $out = array ();
        $n = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        return $out;
    }

    //確保輸出內容符合 CSV 格式，定義下列方法來處理
    private function csvstr(array $fields): string{
        $f = fopen('php://memory', 'r+');
        if (fputcsv($f, $fields) === false) {
            return false;
        }
        rewind($f);
        $csv_line = stream_get_contents($f);
        return rtrim($csv_line);
    }

    public function _get_year_list(){
        $year_list = array();
        $year_now = date('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }
    private function __result( $code,$msg ){
        echo json_encode(array('status' => $code , 'msg' => $msg));
    exit;
  }
}
