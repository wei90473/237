<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\T09tb;
use App\Models\T53tb;
use App\Models\T54tb;
use App\Models\T55tb;
use App\Models\T56tb;
use App\Models\T57tb;
use App\Models\T72tb;
use App\Models\T75tb;
use App\Models\T91tb;
use App\Models\T95tb;
use App\Services\EffectivenessSurveyService;
use App\Services\EmployService;
use App\Services\User_groupService;
use App\Services\Term_processService;
use Auth;
use DB;
use Illuminate\Http\Request;

class EffectivenessSurveyController extends Controller
{
    /**
     * EffectivenessSurveyController constructor.
     * @param EffectivenessSurveyService $effectivenessSurveyService
     */
    public function __construct(EffectivenessSurveyService $effectivenessSurveyService,EmployService $employservice, User_groupService $user_groupService, Term_processService $term_processService)
    {
        $this->effectivenessSurveyService = $effectivenessSurveyService;
        $this->employService = $employservice;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('effectiveness_survey', $user_group_auth)){
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

        $sponsor = $this->employService->getSponsor();
        $this_yesr = date('Y') - 1911;
        if (null == $request->get('yerly')) {
            $queryData['yerly'] = $this_yesr;
        } else {
            $queryData['yerly'] = $request->get('yerly');
        }
        //班號
        $queryData['class'] = $request->get('class');
        $queryData['name'] = $request->get('name');

        // 分班名稱**
        $queryData['class_branch_name'] = $request->get('class_branch_name');
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
        $queryData['search'] = $request->get('search');

        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料*/
        //$data = $this->effectivenessSurveyService->getEffectivenessSurveyList($queryData);

        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;

        $sql = '
            SELECT  X.class, RTRIM(X.name) AS name
            FROM ( SELECT A.class, A.name, 0 AS sort
            FROM t01tb A
            INNER JOIN t04tb B ON A.class=B.class
            INNER JOIN m09tb C  ON B.sponsor=C.userid
            WHERE UPPER(B.sponsor)=\'' . $uesr . '\'
            AND A.type<>\'13\'
            GROUP BY A.class,A.name
            UNION ALL
            SELECT class,name,1 AS sort FROM t01tb  WHERE type<>\'13\' ) X
            ORDER BY X.sort ASC,X.class DESC
            ';

        $classList = DB::select($sql);
        $mode='list';
        if($queryData['search'] != 'search' ){
			$queryData2['class'] = 'none';
			$data = $this->effectivenessSurveyService->getEffectivenessSurveyList($queryData2);
            $sess = $request->session()->get('lock_class');
            if($sess){
              $queryData2['class'] = $sess['class'];
              $queryData2['term'] = $sess['term'];
              $queryData2['yerly'] = substr($sess['class'], 0, 3);
              $data = $this->effectivenessSurveyService->getEffectivenessSurveyList($queryData2);
            }
		}else{
            $data = $this->effectivenessSurveyService->getEffectivenessSurveyList($queryData,$mode);
        }
        $sess = $request->session()->get('lock_class');
        //$test=session()->get('lock_class');
        //var_dump(empty($sess));
        //var_dump($sess);
        return view('admin/effectiveness_survey/list', compact('data', 'queryData', 'classList','sponsor','sess'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($class_info)
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;
        $class_info=unserialize($class_info);
        $mode='create';
        $data = $this->effectivenessSurveyService->getEffectivenessSurveyList($class_info,$mode);
        //dd($data);
        $data = $data[0]->toArray();

        /*$sql = '
            SELECT  X.class, RTRIM(X.name) AS name
            FROM ( SELECT A.class, A.name, 0 AS sort
            FROM t01tb A
            INNER JOIN t04tb B ON A.class=B.class
            INNER JOIN m09tb C  ON B.sponsor=C.userid
            WHERE UPPER(B.sponsor)=\'' . $uesr . '\'
            AND A.type<>\'13\'
            GROUP BY A.class,A.name
            UNION ALL
            SELECT class,name,1 AS sort FROM t01tb  WHERE type<>\'13\' ) X
            ORDER BY X.sort ASC,X.class DESC';
        $classList=DB::select($sql);*/


        // 未選取的課程
        $courseNot = '
            SELECT A.class, A.term, IFNULL(B.date,\'\') AS date,  RTRIM(IFNULL(B.name,\'\')) AS coursename,
            D.cname AS cname,
            IFNULL(B.course,\'\') AS course,
            IFNULL(C.idno,\'\') AS idno
            FROM t04tb A
            INNER JOIN t06tb B
            ON B.class=A.class AND B.term=A.term
            INNER JOIN t09tb C
            ON C.class=B.class AND C.term=B.term
            AND C.course=B.course  AND C.type=\'1\'
            LEFT JOIN m01tb D ON D.idno=C.idno
            WHERE A.class = \'' . $class_info['class'] . '\'
            AND A.term= \'' . $class_info['term'] . '\'
            AND NOT EXISTS(select * from t54tb where class=A.class and term=A.term and course=B.course and
            (idno=C.idno or idno is null))
            ';

        $courseNot = DB::select($courseNot);
        //dd($courseNot);
        // 已選取的課程
        /*$course = '
            SELECT IFNULL(B.date,\'\') as date,
             RTRIM(IFNULL(B.name,\'\')) as coursename,
             IFNULL(C.cname,\'\') as cname,
             A.course, A.idno
             FROM t54tb A
             LEFT JOIN t06tb B
            ON B.class=A.class AND B.term=A.term
            AND B.course=A.course
            LEFT JOIN m01tb C
            ON C.idno=A.idno

            WHERE A.class=\'' . $class_info['class'] . '\'
            AND A.term=\'' . $class_info['term'] . '\'
            AND A.times=\'' . $class_info['times'] . '\'

            ORDER BY A.sequence';

        $course = DB::select($course);*/
        $course=[];
        $create=[1];//判斷是create

        return view('admin/effectiveness_survey/form', compact('courseNot','course','data','create'));
    }

    public function detail($class_info=[])
    {
        //dd($class_info);
        $class_info=unserialize($class_info);
        //dd($class_info);
        $mode='list';
        $class_basic_info = $this->effectivenessSurveyService->getEffectivenessSurveyList($class_info,$mode);
        $data = $this->effectivenessSurveyService->getClassTimes($class_info);
        //var_dump($class_info);
        //dd($class_basic_info);

        return view('admin/effectiveness_survey/detail',compact('data','class_basic_info'));
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
        $data['class'] = $request->input('class');
        $data['term'] = $request->input('term');
        $data['times'] = $request->input('times');
        $data['copy'] = $request->input('copy');
        $data['fillsdate'] = $request->input('fillsdate');
        $data['filledate'] = $request->input('filledate');
        $data['upddate'] = date('Y-m-d H:i:s');
        // 日期格式
        $data['fillsdate']= str_pad($data['fillsdate'],7,'0', STR_PAD_LEFT);
        $data['filledate']= str_pad($data['filledate'],7,'0', STR_PAD_LEFT);

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('effectiveness_survey', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        //dd($data);
        // 檢查是否已經存在
        if (T53tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->exists()) {
            return back()->with('result', '0')->with('message', '已有此問卷資料，新增失敗');
        }

        //新增
        $result = T53tb::create($data);
        // 新增新資料
        $course = $request->input('course');
        $date = date('Y-m-d H:i:s');
        if ($course && is_array($course)) {

            foreach ($course as $key => $va) {

                // 拆分
                $ary = array_diff(explode('_', $va), array(null, 'null', '', ' '));

                T54tb::create([
                    'class' => $data['class'],
                    'term' => $data['term'],
                    'times' => $data['times'],
                    'course' => $ary[0],
                    'idno' => $ary[1],
                    'sequence' => $key + 1,
                    'upddate' => $date,
                ]);
            }
        }
        $class_info=['class'=>$data['class'],
                     'term'=>$data['term'],
                     'times'=>$data['times']];
        $class_info=serialize($class_info);
        return redirect("/admin/effectiveness_survey/create/{$class_info}")->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $effectiveness_survey_id
     */
    public function show($effectiveness_survey_id)
    {
        return $this->edit($effectiveness_survey_id);
    }

    /**
     * 編輯頁
     *
     * @param $effectiveness_survey_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($effectiveness_survey_id)
    {

        $class_info=unserialize($effectiveness_survey_id);
        //$data = T53tb::find($class_info);
        //dd($class_info);
        $mode='edit';
        $data = $this->effectivenessSurveyService->getEffectivenessSurveyList($class_info,$mode);
        $ans = $this->effectivenessSurveyService->getAns($class_info);//問卷答案
        //dd($ans);
        $control_edit=1;
        if(!empty($ans)){
            $control_edit=0;
        }
        $data=$data[0]->toArray();

        //dd($data);

        if (!$data) {
            return view('admin/errors/error');
        }

        // 未選取的課程
        $courseNot = '
            SELECT A.class, A.term, IFNULL(B.date,\'\') AS date,  RTRIM(IFNULL(B.name,\'\')) AS coursename,
            D.cname AS cname,
            IFNULL(B.course,\'\') AS course,
            IFNULL(C.idno,\'\') AS idno
            FROM t04tb A
            INNER JOIN t06tb B
            ON B.class=A.class AND B.term=A.term
            INNER JOIN t09tb C
            ON C.class=B.class AND C.term=B.term
            AND C.course=B.course  AND C.type=\'1\'
            LEFT JOIN m01tb D ON D.idno=C.idno
            WHERE A.class = \'' . $data['class'] . '\'
            AND A.term= \'' . $data['term'] . '\'
            AND NOT EXISTS (
            SELECT * FROM t54tb
            WHERE class=A.class
            AND term=A.term  AND course=B.course  AND (idno=C.idno OR idno=\'\') AND times=\''.$data['times'].'\' ) ORDER BY B.date,B.stime ';

        $courseNot = DB::select($courseNot);
        //dd($courseNot);

        // 已選取的課程
        $course = '
            SELECT IFNULL(B.date,\'\') as date,
             RTRIM(IFNULL(B.name,\'\')) as coursename,
             IFNULL(C.cname,\'\') as cname,
             A.course, A.idno
             FROM t54tb A
             LEFT JOIN t06tb B
            ON B.class=A.class AND B.term=A.term
            AND B.course=A.course
            LEFT JOIN m01tb C
            ON C.idno=A.idno

            WHERE A.class=\'' . $data['class'] . '\'
            AND A.term=\'' . $data['term'] . '\'
            AND A.times=\'' . $data['times'] . '\'

            ORDER BY A.sequence';

        $course = DB::select($course);
        //dd($course);
        //return view('admin/effectiveness_survey/form', compact('data'));
        return view('admin/effectiveness_survey/form', compact('data', 'courseNot', 'course','control_edit'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $effectiveness_survey_id)
    {
        $class_info=unserialize($effectiveness_survey_id);
        //var_dump($class_info);
        //var_dump($request->input());
        //die();
        // 取得POST資料
        $data['copy'] = $request->input('copy');
        $data['fillsdate'] = $request->input('fillsdate');
        $data['filledate'] = $request->input('filledate');
        $data['upddate'] = date('Y-m-d H:i:s');
        $data['fillsdate']= str_pad($data['fillsdate'],7,'0', STR_PAD_LEFT);
        $data['filledate']= str_pad($data['filledate'],7,'0', STR_PAD_LEFT);

        // 取得班別 期別 第幾次調查
        $class = $request->input('class');
        $term = $request->input('term');
        $times = $request->input('times');
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('effectiveness_survey', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法更新');
        }

        // 日期格式
        //$data['fillsdate'] = str_pad($data['fillsdate']['year'], 3, '0', STR_PAD_LEFT) . str_pad($data['fillsdate']['month'], 2, '0', STR_PAD_LEFT) . str_pad($data['fillsdate']['day'], 2, '0', STR_PAD_LEFT);
        //$data['filledate'] = str_pad($data['filledate']['year'], 3, '0', STR_PAD_LEFT) . str_pad($data['filledate']['month'], 2, '0', STR_PAD_LEFT) . str_pad($data['filledate']['day'], 2, '0', STR_PAD_LEFT);

        //更新
        //T53tb::find($effectiveness_survey_id)->update($data);

        $this->effectivenessSurveyService->getT53tb($data,$class_info);

        // 刪除舊資料
        T54tb::where('class', $class)->where('term', $term)->where('times', $times)->delete();
        // 新增新資料
        $course = $request->input('course');
        //dd($course);
        $date=date('Y-m-d H:i:s');
        if ($course && is_array($course)) {

            foreach ($course as $key => $va) {
                // 拆分
                $ary = array_diff(explode('_', $va), array(null, 'null', '', ' '));

                T54tb::create([
                    'class' => $class,
                    'term' => $term,
                    'times' => $times,
                    'course' => $ary[0],
                    'idno' => $ary[1],
                    'sequence' => $key + 1,
                    'upddate' => $date,
                ]);
            }
        }

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    /**
     * 刪除處理
     *
     * @param $effectiveness_survey_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($effectiveness_survey_id)
    {
        //$data = T53tb::find($effectiveness_survey_id);
        $class_info=unserialize($effectiveness_survey_id);
        $mode='edit';
        $data = $this->effectivenessSurveyService->getEffectivenessSurveyList($class_info,$mode);


        $data=$data[0]->toArray();

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('effectiveness_survey', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }

        //dd($data);
        if (!$data) {

            return view('admin/errors/error');
        }

        // 已選取的課程
        $course = '
            SELECT IFNULL(B.date,\'\') as date,
             RTRIM(IFNULL(B.name,\'\')) as coursename,
             IFNULL(C.cname,\'\') as cname,
             A.course, A.idno
             FROM t54tb A
             LEFT JOIN t06tb B
            ON B.class=A.class AND B.term=A.term
            AND B.course=A.course
            LEFT JOIN m01tb C
            ON C.idno=A.idno

            WHERE A.class=\'' . $data['class'] . '\'
            AND A.term=\'' . $data['term'] . '\'
            AND A.times=\'' . $data['times'] . '\'

            ORDER BY A.sequence';

        $course = DB::select($course);
        //dd($course);
        if ($course) {
            foreach ($course as $va) {
                T09tb::where('class', $data['class'])
                    ->where('term', $data['term'])
                    ->where('course', $va->course)
                    ->where('idno', $va->idno)
                    ->update(['okrate' => 0]);
            }
        }

        T53tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->delete();
        T54tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->delete();
        T55tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->delete();
        T72tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->delete();
        T75tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->delete();
        T91tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->delete();
        T95tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->delete();
        T56tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->delete();
        T57tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->delete();

        if (!T54tb::where('class', $data['class'])->where('term', $data['term'])->where('times', '!=', '')->exists()) {
            // 如果此班期之問卷(t54tb times<>'')都刪除了
            T57tb::where('class', $data['class'])->where('term', $data['term'])->where('times', '')->delete();
        }

        $arr=['class'=>$class_info['class'],'term'=>$class_info['term']];
        $arr=serialize($arr);
        //var_dump($test);
        //dd($arr);
        //dd($arr);
        return redirect("/admin/effectiveness_survey/$arr/detail")->with('result', '1')->with('message', '刪除成功!');
    }

    /**
     * 取得期別
     *
     * @param $class
     * @return string
     */
    public function getTerm(Request $request)
    {
        $class = $request->input('classes');

        $selected = $request->input('selected');

        if (is_numeric(mb_substr($class, 0, 1))) {

            $data = DB::select('SELECT DISTINCT term FROM t04tb WHERE class = \'' . $class . '\' ORDER BY `term`');
        } else {

            $data = DB::select('SELECT DISTINCT term FROM t38tb WHERE meet = \'' . $class . '\' ORDER BY `term`');
        }

        $result = '';

        foreach ($data as $va) {
            $result .= '<option value="' . $va->term . '"';
            $result .= ($selected == $va->term) ? ' selected>' : '>';
            $result .= $va->term . '</option>';
        }

        return $result;
    }

    /**
     * 取得課程
     *
     * @param Request $request
     * @return string
     */
    public function getCourse(Request $request)
    {
        $class = $request->input('classes');

        $term = $request->input('term');

        $courseNot = '
            SELECT A.class, A.term, IFNULL(B.date,\'\') AS date,  RTRIM(IFNULL(B.name,\'\')) AS coursename,
            D.cname AS cname,
            IFNULL(B.course,\'\') AS course,
            IFNULL(C.idno,\'\') AS idno
            FROM t04tb A
            INNER JOIN t06tb B
            ON B.class=A.class AND B.term=A.term
            INNER JOIN t09tb C
            ON C.class=B.class AND C.term=B.term
            AND C.course=B.course  AND C.type=\'1\'
            LEFT JOIN m01tb D ON D.idno=C.idno
             WHERE A.class = \'' . $class . '\'
            AND A.term= \'' . $term . '\'
             AND NOT EXISTS (
             SELECT * FROM t54tb
            WHERE class=A.class
            AND term=A.term  AND course=B.course  AND (idno=C.idno OR idno=\'\')  ) ORDER BY B.date,B.stime ';

        $courseNot = DB::select($courseNot);

        $result = '';

        foreach ($courseNot as $key => $va) {

            $result .= '<div class="checkbox checkbox-primary">
                            <input id="course' . $key . '" name="course[]" value="' . $va->course . '_' . $va->idno . '" type="checkbox">
                            <label for="course' . $key . '">
                                ' . $this->showDate($va->date) . ' ' . $va->coursename . ' ' . $va->cname . ' ' . $va->idno . '
                            </label>
                            <i class="fa fa-arrow-up pointer text-secondary" onclick="prev(this);"></i>
                            <i class="fa fa-arrow-down pointer text-secondary" onclick="next(this);"></i>
                        </div>';
        }

        return $result;
    }

    /**
     * 顯示日期格式
     *
     * @param $date
     * @return string
     */
    public function showDate($date)
    {
        return ($date) ? mb_substr($date, 0, 3) . '/' . mb_substr($date, 3, 2) . '/' . mb_substr($date, 5, 2) : '';
    }

    /**
     * 更換課程/講座
     *
     * @param $effectiveness_survey_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function changeEdit($effectiveness_survey_id)
    {
        $class_info=unserialize($effectiveness_survey_id);
        $mode='edit';
        $data = $this->effectivenessSurveyService->getEffectivenessSurveyList($class_info,$mode);

        $data=$data[0]->toArray();

        //$data = T53tb::find($effectiveness_survey_id);

        if (!$data) {

            return view('admin/errors/error');
        }

        // 未選取的課程
        $courseNot = '
            SELECT A.class, A.term, IFNULL(B.date,\'\') AS date,  RTRIM(IFNULL(B.name,\'\')) AS coursename,
            D.cname AS cname,
            IFNULL(B.course,\'\') AS course,
            IFNULL(C.idno,\'\') AS idno
            FROM t04tb A
            INNER JOIN t06tb B
            ON B.class=A.class AND B.term=A.term
            INNER JOIN t09tb C
            ON C.class=B.class AND C.term=B.term
            AND C.course=B.course  AND C.type=\'1\'
            LEFT JOIN m01tb D ON D.idno=C.idno
             WHERE A.class = \'' . $data['class'] . '\'
            AND A.term= \'' . $data['term'] . '\'
             AND NOT EXISTS (
             SELECT * FROM t54tb
            WHERE class=A.class
            AND term=A.term  AND course=B.course  AND (idno=C.idno OR idno=\'\') AND times=\''.$data['times'].'\'  ) ORDER BY B.date,B.stime ';

        $courseNot = DB::select($courseNot);

        // 已選取的課程
        $course = '
            SELECT IFNULL(B.date,\'\') as date,
             RTRIM(IFNULL(B.name,\'\')) as coursename,
             IFNULL(C.cname,\'\') as cname,
             A.course, A.idno
             FROM t54tb A
             LEFT JOIN t06tb B
            ON B.class=A.class AND B.term=A.term
            AND B.course=A.course
            LEFT JOIN m01tb C
            ON C.idno=A.idno

            WHERE A.class=\'' . $data['class'] . '\'
            AND A.term=\'' . $data['term'] . '\'
            AND A.times=\'' . $data['times'] . '\'

            ORDER BY A.sequence';

        $course = DB::select($course);

        return view('admin/effectiveness_survey/form_change', compact('data', 'courseNot', 'course'));
    }

    /**
     * 換課程/講座處理
     *
     * @param Request $request
     * @param $effectiveness_survey_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeUpdate(Request $request, $effectiveness_survey_id)
    {
        $class_info=unserialize($effectiveness_survey_id);

        $data = $this->effectivenessSurveyService->getEffectivenessSurveyList($class_info);

        $data=$data[0]->toArray();

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('effectiveness_survey', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法更新');
        }

        // 新資料陣列
        $newAry = array_diff(explode('_', $request->input('new')), array(null, 'null', '', ' '));
        // 舊資料
        $oldAry = array_diff(explode('_', $request->input('old')), array(null, 'null', '', ' '));

        T54tb::where('class', $data['class'])
            ->where('term', $data['term'])
            ->where('times', $data['times'])
            ->where('course', $oldAry['0'])
            ->where('idno', $oldAry['1'])
            ->update([
                'course' => $newAry['0'],
                'idno' => $newAry['1'],
                'upddate' => date('Y-m-d H:i:s'),
            ]);

        return back()->with('result', '1')->with('message', '更新成功');
    }
}
