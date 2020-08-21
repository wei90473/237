<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\LeaveService;
use App\Services\Term_processService;
use App\Services\User_groupService;

use App\Models\T01tb;
use App\Models\T13tb;
use App\Models\T14tb;
use App\Models\S01tb;
use App\Models\M09tb;
use DB;
use DateTime;
use App\Helpers\History;

class LeaveController extends Controller
{
    /**
     * LeaveController constructor.
     * @param LeaveService $leaveService
     */
    public function __construct(LeaveService $leaveService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        setProgid('leave');
        $this->leaveService = $leaveService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('leave', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        
        $this->middleware(function($request, $next){
            History::record();
            return $next($request);
        });
        
    }

    public function class_list(Request $request)
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
        
        $s01tbM = S01tb::where('type', '=', 'M')->get()->pluck('name', 'code');
        $sponsors = M09tb::all();

        $data = [];
        if ($request->all()){
            $data = $this->leaveService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->leaveService->getOpenClassList($queryData);
            }
        }

        return view('admin/leave/class_list', compact('data', 'queryData', 'sponsors', 's01tbM'));

    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $class, $term)
    {
        $listHistory = History::getHistory('admin/leave/class_list');

        $t04tb_info = compact('class', 'term');
        $t04tb = $this->leaveService->getT04tb($t04tb_info);
        // 取得列表資料
        $data = $this->leaveService->getLeaveList($t04tb_info);

        return view('admin/leave/list', compact('data', 't04tb', 'listHistory'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->leaveService->getT04tb($t04tb_info);
        $t13tbs = $t04tb->t13tbs;

        $students = ['' => '請選擇'];

        foreach($t13tbs as  $t13tb){
            $students[$t13tb->idno] = $t13tb->no." ".$t13tb->m02tb->cname;
        }

        if ($t04tb->type == 13){
            $types = [
                '' => '請選擇',
                4 => '請假',
                5 => '缺課'
            ];
        }else{
            $types = [
                '' => '請選擇',
                1 => '事假',
                2 => '喪假',
                3 => '病假'
            ];
        }

        return view('admin/leave/form', compact(['t13tbs', 't04tb', 'students', 'types']));
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('leave', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        // 取得POST資料
        $newT14tb = $request->only(['idno', 'sdate', 'edate', 'stime', 'etime', 'type', 'hour', 'reason']);

        // 時間處理
        $newT14tb['stime'] = str_pad($request->stime_hour, 2,'0',STR_PAD_LEFT).str_pad($request->stime_minute,2,'0',STR_PAD_LEFT);
        $newT14tb['etime'] = str_pad($request->etime_hour,2,'0',STR_PAD_LEFT).str_pad($request->etime_minute,2,'0',STR_PAD_LEFT);

        $t04tbKey = compact(['class', 'term']);
        //新增
        // $result = T14tb::create($data);
        $t14tb = $this->leaveService->createLeave($t04tbKey, $newT14tb);

        return redirect('/admin/leave/'.$t14tb->id.'/edit')->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $leave_id
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

        $t04tb_info = compact(['class', 'term']);
        // $t04tb = $this->leaveService->getT04tb($t04tb_info);

        $t14tb = T14tb::find($id);

        $t14tb->stime_hour = substr($t14tb->stime, 0, 2);
        $t14tb->stime_minute = substr($t14tb->stime, 2, 2);
        $t14tb->etime_hour = substr($t14tb->etime, 0, 2);
        $t14tb->etime_minute = substr($t14tb->etime, 2, 2);

        $t04tb = $t14tb->t04tb;

        $t13tbs = $t04tb->t13tbs;

        if (!$t14tb) {
            return view('admin/errors/error');
        }

        $students = ['' => '請選擇'];

        foreach($t13tbs as $t13tb){
            $students[$t13tb->idno] = $t13tb->no." ".$t13tb->m02tb->cname;
        }

        if ($t04tb->type == 13){
            $types = [
                '' => '請選擇',
                4 => '請假',
                5 => '缺課'
            ];
        }else{
            $types = [
                '' => '請選擇',
                1 => '事假',
                2 => '喪假',
                3 => '病假'
            ];
        }

        return view('admin/leave/form', compact(['t14tb', 't13tbs', 't04tb', 'students', 'types']));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {

        $t14tb_data = T14tb::find($id);
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('leave', $t14tb_data->class, $t14tb_data->term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $newT14tb = $request->only([
            'sdate',
            'edate',
            'type',
            'hour',
            'reason'
        ]);

        $newT14tb['stime'] = $request->stime_hour.$request->stime_minute;
        $newT14tb['etime'] = $request->etime_hour.$request->etime_minute;

        // 日期處理
        // $data['sdate'] = str_pad($data['sdate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['sdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['sdate']['day'] ,2,'0',STR_PAD_LEFT);
        // $data['edate'] = str_pad($data['edate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['edate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['edate']['day'] ,2,'0',STR_PAD_LEFT);
        // 時間處理
        // $data['stime'] = str_pad($data['stime']['hour'],2,'0',STR_PAD_LEFT).str_pad($data['stime']['minute'],2,'0',STR_PAD_LEFT);
        // $data['etime'] = str_pad($data['etime']['hour'],2,'0',STR_PAD_LEFT).str_pad($data['etime']['minute'],2,'0',STR_PAD_LEFT);

        //更新
        $this->leaveService->update(compact('id'), $newT14tb);
        // T14tb::find($id)->update($t14tb);

        return back()->with('result', '1')->with('message', '儲存成功!');
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
            $t14tb_data = T14tb::find($id);
            //班務流程凍結
            $freeze = $this->term_processService->getFreeze('leave', $t14tb_data->class, $t14tb_data->term);
            if($freeze == 'Y'){
                return back()->with('result', 0)->with('message', '凍結中無法刪除');
            }

            T14tb::find($id)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
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
     * 取得學員姓名及身分證字號
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getName(Request $request)
    {
        $class = $request->input('classes');
        $no = str_pad($request->input('no') ,3,'0',STR_PAD_LEFT);

        $data = T13tb::select('m02tb.idno', 'cname')->where('class', $class)->where('no', $no)->join('m02tb', 'm02tb.idno', '=', 't13tb.idno')->first();

        $result = [
            'result' => ($data)? 1 : 0,
            'cname' => (isset($data->cname))? $data->cname : '',
            'idno' => (isset($data->idno))? $data->idno : '',
        ];

        return response()->json($result);
    }

    public function suspendClassesPage($class, $term)
    {
        $t04tb_info = compact('class', 'term');
        $t04tb = $this->leaveService->getT04tb($t04tb_info);
        // 取得列表資料
        $t13tbs = $t04tb->t13tbs;

        return view('admin/leave/suspend_classes', compact('t04tb', 't13tbs'));
    }

    public function suspendClasses(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('leave', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $t04tb_info = compact(['class', 'term']);
        if ($request->suspend_type == 'all'){
            if (isset($request->hour)){
                $suspend = $this->leaveService->suspendAllByClass($t04tb_info, $request->hour);
            }
        }elseif ($request->suspend_type == 'part'){
            $suspend_infos = $request->suspend_info;
            foreach($suspend_infos as $no => $suspend_info){
                if ($suspend_infos[$no]['sdate'] <= $suspend_infos[$no]['edate']){
                    $suspend_infos[$no]['stime'] = str_pad(str_replace(":", "", $suspend_infos[$no]['stime']), 4, '0', STR_PAD_LEFT);
                    $suspend_infos[$no]['etime'] = str_pad(str_replace(":", "", $suspend_infos[$no]['etime']), 4, '0', STR_PAD_LEFT);
                }else{
                    unset($suspend_infos[$no]);
                }
            }

            $suspend = $this->leaveService->suspendPartByClass($t04tb_info, $suspend_infos);
        }

        if ($suspend){
            return back()->with('result', 1)
                         ->with('message', '儲存成功');
        }else{
            return back()->with('result', 1)
                         ->with('message', '儲存失敗');
        }


    }

}
