<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassScheduleService;
use App\Services\MethodService;
use App\Services\User_groupService;
use App\Services\ScheduleService;
use App\Repositories\T09tbRepository;
use App\Models\S02tb;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T06tb;
use App\Models\T08tb;
use App\Models\T09tb;
use App\Models\T22tb;
use App\Models\T36tb;
use App\Models\T97tb;
use App\Models\M14tb;
use App\Helpers\ModifyLog;
use App\Models\Edu_classroom;
use App\Models\Dbteachingmaterial;
use DB;
use App\Services\Term_processService;


class ClassScheduleController extends Controller
{
    private $weeklist = array('1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','7'=>'日');
    /**
     * ClassScheduleController constructor.
     * @param ClassScheduleService $classScheduleService
     */
    public function __construct(ScheduleService $scheduleService,ClassScheduleService $classScheduleService,MethodService $methodService,T09tbRepository $t09tbRepository, Term_processService $term_processService, User_groupService $user_groupService)
    {
        $this->scheduleService = $scheduleService;
        $this->classScheduleService = $classScheduleService;
        $this->methodService = $methodService;
        $this->t09tbRepository = $t09tbRepository;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_schedule', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('class_schedule');
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 年度
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 班號
        $queryData['class'] = $request->get('class');
        // 班別名稱
        $queryData['name'] = $request->get('name');
        // 分班名稱
        $queryData['branchname'] = $request->get('branchname');
        // 取得期別
        $queryData['term'] = $request->get('term');
        // 上課地點
        $queryData['sitebranch'] = $request->get('sitebranch');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 班別類型
        $queryData['process'] = $request->get('process');
        // 委訓單位
        $queryData['commission'] = $request->get('commission');
        // 班務人員
        $queryData['sponsor'] = $request->get('sponsor');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班別性質
        $queryData['type'] = $request->get('type');
        // 類別1**
        $queryData['categoryone'] = $request->get('categoryone');
        //開訓日期
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate']   = $request->get('edate');
        //結訓日期
        $queryData['sdate2'] = $request->get('sdate2');
        $queryData['edate2']   = $request->get('edate2');
        //在訓期間
        $queryData['sdate3'] = $request->get('sdate3');
        $queryData['edate3']   = $request->get('edate3');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        if(empty($request->all())) {
            $sess = $request->session()->get('lock_class');
            $queryData['choices'] = $this->_get_year_list();
            if($sess){
                $queryData2['class'] = $sess['class'];
                $queryData2['term'] = $sess['term'];
                $queryData2['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->methodService->getClassList($queryData2);
                return view('admin/class_schedule/list', compact('data', 'queryData'));
            }
            return view('admin/class_schedule/list', compact('queryData'));
        }
        $data = $this->methodService->getClassList($queryData);
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/class_schedule/list', compact('data', 'queryData'));
    }
    /**
     * 顯示頁
     *
     * @param $class_term
     */
    public function show($class_term)
    {
        return $this->edit($class_term);
    }

    /**
     * 編輯頁
     *
     * @param $class_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($class_term)
    {
        $term = $queryData['term'] = substr($class_term, -2);
        $class = $queryData['class'] = substr($class_term, 0,-2);
        $queryData = $this->methodService->getClassList($queryData);
        $queryData = $queryData[0];
        //備註
        $remark = T04tb::select('remark','site','location','site_branch')->where('class', $class)->where('term', $term)->first();
        //教室
        $site = $this->classScheduleService->getsitename($remark['site'],$remark['site_branch']);//主教室

        $data = T06tb::select('class','term','course','name','hour','date','stime','etime','site','location','branch')->where('class',$class)->where('term',$term)->orderBy('date')->orderBy('stime')->get();
        if (!$data||empty($data)) {
            return back()->with('result', '0')->with('message', '查無課程資料！');
        }
        $i = 0;
        foreach ($data as $key => $value) {
            // 講座
            $cnamedata = T08tb::select('cname','ename','idkind','idno')->where('class', $value['class'])->where('term', $value['term'])->where('course', $value['course'])->where('hire',DB::RAW("'Y'"))->first();
            if(is_null($cnamedata)){
                $data[$i]->cname = '';
            }elseif($cnamedata->idkind=='1'){
                $data[$i]->cname = $cnamedata->ename;
            }elseif($cnamedata->idkind=='0'){
                $data[$i]->cname = $cnamedata->cname;
            }else{
                $data[$i]->cname = '';
            }
            if($value['site']=='')  { //如果沒有新增實際教室 則使用主教室
                if($remark['site'] =='oth') {
                   $site = T04tb::select('loction')->where('class', $value['class'])->where('term', $value['term'])->where('course', $value['course'])->first();
                   $data[$i]->sitename = $site->loction;
                }else{
                   $data[$i]->sitename = empty($site)?'':$site->name;
                }
            }else{
                if($value['site'] =='oth') {
                    $data[$i]->sitename = $value['location'];
                }else{
                    $sitedata = $this->classScheduleService->getsitename($value['site'],$value['branch']);
                    $data[$i]->sitename =  $sitedata['name'];
                }
            }
          //  $data[$i]->location = $remark['location'];
            $i++;
        }
        // 日期
        // $dateData = T36tb::where('class', $class)->where('term', $term)->get();
        // 講座
        // $teacherData = T08tb::select('cname')->where('class', $class)->where('term', $term)->where('course', $data[0]->course)->first();
        // $teacherData = empty($teacherData)? '':$teacherData;
        return view('admin/class_schedule/form', compact('data', 'remark','queryData'));
    }
    /**
     * 課程詳細資料編輯頁
     *
     * @param $class_term_course
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function classedit(Request $request,$class_term_course)
    {

        $course = substr($class_term_course, -2);
        $class = $queryData['class'] = substr($class_term_course, 0,-4);
        $term = $queryData['term'] = substr($class_term_course, -4,-2);
        $queryData = $this->methodService->getClassList($queryData);
        $queryData = $queryData[0];
        $queryData->course = $course;
        $queryData->Scale = $request->get('Scale');
        $data = T06tb::select('class','term','course','name','hour','date','stime','etime','site','branch','mergeclass','teachingmaterial','location')->where('class',$class)->where('term',$term)->where('course',$course)->get();
        if (!$data||empty($data[0])) {
            return back()->with('result', '0')->with('message', '查無課程資料！');
        }
        $data = $data[0];
        // 講座
        $cnamedata = T08tb::select('cname','ename','idkind','idno')->where('class',$class)->where('term',$term)->where('course',$course)->where('hire',DB::RAW("'Y'"))->first();
        if(is_null($cnamedata)){
            $data->cname = '';
        }elseif($cnamedata->idkind=='1'){
            $data->cname = $cnamedata->ename;
        }elseif($cnamedata->idkind=='0'){
            $data->cname = $cnamedata->cname;
        }else{
            $data->cname = '';
        }
        // 合堂
        // $mergeclass = T36tb::select('mergeclass')->where('class', $class)->where('term', $term)->first();
        // $data->mergeclass = ($mergeclass)? $mergeclass['mergeclass']:'';
        // 教室
        if(is_null($data->site)){
            //使用主教室
            $site = T04tb::select('site','site_branch')->where('class', $class)->where('term', $term)->first();
            $data->site = $site->site;
            $data->branch = $site->site_branch;
        }
        // 場地列表
        $Nantoulist = Edu_classroom::select('roomno','roomname')->get();
        $Taipeilist = M14tb::get();
        // 教材

        $teachingmaterial = is_null($cnamedata)? '': Dbteachingmaterial::select('m01tb.serno','teachingmaterial.filename','teachingmaterial.id')->join('m01tb','teachingmaterial.m01serno','m01tb.serno')->where('m01tb.idno',$cnamedata->idno)->get();
        return view('admin/class_schedule/form_edit', compact('data', 'queryData','Nantoulist','Taipeilist','teachingmaterial'));
    }
    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $key)
    {
        // var_dump($request->all());
         if(strlen($key)>9){
            $course = substr($key, -2);
            $class = substr($key, 0,-4);
            $term = substr($key, -4,-2);
        }else{
            $term = substr($key, -2);
            $class = substr($key, 0,-2);
        }

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('class_schedule', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        // 檢查是否已經轉過帳
        if ($this->check($key)) {

            return back()->with('result', '0')->with('message', '已作過轉帳，資料不可修改或刪除！');
        }
        // 詳細資料更新
        $data['name'] = $request->input('classname');
        if(!is_null($data['name'])){
            //資料對比
            $data['date'] = $request->input('sdate_begin');
            $t04data = T04tb::select('sdate','edate','site','site_branch','sponsor')->where('class',$class)->where('term',$term)->first();
            if($t04data->sdate > $data['date'] || $t04data->edate < $data['date']) return back()->with('result', '0')->with('message', '更新失敗，設定日期不為上課日期');

            $t06data = T06tb::select('name','hour','date','branch','stime','etime','site')->where('class',$class)->where('term',$term)->where('course',$course)->first();
            $data['hour'] = $request->input('hour');

            $data['stime'] = $request->input('stime');
            $data['etime'] = $request->input('etime');
            $data['name'] = $request->input('classname');
            $data['mergeclass'] = $request->input('mergeclass');
            $data['location'] = $request->input('location');
            $data['teachingmaterial'] = $request->input('teachingmaterial');
            $data['otherlocation'] = $request->input('otherlocation');
            $data['branch'] = $request->input('branch');
            if($data['otherlocation']=='Y'){
                $data['branch'] = '3';
                $data['site']  = 'oth';
            }elseif($data['branch'] == '1'){
                // '檢查開課日期是否已過確認凍結日
                $freezeday = s02tb::select('weekly','monthly')->first(); /*凍結日(週、月)(2、05) */
                $affirmVids = date('Ymd',strtotime($data['date'] .' -'.(date('w',strtotime($data['date']))+8-$freezeday['weekly']).'Day')); //確認凍結日
                $requestVids = date('Ym',strtotime($data['date'] .' -1 month')).$freezeday['monthly'];  //需求凍結日
                if(strtotime($affirmVids) > strtotime('now') ) return back()->with('result', '0')->with('message', '上課日期已過確認凍結日期');

                $data['site'] = $request->input('Tsite');
                $DB = 't22tb';
            }elseif($data['branch'] == '2'){
                $data['site'] = $request->input('Nsite');
                $DB = 't97tb';
            }else{
                $data['branch'] = NULL ;
                $data['site'] = NULL;
                $DB = $t06data['branch']=='1'?'t22tb':'t97tb';
            }
            // 賦予時段(場地預約用)
            if($data['stime']  == '' || $data['etime'] == ''){
                $time = 'D';
            }elseif( $data['stime']  < '1300' && $data['etime'] < '1300'){
                $time = 'A';
            }elseif( $data['stime']  < '1300' && $data['etime'] < '1800'){
                $time = 'D';
            }elseif( $data['stime']  < '1300' ){
                $time = 'E';
            }elseif( $data['stime']  < '1800' && $data['etime'] < '1800'){
                $time = 'B';
            }elseif( $data['stime']  >= '1800' && $data['etime'] > '1800'){
                $time = 'C';
            }else{
                return back()->with('result', '0')->with('message', '調整失敗，錯誤時間!');
            }
            $timebase = $this->gettime($time);
            // var_dump($data);exit();
            DB::beginTransaction();
            DB::connection()->enableQueryLog(); //啟動SQL_LOG
            try{
                // 場地或時間異動
                if($data['site']!= $t06data['site'] || $data['branch']!=$t06data['branch'] || $data['date'] != $t06data['date'] ){
                    //  非外地上課 正常班期
                    if($data['otherlocation']!='Y' ){
                        if(!is_null($data['branch']) ){
                            //檢查實際教室有無衝突
                            $check06 =T06tb::select('class','term','course','site','mergeclass')->where('site',$data['site'])
                            ->where('branch',$data['branch'])->where('date',$data['date'])
                            ->where('stime','<>',$data['etime'])->where('etime','<>',$data['stime'])
                            ->where(function($query) use ($data){
                                $query->wherebetween('stime', [$data['stime'],$data['etime'] ])
                                    ->orwherebetween('etime', [$data['stime'],$data['etime'] ]);
                            } )->get()->toArray();
                            $checkpoint = 0;
                            foreach ($check06 as $key => $value) {
                                if ($value['class'] == $class && $value['term'] == $term && $value['course'] == $course ){//排除自己
                                    continue;
                                }elseif($data['mergeclass']=='Y' && $value['mergeclass'] == 'Y'){//雙方合堂上課
                                    continue;
                                }else{
                                    return back()->with('result', '0')->with('message', '調整失敗，該時段的場地已被預約!');
                                }
                            }
                        }
                        // 課程教室為主教室 不刪除
                        if($t04data['site'] != $t06data['site'] || $t04data['site_branch'] != $t06data['branch']){
                            // 刪除確認
                            $check06 =T06tb::select('class','term','course','site','mergeclass')->where('class',$class)->where('term',$term)->where('course','<>',$course)->where('site',$t06data['site'])->where('date',$data['date'])->count();
                            // 同班期同一天無其他課程
                            if($check06 == 0){

                                $DL1 = T36tb::where('class',$class)->where('term',$term)->where('date',$t06data['date'])->where('site',$t06data['site'])->where('site_branch',$t06data['branch'])->delete();
                                $DL2 = DB::table($DB)->where('class',$class)->where('term',$term)->where('date',$t06data['date'])->where('site',$t06data['site'])->delete();
                            }
                        }
                        if(!is_null($data['branch']) ){
                            // 檢查課程行事曆有無衝突
                            $calendarReserve = TRUE;
                            $siteReserve = TRUE;
                            $check36 = T36tb::select('class','term')->where('site',$data['site'])->where('site_branch',$data['branch'])->where('date',$data['date'])->get()->toArray();
                            foreach ($check36 as $key => $value) {
                                if ($value['class'] == $class && $value['term'] == $term ){//排除自己的班期
                                    $calendarReserve = FALSE;
                                    continue;
                                }else{
                                    if($data['mergeclass']!='Y'){// 合堂上課不檢查衝突
                                        return back()->with('result', '0')->with('message', '調整失敗，該時段的場地已被預約!!');
                                    }
                                }
                            }
                            // 檢查教室預約有無衝突
                            foreach ($timebase  as $key => $value) {
                                $times[] = $value['time'];
                            }
                            $checksite = DB::table($DB)->where('site',$data['site'])->where('date',$data['date'])
                            ->whereIn('time',$times )->get()->toArray();
                            foreach ($checksite as $key => $value) {
                                if ($value->class == $class && $value->term == $term ){//排除自己的班期
                                    $siteReserve = FALSE;
                                    continue;
                                }else{
                                    if($data['mergeclass']!='Y'){// 合堂上課不檢查衝突
                                        return back()->with('result', '0')->with('message', '調整失敗，該時段的場地已被預約!!!');
                                    }
                                }
                            }
                            // 新增T36
                            if($calendarReserve){
                                T36tb::create(array('class'=>$class,'term'=>$term,'date'=>$data['date'],'site'=>$data['site'],'site_branch'=>$data['branch']));
                                $sql = DB::getQueryLog();
                                $nowdata = T36tb::where('class',$class)->where('term',$term)->where('date',$date)->where('site',$site)->get()->toarray();
                                createModifyLog('I','t36tb','',$nowdata,end($sql));
                            }
                            // 預約教室
                            if($siteReserve){
                                foreach ($timebase as $key => $value) {

                                    $inputdata = array( 'class' =>$class,           'term'  =>$term,
                                                        'date'  =>$data['date'],    'site'  =>$data['site'],
                                                        'stime' =>$value['stime'],  'etime' =>$value['etime'],
                                                        'time'  =>$value['time']);
                                    if($data['branch']=='1'){
                                        $indata['reserve'] = $user_data->userid; // **預約人userid
                                        $indata['liaison'] = $t04data['sponsor'];   // 聯絡人 = 班務人員
                                        $inputdata['seattype'] = 'C'; // 預設
                                        $inputdata['affirm']  = (date('Y',strtotime($affirmVids))-1911).date('md',strtotime($affirmVids)); //確認凍結日
                                        $inputdata['request'] = (date('Y',strtotime($requestVids))-1911).date('md',strtotime($requestVids)); //需求凍結日
                                        if($data['site']=='404' || $data['site']=='405'){
                                            $inputdata['status'] = 'Y';
                                        }else{
                                            $inputdata['status'] = 'N';
                                        }
                                    }
                                    DB::table($DB)->insert($inputdata);
                                    $sql = DB::getQueryLog();
                                    $nowdata = DB::table($DB)->where('class',$class)->where('times',$value['time'])->where('date',$data['date'])->where('site',$value['site'])->get()->toarray();
                                    createModifyLog('I',$DB,'',$nowdata,end($sql));
                                }
                            }
                        }
                    }
                }
                unset($data['otherlocation']);
                // 判斷時數是否有變動
                if($t06data['hour']!= $data['hour']){
                    //重新計算費用...
                    $t09data = T09tb::where('class', $class)->where('term', $term)->where('course', $course)->get()->toArray();
                    $i=0;
                    //更新09_費用相關
                    foreach ($t09data as $key => $value) {
                        if($value['lecthr']!==0){
                            //$t09data[$i]['lecthr'] = $newhour;
                            $newlectamt = $value['lectamt']/$value['lecthr']*$data['hour'];
                            $olddata = T09tb::where('class', $class)->where('term', $term)->where('course', $course)->where('lecthr', $t06data['hour'])->get()->toarray();
                            T09tb::where('class', $class)->where('term', $term)->where('course', $course)->where('lecthr', $t06data['hour'])->update(array('lecthr'=>$data['hour'],'lectamt'=>$newlectamt));
                            $sql = DB::getQueryLog();
                            $nowdata = T09tb::where('class', $class)->where('term', $term)->where('course', $course)->where('lecthr', $t06data['hour'])->get()->toarray();
                            createModifyLog('U','t09tb',$olddata,$nowdata,end($sql));
                        }
                    }
                }

                // 更新06_課程基本資料
                $olddata = T06tb::where('class', $class)->where('term', $term)->where('course', $course)->get()->toarray();
                T06tb::where('class', $class)->where('term', $term)->where('course', $course)->update($data);
                $sql = DB::getQueryLog();
                $nowdata = T06tb::where('class', $class)->where('term', $term)->where('course', $course)->get()->toarray();
                createModifyLog('U','t06tb',$olddata,$nowdata,end($sql));
                if(!empty($data['teachingmaterial'])){

                    $teachfile = Dbteachingmaterial::where('id', $data['teachingmaterial'])->get();

                    //20200620 API
                    $today = date('Ymd');
                    $today_code = (intval($today) + 5401) * 365;
                    $url = "https://appweb-fet.hrd.gov.tw/api/sunnet_material.php";
                    $file = public_path()."/Uploads/teachingmaterial/".$teachfile[0]->filename;
                    $file_info = filesize($file);
                    $file_content = base64_encode(file_get_contents($file));
                    // dd($file_content);
                    // dd($today_code);
                    $json_data = array();
                    $test_data['date'] = $data['date'];
                    $test_data['stime'] = $data['stime'];
                    $test_data['etime'] = $data['etime'];
                    $test_data['coursename'] = urlencode($data['name']);
                    $test_data['profname'] = urlencode($request->input('teacher_name'));
                    $test_data['efile'] = $file_content;
                    $test_data['filesize'] = $file_info;
                    $test_data['filename'] = urlencode($teachfile[0]->filename);
                    $test_data['class'] = $class;
                    $test_data['term'] = $term;

                    array_push($json_data, $test_data);
                    // dd($json_data);
                    $post_data = array(
                        'code' => $today_code,
                        'data' => json_encode($json_data),
                    );
                    // dd($post_data);
                    $return_data = $this->curlPost($url, $post_data);

                    // dd($return_data);
                }

                DB::commit();
                return redirect('/admin/class_schedule/'.$class.$term.'/edit')->with('result', '1')->with('message', '儲存成功!');
            }catch ( Exception $e ){
                DB::rollback();
                return back()->with('result', '0')->with('message', '儲存失敗，無更新資料!');
            }
        }else{
            // 更新備註
            $olddata = T04tb::where('class', $class)->where('term', $term)->get()->toarray();
            T04tb::where('class', $class)->where('term', $term)->update(array('remark' => $request->input('remark')));
            $sql = DB::getQueryLog();
            $nowdata = T04tb::where('class', $class)->where('term', $term)->get()->toarray();
            createModifyLog('U','t04tb',$olddata,$nowdata,end($sql));
            return redirect('/admin/class_schedule/'.$class.$term.'/edit')->with('result', '1')->with('message', '儲存成功!');
        }
    }

    function curlPost($url=NULL, $params=array()) {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_VERBOSE => 0,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYHOST=>FALSE,
            CURLOPT_SSL_VERIFYPEER=>FALSE,
            CURLOPT_POST => TRUE,
            CURLOPT_USERAGENT => "Google Bot",
            CURLOPT_POSTFIELDS => http_build_query($params),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * 調整主教室
     *
     * @param $class_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function siteEdit($class_term)
    {
        $term =  substr($class_term, -2);
        $class = substr($class_term, 0,-2);
        $t01data = T01tb::select('class', 'branch')->where('class', $class)->first();
        // $data = T04tb::select('class', 'term', 'site','location')->where('class', $class)->where('term', $term)->first();
        $data = T04tb::select('class', 'term', 'site','location','site_branch')->where('class', $class)->where('term', $term)->first();
        // 舊資料 院區抓T01
        if(is_null($data->site_branch) && !is_null($data->site) ){
            $data->site_branch = $t01data['branch'];
        }
        if ( ! $data) {
            return view('admin/errors/error');
        }
        $Nantoulist = Edu_classroom::select('roomno','roomname')->get();
        $Taipeilist = M14tb::get();
        return view('admin/class_schedule/form_site', compact('data', 'Nantoulist','Taipeilist'));
    }

    /**
     * 調整主教室儲存
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function siteUpdate(Request $request){
        $data = $request->all();
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('siteedit', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }
        $T04data = T04tb::select('class','term','quota','sdate','edate','sponsor','section','counselor')->where('class', $data['class'])->where('term', $data['term'])->get()->toarray();
        $T04data = $T04data[0];
        $T04data['site_branch'] = $data['branch'];
        if($data['branch']=='3'){ // 外地上課
            $T04data['site'] = 'oth';
            $T04data['location'] = $data['location'];
        }else{
            $T04data['location'] ='';
            $T04data['site'] = ($data['branch']=='1')? $request->input('siteT'):$request->input('siteN');
        }

        $exist_datas = $this->scheduleService->getExistData(collect([$T04data]));
        $exist_data = [];
        $exist_data['t04tb'] = (isset($exist_datas['t04tbs'][$data['class']][$data['term']])) ? $exist_datas['t04tbs'][$data['class']][$data['term']] : null;
        $exist_data['t01tb'] = (isset($exist_datas['t01tbs'][$data['class']])) ? $exist_datas['t01tbs'][$data['class']] : collect();
        $exist_data['t03tbs'] = (isset($exist_datas['t03tbs'][$data['class']][$data['term']])) ? $exist_datas['t03tbs'][$data['class']][$data['term']] : collect();
        $exist_data['grade1_m17tb'] = $exist_datas['grade1_m17tb'];
        $exist_data['t51tbs'] = (isset($exist_datas['t51tbs'][$data['class']][$data['term']])) ? $exist_datas['t51tbs'][$data['class']][$data['term']] : collect();
        $result = $this->scheduleService->validateT04tb($T04data, $exist_data);

        if ($result['status'] == 0){
            $exist_data = array_merge($exist_data, $result['data']);
            DB::beginTransaction();
            DB::connection()->enableQueryLog(); //啟動SQL_LOG
            try {
                $this->scheduleService->storeT04tb($T04data, $exist_data, "update");
                DB::commit();
                DB::connection()->disableQueryLog();
                return back()->with('result', 1)->with('message', '更新成功!!');
            } catch (Exception $e) {
                DB::rollback();
                DB::connection()->disableQueryLog();
                var_dump($e->getMessage());
                die;
                // something went wrong
                return back()->with('result', 0)->with('message', '更新失敗!!');
            }
        }else{
            return back()->with('result', 0)->with('message', $result['message']);
        }

    }
    /**
     * 分割課程處理
     *
     * @param $class_term_course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cuttingUpdate($class_term_course)
    {
        //檢查是否已經轉過帳
        if ($this->check($class_term_course)) {

            return back()->with('result', '0')->with('message', '已作過轉帳，資料不可修改或刪除！');
        }
        $course = substr($class_term_course, -2);
        $class = substr($class_term_course, 0,-4);
        $term = substr($class_term_course, -4,-2);

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('class_schedule', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $data = $this->classScheduleService->getcourse($class,$term,$course);// 取得要分割的資料

        $maxcourse = T06tb::where('class', $data->class)->where('term', $data->term)->max('course') + 1;
        $maxcourse = str_pad($maxcourse,2,'0',STR_PAD_LEFT);

        $hour1 = sprintf("%.1f",substr(sprintf("%.3f", $data->hour/2), 0, -2));
        $hour2 = $data->hour - $hour1;
        // 計算拆分後的時數
        //$data->hour = 7.5;

        // if ($data->hour / 2 > floor($data->hour / 2)) {
        //     // 除不盡
        //     $hour1 = floor($data->hour / 2);
        //     $hour2 = ($data->hour / 2) + (($data->hour / 2) - $hour1);
        // } else {
        //     // 可以除以二
        //     $hour1 = $data->hour / 2;
        //     $hour2 = $hour1;
        // }

        // 更新資料
        $olddata = T06tb::where('class',$class)->where('term',$term)->where('course',$course)->get()->toarray();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        T06tb::where('class',$class)->where('term',$term)->where('course',$course)->update(array('hour'=>$hour1,'date'=>'','stime'=>'','etime'=>''));
        $sql = DB::getQueryLog();
        $nowdata = T06tb::where('class',$class)->where('term',$term)->where('course',$course)->get()->toarray();
        createModifyLog('U','t06tb',$olddata,$nowdata,end($sql));
        // 新增資料第二筆
        T06tb::create(array(
            'class' => $data->class,
            'term' => $data->term,
            'course' => $maxcourse,
            'name' => $data->name,
            'hour' => $hour2,
        ));
        $sql = DB::getQueryLog();
        $nowdata = T06tb::where('class',$class)->where('term',$term)->where('course',$course)->get()->toarray();
        createModifyLog('I','t06tb','',$nowdata,end($sql));
        // 新增講座資料T08
        $teacherData = T08tb::where('class',$class)->where('term',$term)->where('course',$course)->get()->toArray();
        $i = 0;
        foreach ($teacherData as  $value) {
            $teacherData[$i]['course'] = $maxcourse;
            T08tb::create($teacherData[$i]);
            $sql = DB::getQueryLog();
            $nowdata = T08tb::where('class',$class)->where('term',$term)->where('course',$course)->get()->toarray();
            createModifyLog('I','t06tb','',$nowdata,end($sql));
            $i++;
        }
        // 新增講座任課資料檔T09
        $t09data = T09tb::where('class',$class)->where('term',$term)->where('course',$course)->get()->toArray();
        $i = 0;
        foreach ($t09data as $key => $value) {
            $t09data[$i]['t09data'] = $maxcourse;
            T09tb::create($t09data[$i]);
            $sql = DB::getQueryLog();
            $nowdata = T09tb::where('class',$class)->where('term',$term)->where('course',$course)->get()->toarray();
            createModifyLog('I','t09tb','',$nowdata,end($sql));
            $i++;
        }
        DB::connection()->disableQueryLog();
        return back()->with('result', '1')->with('message', '儲存成功!');
    }
    /**
     * 網頁公告編輯
     *
     * @param $class_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function publishEdit($class_term)
    {
        $term =  substr($class_term, -2);
        $class = substr($class_term, 0,-2);

        $data = T04tb::
        select('t01tb.name', 't04tb.class', 't04tb.term', 'publish2')
            ->join('t01tb', 't01tb.class', '=', 't04tb.class', 'left')
            ->where('t04tb.class', $class)
            ->where('t04tb.term', $term)
            ->first();

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/class_schedule/form_publish', compact('data'));
    }

    /**
     * 網頁公告儲存
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publishUpdate(Request $request)
    {
        $class = $request->input('class');

        $term = $request->input('term');

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('publishedit', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $data['publish2'] = 'Y';
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        // 更新
        $olddata = T04tb::where('class',$class)->where('term',$term)->get()->toarray();
        if($olddata[0]['publish2']!='Y'){
            T04tb::where('class', $class)->where('term', $term)->update($data);
            $sql = DB::getQueryLog();
            $nowdata = T04tb::where('class',$class)->where('term',$term)->get()->toarray();
            createModifyLog('U','t04tb',$olddata,$nowdata,end($sql));
        }
        
        return back()->with('result', '1')->with('message', '儲存成功!');
    }



    /**
     * 刪除處理
     *
     * @param $class_term_course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($class_term_course)
    {
        if(!isset($class_term_course)) return back()->with('result', 0)->with('message', '刪除失敗!');

        $course = substr($class_term_course, -2);
        $class = substr($class_term_course, 0,-4);
        $term = substr($class_term_course, -4,-2);
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('class_schedule', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }

        // 檢查是否已經轉過帳
        if ($this->check($class_term_course)) {

            return back()->with('result', '0')->with('message', '已作過轉帳，資料不可修改或刪除！');
        }
        //開刪
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try {
            $olddata = T06tb::where('class', $class)->where('term', $term)->where('course', $course)->get()->toarray();
            T06tb::where('class', $class)->where('term', $term)->where('course', $course)->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t06tb',$olddata,'',end($sql));
            $olddata = T08tb::where('class', $class)->where('term', $term)->where('course', $course)->get()->toarray();
            T08tb::where('class', $class)->where('term', $term)->where('course', $course)->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t08tb',$olddata,'',end($sql));
            $olddata = T09tb::where('class', $class)->where('term', $term)->where('course', $course)->get()->toarray();
            T09tb::where('class', $class)->where('term', $term)->where('course', $course)->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t09tb',$olddata,'',end($sql));
            DB::commit();
            DB::connection()->disableQueryLog();
            return redirect('/admin/class_schedule/'.$class.$term.'/edit')->with('result', '1')->with('message', '刪除成功!');
        } catch (Exception $e) {
            DB::rollback();
            DB::connection()->disableQueryLog();
            var_dump($e->getMessage());
            die;
            // something went wrong
            return back()->with('result', 0)->with('message', '刪除失敗!!');
        }
    }

    /**
     * 檢查是否已經轉帳
     *
     * @param $oldData
     * @return bool
     */
    private function check($class_term_course)
    {
        $course = substr($class_term_course, -2);
        $class = substr($class_term_course, 0,-4);
        $term = substr($class_term_course, -4,-2);
        // 取得舊資料
        $data = T09tb::where('term', $term)->where('class', $class)->where('course', $course)->where('paidday', '!=', '')->first();

        if ($data['paidday']!='') { //轉帳日期
            return true;
        } else {
            return false;
        }
    }

    /**
     * 取得期別**
     *
     * @param Request $request
     * @return string
     */
    public function getTerm(Request $request)
    {
        $class = $request->input('class');

        $selected = $request->input('selected');

        if ( ! $class) {

            return '';
        }

        if (is_numeric( mb_substr($class, 0, 1))) {

            $data = DB::select('SELECT DISTINCT term FROM t04tb WHERE class = \''.$class.'\' ORDER BY `term`');
        } else {

            $data = DB::select('SELECT DISTINCT term FROM t38tb WHERE meet = \''.$class.'\' ORDER BY `term`');
        }

        $result = '';

        foreach ($data as $va) {
            $result .= '<option value="'.$va->term.'"';
            $result .= ($selected == $va->term)? ' selected>' : '>';
            $result .= $va->term.'</option>';
        }

        return $result;
    }

    /**
     * 課程表
     *
     * @param $class_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function calendar(Request $request ,$class_term)
    {
        $Scale = $request->get('Scale');
        $term = substr($class_term, -2);
        $class = substr($class_term, 0,-2);
        // $queryData = $this->methodService->getClassList($queryData);
        // $queryData = $queryData[0];
        $classdata = T06tb::select('class','term','course','name','hour','date','stime','etime')->where('class',$class)->where('term',$term)->orderBy('date')->orderBy('stime')->get();
        if (count($classdata)==0) {
            return redirect('/admin/class_schedule/'.$class_term.'/edit')->with('result', '0')->with('message', '查無課程資料！');
        }
        $T04data = T04tb::select('sdate','edate')->where('class', $class)->where('term', $term)->first();
        $i = 0;
        foreach ($classdata as $key => $value) {
            // 講座
            $cnamedata = T08tb::select('cname','ename','idkind')->where('class', $value['class'])->where('term', $value['term'])->where('course', $value['course'])->where('hire',DB::RAW("'Y'"))->first();
            if($cnamedata['idkind']=='1'){
                $classdata[$i]->cname = $cnamedata['ename'];
            }elseif($cnamedata['idkind']=='0'){
                $classdata[$i]->cname = $cnamedata['cname'];
            }else{
                $classdata[$i]->cname = '';
            }
            // 檢查是否已經轉過帳
            $classdata[$i]['paymoney'] = ($this->check($class_term.$value['course']))?  1:0; //1:已轉帳 0:NO
            $i++;
        }

        $classdata->Scale = $Scale;
        $i = 0;
        $date = array();
        $base = array();
        foreach ($classdata as $key => $value) {
            if(!is_null($value['date']) && $value['stime']!=''){
                //$cnamedata = T08tb::select('cname')->where('class', $value['class'])->where('term', $value['term'])->where('course', $value['course'])->first();
                $datebase = strtotime(str_pad($value['date'],8,'0',STR_PAD_LEFT) );
                $year = substr($value['date'],0,-4)+1911;
                $week =  date('w', strtotime($year.substr($value['date'],-4)));
                $reslue = date("Y/m/d",$datebase);
                $date[$value['date']]['date'] = ltrim($reslue,0);
                $date[$value['date']]['week'] = $this->weeklist[$week];
                //$classdata[$i]->cname = ($cnamedata)? $cnamedata->cname:'';
                if($Scale ==10){
                    $timerange = ((substr($value['etime'],0,-2) - substr($value['stime'],0,-2))*60 + substr($value['etime'],-2)-substr($value['stime'],-2))/10;
                    $hour = ltrim(substr($value['stime'],0,-2),0);
                    $min = ( ltrim(substr($value['stime'],-2)+5,0) %10==0)? ltrim(substr($value['stime'],-2)+5,0) : ltrim(substr($value['stime'],-2)+5,0) +5;
                    $base[$value['date']][$hour][$min]['name']=$value['name'];
                    $base[$value['date']][$hour][$min]['range']= (ceil($timerange) <= 0)? 1:ceil($timerange);
                    $j=1;
                    for($i=$timerange;$i>1;$i--){
                        $newmin = ($j*10+$min-10) % 60 +10;
                        $newhour = $hour + floor(($j*10+$min)/60);
                        $base[$value['date']][$newhour][$newmin]['style'] ='1';
                        $j++;
                    }
                }else{
                    $timerange = ((substr($value['etime'],0,-2) - substr($value['stime'],0,-2))*60 + substr($value['etime'],-2)-substr($value['stime'],-2))/5;
                    $hour = ltrim(substr($value['stime'],0,-2),0);
                    $min = ltrim(substr($value['stime'],-2)+5,0);
                    $base[$value['date']][$hour][$min]['name']=$value['name'];
                    $base[$value['date']][$hour][$min]['range']=( $timerange <= 0)? 1:$timerange;
                    $j=1;
                    //合併行隱藏
                    for($i=$timerange;$i>1;$i--){

                        $newmin = ($j*5+$min-5) % 60 +5;
                        $newhour = $hour + floor(($j*5+$min)/60);
                        $base[$value['date']][$newhour][$newmin]['style'] ='1';
                        $j++;
                    }
                }
            }
        }
        $i=1;
        $calendar = array();

        foreach ($base as $key => $value) {
           $calendar[$i]=$value;
           $i++;
        }
        return view('admin/class_schedule/calendar', compact('classdata','date','calendar'));
    }
    /**
     * 課程配當
     *
     * @param $class_term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function courseassignment(Request $request ) {
        $class = $request->input('class');
        $term = $request->input('term');
        $classdata = T06tb::select('class','term','course','name','hour','date','stime','etime','site')->where('class',$class)->where('term',$term)->orderBy('date')->orderBy('stime')->get();
        $calendardata = T04tb::select('sdate', 'edate', 'site')->where('class', $class)->where('term', $term)->first();
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try {
            $errormsg = '';
            foreach ($classdata as $key => $value) {
                $newstime = $request->input('stime'.$value['course']);
                $newdate = $request->input('date'.$value['course']);
                $timediff = (strtotime($newstime) - strtotime($value['stime'])); //時間差(秒)
                $newetime = date('Hi', strtotime($value['etime'])+$timediff);
                if (empty($newstime) || empty($newdate)){
                    continue;
                }elseif ($calendardata->sdate > $newdate || $calendardata->edate < $newdate ){
                    return redirect('/admin/class_schedule/calendar/'.$class.$term)->with('result', '0')->with('message', '調整失敗，上課日期為'.$calendardata->sdate.'~'.$calendardata->edate);
                }
                $olddata = T06tb::where('class',$class)->where('term',$term)->where('course',$value['course'])->get()->toArray();
                if($olddata[0]['stime'] == $newstime && $olddata[0]['date'] == $newdate){
                    continue;
                }else{
                    if(!$this->check($class.$term.$value['course'])){ //未轉帳
                        T06tb::where('class',$class)->where('term',$term)->where('course',$value['course'])->delete();
                        $sql = DB::getQueryLog();
                        createModifyLog('D','t06tb',$olddata,'',end($sql));
                        //檢查實際教室有無衝突
                        $check = T06tb::where('class',$class)->where('term',$term)->where('site',$value['site'])->where('date',$newdate)->whereBetween('stime',array($newstime,$newetime))->whereBetween('etime',array($newstime,$newetime))->count();
                        if($check > 0 ) return redirect('/admin/class_schedule/calendar/'.$class.$term)->with('result', '0')->with('message', '調整失敗，該時段的場地已被預約。');
                        $nowdata = $olddata[0];
                        $nowdata['stime'] = $newstime;
                        $nowdata['etime'] = $newetime;
                        $nowdata['date'] = $newdate;
                        T06tb::create($nowdata);
                        $sql = DB::getQueryLog();
                        $nowdata = T06tb::where('class',$class)->where('term',$term)->where('course',$value['course'])->get()->toarray();
                        createModifyLog('I','t06tb','',$nowdata,end($sql));
                    }else{
                        $errormsg .= $value['date'].'_'.$value['name'].'，';
                    }

                }
            } 
            if(strlen($errormsg) >1){
                $msg = $errormsg.'已作過轉帳，資料不可修改或刪除！';
            }else{
                $msg = '';
            }   
            DB::commit();
            DB::connection()->disableQueryLog();
            return redirect('/admin/class_schedule/calendar/'.$class.$term)->with('result', '1')->with('message', '更新完畢'.$msg);
        } catch (Exception $e) {
            DB::rollback();
            DB::connection()->disableQueryLog();
            var_dump($e->getMessage());
            die;
            // something went wrong
            return back()->with('result', 0)->with('message', '調整失敗!!');
        }
        
        
    }
    // 重新計算補充保費(同一班期同一講師同一日 累計 鐘點費、稿酬、講演費)
    public function updateInsurerate($course_info){
        $class_info = [
            'class' => $course_info['class'],
            'term' => $course_info['term']
        ];
        $t04tb = $this->t04tbRepository->find($class_info);
        $insurerate = SystemParam::get('insurerate');
        $insurerate_info = $this->t09tbRepository->getComputeInsurerateInfo($class_info);

        $total = [];

        foreach($insurerate_info as $t09tb){
            if (empty($total[$t09tb->idno])){
                $total[$t09tb->idno]['lectamt_acc'] = 0;
                $total[$t09tb->idno]['noteamt_acc'] = 0;
                $total[$t09tb->idno]['speakamt_acc'] = 0;
            }
            $total[$t09tb->idno]['lectamt_acc'] += $t09tb->lectamt;
            $total[$t09tb->idno]['noteamt_acc'] += $t09tb->noteamt;
            $total[$t09tb->idno]['speakamt_acc'] += $t09tb->speakamt;
        }
        $insureamt1_new = [];
        foreach($insurerate_info as $t09tb){

            $insureamt1_new = 0;
            $insureamt2_new = 0;

            if ($t09tb->insuremk1 == 'Y'){
                if ($t09tb->t06tb_date < 1030901 && ($total[$t09tb->idno]['lectamt_acc'] + $total[$t09tb->idno]['speakamt_acc']) > 5000){
                    $insureamt1_new = round(($t09tb->lectamt + $t09tb->speakamt) * $insurerate, 0);
                }else if (($t09tb->t06tb_date >= 1030901 && $t09tb->t06tb_date <= 1040630) && ($total[$t09tb->idno]['lectamt_acc'] + $total[$t09tb->idno]['speakamt_acc']) > 19273){
                    $insureamt1_new = round(($t09tb->lectamt + $t09tb->speakamt) * $insurerate, 0);
                }else if ($t09tb->t06tb_date >= 1040701 && ($total[$t09tb->idno]['lectamt_acc'] + $total[$t09tb->idno]['speakamt_acc']) >= 20008){
                    $insureamt1_new = round(($t09tb->lectamt + $t09tb->speakamt) * $insurerate, 0);
                }
            }

            if ($t09tb->insuremk2 == 'Y'){
                if ($t09tb->t06tb_date < 1050101 && $total[$t09tb->idno]['lectamt_acc'] > 5000){
                    $insureamt2_new = round($t09tb->noteamt * $insurerate, 0);
                }

                if ($t09tb->t06tb_date >= 1050101 && $total[$t09tb->idno]['noteamt_acc'] >= 20000){
                    $insureamt2_new = round($t09tb->noteamt * $insurerate, 0);
                }

            }

            // 補充保費 未改變資料 停止
            if (($t09tb->insureamt1 == $insureamt1_new) && $t09tb->insureamt2 == $insureamt2_new) {
                continue;
            }

            $t09tb->insureamt1 = $insureamt1_new;
            $t09tb->insureamt2 = $insureamt2_new;
            $t09tb->insuretot = $insureamt1_new + $insureamt2_new;
            $t09tb->netpay = $t09tb->teachtot - $t09tb->deductamt - $t09tb->insuretot;
            $t09tb->totalpay = $t09tb->netpay + $t09tb->tratot;

            $t09tb->save();
        }

    }


    //取得時間
    public function gettime($timetype=NULL){ //A:早上 B:下午 C:晚間 D:白天(A+B) E:全天(A+B+C) F:(B+C)
        $data = array();
        if($timetype=='A'){
            $data[0]['time'] = 'A';
            $data[0]['stime'] = '0830';
            $data[0]['etime'] = '1200';
        }elseif($timetype=='B'){
            $data[0]['time'] = 'B';
            $data[0]['stime'] = '1300';
            $data[0]['etime'] = '1630';
        }elseif($timetype=='C'){
            $data[0]['time'] = 'C';
            $data[0]['stime'] = '1800';
            $data[0]['etime'] = '2130';
        }elseif(is_null($timetype) || $timetype=='D'){  //預設
            $data[0]['time'] = 'A';
            $data[0]['stime'] = '0830';
            $data[0]['etime'] = '1200';
            $data[1]['time'] = 'B';
            $data[1]['stime'] = '1300';
            $data[1]['etime'] = '1630';
        }elseif($timetype=='E'){
            $data[0]['time'] = 'A';
            $data[0]['stime'] = '0830';
            $data[0]['etime'] = '1200';
            $data[1]['time'] = 'B';
            $data[1]['stime'] = '1300';
            $data[1]['etime'] = '1630';
            $data[2]['time'] = 'C';
            $data[2]['stime'] = '1800';
            $data[2]['etime'] = '2130';
        }elseif($timetype=='F'){
            $data[0]['time'] = 'B';
            $data[0]['stime'] = '1300';
            $data[0]['etime'] = '1630';
            $data[1]['time'] = 'C';
            $data[1]['stime'] = '1800';
            $data[1]['etime'] = '2130';
        }else{
            return FALSE;
        }
        return $data;
    }

    public function _get_year_list()
    {
        $year_list = array();
        $year_now = date('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }
}
