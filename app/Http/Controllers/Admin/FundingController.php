<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FundingService;
use App\Services\Term_processService;
use App\Services\User_groupService;

use App\Models\S01tb;
use App\Models\M09tb;

use DateTime;

class FundingController extends Controller
{
    /**
     * ForumController constructor.
     * @param FundingService $forumService
     */
    public function __construct(FundingService $fundingService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        setProgid('funding');
        $this->fundingService = $fundingService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('funding', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function classList(Request $request)
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

        $data = null;
        if ($request->all()){
            $data = $this->fundingService->getT07tbs($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->fundingService->getT07tbs($queryData);
            }
        }
        $fields = config('database_fields');

        return view('admin/funding/class_list', compact('data', 'queryData', 'fields', 'sponsors', 's01tbM'));
    }

    public function edit($class, $term, $type){

        $t07tb_info = compact(['class', 'term', 'type']);
        $t07tb = $this->fundingService->getT07tb($t07tb_info);

        if (empty($t07tb)) return back()->with('result', 0)->with('message', '找不到該資料');

        $t04tb = $t07tb->t04tb;

        return view('admin/funding/formEstimate', compact(['t07tb', 't04tb']));
    }

    public function update(Request $request, $class, $term, $type){
        $t07tb_info = compact(['class', 'term', 'type']);
        $t07tb = $this->fundingService->getT07tb($t07tb_info);

        if (empty($t07tb)){
            return back();
        }        //班務流程凍結
        
        $freeze = $this->term_processService->getFreeze('class_schedule', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('funding_edit_type1', '凍結中無法修改');
        }

        $fields = [
            "sincnt",       // 單人房人數
            "donecnt",      // 雙人房人數
            "vipcnt",       // 行政套房住宿人數
            "meacnt",       // 早餐人數
            "luncnt",       // 午餐人數
            "dincnt",       // 晚餐人數
            "motorcnt",     // 汽車人次
            "trainamt",     // 火車金額
            "planeamt",     // 飛機金額
            // "noteamt",      // 稿酬
            "speakamt",     // 講演費
            "drawcnt",      // 課程規劃次數
            "drawunit",     // 課程規劃單價
            "pencnt",       // 文具人份
            "placecnt",     // 場地租借場次
            "placeunit",    // 場地租借單價
            "daytype",      // 校外教學天數
            "inscnt",       // 保險費人數
            "actcnt",       // 活動費人數
            'caramt',       // 租車費金額
            "teacnt",       // 茶水費場次
            "prizecnt",     // 獎品費班次
            "birthcnt",     // 慶生活動費班次
            "unioncnt",     // 聯誼活動班次
            "setcnt",       // 場地佈置費班次
            "dishcnt",      // 加菜金次數
            "otheramt1",    // 其他一金額
            "otheramt2",    // 其他二金額
            "kind",         // 科目
            "shipamt"
        ];

        if ($type != 2){
            $hourly_fee_info = [
                "inlecthr",     // 內聘鐘點費時數
                "burlecthr",    // 總處鐘點費時數
                "outlecthr",    // 外聘鐘點費時數
                "othlecthr",    // 其他鐘點費時數
                "othlectunit"   // 其他鐘點費單價
            ];
            $fields = array_merge($fields, $hourly_fee_info);
        }

        $new_t07tb = $request->only($fields);

        $t07tb_info = compact(['class', 'term', 'type']);

        $update = $this->fundingService->updateT07tb($t07tb, $new_t07tb);

        if ($update){
            return back()->with('result', 1)->with('message', '儲存成功');
        }else{
            return back()->with('result', 0)->with('message', '儲存失敗');
        }
    }

    public function selectProbably(Request $request)
    {
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
            'training_start_date',
            'training_end_date'
        ]);

        $data = [];
        if ($request->all()){
            $data = $this->fundingService->getT04tbs($queryData); // 取得開班資料
        }

        $fields = config('database_fields');
        $type = "probably";

        return view('admin/funding/class_list_select', compact('data', 'queryData', 'fields', 'type'));
    }

    public function selectConclusion(Request $request)
    {
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
            'training_start_date',
            'training_end_date'
        ]);

        $data = [];
        if ($request->all()){
            $data = $this->fundingService->getT04tbs($queryData); // 取得開班資料
        }

        $fields = config('database_fields');
        $type = "conclusion";

        return view('admin/funding/class_list_select', compact('data', 'queryData', 'fields', 'type'));
    }

    /*
        產生概算
    */
    public function batchInsertProbably(Request $request)
    {
        $select_class = $this->fundingService->splitClassString($request->select);

        if (empty($select_class)){
            return back()->with('result', 0)
                         ->with('message', '請選擇班期');
        }

        $insert_probably = $this->fundingService->batchInsertProbably($select_class);

        if ($insert_probably){
            return back()->with('result', 1)
                         ->with('message', '批次新增成功');
        }else{
            return back()->with('result', 0)
                         ->with('message', '批次新增失敗');
        }
    }

    /*
        產生結算
    */
    public function batchInsetConclusion(Request $request)
    {
        $select_class = $this->fundingService->splitClassString($request->select);
        $insert_probably = $this->fundingService->batchInsertConclusion($select_class);

        if ($insert_probably){
            return back()->with('result', 1)
                         ->with('message', '結算成功');
        }else{
            return back()->with('result', 0)
                         ->with('message', '結算失敗');
        }
    }

    /*
        更新單價功能
        更新某段範圍內的所有單價
    */
    public function updateUnitPrice(Request $request)
    {

        $user_data = \Auth::user();
        $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);

        if(!in_array('fundingUpdateUnitPrice', $user_group_auth)){
            return redirect('admin/funding/class_list')->with('result', '0')->with('message', $this->user_group_msg);
        }

        $queryData = $request->only(['updateSdate', 'updateEdate']);
        $this->validate($request, ['updateSdate' => 'required', 'updateEdate' => 'required'], ['updateSdate.required' => '必須輸入開課日期(起)', 'updateEdate.required' => '必須輸入開課日期(迄)']);
        $result = $this->fundingService->updateUnitPrice($queryData);
        if ($result){
            return back()->with('result', 1)
                         ->with('message', '更新單價成功');
        }else{
            return back()->with('result', 0)
                         ->with('message', '更新單價失敗');
        }
    }

    public function delete($class, $term, $type)
    {
        $delete = $this->fundingService->deleteT07tb($class, $term, $type);
        if ($delete){
            return redirect('/admin/funding/class_list')->with('result', 1)
                                                        ->with('message', '刪除成功');
        }else{
            return back()->with('result', 0)
                         ->with('message', '刪除失敗');
        }        
    }
}
