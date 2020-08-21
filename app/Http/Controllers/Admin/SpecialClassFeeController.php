<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SpecialClassFeeService;
use App\Services\Term_processService;
use Auth;
use App\Services\User_groupService;

use App\Models\S01tb;
use App\Models\M09tb;
use DateTime;

class SpecialClassFeeController extends Controller
{
    /**
     * SiteSurveyOldController constructor.
     * @param SiteSurveyOldService $siteSurveyOldService
     */
    public function __construct(SpecialClassFeeService $specialClassFeeService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        setProgid('special_class_fee');

        $this->specialClassFeeService = $specialClassFeeService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('special_class_fee', $user_group_auth)){
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

        $data = [];
        if ($request->all()){
            $data = $this->specialClassFeeService->getT04tbs($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->specialClassFeeService->getT04tbs($queryData);
            }
        }

        $ignoreQueryField = ['t01tb' => ['process' => true]];
        $fields = config('database_fields');

        return view('admin/special_class_fee/class_list', compact('data', 'queryData', 'sponsors', 's01tbM', 'ignoreQueryField', 'fields'));

    }

    public function edit($class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->specialClassFeeService->getT04tb($t04tb_info); // 取得開班資料
        $action = "edit";

        return view('admin/special_class_fee/form', compact(['t04tb', 'action']));
    }

    public function update(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('special_class_fee', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->specialClassFeeService->getT04tb($t04tb_info); // 取得開班資料

        if (!empty($t04tb)){
            if ($t04tb->t13tbs->count() < 40){
                $special_class_fee = $request->only([
                    // 'service_fee_quantity',             // 業務費數量
                    // 'service_fee_days',                 // 業務費天數
                    // 'service_fee_unit_price',           // 業務費單價
                    // 'service_fee_budget',               // 業務費預算
                    // 'hourly_fee_quantity',              // 鐘點費數量
                    // 'hourly_fee_budget',                // 鐘點費預算
                    // 'extra_quantity',                   // 額外項單價
                    // 'extra_budget',                     // 額外項預算
                    'oh_hourly_fee_quantity',           // 外聘鐘點費數量
                    // 'oh_hourly_fee_unit_price',         // 外聘鐘點費單價
                    // 'oh_hourly_fee_budget',             // 外聘鐘點費預算
                    'ohbe_hourly_fee_quantity',         // 外聘隸屬鐘點費數量
                    // 'ohbe_hourly_fee_unit_price',    // 外聘隸屬鐘點費單價
                    // 'ohbe_hourly_fee_budget',           // 外聘隸屬鐘點費預算
                    'ih_hourly_fee_quantity',           // 內聘鐘點費數量
                    // 'ih_hourly_fee_unit_price',      // 內聘鐘點費單價
                    // 'ih_hourly_fee_budget',             // 內聘鐘點費預算
                    'ass_hourly_fee_quantity',          // 助教鐘點費數量
                    // 'ass_hourly_fee_unit_price',     // 助教鐘點費單價
                    // 'ass_hourly_fee_budget',            // 助教鐘點費預算
                    'business_pay_quantity',            // 業務支出數量
                    // 'business_pay_unit_price',       // 業務支出單價
                    // 'business_pay_budget',              // 業務支出預算
                    'food_expenses_quantity',           // 伙食費數量
                    // 'food_expenses_days',               // 伙食費天數
                    // 'food_expenses_unit_price',         // 伙食費單價
                    // 'food_expenses_budget',             // 伙食費預算
                    'rent_car_quantity',                // 租車數量
                    'rent_car_unit_price',              // 租車單價
                    // 'rent_car_budget',                  // 租車預算
                    'insurance_quantity',               // 保險費數量
                    'insurance_unit_price',             // 保險費單價
                    // 'insurance_budget',                 // 保險費預算
                    'reward_quantity',                  // 獎品費數量
                    'reward_unit_price',                // 獎品費單價
                    // 'reward_budget'                     // 獎品費預算
                ]);
            }elseif ($t04tb->t13tbs->count() >= 40){
                $special_class_fee = $request->only([
                    'service_fee_quantity',             // 業務費數量
                    // 'service_fee_days',                 // 業務費天數
                    // 'service_fee_unit_price',           // 業務費單價
                    // 'service_fee_budget',               // 業務費預算
                    'hourly_fee_quantity',              // 鐘點費數量
                    'hourly_fee_budget',                // 鐘點費預算
                    // 'extra_quantity',                   // 額外項單價
                    'extra_budget',                     // 額外項預算
                    'oh_hourly_fee_quantity',           // 外聘鐘點費數量
                    // 'oh_hourly_fee_unit_price',         // 外聘鐘點費單價
                    // 'oh_hourly_fee_budget',             // 外聘鐘點費預算
                    'ohbe_hourly_fee_quantity',         // 外聘隸屬鐘點費數量
                    // 'ohbe_hourly_fee_unit_price',    // 外聘隸屬鐘點費單價
                    // 'ohbe_hourly_fee_budget',           // 外聘隸屬鐘點費預算
                    'ih_hourly_fee_quantity',           // 內聘鐘點費數量
                    // 'ih_hourly_fee_unit_price',      // 內聘鐘點費單價
                    // 'ih_hourly_fee_budget',             // 內聘鐘點費預算
                    'ass_hourly_fee_quantity',          // 助教鐘點費數量
                    // 'ass_hourly_fee_unit_price',     // 助教鐘點費單價
                    // 'ass_hourly_fee_budget',            // 助教鐘點費預算
                    // 'business_pay_quantity',            // 業務支出數量
                    // 'business_pay_unit_price',       // 業務支出單價
                    // 'business_pay_budget',              // 業務支出預算
                    'food_expenses_quantity',           // 伙食費數量
                    // 'food_expenses_days',               // 伙食費天數
                    // 'food_expenses_unit_price',         // 伙食費單價
                    // 'food_expenses_budget',             // 伙食費預算
                    'rent_car_quantity',                // 租車數量
                    'rent_car_unit_price',              // 租車單價
                    // 'rent_car_budget',                  // 租車預算
                    'insurance_quantity',               // 保險費數量
                    'insurance_unit_price',             // 保險費單價
                    // 'insurance_budget',                 // 保險費預算
                    'reward_quantity',                  // 獎品費數量
                    'reward_unit_price',                // 獎品費單價
                    // 'reward_budget'                     // 獎品費預算
                ]);
            }

            $status = $this->specialClassFeeService->storeSpecialClassFee($t04tb, $special_class_fee);
            if ($status){
                return back()->with('result', 1)->with('message', '儲存成功');
            }else{
                return back()->with('result', 0)->with('message', '儲存失敗');
            }            
        }
    }

    public function computeFee(Request $request)
    {
        $this->validate($request, ['computeClassNo' => 'required', 'computeTerm' => 'required']);
        $t04tbKey = [
            'class' => $request->computeClassNo,
            'term' => $request->computeTerm,
        ];

        $t04tb = $this->specialClassFeeService->getT04tb($t04tbKey);
        if (empty($t04tb)){
            return back()->with('result', 0)->with('message', '找不到該班期');
        }

        if (!empty($t04tb->specailClassFee)){
            return back()->with('result', 0)->with('message', '該班期已計算請至編輯修改');
        }

        $insert = $this->specialClassFeeService->computeFeeDefault($t04tb);

        if ($insert){
            return redirect("/admin/special_class_fee/edit/{$t04tb->class}/{$t04tb->term}")->with('result', 1)->with('message', '計算完成');;
        }else{
            return back()->with('result', 0)->with('message', '計算失敗');
        }        
    }
}