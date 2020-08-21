<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SignupService;
use App\Services\Term_processService;
use App\Services\User_groupService;

use App\Models\T04tb;
use App\Models\T51tb;
use App\Models\S01tb;
use App\Models\M09tb;

use DB;
use Auth;
use DateTime;

class SignupController extends Controller
{
    /**
     * SignupController constructor.
     * @param SignupService $signupService
     */
    public function __construct(SignupService $signupService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        setProgid('signup');
        $this->signupService = $signupService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('signup', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function index(Request $request)
    {
        /*
        $queryData = $request->only([
            'yerly',                // 年度
            'class',                // 班號
            'class_name',           // 班級名稱
            'sub_class_name',       // 分班名稱
            'term',                 // 期別
            'class_location',       // 上課地點
            'branch',               // 辦班院區
            'process',              // 班別類型
            'entrust_train_unit',   // 委訓單位
            'worker',               // 班務人員
            'train_start_date',     // 開訓日期範圍(起)
            'train_end_date',       // 開訓日期範圍(訖)
            'graduate_start_date',  // 結訓日期範圍(起)
            'graduate_end_date',    // 結訓日期範圍(訖)
            '_paginate_qty',        // 分頁資料數量
        ]);

        $data = [];
        if ($request->all()){
            $data = $this->signupService->getT04tbs($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData2['class'] = $sess['class'];
                $queryData2['term'] = $sess['term'];
                $queryData2['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->signupService->getT04tbs($queryData2);
            }
        }
        */

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
            $data = $this->signupService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->signupService->getOpenClassList($queryData);
            }
        }        

        return view('admin/signup/index', compact('queryData', 'data', 'sponsors', 's01tbM'));
    }


    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $class, $term)
    {
        // 取得班別
        $queryData['class'] = $class;
        // 取得期別
        $queryData['term'] = $term;
        // 取得列表資料
        $data = $this->signupService->getSignupList($queryData);
        // dd($data);
        // 測試用資料
//        $data = array(
//            json_decode('{"\u6a5f\u95dc\u4ee3\u78bc":"A00000000A","\u6a5f\u95dc\u540d\u7a31":"\u884c\u653f\u9662","\u5e74\u5ea6\u5206\u914d\u4eba\u6578":1,"\u7dda\u4e0a\u5206\u914d\u4eba\u6578":1}'),
//            json_decode('{"\u6a5f\u95dc\u4ee3\u78bc":"301000000A","\u6a5f\u95dc\u540d\u7a31":"\u5167\u653f\u90e8","\u5e74\u5ea6\u5206\u914d\u4eba\u6578":2,"\u7dda\u4e0a\u5206\u914d\u4eba\u6578":2}'),
//            json_decode('{"\u6a5f\u95dc\u4ee3\u78bc":"303000000B","\u6a5f\u95dc\u540d\u7a31":"\u5916\u4ea4\u90e8","\u5e74\u5ea6\u5206\u914d\u4eba\u6578":0,"\u7dda\u4e0a\u5206\u914d\u4eba\u6578":0}'),
//            json_decode('{"\u6a5f\u95dc\u4ee3\u78bc":"305000000C","\u6a5f\u95dc\u540d\u7a31":"\u570b\u9632\u90e8","\u5e74\u5ea6\u5206\u914d\u4eba\u6578":1,"\u7dda\u4e0a\u5206\u914d\u4eba\u6578":1}'),
//            json_decode('{"\u6a5f\u95dc\u4ee3\u78bc":"307000000D","\u6a5f\u95dc\u540d\u7a31":"\u8ca1\u653f\u90e8","\u5e74\u5ea6\u5206\u914d\u4eba\u6578":6,"\u7dda\u4e0a\u5206\u914d\u4eba\u6578":6}'),
//        );
        // $t01tb = $this->signupService->getT01tb($class);
        // 取得派訓日期
        $dateData = $this->signupService->getDateData($queryData);
        // 取得課程列表
        // $classList = $this->signupService->getClassList($queryData);
        $t04tb_info = [
            "class" => $class,
            "term" => $term
        ];
        $t04tb = $this->signupService->getT04tb($t04tb_info);
        $t01tb = $t04tb->t01tb;
        $online_apply_organs = $t04tb->online_apply_organs;

        return view('admin/signup/form', compact('data', 'queryData', 'classList', 'dateData', 't04tb','online_apply_organs'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // 取得班別,期數
        $class = $request->input('class');
        $term = $request->input('term');

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('signup_edit_type1', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }
        $freeze = $this->term_processService->getFreeze('signup_edit_type2', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        // 取得日期
        $data = $request->only([
            'pubsdate',
            'pubedate',
            'notice',
            'apply_code',
            'apply_password'
        ]);

        // 更新T04tb
        T04tb::where('class', $class)->where('term', $term)->update($data);
        // 更新T51tb
        T51tb::where('class', $class)->where('term', $term)->update(['pubsdate' => $data['pubsdate'], 'pubedate' => $data['pubedate']]);

        $value = is_array($request->input('value'))? $request->input('value') : array();
        $this->signupService->updateT51tb($class, $term, $data['pubsdate'], $data['pubedate'], $value);

        return back()->with('result', '1')->with('message', '儲存成功!');
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
    /*
    *  更新委訓班線上報名資訊
    */
    public function updateProcess2(Request $request, $class, $term)
    {

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('signup_edit_type1', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }
        $freeze = $this->term_processService->getFreeze('signup_edit_type2', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $class_info = [
            "class" => $class,
            "term" => $term
        ];
        $t04tb = $request->only([
            "apply_code",         // 報名代碼
            "apply_password",     // 報名密碼
            "pubsdate",           // 報名開始時間
            "pubedate",           // 報名結束時間
            "apply_limit",        // 報名身份
            "assign_type",        // 名額分配方式
            "officially_enroll",  // 正取
            "secondary_enroll"    // 備取
        ]);

        if ($t04tb['apply_limit'] != 1 && $t04tb['assign_type'] == 2){
            return back()->with('result', '0')->with('message', '僅限公務人員可以依機關分配!')->withInput();
        }

        $update = $this->signupService->updateT04tb($class_info, $t04tb);

        if ($update){
            return back()->with('result', '1')->with('message', '儲存成功!');
        }else{
            return back()->with('result', '0')->with('message', '儲存失敗!');
        }

    }
}
