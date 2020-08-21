<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SatisfactionService;
use App\Services\User_groupService;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\T08tb;
use App\Models\T09tb;
use App\Models\T01tb;
use App\Models\M01tb;
use App\Models\T04tb;
use DB ;

class SatisfactionController extends Controller
{
    /**
     * SatisfactionController constructor.
     * @param SatisfactionService $satisfactionService
     */
    public function __construct(SatisfactionService $satisfactionService, User_groupService $user_groupService)
    {
        $this->satisfactionService = $satisfactionService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('satisfaction', $user_group_auth)){
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
        //年
        $this_yesr = date('Y') - 1911;
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData['year_list']);
        // echo "\n</pre>\n";
        if(null == $request->get('yerly1')){
            $queryData['yerly1'] = $this_yesr;
        }else{
            $queryData['yerly1'] = $request->get('yerly1');
        }
        if(null == $request->get('yerly2')){
            $queryData['yerly2'] = $this_yesr;
        }else{
            $queryData['yerly2'] = $request->get('yerly2');
        }
        if(null == $request->get('year_or_day')){
            $queryData['year_or_day'] = '1';
        }else{
          $queryData['year_or_day'] = $request->get('year_or_day');
        }
        // dd($queryData);

        //班號
        $queryData['class'] = $request->get('class');
        $queryData['name'] = $request->get('name');
        $queryData['class_name_1'] = $request->get('class_name_1');
        $queryData['class_name_2'] = $request->get('class_name_2');
        $queryData['class_name_3'] = $request->get('class_name_3');
        $queryData['teacher'] = $request->get('teacher');
        $queryData['idno'] = $request->get('idno');
        $queryData['experience'] = $request->get('experience');
        $queryData['dept'] = $request->get('dept');


        // 分班名稱**
        $queryData['class_branch_name'] = $request->get('class_branch_name');
        // 期別
        $queryData['term'] = $request->get('term');
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');

        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData);
        // echo "\n</pre>\n";
        // die();
        $queryData['search'] = $request->get('search');

        if($queryData['year_or_day'] == '2'){
            if(empty($queryData['sdate']) || empty($queryData['edate'])){
                return back()->with('result', '0')->with('message', '選擇日期區間，日期請勿空白');
            }
        }

        if($queryData['search'] != 'search' ){
          $queryData2['class'] = 'none';
          $data = $this->satisfactionService->getSatisfactionList($queryData2);
        }else{
          if(!empty($queryData['term'])){
            $queryData['term'] = str_pad($queryData['term'],2,'0',STR_PAD_LEFT);
          }
          // dd($queryData['term']);
          $data = $this->satisfactionService->getSatisfactionList($queryData);
        }
        return view('admin/satisfaction/list', compact('data', 'queryData', 'sponsor'));
    }

    public function export(Request $request)
    {
        $this_yesr = date('Y') - 1911;

        if(null == $request->get('yerly1')){
            $queryData['yerly1'] = $this_yesr;
        }else{
            $queryData['yerly1'] = $request->get('yerly1');
        }
        if(null == $request->get('yerly2')){
            $queryData['yerly2'] = $this_yesr;
        }else{
            $queryData['yerly2'] = $request->get('yerly2');
        }
        if(null == $request->get('year_or_day')){
            $queryData['year_or_day'] = '1';
        }else{
          $queryData['year_or_day'] = $request->get('year_or_day');
        }
        // dd($queryData);
        $queryData['class'] = $request->get('class');
        $queryData['name'] = $request->get('name');
        $queryData['class_name_1'] = $request->get('class_name_1');
        $queryData['class_name_2'] = $request->get('class_name_2');
        $queryData['class_name_3'] = $request->get('class_name_3');
        $queryData['teacher'] = $request->get('teacher');
        $queryData['idno'] = $request->get('idno');
        $queryData['experience'] = $request->get('experience');
        $queryData['dept'] = $request->get('dept');
        $queryData['class_branch_name'] = $request->get('class_branch_name');
        $queryData['term'] = $request->get('term');
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');

        if($queryData['year_or_day'] == '2'){
            if(empty($queryData['sdate']) || empty($queryData['edate'])){
                return back()->with('result', '0')->with('message', '選擇日期區間，日期請勿空白');
            }
        }
        $teacher_data = $this->satisfactionService->getSatisfaction($queryData);
        $title = array('年度', '姓名', '服務單位', '職稱', '班別名稱', '分班名稱', '課程名稱', '時數', '滿意度');
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($teacher_data);
        // echo "\n</pre>\n";
        // die();
        $data[0] = $title;
        $data[1] = $teacher_data;

        Excel::create('teacher_data', function ($excel) use ($data) {//第一參數是檔案名稱
            $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                $ascii=65;
                for($a=0;$a<count($data[0]);$a++){
                    $sheet->setWidth(chr($ascii),15);
                    $ascii=$ascii+1;
                }
                $sheet->row(1,$data[0]);//插入excel欄位
                $row=2;//控制列index
                //插入資料
                for($b=0;$b<count($data[1]);$b++){
                    $sheet->row($row,$data[1][$b]);
                    $row++;
                }
            });
        })->export('xls');
    }

    public function export2(Request $request)
    {
        $this_yesr = date('Y') - 1911;

        if(null == $request->get('yerly1')){
            $queryData['yerly1'] = $this_yesr;
        }else{
            $queryData['yerly1'] = $request->get('yerly1');
        }
        if(null == $request->get('yerly2')){
            $queryData['yerly2'] = $this_yesr;
        }else{
            $queryData['yerly2'] = $request->get('yerly2');
        }
        if(null == $request->get('year_or_day')){
            $queryData['year_or_day'] = '1';
        }else{
          $queryData['year_or_day'] = $request->get('year_or_day');
        }
        // dd($queryData);
        $queryData['class'] = $request->get('class');
        $queryData['name'] = $request->get('name');
        $queryData['class_name_1'] = $request->get('class_name_1');
        $queryData['class_name_2'] = $request->get('class_name_2');
        $queryData['class_name_3'] = $request->get('class_name_3');
        $queryData['teacher'] = $request->get('teacher');
        $queryData['idno'] = $request->get('idno');
        $queryData['experience'] = $request->get('experience');
        $queryData['dept'] = $request->get('dept');
        $queryData['class_branch_name'] = $request->get('class_branch_name');
        $queryData['term'] = $request->get('term');
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');

        if($queryData['year_or_day'] == '2'){
            if(empty($queryData['sdate']) || empty($queryData['edate'])){
                return back()->with('result', '0')->with('message', '選擇日期區間，日期請勿空白');
            }
        }
        $teacher_data = $this->satisfactionService->getSatisfaction2($queryData);
        $title = array('年度', '姓名', '總授課', '已授課', '未授課', '平均滿意度', '總平均滿意度');
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($teacher_data);
        // echo "\n</pre>\n";
        // die();
        $data[0] = $title;
        $data[1] = $teacher_data;

        Excel::create('teacher_AVG', function ($excel) use ($data) {//第一參數是檔案名稱
            $excel->sheet('SheetName', function ($sheet) use ($data) {//第一個參數是sheet名稱
                $ascii=65;
                for($a=0;$a<count($data[0]);$a++){
                    $sheet->setWidth(chr($ascii),15);
                    $ascii=$ascii+1;
                }
                $sheet->row(1,$data[0]);//插入excel欄位
                $row=2;//控制列index
                $setMerge1 = '';
                $setMerge2 = '';
                $setName = '';
                $setAVG = '0';
                $setAVGcount = '0';
                $datarows = count($data[1]);
                //插入資料
                for($b=0;$b<$datarows;$b++){
                    $sheet->row($row,$data[1][$b]);
                    if($row == 2){
                    	$setName = $data[1][$b]['cname'];
                    	$setMerge1 = $row;
                    }
                    if($setName == $data[1][$b]['cname']){
                    	if(!empty($data[1][$b]['okrate'])){
                            // $sheet->cell('J'.$row , $setName);
                    		$setAVG = $setAVG + $data[1][$b]['okrate'];
	                    	$setAVGcount++;
                    	}
                    }

                    if($setName != $data[1][$b]['cname']){
                		$setMerge2 = $row-1;
                		$sheet->setMergeColumn(array(
		                    'columns' => array('G'),
		                    'rows' => array(
		                        array($setMerge1, $setMerge2),
		                    )
		                ));
			                if($setAVG>0 && $setAVGcount>1){
                                // $sheet->cell('H'.$setMerge1 , $setAVG);
                                // $sheet->cell('I'.$setMerge1 , $setAVGcount);
			                	$setAVG = round(($setAVG/$setAVGcount) , 2);
	                    		$sheet->cell('G'.$setMerge1 , $setAVG);
			                }
                    	$setAVG = '0';
                    	$setAVGcount = '0';
		                $setMerge1 = $row;
		                $setName = $data[1][$b]['cname'];
                        if(!empty($data[1][$b]['okrate'])){
                            // $sheet->cell('J'.$row , $setName);
                            $setAVG = $setAVG + $data[1][$b]['okrate'];
                            $setAVGcount++;
                        }
                    }
                    if($b == ($datarows-1)){
                    	$sheet->setMergeColumn(array(
		                    'columns' => array('G'),
		                    'rows' => array(
		                        array($setMerge1, $row),
		                    )
		                ));
			                if($setAVG>0 && $setAVGcount>1){
			                	$setAVG = round(($setAVG/$setAVGcount) , 2);
	                    		$sheet->cell('G'.$setMerge1 , $setAVG);
			                }
                    }
                    $row++;
                }

            });
        })->export('xls');
    }

}
