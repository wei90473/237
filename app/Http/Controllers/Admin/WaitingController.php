<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\WaitingService;
use App\Services\Term_processService;
use App\Services\User_groupService;
use App\Models\T08tb;
use App\Models\T09tb;
use App\Models\T01tb;
use App\Models\M01tb;
use App\Models\T04tb;
use DB ;

class WaitingController extends Controller
{
    /**
     * WaitingController constructor.
     * @param WaitingService $waitingService
     */
    public function __construct(WaitingService $waitingService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        $this->waitingService = $waitingService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('waiting', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('waiting');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $sponsor = $this->waitingService->getSponsor();
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
        $queryData['branchname'] = $request->get('branchname');
        // 期別
        $queryData['term'] = $request->get('term');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 班別類型
        $queryData['process'] = $request->get('process');

        $queryData['sponsor'] = $request->get('sponsor');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班別性質
        $queryData['type'] = $request->get('type');
        $queryData['sitebranch'] = $request->get('sitebranch');
        $queryData['categoryone'] = $request->get('categoryone');
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');
        $queryData['sdate2'] = $request->get('sdate2');
        $queryData['edate2'] = $request->get('edate2');
        $queryData['sdate3'] = $request->get('sdate3');
        $queryData['edate3'] = $request->get('edate3');
        // 遴聘與否
        $queryData['hire'] = $request->get('hire');
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

        if($queryData['search'] != 'search' ){
          $sess = $request->session()->get('lock_class');
          if($sess){
              $queryData2['class'] = $sess['class'];
              $queryData2['term'] = $sess['term'];
              $queryData2['yerly'] = substr($sess['class'], 0, 3);
              $data = $this->waitingService->getWaitingList($queryData2);
              return view('admin/waiting/list', compact('data', 'queryData', 'sponsor'));
          }
          $queryData2['class'] = 'none';
          $data = $this->waitingService->getWaitingList($queryData2);
        }else{
          if(!empty($queryData['term'])){
            $queryData['term'] = str_pad($queryData['term'],2,'0',STR_PAD_LEFT);
          }
          // dd($queryData['term']);
          $data = $this->waitingService->getWaitingList($queryData);
        }

        return view('admin/waiting/list', compact('data', 'queryData', 'sponsor'));
    }

    public function detail(Request $request)
    {
      if(null == $request->get('hire')){
        $queryData['hire'] = '';
      }
      $queryData['class'] = $request->get('class');
        $queryData['term'] = $request->get('term');
        $queryData['hire'] = $request->get('hire');

        $class_data = $this->waitingService->getClass($queryData);
        $class_data['hire'] = $queryData['hire'];

        $teacher_list = $this->waitingService->getDetailList($queryData);

        return view('admin/waiting/detail', compact('data', 'class_data', 'teacher_list'));
    }
/**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function SMS(Request $request)
    {
    // 班別
    if(null == $request->get('class'))
      $queryData['class'] = '';
      else
      $queryData['class'] = $request->get('class');
        // 期別
        $queryData['term'] = $request->get('term');

    $result = "SET NOCOUNT ON "
        ." DECLARE @class  AS varchar(6) /* 班號 */"
      ." DECLARE @term   AS varchar(2) /* 期別 */"
      ." DECLARE @vbCrLf AS varchar(2) "
      ." SET @vbCrLf = CHAR(13)+CHAR(10) "
      ." SET @class ='".$queryData['class']."'"
      ." SET @term = ='".$queryData['term']."'"
      ." "
      ." SELECT "
      ." [message] = '班　　別：'+A.class+' '+RTRIM(A.name)+"
      ."            '期　　別：'+B.term+@vbCrLf+"
      ."            '簡訊通知：'+(CASE B.snsmk WHEN 'Y' THEN '是' ELSE '否' END)+"
      ."            '是否簡訊通知?'"
      ." FROM dbo.t01tb A "
      ." INNER JOIN dbo.t04tb B "
      ." ON A.class = B.class"
    ." WHERE B.class = @class"
      ." AND B.term = @term" ;
      $data =DB::select($result)->first();

        return $data->message ;
    }

   public function SendSMS(Request $request)
    {
    // 班別
    if(null == $request->get('class'))
      $queryData['class'] = '';
      else
      $queryData['class'] = $request->get('class');
        // 期別
        $queryData['term'] = $request->get('term');


    $result = "班　　別：            \n期　　別：\n簡訊通知：";
        return $result;
    }

    public function GetTerm(Request $request)
    {
    // 班別
    if(null == $request->get('class'))
      $class = '';
      else
            $class = $request->get('class');

        $data = DB::select('SELECT DISTINCT term FROM t04tb WHERE class = \''.$class.'\' ORDER BY term');

        return $data;
    }

    public function changepay(Request $request, $id)
    {
      $EditDelete = array(
          'EditorDelete' => 'N',
          'paidday' => '',
      );
      $EditDelete = $this->waitingService->getEditDelete($id);

      $queryData['class'] = $request->get('class');
      $queryData['term'] = $request->get('term');

      //班務流程凍結
        $freeze = $this->term_processService->getFreeze('waiting', $queryData['class'], $queryData['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

      if($EditDelete['EditorDelete'] == 'Y'){

        $this->waitingService->pay($id);

        return redirect("/admin/waiting/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '1')->with('message', '修改成功!');
      }else{
        return back()->with('result', '0')->with('html_message', "已作過轉帳{$EditDelete['paidday']}，不可修改");
      }

    }

    public function changenotpay(Request $request, $id)
    {
      $EditDelete = array(
          'EditorDelete' => 'N',
          'paidday' => '',
      );
      $EditDelete = $this->waitingService->getEditDelete($id);

      $queryData['class'] = $request->get('class');
      $queryData['term'] = $request->get('term');

      //班務流程凍結
        $freeze = $this->term_processService->getFreeze('waiting', $queryData['class'], $queryData['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

      if($EditDelete['EditorDelete'] == 'Y'){

        $this->waitingService->notpay($id);

        return redirect("/admin/waiting/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '1')->with('message', '修改成功!');
      }else{
        return back()->with('result', '0')->with('html_message', "已作過轉帳{$EditDelete['paidday']}，不可修改");
      }

    }

    public function changehire(Request $request, $id)
    {
      $EditDelete = array(
          'EditorDelete' => 'N',
          'paidday' => '',
      );
      $EditDelete = $this->waitingService->getEditDelete($id);
      $queryData['class'] = $request->get('class');
        $queryData['term'] = $request->get('term');

      //班務流程凍結
        $freeze = $this->term_processService->getFreeze('waiting', $queryData['class'], $queryData['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

      if ($id) {

        $teacher_data = $this->waitingService->getById($id);

        $teacher_hour = $this->waitingService->getHourById($id);

        $class_profchk = $this->waitingService->getProfchk($queryData['class']);

        if($teacher_data['hire'] == 'N'){

          if($class_profchk['profchk'] == 'Y'){
            if($teacher_hour > 90){
              return redirect("/admin/waiting/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '0')->with('message', '無法遴聘，該講座年度授課時數超過90小時');
            }else{
                $data =array(
                  'hire' => 'Y'
                );

                T08tb::where('id', $id)->update($data);
                //遴聘 更新資料到講座資料
                if($teacher_data['idkind'] != '1'){
                  $fields = array(
                    'idkind' => $teacher_data['idkind'],
                    'cname' => $teacher_data['cname'],
                    'ename' => $teacher_data['ename'],
                    'dept' => $teacher_data['dept'],
                    'position' => $teacher_data['position'],
                    'liaison' => $teacher_data['liaison'],
                    'mobiltel' => $teacher_data['mobiltel'],
                    'email' => $teacher_data['email'],
                    'offtela1' => $teacher_data['offtela1'],
                    'offtelb1' => $teacher_data['offtelb1'],
                    'offtelc1' => $teacher_data['offtelc1'],
                    'offtela2' => $teacher_data['offtela2'],
                    'offtelb2' => $teacher_data['offtelb2'],
                    'offtelc2' => $teacher_data['offtelc2'],
                    'homtela' => $teacher_data['homtela'],
                    'homtelb' => $teacher_data['homtelb'],
                    'offfaxa' => $teacher_data['offfaxa'],
                    'offfaxb' => $teacher_data['offfaxb'],
                    'homfaxa' => $teacher_data['homfaxa'],
                    'homfaxb' => $teacher_data['homfaxb'],
                    'datadate' => str_pad(date('Y')-1911 ,3,'0',STR_PAD_LEFT).str_pad(date('m') ,2,'0',STR_PAD_LEFT).str_pad(date('d') ,2,'0',STR_PAD_LEFT),
                    'update_date' => date('Y-m-d'),
                  );

                  $name_len = mb_strlen($fields['cname']);
                  if($name_len > 3){
                      $fields['lname'] = mb_substr($fields['cname'] , 0, 2);
                      $fields['fname'] = mb_substr($fields['cname'] , 2, $name_len);
                  }else if($name_len == 3){
                      $fields['lname'] = mb_substr($fields['cname'] , 0, 1);
                      $fields['fname'] = mb_substr($fields['cname'] , 1, 2);
                  }else if($name_len == 2){
                      $fields['lname'] = mb_substr($fields['cname'] , 0, 1);
                      $fields['fname'] = mb_substr($fields['cname'] , 1, 1);
                  }

                  $M01 = M01tb::select('m01tb.idno')->where('idno', $teacher_data['idno'])->get()->toArray();
                  if(empty($M01[0])){
                    $fields['kimd'] = '1';
                    $fields['idno'] = $teacher_data['idno'];
                    M01tb::create($fields);

                    $sql = DB::getQueryLog();
                    if(checkNeedModifyLog('waiting')){
                        $nowdata = M01tb::where('idno', $teacher_data['idno'])->get()->toarray();
                        createModifyLog('I','M01tb','',$nowdata,end($sql));
                    }

                  }else{

                    if(checkNeedModifyLog('waiting')){
                        $olddata = M01tb::where('idno', $teacher_data['idno'])->get()->toarray();
                    }

                    M01tb::where('idno', $teacher_data['idno'])->update($fields);

                    $sql = DB::getQueryLog();
                    if(checkNeedModifyLog('waiting')){
                        $nowdata = M01tb::where('idno', $teacher_data['idno'])->get()->toarray();
                        createModifyLog('U','M01tb',$olddata,$nowdata,end($sql));
                    }

                  }

                  if($teacher_data['idkind'] != '1'){
                    $this->waitingService->WhenMark($id);
                  }

                }


                if($teacher_hour > 70){
                  return redirect("/admin/waiting/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '1')->with('message', '遴聘成功!該講座年度授課時數超過70小時');
                }else{
                  return redirect("/admin/waiting/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '1')->with('message', '遴聘成功!');
                }
            }
          }else{

            $data =array(
              'hire' => 'Y'
            );

            T08tb::where('id', $id)->update($data);
            //遴聘 更新資料到講座資料
            if($teacher_data['idkind'] != '1'){
              $fields = array(
                'idkind' => $teacher_data['idkind'],
                'cname' => $teacher_data['cname'],
                'ename' => $teacher_data['ename'],
                'dept' => $teacher_data['dept'],
                'position' => $teacher_data['position'],
                'liaison' => $teacher_data['liaison'],
                'mobiltel' => $teacher_data['mobiltel'],
                'email' => $teacher_data['email'],
                'offtela1' => $teacher_data['offtela1'],
                'offtelb1' => $teacher_data['offtelb1'],
                'offtelc1' => $teacher_data['offtelc1'],
                'offtela2' => $teacher_data['offtela2'],
                'offtelb2' => $teacher_data['offtelb2'],
                'offtelc2' => $teacher_data['offtelc2'],
                'homtela' => $teacher_data['homtela'],
                'homtelb' => $teacher_data['homtelb'],
                'offfaxa' => $teacher_data['offfaxa'],
                'offfaxb' => $teacher_data['offfaxb'],
                'homfaxa' => $teacher_data['homfaxa'],
                'homfaxb' => $teacher_data['homfaxb'],
                'datadate' => str_pad(date('Y')-1911 ,3,'0',STR_PAD_LEFT).str_pad(date('m') ,2,'0',STR_PAD_LEFT).str_pad(date('d') ,2,'0',STR_PAD_LEFT),
                'update_date' => date('Y-m-d'),
              );

              $name_len = mb_strlen($fields['cname']);
              if($name_len > 3){
                  $fields['lname'] = mb_substr($fields['cname'] , 0, 2);
                  $fields['fname'] = mb_substr($fields['cname'] , 2, $name_len);
              }else if($name_len == 3){
                  $fields['lname'] = mb_substr($fields['cname'] , 0, 1);
                  $fields['fname'] = mb_substr($fields['cname'] , 1, 2);
              }else if($name_len == 2){
                  $fields['lname'] = mb_substr($fields['cname'] , 0, 1);
                  $fields['fname'] = mb_substr($fields['cname'] , 1, 1);
              }

              $M01 = M01tb::select('m01tb.idno')->where('idno', $teacher_data['idno'])->get()->toArray();
              if(empty($M01[0])){
                $fields['kimd'] = '1';
                $fields['idno'] = $teacher_data['idno'];

                M01tb::create($fields);

                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('waiting')){
                    $nowdata = M01tb::where('idno', $teacher_data['idno'])->get()->toarray();
                    createModifyLog('I','M01tb','',$nowdata,end($sql));
                }

              }else{

                if(checkNeedModifyLog('waiting')){
                    $olddata = M01tb::where('idno', $teacher_data['idno'])->get()->toarray();
                }

                M01tb::where('idno', $teacher_data['idno'])->update($fields);
                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('waiting')){
                    $nowdata = M01tb::where('idno', $teacher_data['idno'])->get()->toarray();
                    createModifyLog('U','M01tb',$olddata,$nowdata,end($sql));
                }

              }

              if($teacher_data['idkind'] != '1'){
                $this->waitingService->WhenMark($id);
              }

            }
            return redirect("/admin/waiting/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '1')->with('message', '遴聘成功!');

          }

        }else{

          if($EditDelete['EditorDelete'] == 'Y'){

            $this->waitingService->MarkDelete($id);

            $data =array(
              'hire' => 'N'
            );

            if(checkNeedModifyLog('waiting')){
                $olddata = T08tb::where('id', $id)->get()->toarray();
            }

            T08tb::where('id', $id)->update($data);
            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('waiting')){
                $nowdata = T08tb::where('id', $id)->get()->toarray();
                createModifyLog('U','T08tb',$olddata,$nowdata,end($sql));
            }

            return redirect("/admin/waiting/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '1')->with('message', '修改成功!');
          }else{
            return back()->with('result', '0')->with('html_message', "已作過轉帳{$EditDelete['paidday']}，資料不可修改或刪除!");
          }


        }

      } else {

          return redirect("/admin/waiting/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '0')->with('message', '修改失敗!');
      }
    }

    public function GetCourse(Request $request)
    {
    // 班別
    if(null == $request->get('class'))
      $class = '';
      else
            $class = $request->get('class');

        // 期別
    if(null == $request->get('term'))
            $term = '';
        else
            $term = $request->get('term');

        if($class == '' || $term == '')
            return [];

        $data = DB::select('SELECT DISTINCT name, course FROM t06tb WHERE class = \''.$class.'\' and term = \''.$term.'\' ORDER BY course');

        return $data;
    }

   public function Mark(Request $request)
    {
        $where = '1=1 ';

    // 班別(條件)
    if(null == $request->get('class') || $request->get('class') == ''){
            $queryData['class'] = '';
            return "請選擇班別";
        }
      else{
            $queryData['class'] = $request->get('class');
            $where .= "and class='".$queryData['class']."' ";
        }


        // 期別(條件)
        if(null == $request->get('term') || $request->get('term') == '') {
            $queryData['term'] = '';
        }
      else {
            $queryData['term'] = $request->get('term');
            $where .= "and term='".$queryData['term']."' ";
        }

        // 遴聘與否(條件)
        if(null == $request->get('hire') || $request->get('hire') == '') {
            $queryData['hire'] = '';
        }
        else {
            $queryData['hire'] = $request->get('hire');
            $where .= "and hire='".$queryData['hire']."' ";
        }

        // 遴聘與否(設定值)(0:有料、1:無料)
        $mark = '';
        if($request->get('mark') == 0) {
            $mark = 'Y';
        }
        else if($request->get('mark') == 1) {
            $mark = 'N';
        }

        $data = DB::select("UPDATE t08tb SET hire='".$mark."' WHERE ".$where);

        return "ok!!";
    }
    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {

        $queryData['class'] = $request->get('class');
        $queryData['term'] = $request->get('term');

        $class_data = $this->waitingService->getClass($queryData);
        $class_data['course'] = DB::select("SELECT DISTINCT name, course FROM t06tb WHERE class = '{$queryData['class']}' and term = '{$queryData['term']}' ORDER BY course");

        return view('admin/waiting/form', compact('class_data'));
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 取得POST資料
        $data = $request->all();

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('waiting', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        $data['hire'] = 'N';
        if($data['idno'] == '') {
            $idno = $data['class'].$data['term'];
            $query = T08tb::select(DB::raw('MAX(idno) as idno'));
            $m08_data = $query->where('t08tb.idno', 'LIKE', $idno.'%')->get()->toArray();

            if(!empty($m08_data['0']['idno'])){
              $idno = $m08_data['0']['idno']+1;
            }else{
            	$idno = $idno."01";
            }

            $data['idno'] = $idno;
        }else{

          $query = M01tb::select('idno');
          $m01_data = $query->where('idno', $data['idno'])->where('cname', $data['cname'])->get()->toArray();
          $m01_data = $m01_data[0];

          if(!empty($m01_data)){
            // return back()->with('result', '0')->with('html_message', "身分證重複");
          }

        }

        $name_len = mb_strlen($data['cname']);
        if($name_len > 3){
            $data['lname'] = mb_substr($data['cname'] , 0, 2);
            $data['fname'] = mb_substr($data['cname'] , 2, $name_len);
        }else if($name_len == 3){
            $data['lname'] = mb_substr($data['cname'] , 0, 1);
            $data['fname'] = mb_substr($data['cname'] , 1, 2);
        }else if($name_len == 2){
            $data['lname'] = mb_substr($data['cname'] , 0, 1);
            $data['fname'] = mb_substr($data['cname'] , 1, 1);
        }

        //新增
        $result = T08tb::create($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('waiting')){
            $nowdata = T08tb::where('idno', $data['idno'])->get()->toarray();
            createModifyLog('I','T08tb','',$nowdata,end($sql));
        }

        return redirect('/admin/waiting/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $id
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * 編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = T08tb::find($id);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        $queryData['class'] = $data->class;
        $queryData['term'] = $data->term;

        $class_data = $this->waitingService->getClass($queryData);
        $class_data['course'] = DB::select("SELECT DISTINCT name, course FROM t06tb WHERE class = '{$queryData['class']}' and term = '{$queryData['term']}' ORDER BY course");

        $teacher_hour = $this->waitingService->getHourById($id);

        return view('admin/waiting/form', compact('data', 'class_data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $EditDelete = array(
            'EditorDelete' => 'N',
            'paidday' => '',
        );
        $EditDelete = $this->waitingService->getEditDelete($id);
        // 取得POST資料
        $data = $request->all();

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('waiting', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法更新');
        }
        // dd($data);

        unset($data['_method'], $data['_token']);

        $teacher_data = T08tb::find($id);

        if($EditDelete['EditorDelete'] == 'Y'){

          if(checkNeedModifyLog('waiting')){
              $olddata = T08tb::where('id', $id)->get()->toarray();
          }

          //更新
          T08tb::where('id', $id)->update($data);

          $sql = DB::getQueryLog();
          if(checkNeedModifyLog('waiting')){
              $nowdata = T08tb::where('id', $id)->get()->toarray();
              createModifyLog('U','T08tb',$olddata,$nowdata,end($sql));
          }

          if($teacher_data->hire == 'Y' && $data['idkind'] != '1'){
            $fields = array(
              'idkind' => $data['idkind'],
              'cname' => $data['cname'],
              'ename' => $data['ename'],
              'dept' => $data['dept'],
              'position' => $data['position'],
              'liaison' => $data['liaison'],
              'mobiltel' => $data['mobiltel'],
              'email' => $data['email'],
              'offtela1' => $data['offtela1'],
              'offtelb1' => $data['offtelb1'],
              'offtelc1' => $data['offtelc1'],
              'offtela2' => $data['offtela2'],
              'offtelb2' => $data['offtelb2'],
              'offtelc2' => $data['offtelc2'],
              'homtela' => $data['homtela'],
              'homtelb' => $data['homtelb'],
              'offfaxa' => $data['offfaxa'],
              'offfaxb' => $data['offfaxb'],
              'homfaxa' => $data['homfaxa'],
              'homfaxb' => $data['homfaxb'],
              'datadate' => str_pad(date('Y')-1911 ,3,'0',STR_PAD_LEFT).str_pad(date('m') ,2,'0',STR_PAD_LEFT).str_pad(date('d') ,2,'0',STR_PAD_LEFT),
              'update_date' => date('Y-m-d'),
            );

            $name_len = mb_strlen($fields['cname']);
            if($name_len > 3){
                $fields['lname'] = mb_substr($fields['cname'] , 0, 2);
                $fields['fname'] = mb_substr($fields['cname'] , 2, $name_len);
            }else if($name_len == 3){
                $fields['lname'] = mb_substr($fields['cname'] , 0, 1);
                $fields['fname'] = mb_substr($fields['cname'] , 1, 2);
            }else if($name_len == 2){
                $fields['lname'] = mb_substr($fields['cname'] , 0, 1);
                $fields['fname'] = mb_substr($fields['cname'] , 1, 1);
            }

            $M01 = M01tb::select('m01tb.idno')->where('idno', $teacher_data['idno'])->get()->toArray();
            if(empty($M01[0])){
              M01tb::create($fields);

              $sql = DB::getQueryLog();
              if(checkNeedModifyLog('waiting')){
                  $nowdata = M01tb::where('idno', $teacher_data['idno'])->get()->toarray();
                  createModifyLog('I','M01tb','',$nowdata,end($sql));
              }

            }else{

              if(checkNeedModifyLog('waiting')){
                  $olddata = M01tb::where('idno', $teacher_data['idno'])->get()->toarray();
              }

              M01tb::where('idno', $teacher_data['idno'])->update($fields);

              $sql = DB::getQueryLog();
              if(checkNeedModifyLog('waiting')){
                  $nowdata = M01tb::where('idno', $teacher_data['idno'])->get()->toarray();
                  createModifyLog('U','M01tb',$olddata,$nowdata,end($sql));
              }

            }
          }

          return back()->with('result', '1')->with('message', '儲存成功!');
        }else{
          return back()->with('result', '0')->with('message', "已作過轉帳{$EditDelete['paidday']}，資料不可修改或刪除!");
        }

    }

    /**
     * 刪除處理
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($id) {

        	$data = T08tb::find($id);
	        $queryData['class'] = $data->class;
	        $queryData['term'] = $data->term;

          //班務流程凍結
          $freeze = $this->term_processService->getFreeze('waiting', $queryData['class'], $queryData['term']);
          if($freeze == 'Y'){
              return back()->with('result', 0)->with('message', '凍結中無法刪除');
          }
          //刪除[T08tb擬聘講座]、[t09tb講座聘任]、[t10tb 班別教材資料檔]、[t54tb 成效問卷題目檔二]、[t56tb 成效問卷題目檔二]、[t98tb 講座教學教法資料檔]的資料
          //已作過轉帳 已有轉帳日期(t09tb.paidday <> '')，則不可以修改或刪除
          // return back()->with('result', '1')->with('message', '刪除成功!');
          $EditDelete = $this->waitingService->getEditDelete($id);

          if($EditDelete['EditorDelete'] == 'Y'){
            $T09 = T09tb::select('t09tb.id')->where('class', $data->class)->where('term', $data->term)->where('course', $data->course)->where('idno', $data->idno)->get()->toArray();
            if(!empty($T09[0]['id'])){

              if(checkNeedModifyLog('waiting')){
                  $olddata = T09tb::where('id', $T09[0]['id'])->get()->toarray();
              }

              T09tb::find($T09[0]['id'])->delete();

              $sql = DB::getQueryLog();
              if(checkNeedModifyLog('waiting')){
                  createModifyLog('D','T09tb',$olddata,'',end($sql));
              }

            }

            if(checkNeedModifyLog('waiting')){
                $olddata = T08tb::where('id', $id)->get()->toarray();
            }

            T08tb::find($id)->delete();

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('waiting')){
                createModifyLog('D','T08tb',$olddata,'',end($sql));
            }

            return redirect("/admin/waiting/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '1')->with('message', '刪除成功!');
          }else{
            return back()->with('result', '0')->with('message', "已作過轉帳{$EditDelete['paidday']}，資料不可修改或刪除!");
          }

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    /**
     * 取得姓名
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function getIdno(Request $request)
    {
        $keyword = $request->get('search');

        $query = M01tb::select('idno', 'cname', 'ename', 'education', 'dept');

        $query->where(function ($query) use ($keyword) {
            $query->where('cname', 'like', '%'.$keyword.'%')
                ->orwhere('ename', 'like', '%'.$keyword.'%');
        });

        $data = $query->get(10);

        $result = array();

        foreach ($data as $va) {

            $newData = array();
            $newData['id'] = $va->idno;
            $newData['text'] = $va->cname . $va->ename;
            $newData['text'] .= ($va->education)? '('.$va->education.')' : '';
            $newData['text'] .= ' '. $va->dept;

            $result[] = $newData;
        }

       return response()->json($result);
    }

    function getTeacher(Request $request)
    {
        $idno = (string)$request->input('idno');

        $data = M01tb::where('idno', $idno)->first()->toArray();

        // dd($data);

        $result = $data;

        return response()->json($data);
    }

}
