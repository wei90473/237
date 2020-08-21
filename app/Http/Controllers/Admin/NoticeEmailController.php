<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\NoticeEmailService;
use App\Services\User_groupService;
use App\Services\TrainQuestSettingService;
use Illuminate\Support\Facades\Mail;
use DB;
use App\Models\Class_mail;


class NoticeEmailController extends Controller
{
    /**
     * NoticeEmailController constructor.
     * @param NoticeEmailService $noticeEmailService
     */
    public function __construct(NoticeEmailService $noticeEmailService, User_groupService $user_groupService, TrainQuestSettingService $trainQuestSettingService)
    {
        $this->noticeEmailService = $noticeEmailService;
        $this->user_groupService = $user_groupService;
        $this->trainQuestSettingService = $trainQuestSettingService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('notice_emai', $user_group_auth)){
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
        // 課程名稱
        $queryData['keyword'] = $request->get('keyword');
        // 期別
        $queryData['term'] = $request->get('term');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;

        $sponsor = $this->noticeEmailService->getSponsor();
        //年
        $this_yesr = date('Y') - 1911;
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData['year_list']);
        // echo "\n</pre>\n";
        if(null == $request->get('yerly')){
            $queryData['yerly'] = $this_yesr;
        }else{
            $queryData['yerly'] = $request->get('yerly');
        }
        //班號
        $queryData['class'] = $request->get('class');
        $queryData['name'] = $request->get('name');
        // 分班名稱**
        $queryData['class_branch_name'] = $request->get('class_branch_name');
        // 期別
        $queryData['term'] = $request->get('term');
        $queryData['type'] = $request->get('type');
        $queryData['traintype'] = $request->get('traintype');
        $queryData['sitebranch'] = $request->get('sitebranch');
        $queryData['categoryone'] = $request->get('categoryone');
        $queryData['branch'] = $request->get('branch');
        // 班別類型
        $queryData['process'] = $request->get('process');
        // $queryData['commission'] = $request->get('commission');
        $queryData['sponsor'] = $request->get('sponsor');
        // 委訓單位
        $queryData['class_entrusted'] = $request->get('class_entrusted');
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');
        $queryData['sdate2'] = $request->get('sdate2');
        $queryData['edate2'] = $request->get('edate2');
        //
        $queryData['search'] = $request->get('search');

		if($queryData['search'] != 'search')
		{
            $sess = $request->session()->get('lock_class');
            if($sess){
              $queryData2['class'] = $sess['class'];
              $queryData2['term'] = $sess['term'];
              $queryData2['yerly'] = substr($sess['class'], 0, 3);
              $data = $this->noticeEmailService->getNoticeEmailList($queryData2);
              return view('admin/notice_email/list', compact('data', 'queryData', 'sponsor'));
            }
			$queryData2['class'] = 'none';
			$data = $this->noticeEmailService->getNoticeEmailList($queryData2);
		}
		else{
            if(!empty($queryData['term'])){
                $queryData['term'] = str_pad($queryData['term'],2,'0',STR_PAD_LEFT);
              }
            $data = $this->noticeEmailService->getNoticeEmailList($queryData);
        }



      //  $classList = T01tb::select('class', 'name')->get();
        return view('admin/notice_email/list', compact('data', 'queryData', 'sponsor'));
    }

    public function detail(Request $request)
    {

        $queryData['class'] = $request->get('class');
        $queryData['term'] = $request->get('term');
        $class_mail_data = array();
        $class_mail_data['title'] = '問卷填答通知';
        $class_mail_data['content'] = "";

        $class_data = $this->noticeEmailService->getClass($queryData);
        $mail_data = $this->noticeEmailService->getMailData($queryData);
        $Quest_data = $this->trainQuestSettingService->getTrainQuestSettings($queryData['class'], $queryData['term']);
        if(!empty($Quest_data)){
            foreach($Quest_data as $Quest_row){
                $class_mail_data['content'] .= config('app.train_quest_type')[$Quest_row->type].'填答網址：<a href="'.$Quest_row->url.'" target="_blank" >'.$Quest_row->url.'</a><br>';
            }
        }

        if(!empty($mail_data)){
            if(!empty($mail_data['title'])){
                $class_mail_data['title'] = $mail_data['title'];
            }
            // if(!empty($mail_data['content'])){
            //     $class_mail_data['content'] = $mail_data['content'];
            // }
            if(!empty($mail_data['date'])){
                $class_mail_data['date'] = (date("Y",strtotime($mail_data['date']))-1911).date("/m/d H:i",strtotime($mail_data['date']));
            }
        }

        return view('admin/notice_email/detail', compact('data', 'class_data', 'class_mail_data'));
    }

    public function list(Request $request, $id)
    {
        $class_data = explode("_",$id);
        $queryData['class'] = $class_data[0];
        $queryData['term'] = $class_data[1];
        $data = $this->noticeEmailService->getStudentMail($queryData);

        // dd($data);

        return view('admin/notice_email/select_mail', compact('data', 'class_data', 'queryData'));
    }

    public function save_list(Request $request)
    {
        //先 只要有上次寄送日期 不要cc
        $data = $request->all();

        if(isset($data['checkbox']) && !empty($data['checkbox'])){
            foreach($data['checkbox'] as $key => $row){
                if(empty($row)){
                    unset($data['checkbox'][$key]);
                }
            }
            $data['checkbox'] = implode(",",$data['checkbox']);
        }else{
            $data['checkbox'] = '';
        }


        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();

        $queryData['class'] = $data['class'];
        $queryData['term'] = $data['term'];
        $mail_data = $this->noticeEmailService->getMailData($queryData);
        if(!empty($mail_data)){
            $fields = array(
                'mail_list' => $data['checkbox'],
            );
            //更新
            Class_mail::where('class', $queryData['class'])->where('term', $queryData['term'])->update($fields);
        }else{
            $fields = array(
                'class' => $data['class'],
                'term' => $data['term'],
                'mail_list' => $data['checkbox'],
            );
            $result = Class_mail::create($fields);
        }

        return redirect('/admin/notice_emai/detail?class='.$queryData['class'].'&term='.$queryData['term'])->with('result', '1')->with('message', '收件者選擇成功!');
    }

    public function save_mail(Request $request)
    {
        //先 只要有上次寄送日期 不要cc
        $data = $request->all();

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();
        $queryData['class'] = $data['class'];
        $queryData['term'] = $data['term'];
        $mail_data = $this->noticeEmailService->getMailData($queryData);
        if(!empty($mail_data)){
            $fields = array(
                'title' => $data['title'],
                'content' => $data['content'],
                'date' => date('Y-m-d H:i:s'),
            );
            //更新
            Class_mail::where('class', $queryData['class'])->where('term', $queryData['term'])->update($fields);
        }else{
            $fields = array(
                'class' => $data['class'],
                'term' => $data['term'],
                'title' => $data['title'],
                'content' => $data['content'],
                'date' => date('Y-m-d H:i:s'),
            );
            $result = Class_mail::create($fields);
        }
        // dd($mail_data);

        if(!empty($mail_data['mail_list'])){
            $mail = explode(",",$mail_data['mail_list']);
            $mail = array('peter19841115@hotmail.com', 'clairec4305@gmail.com');

            Mail::send("admin/notice_email/send", $data, function ($message) use ($mail,$data){
                $message->from('fet@hrd.gov.tw', 'CSDI自動寄信通知');
                $message->subject($data['title']);
                $message->to($mail);
            });

            return back()->with('result', '1')->with('message', '寄送成功!');
        }else{
            return back()->with('result', '0')->with('message', '尚未選擇收件者!');
        }


    }

    public function mail_to_me(Request $request)
    {
        //先 只要有上次寄送日期 不要cc
        $data = $request->all();

        // $mail = array('peter19841115@hotmail.com');
        $mail = auth()->user()->email;
        // dd($mail);
        // die();
        // if($mail == ''){
            $mail = array('hws0106@gmail.com', 'clairec4305@gmail.com');

        // }


        Mail::send("admin/notice_email/send", $data, function ($message) use ($mail,$data){
            $message->from('fet@hrd.gov.tw', 'CSDI自動寄信通知');
            $message->subject($data['title']);
            $message->to($mail);
        });

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();
        $queryData['class'] = $data['class'];
        $queryData['term'] = $data['term'];
        $mail_data = $this->noticeEmailService->getMailData($queryData);
        if(!empty($mail_data)){
            $fields = array(
                'title' => $data['title'],
                'content' => $data['content'],
            );
            //更新
            Class_mail::where('class', $queryData['class'])->where('term', $queryData['term'])->update($fields);
        }else{
            $fields = array(
                'class' => $data['class'],
                'term' => $data['term'],
                'title' => $data['title'],
                'content' => $data['content'],
            );
            $result = Class_mail::create($fields);
        }

        return back()->with('result', '1')->with('message', '寄送成功!');
    }


}
