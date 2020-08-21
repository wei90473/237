<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Services\Term_processService;
use App\Services\User_groupService;
use App\Models\Term_process;
use App\Models\Class_process;
use App\Models\Class_process_job;
use App\Models\T04tb;
use Illuminate\Support\Facades\Mail;


class Term_processController extends Controller
{
    public function __construct(Term_processService $term_processService, User_groupService $user_groupService)
    {
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('term_process', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }
    public function index(Request $request)
    {
        $data=[];
        $this_yesr = date('Y') - 1911;

        if(null == $request->get('yerly')){
            $queryData['yerly'] = $this_yesr;
        }else{
            $queryData['yerly'] = $request->get('yerly');
        }

        $queryData['process_complete'] = $request->get('process_complete');
        $queryData['job_complete'] = $request->get('job_complete');

        $queryData['search'] = $request->get('search');
        // 測試用 NIMA
        // $queryData['sponsor'] = 'NIMA';
        $queryData['sponsor'] = auth()->user()->userid;

        if($queryData['search'] != 'search' ){
          $queryData2['yerly'] = 'none';
          $data=$this->term_processService->getList($queryData2);
        }else{

          $data=$this->term_processService->getList($queryData);
        }

        return view('admin/term_process/list',compact('data','queryData'));

    }

    public function completeChange(Request $request)
    {
    	$data = $request->all();

    	$Term_process_data = Term_process::select('id')->where('class_process_id', $data['class_process_id'])->where('class_process_job_id', $data['class_process_job_id'])->where('class', $data['class'])->where('term', $data['term'])->get()->toArray();

        if(empty($Term_process_data)){
        	$fields = array(
        		'class_process_id' => $data['class_process_id'],
        		'class_process_job_id' => $data['class_process_job_id'],
        		'class' => $data['class'],
        		'term' => $data['term'],
        		'complete' => 'Y',
        	);
        	Term_process::create($fields);
        }else{
        	Term_process::find($Term_process_data[0]['id'])->delete();
        }

        $process_job_data = Class_process_job::select('id', 'class_process_id')->where('class_process_id', $data['class_process_id'])->get()->toArray();
        $job_complete = '0';
        if(!empty($process_job_data)){
        	foreach($process_job_data as $row){
        		$Term_process_data = Term_process::select('id', 'class_process_id', 'class_process_job_id', 'class', 'term', 'complete')->where('class_process_id', $row['class_process_id'])->where('class_process_job_id', $row['id'])->where('class', $data['class'])->where('term', $data['term'])->get()->toArray();
        		if(!empty($Term_process_data)){
        			if($Term_process_data[0]['complete'] == 'Y'){
        				$job_complete++;
        			}
        		}
        	}
        	if($job_complete == count($process_job_data)){
        		$fields = array(
        			'process_complete' => 'Y',
        		);
        	}else{
        		$fields = array(
        			'process_complete' => 'N',
        		);
        	}
        	T04tb::where('class', $data['class'])->where('term', $data['term'])->update($fields);
        }
    	   // echo '<pre style="text-align:left;">' . "\n";
	       // print_r($data);
	       // echo "\n</pre>\n";
	       // die();
        $data_return = '工作完成狀態修改成功';
    	return $data_return;
    }

    public function getMail()
    {
        $mail_data = $this->term_processService->getMail();
        foreach($mail_data as $mail_row){
            $data = array(
                'title' => $mail_row['title'],
                'content' => $mail_row['content'],
            );
            $mail = $mail_row['mail_to'];
            Mail::send("email/send", $data, function ($message) use ($mail,$data){
                $message->from('fet@hrd.gov.tw', 'CSDI自動寄信通知');
                $message->subject($data['title']);
                $message->to($mail);
            });
        }

        // dd($data);
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
            array('lv'=>'2','name'=>'班期調派訓異常統計表','job'=>''),
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
            array('lv'=>'2','name'=>'講師基本資料表','job'=>''),
            array('lv'=>'2','name'=>'講座聘函','job'=>''),
            array('lv'=>'2','name'=>'講師簽名單','job'=>''),
            array('lv'=>'1','name'=>'學員資料','job'=>''),
            array('lv'=>'2','name'=>'學員報名處理','job'=>''),
            array('lv'=>'3','name'=>'審核換員、補報及取消報名','job'=>'modify_manage'),
            array('lv'=>'3','name'=>'匯入名冊、維護報名資料、公告學員名冊','job'=>'student_apply'),
            array('lv'=>'3','name'=>'編組別','job'=>'arrange_group'),
            array('lv'=>'3','name'=>'序學號','job'=>'arrange_stno'),
            array('lv'=>'2','name'=>'學員請假處理','job'=>''),
            array('lv'=>'3','name'=>'維護請假資料','job'=>'leave'),
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
            array('lv'=>'1','name'=>'場地管理','job'=>''),
            array('lv'=>'2','name'=>'辦班需求(確認)處理','job'=>''),
            array('lv'=>'1','name'=>'例行業務','job'=>''),
            array('lv'=>'2','name'=>'教材交印資料處理','job'=>''),
            array('lv'=>'2','name'=>'教材印製統計處理','job'=>''),
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