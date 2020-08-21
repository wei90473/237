<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Services\Class_processService;
use App\Models\Class_process;
use App\Models\Class_process_job;
use App\Services\User_groupService;


class Class_processController extends Controller
{
    public function __construct(Class_processService $class_processService, User_groupService $user_groupService)
    {
        $this->class_processService = $class_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_process', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function index(Request $request)
    {
        $data=[];
        $queryData['name'] = $request->get('name');
        $queryData['branch'] = $request->get('branch');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;

        $queryData['search'] = $request->get('search');

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData);
        // echo "\n</pre>\n";
        // die();

        if($queryData['search'] != 'search' ){
          $queryData2['name'] = 'none';
          $data=$this->class_processService->getList($queryData2);
        }else{

          $data=$this->class_processService->getList($queryData);
        }


        return view('admin/class_process/list',compact('data','queryData'));

    }

    public function create(Request $request)
    {

        return view('admin/class_process/form', compact('class_data'));
    }

    public function store(Request $request)
    {
        // 取得POST資料
        $data = $request->all();

        $fields = array(
            'name' => $data['name'],
            'branch' => $data['branch'],
            'process' => $data['process'],
        );

        if(isset($data['preset'])){
        	$fields['preset'] = $data['preset'];
        }else{
        	$fields['preset'] = 'N';
        }

        //新增
        $result = Class_process::create($fields);

        return redirect('/admin/class_process/')->with('result', '1')->with('message', '新增成功!');
    }

    public function detail($id)
    {
        // 取得POST資料
        $data = Class_process::find($id);
        $sub_data = $this->class_processService->getSub($id);
        if ( ! $data) {
            return view('admin/errors/error');
        }

        return view('admin/class_process/form', compact('data', 'sub_data'));
    }

    public function update(Request $request, $id)
    {

        $data = $request->all();

        $fields = array(
            'name' => $data['name'],
            'branch' => $data['branch'],
            'process' => $data['process'],
        );

        if(isset($data['preset'])){
        	$fields['preset'] = $data['preset'];
        }else{
        	$fields['preset'] = 'N';
        }

        Class_process::where('id', $id)->update($fields);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    public function create_job(Request $request)
    {
        $job = $this->getJob();
        $class_process_id = $request->get('class_process');
        return view('admin/class_process/form_job', compact('class_process_id', 'job'));
    }

    public function store_job(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');  //獲取UploadFile例項
            if ( $file->isValid()) { //判斷檔案是否有效
                $filename = $file->getClientOriginalName(); //檔案原名稱

                //$filename = time() . "." . $extension;    //重新命名
                $file->move(public_path()."/Uploads/class_process/", $filename);
            }
        }
        unset($request['upload']);
        // 取得POST資料
        $data = $request->all();
        // deadline 1:無期限  2:開課前  3:結訓後
        // deadline_type 1 開課前 deadline_day_1 天
        // deadline_type 2 開課前 deadline_day_2 上週星期
        // deadline_type 3 開課前 deadline_day_3 號
        // deadline_type 4 結訓後 deadline_day_4 天
        // deadline_type 5 結訓後 deadline_day_5 上週星期
        // deadline_type 6 結訓後 deadline_day_6 號

        $fields = array(
            'class_process_id' => $data['class_process_id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'job' => $data['job'],
            'deadline' => $data['deadline'],
            'email' => $data['email'],
            'freeze' => $data['freeze'],
            'file' => $data['file'],
        );

        if($data['deadline'] != '1' && !isset($data['deadline_type']) || $data['deadline'] != '1' && empty($data['deadline_day_'.$data['deadline_type']])){
            return back()->with('result', '0')->with('message', '工作期限若不是無期限, 請選擇期限還有日期!');
        }

        if($data['deadline'] != '1'){
            $fields['deadline_type'] = $data['deadline_type'];
            $fields['deadline_day'] = $data['deadline_day_'.$data['deadline_type']];
        }
        // dd($fields);
        $result = Class_process_job::create($fields);

        return redirect('/admin/class_process/detail/'.$data['class_process_id'])->with('result', '1')->with('message', '新增成功!');
    }

     public function edit_job($id)
    {
        $job = $this->getJob();
        $data = Class_process_job::find($id)->toArray();
        if($data['deadline_type'] == '1'){
            $data['deadline_day_1'] = $data['deadline_day'];
        }
        if($data['deadline_type'] == '2'){
            $data['deadline_day_2'] = $data['deadline_day'];
        }
        if($data['deadline_type'] == '3'){
            $data['deadline_day_3'] = $data['deadline_day'];
        }
        if($data['deadline_type'] == '4'){
            $data['deadline_day_4'] = $data['deadline_day'];
        }
        if($data['deadline_type'] == '5'){
            $data['deadline_day_5'] = $data['deadline_day'];
        }
        if($data['deadline_type'] == '6'){
            $data['deadline_day_6'] = $data['deadline_day'];
        }
        $class_process_id = $data['class_process_id'];
        // dd($data);
        return view('admin/class_process/form_job', compact('data', 'class_process_id', 'job'));
    }

    public function update_job(Request $request, $id)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');  //獲取UploadFile例項
            if ( $file->isValid()) { //判斷檔案是否有效
                $filename = $file->getClientOriginalName(); //檔案原名稱
                if($filename != $request['old_file']){
                    if(file_exists(public_path()."/Uploads/class_process/".$request['old_file'])){
                        unlink(public_path()."/Uploads/class_process/".$request['old_file']);
                    }
                }

                $file->move(public_path()."/Uploads/class_process/", $filename);
            }
        }
        unset($request['upload']);

        $data = $request->all();
        // dd($data);

        $fields = array(
            'class_process_id' => $data['class_process_id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'job' => $data['job'],
            'deadline' => $data['deadline'],
            'email' => $data['email'],
            'freeze' => $data['freeze'],
            'file' => $data['file'],
        );

        if($data['deadline'] != '1' && !isset($data['deadline_type']) || $data['deadline'] != '1' && empty($data['deadline_day_'.$data['deadline_type']])){
            return back()->with('result', '0')->with('message', '工作期限若不是無期限, 請選擇期限還有日期!');
        }

        if($data['deadline'] != '1'){
            $fields['deadline_type'] = $data['deadline_type'];
            $fields['deadline_day'] = $data['deadline_day_'.$data['deadline_type']];
        }

        Class_process_job::where('id', $id)->update($fields);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    public function download_file($id)
    {
        $data = Class_process_job::find($id)->toArray();
        if($data['file']){
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename={$data['file']}");
            readfile(public_path()."/Uploads/class_process/".$data['file']);
            exit;
        }
    }

    public function destroy($id)
    {
        if ($id) {
            Class_process::find($id)->delete();
            return redirect("/admin/class_process?search=search")->with('result', '1')->with('message', '刪除成功!');
        } else {
            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    public function destroy_job($id)
    {
        if ($id) {
            $data = Class_process_job::find($id)->toArray();
            // dd($data);
            Class_process_job::find($id)->delete();
            return redirect("/admin/class_process/detail/{$data['class_process_id']}")->with('result', '1')->with('message', '刪除成功!');
        } else {
            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    public function getJob()
    {
        $job = array(
            array('lv'=>'1','name'=>'訓練班務','job'=>''),
            array('lv'=>'2','name'=>'課程配當安排','job'=>''),
            array('lv'=>'3','name'=>'維護單元','job'=>'unit'),
            array('lv'=>'3','name'=>'維護課程','job'=>'arrangement_class'),
            array('lv'=>'3','name'=>'上傳實施計畫','job'=>'arrangement_upload'),
            array('lv'=>'2','name'=>'課程表處理','job'=>''),
            array('lv'=>'3','name'=>'維護課程表','job'=>'class_schedule'),
            array('lv'=>'3','name'=>'調整主教室','job'=>'siteedit'),
            array('lv'=>'3','name'=>'網頁公告','job'=>'publishedit'),
            array('lv'=>'2','name'=>'講座授課及教材資料登錄','job'=>''),
            array('lv'=>'3','name'=>'維護講座課程教材 (無法凍結)','job'=>'teaching_material'),
            array('lv'=>'2','name'=>'教學教法處理','job'=>''),
            array('lv'=>'3','name'=>'設定教學教法','job'=>'method'),
            array('lv'=>'2','name'=>'經費概(結)處理','job'=>''),
            array('lv'=>'3','name'=>'維護概算資料','job'=>'funding_edit_type1'),
            array('lv'=>'3','name'=>'維護結算資料','job'=>'funding_edit_type2'),
            array('lv'=>'2','name'=>'線上報名設定','job'=>''),
            array('lv'=>'3','name'=>'非委訓班','job'=>'signup_edit_type1'),
            array('lv'=>'3','name'=>'委訓班','job'=>'signup_edit_type2'),
            array('lv'=>'2','name'=>'審核報名處理','job'=>''),
            array('lv'=>'3','name'=>'CSV匯入報名資料','job'=>'importApplyData'),
            array('lv'=>'3','name'=>'審核及維護報名資料','job'=>'review_apply'),
            array('lv'=>'3','name'=>'維護可報名人數','job'=>'assign'),
            array('lv'=>'2','name'=>'委訓班費用處理','job'=>''),
            array('lv'=>'3','name'=>'維護班期費用資料','job'=>'special_class_fee'),
            array('lv'=>'2','name'=>'調訓函','job'=>''),
            array('lv'=>'3','name'=>'寄送調訓函(無法凍結)','job'=>'transfer_training_letter'),//20200618
            // array('lv'=>'2','name'=>'班期調派訓異常統計表','job'=>''),
            array('lv'=>'1','name'=>'講師資料','job'=>''),
            array('lv'=>'2','name'=>'講座擬聘處理','job'=>''),
            array('lv'=>'3','name'=>'維護講座擬聘資料','job'=>'waiting'),
            array('lv'=>'2','name'=>'講座資料登錄','job'=>''),
            array('lv'=>'3','name'=>'維護講座基本資料 (無法凍結)','job'=>'lecture'),
            array('lv'=>'3','name'=>'上傳個資授權書 (無法凍結)','job'=>'lecture_upload'),
            array('lv'=>'2','name'=>'講座聘任處理','job'=>''),
            array('lv'=>'3','name'=>'維護講座聘任資料','job'=>'employ'),
            array('lv'=>'2','name'=>'講座用餐、住宿、派車資料登錄','job'=>''),
            array('lv'=>'3','name'=>'維護講座服務資料','job'=>'teacher_related'),
            array('lv'=>'3','name'=>'取得講座填寫連結 (無法凍結)','job'=>'teacher_related_url'),
            // array('lv'=>'2','name'=>'講師基本資料表','job'=>''),
            array('lv'=>'2','name'=>'講座聘函','job'=>''),
            array('lv'=>'3','name'=>'寄送講座聘函(無法凍結)','job'=>'lecture_mail'),//20200618
            // array('lv'=>'2','name'=>'講師簽名單','job'=>''),
            array('lv'=>'1','name'=>'學員資料','job'=>''),
            array('lv'=>'2','name'=>'學員報名處理','job'=>''),
            array('lv'=>'3','name'=>'審核換員、補報及取消報名','job'=>'modify_manage'),
            array('lv'=>'3','name'=>'匯入名冊、維護報名資料、公告學員名冊','job'=>'student_apply'),
            array('lv'=>'3','name'=>'編組別','job'=>'arrange_group'),
            array('lv'=>'3','name'=>'序學號','job'=>'arrange_stno'),
            array('lv'=>'2','name'=>'學員請假處理','job'=>''),
            array('lv'=>'3','name'=>'維護請假資料','job'=>'leave'),
            array('lv'=>'3','name'=>'學員刷卡處理 (無法凍結)','job'=>'punch'),//20200618
            array('lv'=>'2','name'=>'成績輸入處理','job'=>''),
            array('lv'=>'3','name'=>'項目比例設定','job'=>'student_grade_setting'),
            array('lv'=>'3','name'=>'成績輸入','job'=>'student_grade_input_grade'),
            array('lv'=>'2','name'=>'數位時數處理','job'=>''),
            array('lv'=>'3','name'=>'設定數位課程','job'=>'digital_class_setting'),
            array('lv'=>'3','name'=>'維護課程完成狀態','job'=>'digital_student'),
            array('lv'=>'1','name'=>'問卷調查','job'=>''),
            array('lv'=>'2','name'=>'訓前訓中訓後問卷設定','job'=>''),
            array('lv'=>'3','name'=>'設定問卷','job'=>'trainQuestSetting'),
            array('lv'=>'2','name'=>'Email線上問卷填答通知','job'=>''),
            array('lv'=>'3','name'=>'發送問卷連結E-mail給學員 (無法凍結)','job'=>'notice_emai'),
            array('lv'=>'2','name'=>'成效問卷','job'=>''),
            array('lv'=>'3','name'=>'設定成效問卷','job'=>'effectiveness_survey'),//20200618
            array('lv'=>'1','name'=>'場地管理','job'=>''),
            array('lv'=>'2','name'=>'辦班需求(確認)處理','job'=>''),
            array('lv'=>'3','name'=>'維護辦班需求(確認)','job'=>'classes_requirements'),//20200618
            array('lv'=>'1','name'=>'例行業務','job'=>''),
            array('lv'=>'2','name'=>'教材','job'=>''),
            array('lv'=>'3','name'=>'教材交印資料處理','job'=>'teaching_material_print'),//20200618
            array('lv'=>'1','name'=>'資料匯出','job'=>''),
            array('lv'=>'2','name'=>'入口網站資料匯出','job'=>''),
            array('lv'=>'3','name'=>'匯出時數資料 (無法凍結)','job'=>'entryexport'),
        );

        // $data = array();
        // foreach($job as $row){
        //     if($row['lv'] == '3'){
        //         $data[$row['job']] = $row['name'];
        //     }
        // }
        // dd($data);
        return $job;
    }


}