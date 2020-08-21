<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ScheduleService;
use App\Services\User_groupService;
use App\Models\T69tb;
use App\Models\T01tb;
use App\Models\T03tb;
use App\Models\T04tb;
use App\Models\S01tb;

use App\Helpers\Common;
use App\Helpers\TWDateTime;

use DateTime;
use Excel;
use DB;

use App\Api\ELevelApp;

/*
    訓練排程處理控制器
*/
class ScheduleController extends Controller
{
    /**
     * ScheduleController constructor.
     * @param ScheduleService $ScheduleService
     */
    public function __construct(ScheduleService $scheduleService, User_groupService $user_groupService)
    {
        setProgid('schedule');
        $this->scheduleService = $scheduleService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('schedule', $user_group_auth)){
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
        $queryData = $request->only([
            't01tb.yerly',                // 年度
            't01tb.class',                // 班號
            't01tb.name',                 // 班級名稱
            't01tb.branchname',           // 分班名稱
            't01tb.branch',               // 辦班院區
            't01tb.process',              // 班別類型
            't01tb.commission',           // 委訓單位
            't01tb.traintype',            // 訓練性質
            't01tb.type',                 // 班別性質
            't01tb.categoryone',           // 類別1
            "t04tb.term",                  // 期別
            "t04tb.site_branch",           // 上課地點
            "t04tb.sponsor",                // 班務人員
            "t04tb.month",                 // 月份
            'sdate_start',                      // 開訓日期範圍(起)
            'sdate_end',                        // 開訓日期範圍(訖)
            'edate_start',                      // 結訓日期範圍(起)
            'edate_end',                        // 結訓日期範圍(訖)
            'training_start',                   // 在訓期間範圍(起)
            'training_end',                     // 在訓期間範圍(起)
            '_paginate_qty'
        ]);

        if (empty($queryData['t01tb']['yerly'])){
            $queryData['t01tb']['yerly'] = new DateTime();
            $queryData['t01tb']['yerly'] = $queryData['t01tb']['yerly']->format('Y') - 1911;
        }

        $sponsors = $this->scheduleService->getSponsors();
        $s01tbM = S01tb::where('type', '=', 'M')->get()->pluck('name', 'code');

        $data = [];

        if ($request->all()){
            $data = $this->scheduleService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->scheduleService->getOpenClassList($queryData);
            }
        }

        return view('admin/schedule/list', compact('data', 'queryData', 'sponsors', 's01tbM'));
    }

    /**
     * 顯示頁
     *
     * @param $class
     */
    public function show($class)
    {
        return $this->edit($class);
    }

    /**
     * 新增頁面
     *
     *
     */

    public function create()
    {
        // 辦班人員
        $sponsors = $this->scheduleService->getSponsors();
        // 教室
        $class_rooms = $this->scheduleService->getClassRooms();
        // 部門
        $sections = $this->scheduleService->getSections();
        return view('admin/schedule/form', compact(['sponsors', 't01tbs', 'class_rooms', 'sections']));
    }

    public function store(Request $request)
    {
        /*
            從[t03tb]各期參訓單位報名檔
            取得該班期各單位人數總和 當做該班人數
            (若有設定，則不得使用使用者輸入的值)
        */

        if (!empty($request->class) && !empty($request->term)){
            $t03tb_sum_quota = $this->scheduleService->getT03tbSumQuota($request->class, $request->term);
            if ($t03tb_sum_quota !== false){
                $request_all = $request->all();
                $request_all['quota'] = $t03tb_sum_quota;
                $request->replace($request_all);
            }
        }

        $this->validate($request, [
            'class' => 'required',
            'term' => 'required',
            // 'quota' => 'required',
            'sdate' => 'required',
            'edate' => 'required'
        ],[
            "class.required" => "班號 欄位不可為空",
            "term.required" => "期別 欄位不可為空",
            "quota.required" => "人數 欄位不可為空",
            "sdate.required" => "開課日期 欄位不可為空",
            "edate.required" => "結束日期 欄位不可為空",
            "sdate.date" => "開課日期 日期格式錯誤",
            "edate.date" => "結束日期 日期格式錯誤"
        ]);

        $new_t04tb = $request->only([
            "class",        // 班號
            "term",         // 期別
            "quota",        // 人數
            "sdate",        // 開課日期
            "edate",        // 結束日期
            "site",         // 教室
            "location",     // 其他教室
            "sponsor",      // 辦班人員
            "section",      // 部門
            "counselor",    // 帶班輔導員(非必填)
            "site_branch"   // 教室院區
        ]);

        // $new_t04tb["site"] = !empty($new_t04tb["site"][$new_t04tb["site_branch"]]) ? $new_t04tb["site"][$new_t04tb["site_branch"]] : "";
        $new_t04tb["term"] = str_pad($new_t04tb["term"], 2, '0', STR_PAD_LEFT);

        $exist_datas = $this->scheduleService->getExistData(collect([$new_t04tb]));

        /*
            從[t03tb]各期參訓單位報名檔
            取得該班期各單位人數總和 當做該班人數
            (若有設定，則不得使用使用者輸入的值)
        */
        $new_t04tb['quota'] = (isset($quotas[$new_t04tb['class']][$new_t04tb['term']])) ? $quotas[$new_t04tb['class']][$new_t04tb['term']] : $new_t04tb['quota'];

        $exist_data = [];
        $exist_data['t04tb'] = (isset($exist_datas['t04tbs'][$new_t04tb['class']][$new_t04tb['term']])) ? $exist_datas['t04tbs'][$new_t04tb['class']][$new_t04tb['term']] : null;

        if (empty($exist_data['t04tb'])){
            $exist_data['t01tb'] = (isset($exist_datas['t01tbs'][$new_t04tb['class']])) ? $exist_datas['t01tbs'][$new_t04tb['class']] : collect();
            $exist_data['t03tbs'] = (isset($exist_datas['t03tbs'][$new_t04tb['class']][$new_t04tb['term']])) ? $exist_datas['t03tbs'][$new_t04tb['class']][$new_t04tb['term']] : collect();
            $exist_data['grade1_m17tb'] = $exist_datas['grade1_m17tb'];
            $exist_data['t51tbs'] = (isset($exist_datas['t51tbs'][$new_t04tb['class']][$new_t04tb['term']])) ? $exist_datas['t51tbs'][$new_t04tb['class']][$new_t04tb['term']] : collect();

            $result = $this->scheduleService->validateT04tb($new_t04tb, $exist_data);
            if ($result['status'] == 0){
                $exist_data = array_merge($exist_data, $result['data']);

                DB::beginTransaction();

                try {
                    $this->scheduleService->storeT04tb($new_t04tb, $exist_data, "insert");

                    // DB::rollback();
                    DB::commit();
                    return back()->with('result', 1)->with('message', '新增成功');
                } catch (\Exception $e) {
                    DB::rollback();
                    // return back()->with('result', 0)->with('message', '新增失敗');
                    $status = false;
                    var_dump($e->getMessage());
                    die;
                    // something went wrong
                }

            }else{
                return back()->with('result', 0)
                             ->with('html_message', $result['message'])
                             ->withInput($request->all());
            }
        }else{
            $request->flash();
            return back()->with('result', 0)
                         ->with('html_message', '該班級已存在')
                         ->withInput(
                            $request->all()
                         );
        }

    }


    /**
     *
     * 進入排程明細 details
     *
     */
    public function details(Request $request)
    {
        ini_set('memory_limit', '512M')  ;
        $queryData = $request->only([
            "yerly", "s_month", "e_month", "weekday"
        ]);
        $now = new DateTime();

        $queryData['yerly'] = (empty($queryData['yerly'])) ? $now->format('Y') - 1911 : $queryData['yerly'];
        $queryData['s_month'] = (empty($queryData['s_month'])) ? (int)$now->format('m') : $queryData['s_month'];
        $queryData['e_month'] = (empty($queryData['e_month'])) ? (int)$now->format('m') : $queryData['e_month'];
        $queryData['weekday'] = (empty($queryData['weekday'])) ? 'week' : $queryData['weekday'];

        $queryYearly = new DateTime(($queryData['yerly'] + 1911).'-01-01');
        $start = new DateTime($queryYearly->format("Y-01-01"));
        $end = new DateTime($queryYearly->format("Y-12-31"));

        $dates = $calendar = $total = [];
        $day = clone $start;
        $year = $queryYearly->format("Y");

        if ($queryData['weekday'] == 'week'){
            if ($day->format('w') != 1){
                $day->modify("+".(8 - $day->format('w'))." day");
            }
        }

        $n_td = 0;
        // 產生日期
        if ($queryData['weekday'] == 'week'){
            while((int)$day->format("Y") == $year){
                $dates[(int)$day->format("m")][(int)$day->format("W")] = $day->format("d");
                $calendar[(int)$day->format("W")] = [
                    'is_class' => false,
                    'quota' => 0,
                    'term' => []
                ];
                $total[(int)$day->format("W")] = 0;
                $day->modify("+7 day");
            }
        }elseif ($queryData['weekday'] == 'day'){
            while((int)$day->format("Y") == $year){
                $dates[(int)$day->format("m")][(int)$day->format("d")] = $day->format("d");
                $calendar[(int)$day->format("m")][(int)$day->format("d")] = [
                    'is_class' => false,
                    'quota' => 0,
                    'term' => []
                ];
                $total[(int)$day->format("m")][(int)$day->format("d")] = 0;
                $day->modify("+1 day");
            }
        }

        $dates = collect($dates);

         // 取得資料

        $t04tbs = $this->scheduleService->getDeatil($queryData);

        $t01tbs = $t04tbs->map(function($t36tbs){
            return $t36tbs[0]->t01tb_name;
        });

        if ($queryData['weekday'] == 'week'){
            $calendars = $t04tbs->map(function($t36tbs) use($calendar, &$total){
                $term_info = $t36tbs->pluck('t04tb_quota', 'term');

                foreach ($t36tbs as $t36tb){
                    $date = Common::dateRocToCeFormat($t36tb->date);
                    $date = new DateTime($date);
                    $week = (int)$date->format('W');

                    if (isset($calendar[$week])){
                        $calendar[$week]['is_class'] = true;
                        if (!in_array($t36tb->term, $calendar[$week]['term'])){
                            $calendar[$week]['term'][] = $t36tb->term;
                            $total[$week] += $t36tb->t04tb_quota;
                            $calendar[$week]['quota'] += $t36tb->t04tb_quota;
                        }
                    }

                }
                unset($total);
                return collect([
                    'name' => $t36tbs[0]['t01tb_name'],
                    'total_quota' => $term_info->sum(),
                    'term_num' => $term_info->keys()->count(),
                    'calendar' => collect($calendar)
                ]);

            });

        }elseif ($queryData['weekday'] == 'day'){
            $calendars = $t04tbs->map(function($t36tbs) use($calendar, &$total){
                $term_info = $t36tbs->pluck('t04tb_quota', 'term');

                foreach ($t36tbs as $t36tb){
                    $date = Common::dateRocToCeFormat($t36tb->date);
                    $date = new DateTime($date);

                    $month = (int)$date->format('m');
                    $day = (int)$date->format('d');

                    if (isset($calendar[$month][$day])){
                        $calendar[$month][$day]['is_class'] = true;
                        if (!in_array($t36tb->term, $calendar[$month][$day]['term'])){
                            $calendar[$month][$day]['term'][] = $t36tb->term;
                        }
                        $total[$month][$day] += $t36tb->t04tb_quota;
                        $calendar[$month][$day]['quota'] += $t36tb->t04tb_quota;
                    }
                }
                unset($total);
                return collect([
                    'name' => $t36tbs[0]['t01tb_name'],
                    'total_quota' => $term_info->sum(),
                    'term_num' => $term_info->keys()->count(),
                    'calendar' => collect($calendar)
                ]);
            });
        }

        return view('admin/schedule/details', compact('calendars','dates', 'queryData', 't01tbs', 'total'));
    }

    /**
     * 編輯頁
     *
     * @param $class
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($class, $term)
    {
        // 辦班人員
        $sponsors = $this->scheduleService->getSponsors();
        // 教室
        $class_rooms = $this->scheduleService->getClassRooms();
        // 部門
        $sections = $this->scheduleService->getSections();
        // 可開班清單
        // $t01tbs = $this->scheduleService->getAllT01tbs();
        $t04tb = $this->scheduleService->getT04tb($class, $term);
        $t03tb_sum_quota = $this->scheduleService->getT03tbSumQuota($class, $term);
        // dd($class_rooms);
        return view('admin/schedule/form', compact(['sponsors', 't01tbs', 'class_rooms', 'sections', 't04tb', 't03tb_sum_quota']));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $class, $term)
    {
       //  $eLevelApp = new ELevelApp();
       //  dd($eLevelApp->getSign(null, null, 2));
       // die;
        /*
            從[t03tb]各期參訓單位報名檔
            取得該班期各單位人數總和 當做該班人數
            (若有設定，則不得使用使用者輸入的值)
        */

        if (!empty($class) && !empty($term)){
            $t03tb_sum_quota = $this->scheduleService->getT03tbSumQuota($class, $term);
            if ($t03tb_sum_quota !== false){
                $request_all = $request->all();
                $request_all['quota'] = $t03tb_sum_quota;
                $request->replace($request_all);
            }
        }

        $this->validate($request, [
            'sdate' => 'required',
            'edate' => 'required'
        ],[
            "sdate.required" => "開課日期 欄位不可為空",
            "edate.required" => "結束日期 欄位不可為空",
            "sdate.date" => "開課日期 日期格式錯誤",
            "edate.date" => "結束日期 日期格式錯誤"
        ]);

        $new_t04tb = $request->only([
            // "class",        // 班號
            // "term",         // 期別
            "quota",        // 人數
            "sdate",        // 開課日期
            "edate",        // 結束日期
            "site",         // 教室
            "location",     // 其他教室
            "sponsor",      // 辦班人員
            "section",      // 部門
            "counselor",    // 帶班輔導員(非必填)
            "site_branch"   // 教室院區
        ]);

        $new_t04tb["class"] = $class;
        $new_t04tb["term"] = $term;

        $exist_datas = $this->scheduleService->getExistData(collect([$new_t04tb]));

        $exist_data = [];
        $exist_data['t04tb'] = (isset($exist_datas['t04tbs'][$class][$term])) ? $exist_datas['t04tbs'][$class][$term] : null;

        $exist_data['t01tb'] = (isset($exist_datas['t01tbs'][$class])) ? $exist_datas['t01tbs'][$class] : null;
        $exist_data['t03tbs'] = (isset($exist_datas['t03tbs'][$class][$term])) ? $exist_datas['t03tbs'][$class][$term] : collect();
        $exist_data['grade1_m17tb'] = $exist_datas['grade1_m17tb'];
        $exist_data['t51tbs'] = (isset($exist_datas['t51tbs'][$class][$term])) ? $exist_datas['t51tbs'][$class][$term] : collect();

        $result = $this->scheduleService->validateT04tb($new_t04tb, $exist_data);
        $error_new_t04tbs = [];

        if ($result['status'] == 0){
            $exist_data = array_merge($exist_data, $result['data']);

            DB::beginTransaction();

            try {
                $this->scheduleService->storeT04tb($new_t04tb, $exist_data, "update");

                $update = true;
                // DB::rollback();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $update = false;
                var_dump($e->getMessage());
                die;
                // something went wrong
            }

            if ($update){
                return back()->with('result', 1)->with('message', '更新成功');
            }else{
                return back()->with('result', 0)->with('message', '更新失敗');
            }

        }else{
            return back()->with('result', 0)
                         ->with('message', $result['message']);
        }

    }

    public function getClassInfo($class, $term)
    {

        $t04tb = $this->scheduleService->getT04tb($class, $term);
        $t01tb = $this->scheduleService->getClass($class);

        $sum_quota = $t01tb->t03tbs()->where('term', '=', $term)->sum('quota');
        $style = (isset($t01tb)) ? $t01tb->style : 0;
        $t01tb = $t01tb->toArray();
        unset($t01tb['planmk']);
        return response()->json([
            "term" => $term,
            "sum_quota" => $sum_quota,
            "t01tb" => $t01tb
        ]);

    }

    public function updateBydetail(Request $request)
    {
        $t04tb_info = $request->only([
            "class", "term"
        ]);

        $update_data = $request->only([
            "sdate", "edate"
        ]);

        if (!empty($t04tb_info['class']) && !empty($t04tb_info['term'])){
            $update = $this->scheduleService->updateT04tbBydetail($t04tb_info, $update_data);
        }else{
            $update = false;
        }

        if ($update === true){
            return back()->with('result', 1)
                         ->with('message', '更新成功');
        }else{
            return back()->with('result', 0)
                         ->with('message', $update['message']);
        }
    }

    public function delete(Request $request, $class, $term)
    {
        $class_info = [
            "class" => $class,
            "term" => $term
        ];
        $delete = $this->scheduleService->deleteT04tb($class_info);
        if ($delete){
            return redirect("admin/schedule")->with('result', 1)
                         ->with('message', '刪除成功');
        }else{
            return redirect("admin/schedule")->with('result', 0)
                         ->with('message', '刪除失敗');
        }
    }

    public function getTerms($class)
    {
        $t01tb = $this->scheduleService->getClass($class);
        $t04tb_terms = $t01tb->t04tbs->pluck("term")->toArray();
        return response()->json(["terms" => $t04tb_terms]);
    }

    public function batchOperate(Request $request)
    {
        $this->validate($request, [
            'yerly' => 'required',
            'operate' => 'required'
        ]);

        $yerly = str_pad($request->yerly, 3, '0', STR_PAD_LEFT);

        if ($request->operate == "insert"){
            $result = $this->scheduleService->batchInsert($yerly);
        }elseif ($request->operate == "delete"){
            $result = $this->scheduleService->batchDelete($yerly);
        }else{
            die('operate error');
        }

        return back()->with('result', $result['status'])->with('message', $result['message']);
    }

    public function importExample()
    {
        $this->scheduleService->createT04tbImportExample();
    }

    public function import(Request $request)
    {

        $this->validate($request, [
            'import_file' => 'required'
        ]);

        $path = $request->file('import_file')->getRealPath();
        $import_datas = Excel::load($path)->sheet(0)->toArray();
        $messages = $this->scheduleService->import($import_datas);

        $messages = $messages->map(function($errors, $i){

            $errors = array_map(function($error){
                    return join('<br>', $error);
            }, $errors->get('*'));

            if (empty($errors)) return null;

            return '第'.$i.'筆<br>'.join('<br>', $errors);
        })->filter()->toArray();

        if (empty($messages)){
            return back()->with('result', 1)->with('html_message', '匯入成功');
        }else{
            return back()->with('result', 0)->with('html_message', join('<br>', $messages));
        }
    }

    // 計算上課日期
    public function computeAttendClassDate(Request $request){
        $now = new DateTime();

        $t04tbKey = $request->only(['class', 'term']);
        $t04tb = $this->scheduleService->getT04tb($t04tbKey['class'], $t04tbKey['term']);
        if (empty($t04tb) || empty($t04tb->t01tb) || empty($request->sdate)){
            $message = [
                "status" => 3,
                "message" => "參數有誤"
            ];
        }else{
            $sdate = new TWDateTime();
            $sdate->setTWDateTime($request->sdate);

            $trainDays = $this->scheduleService->getTrainDate($t04tb->t01tb, $sdate);

            $affirm_date = \App\Helpers\TrainSchedule::getAffirmDate(clone $sdate);
            // 檢查開課日期是否已過確認凍結日
            if ($affirm_date->getTimeStamp() < $now->getTimeStamp()){
                $message = [
                    "status" => 3,
                    "message" => "上課日期已過確認凍結日期"
                ];
            }elseif ($trainDays == false){
                $message = [
                    "status" => 2,
                    "message" => "開課日期與上課方式不符!上課方式為".config("app.style.".$t04tb->t01tb->style)
                ];
            }else{
                $edate = new TWDateTime(end($trainDays));
                $edate = $edate->getTWFormat();                
                $message = [
                    "status" => 1,
                    "edate" => $edate,
                    "message" => "計算成功"
                ];            
            }             
        }

        return response()->json($message);
    }

}
