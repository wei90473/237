<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PunchService;
use App\Services\User_groupService;
use DB;

use App\Models\S01tb;
use App\Models\M09tb;

use DateTime;

class PunchController extends Controller
{
    /**
     * PunchController constructor.
     * @param PunchService $punchService
     */
    public function __construct(PunchService $punchService, User_groupService $user_groupService)
    {
        setProgid('punch');
        $this->punchService = $punchService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('punch', $user_group_auth)){
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
            $data = $this->punchService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->punchService->getOpenClassList($queryData);
            }
        }

        return view('admin/punch/class_list', compact('data', 'queryData', 'sponsors', 's01tbM'));

    }

    public function index(Request $request, $class, $term)
    {
        $t04tb_info = compact(['class', 'term']);
        $t04tb = $this->punchService->getT04tb($t04tb_info);

        if (empty($t04tb)){
            return back()->with('result', 1)->with('message', '找不到該班期');
        }

        $queryData = $request->only([
            "cname", "no", "dated"
        ]);
        $queryData['status'] = ['C'];
        $t84tbs = $this->punchService->getT84tbs($t04tb_info, $queryData);

        /* 較快的方法 但難以做分頁
        $t84tbs = $t84tbs->groupBy('t13tb_no');

        $t84tbs = $t84tbs->map(function($user_t84tbs){
            return $user_t84tbs->groupBy('dated')->map(function($user_dated_t84tbs){
                $user_dated_t84tbs = $user_dated_t84tbs->keyBy('status');
                $new_status = [
                    'A' => [
                        'timed' => ''
                    ],
                    'B' => [
                        'timed' => ''
                    ]
                ];
                if (isset($user_dated_t84tbs['A'])){
                    $new_dated_t84tbs = $user_dated_t84tbs['A'];
                    $new_status['A']['timed'] = $user_dated_t84tbs['A']->timed;
                }

                if (isset($user_dated_t84tbs['B'])){
                    if (!isset($new_dated_t84tbs)){
                        $new_dated_t84tbs = $user_dated_t84tbs['B'];
                    }
                    $new_status['B']['timed']  = $user_dated_t84tbs['B']->timed;
                }
                $new_dated_t84tbs->status = $new_status;
                unset($new_dated_t84tbs->timed);
                return $new_dated_t84tbs;

            });
            // return $user_t84tbs->groupBy('dated');
        });
        */

        return view('admin/punch/index', compact(['t04tb', 'queryData', 't84tbs']));

    }

}
