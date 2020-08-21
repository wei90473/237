<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ItineracyService;
use App\Services\User_groupService;
use App\Models\itineracy;
use App\Models\itineracy_schedule;
use App\Models\itineracy_survey;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Config;

class ItineracySurveyloginController extends Controller
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
            if(in_array('itineracy_surveylogin', $user_group_auth)){
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
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        $queryData['max'] = $this->itineracyService->getAnnualMax($queryData['yerly'])+1;
        if(empty($request->all()) ){
           $queryData['choices'] = $this->_get_year_list();
           return view('admin/itineracy_surveylogin/list', compact('queryData'));
        }
        $data = $this->itineracyService->getAnnualList($queryData);
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/itineracy_surveylogin/list', compact('data','queryData'));
    }

     /**
     * 編輯頁更新處理
     */
    public function update(Request $request, $yerly_term) {
        $yerly = substr($yerly_term, 0,3);
        $term  = substr($yerly_term, 3);
        $data = $request->all();
        $updata = itineracy::where('yerly',$yerly)->where('term',$term)->update( array('surveysdate'=>$data['surveysdate'],
                                                                                       'surveyedate'=>$data['surveyedate']) );
        if($updata){
            return redirect('admin/itineracy_surveylogin?yerly='.$yerly)->with('result', '1')->with('message', '更新成功!');
        }else{
            return back()->with('result', '0')->with('message', '更新失敗!');
        };

    }
    /**
     * 填報資料
     *
     * @param $yerly_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request, $yerly_term){
        $queryData['yerly'] = substr($yerly_term, 0,3);
        $queryData['term']  = substr($yerly_term, 3);
        $data = $this->itineracyService->getList($queryData);
        $queryData['name'] = itineracy::where('yerly',$queryData['yerly'])->where('term',$queryData['term'])->max('name');

        return view('admin/itineracy_surveylogin/form', compact('data','queryData'));
    }
    /**
     * 列印日程表
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function print($id){
        if(!$id) return back()->with('result', '0')->with('message', '錯誤，請選擇修改項目!');


        $base = $this->itineracyService->getSchedulePrintList(array('id'=>$id));
        $name = itineracy::select('name')->where('yerly',$base[0]->yerly)->where('term',$base[0]->term)->first();
        $data[0] = [$base[0]->yerly."年度「".$name['name']."」巡迴研習(彙總表)"];
        $data[4] = ['列印日期：'.(date('Y')-1911).date('/m/d')];
        $data[1]= ['期別','縣市別','確定辦理日期','調訓人數','實施地點','課程名稱','期望議題'];
        $data[2] = $this->itineracyService->getSchedulePrintList(array('id'=>$id));
        Excel::create($data[0][0], function ($excel) use ($data) {//第一參數是檔案名稱
            $excel->sheet('data', function ($sheet) use ($data) {//第一個參數是sheet名稱
                $sheet->setFontSize(12);
                $sheet->setFontBold(true);
                $sheet->row(1,$data[0]);//插入excel欄位
                $sheet->cell('A2', function($cell) use($data) {
                    $num=$data[4][0];
                    $cell->setValue($num);
                    $cell->setFont([                    // 一次性設置
                        'size' => 10,
                        'bold' => false,
                    ]);
                });
                $ascii=65;
                for($a=0;$a<count($data[1]);$a++){
                    $sheet->setWidth(chr($ascii),15);
                    $ascii=$ascii+1;
                }
                $sheet->row(3,$data[1]);
                $row=4;
                $times=0;
                $data['quotatotal'] = 0;
                //插入資料
                for($b=0;$b<count($data[2]);$b++){
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
                    $data['quotatotal'] = $data['quotatotal'] + $data[2][$b]->quota;
                    $times=$times+1;
                    $row++;
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
            });
        })->export('xls');


        //設定瀏覽器讀取此份資料為不快取，與解讀行為是下載 CSV 檔案
        // header("Pragma: no-cache");
        // header("Expires: 0");
        // header("Content-type: application/csv");
        //檔案名稱
        // header("Content-Disposition: attachment; filename=".$data[0]->yerly.iconv("UTF-8","big-5","年度「".$name."」巡迴研習(彙總表) ").".csv");
        // $csv_arr[] = array($data[0]->yerly."年度「".$name."」巡迴研習(彙總表) ");
        // $csv_arr[] = array('列印日期：'.(date('Y')-1911).date('/m/d'));
        // $csv_arr[] = array('期別','縣市別','確定辦理日期','調訓人數','實施地點','課程名稱','期望議題');
        // $class = '';
        // $times = 0;
        // $quotatotal = 0;
        // foreach ($data as $key => $value) {
        //     if( $value->actualdate=="" || is_null($value->actualdate) ){
        //         //跳過
        //     }else{
        //         if($class==''){
        //             $class = $value->class;
        //             $times = 1;
        //             $quotatotal = $value->quota;
        //             $csv_arr[] = array($value->term,config('app.city.'.$value->city),$value->actualdate,$value->quota,$value->address,$value->name,$value->remake);
        //         }elseif($class == $value->class){
        //             $csv_arr[] = array('','','','','',$value->name,$value->remake);
        //         }else{
        //             $csv_arr[] = array($value->term,config('app.city.'.$value->city),$value->actualdate,$value->quota,$value->address,$value->name,$value->remake);
        //             $class = $value->class;
        //             $times++;
        //             $quotatotal = $quotatotal+$value->quota;
        //         }
        //     }
        // }
        // $csv_arr[] = array('合計','',$times.'場 0節',$quotatotal );
        //正式循環輸出陣列內容
        // for ($j = 0; $j < count($csv_arr); $j++) {
        //     if ($j == 0) {
                //檔案標頭如果沒補上 UTF-8 BOM 資訊的話，Excel 會解讀錯誤，偏向輸出給程式觀看的檔案
                // echo "\xEF\xBB\xBF";
            // }
            //輸出符合規範的 CSV 字串以及斷行
            // echo $this->csvstr($csv_arr[$j]) . PHP_EOL;
        // }


    }

    /**
     * 填報資料修改頁
     *
     * @param $yerly_term_city
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $yerly_term_city){
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
        return view('admin/itineracy_surveylogin/form2', compact('data','citydata','queryData'));
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function liststore(Request $request){
        $data = $request->all();
        DB::beginTransaction();
        try{
            itineracy_schedule::create(array('yerly'     =>$data['yerly'],  'term'      =>$data['term'],
                                                'city'   =>$data['city'],   'presetdate'=>$data['presetdate'],
                                                'quota'  =>$data['quota'],  'staff'     =>$data['staff'],
                                                'address'=>$data['address'] ));
            $this->itineracyService->updateSurvey($data);
            // $olddata = itineracy_survey::select('presetdate','day')->where('yerly',$data['yerly'])->where('term',$data['term'])->where('city',$data['city'])->first();
            // $day = $olddata['day']+1 ;
            // $presetdate = (strlen($olddata['presetdate'])==0 )? $data['presetdate']: $olddata['presetdate'].','.$data['presetdate'];
            // itineracy_survey::where('yerly',$data['yerly'])->where('term',$data['term'])->where('city',$data['city'])
            //     ->update(array( 'sponsor'=>$data['sponsor'],'presetdate'=>$presetdate,
            //                     'phone1' =>$data['phone1'],'phone2'     =>$data['phone2'],
            //                     'mail'   =>$data['mail'],  'fax'        =>$data['fax'],
            //                     'day'    =>$day            ));
            DB::commit();
            return redirect('admin/itineracy_surveylogin/list/edit/'.$data['yerly'].$data['term'].$data['city'])->with('result', '1')->with('message', '新增成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '新增失敗，請稍後再試!');
        }
    }


    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function listupdate(Request $request, $class)    {
        if(empty($class))  return back()->with('result', '0')->with('message', '錯誤，請選擇修改項目!');

        $data = $request->all();
        DB::beginTransaction();
        try{
            itineracy_schedule::where('class',$class)->update(array('presetdate'=>$data['E_presetdate'],'quota'  =>$data['E_quota'],
                                                                    'staff'     =>$data['E_staff'],     'address'=>$data['E_address'] ));
            $this->itineracyService->updateSurvey($data);
            // $olddata = itineracy_schedule::select('presetdate')->where('yerly',$data['yerly'])->where('term',$data['term'])->where('city',$data['city'])->get()->toArray();
            // $presetdate = '';
            // foreach ($olddata as $key => $value) {
            //     if($value['presetdate']!='') $presetdate = $presetdate.$value['presetdate'].',';
            // }
            // $presetdate = substr($presetdate,0,-1);
            // itineracy_survey::where('yerly',$data['yerly'])->where('term',$data['term'])->where('city',$data['city'])
            //     ->update(array( 'sponsor'=>$data['sponsor'],'presetdate' =>$presetdate,
            //                     'phone1' =>$data['phone1'], 'phone2'     =>$data['phone2'],
            //                     'mail'   =>$data['mail'],   'fax'        =>$data['fax'] ));
            DB::commit();
            return back()->with('result', '1')->with('message', '更新成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '更新失敗，請稍後再試!');
        }
    }

    /**
     * 刪除處理
     *
     * @param $classes_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function listdestroy($class) {
        if(empty($class))  return back()->with('result', '0')->with('message', '錯誤，請選擇修改項目!');

        DB::beginTransaction();
        try{
            $data = itineracy_schedule::select('yerly','term','city')->where('class',$class)->first();
            itineracy_schedule::where('class',$class)->delete();
            $this->itineracyService->updateSurvey($data);
            DB::commit();
            return back()->with('result', '1')->with('message', '刪除成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '刪除失敗，請稍後再試!');
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
