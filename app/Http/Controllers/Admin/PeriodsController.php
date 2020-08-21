<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PeriodsService;
use App\Services\User_groupService;
use App\Models\T03tb;
use App\Models\T51tb;
use App\Models\T01tb;
use App\Models\TmpAssignResult;
use DB;


class PeriodsController extends Controller
{
    /**
     * PeriodsControllerController constructor.
     * @param PeriodsService $periodsService
     */
    public function __construct(PeriodsService $periodsService, User_groupService $user_groupService)
    {
        setProgid('periods');

        $this->periodsService = $periodsService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('periods', $user_group_auth)){
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
        // createModifyLog(1,2,3);
        $request->only(['A 欄位','B 欄位']);
        $queryData = $request->only([
            'yerly',  // 年度
            'class',  // 班號
            'class_name',   // 班別名稱
            'branch',   // 辦班院區
            '_sort_field', // 排序欄位
            '_sort_mode', // 排序模式
            '_paginate_qty'  // 每頁資料筆數
        ]);
        // 每頁幾筆
        $queryData['_paginate_qty'] = (empty($queryData['_paginate_qty'])) ? 20 : $queryData['_paginate_qty'];

        $data = [];
        if ($request->all()){
            // 取得列表資料
            $data = $this->periodsService->getClassList($queryData);
            // dd($data[0]->t03tbs->pluck('quota')->sum());
        }

        return view('admin/periods/list', compact('data', 'queryData'));
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
     * 編輯頁
     *
     * @param $class
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($class)
    {
        $t01tb = T01tb::select('class', 'name', 'quotatot')->where('class', $class)->first();

        if (empty($t01tb)) {
            return view('admin/errors/error');
        }

        $t02tbs = $t01tb->t02tbs()->with(['m13tb'])->get()->sortBy('m13tb.rank')->keyBy('organ');
        $t03tbs = $t01tb->t03tbs->groupBy('term');

        $online_updated_t03tbs = $t03tbs->map(function($t03tb_group){
            $online_update = $t03tb_group->pluck('is_online_update', 'is_online_update');
            return isset($online_update[1]);
        });

        $t03tbs = $t03tbs->map(function($t03tb_group){
            return $t03tb_group->pluck('quota', 'organ');
        });



        return view('admin/periods/form', compact('t01tb', 't02tbs', 't03tbs', 'online_updated_t03tbs'));
    }

    /**
     * 取得分配值
     *
     * @param $class
     * @param $max
     * @return array
     */
    public function getQuota($class, $enrollorgs)
    {
        $this->max = 0;

        $result = array();

        $data = TmpAssignResult::where('class', $class)->orderBy('term')
                               ->whereIn('organ', $enrollorgs)
                               ->get();

        foreach ($data as $va) {

            $result[$va->organ][(int)$va->term] = $va->toArray();

            $this->max = ((int)$va->term > $this->max)? (int)$va->term : $this->max;
        }

        return $result;
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $class)
    {
        $this->validate($request, [
            'quotas' => 'required'
        ]);

        $t01tb = T01tb::find($class);

        $store = $this->periodsService->storeT03tbs($t01tb, $request->quotas);

        if (is_array($store)){
            return back()->with('result', 0)->with('message', "機關 {$store['organ']} 人數 {$store['sum']} 超過可分配人數")->withInput();
        }

        return back()->with('result', '更新成功')->with('message', '更新成功');
    }

    /**
     * 批次線上更新
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function online_update(Request $request)
    {
        $queryData = $request->only(['yerly', 'times']);

        /*
            yerly -> 年度
            class -> 班號
            branch -> 辦班院區
            classes_name -> 班別名稱
            sort_field -> 排序欄位
            _sort_mode -> 排序模式
            _paginate_qty -> 每頁資料筆數
        */

        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $datas = collect([]);

        if($request->all()){
            $datas = $this->periodsService->getClassList($queryData);
        }

        $t01tbs_class = $datas->pluck('class');
        $t03tbs = $this->periodsService->getOnlineUpdateStatus($t01tbs_class);

        $is_online_update = $t03tbs->groupBy('class')->map(function($t03tb_group){
                                return $t03tb_group->groupBy('term')->map(function($term_group){
                                    $is_online_update = $term_group->pluck('is_online_update', 'is_online_update');
                                    return isset($is_online_update[1]);
                                });
                            });

        $terms = $t03tbs->groupBy('term')->keys();

        return view("admin/periods/online_update", compact('datas', 'queryData', 'terms', 'is_online_update'));
    }

    /**
     * 分配
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assign(Request $request)
    {
        $result = [];

        $error_classes = [];

        foreach ($request->assign as $class => $assign_info){
            if (!isset($assign_info['assign_num'])) continue;
            if (empty($assign_info['start_term']) || $assign_info['start_term'] >= 100) continue;
            if (empty($assign_info['end_term']) || $assign_info['end_term'] >= 100) continue;
            $is_online_update = $this->periodsService->checkOnlineUpdate($class, $assign_info['start_term'], $assign_info['end_term']);
            if ($is_online_update){
                $error_classes[$class] = "already online update";
                continue;
            }

            $result[$class] = $this->periodsService->assign($class, (int)$assign_info['assign_num'], (int)$assign_info['start_term'], (int)$assign_info['end_term']);
        }

        $error_classes = array_keys($error_classes);
        $error_message = join("<br>", $error_classes);

        if (!empty($error_message)){
            return back()->with('result', 1)->with('html_message', '分配成功，但以下班期包含已線上更新的期數，請重新調整後在進行分配<br>'.$error_message);
        }

        return back()->with('result', 1)->with('html_message', '分配成功');
    }

    public function exec_online_update(Request $request)
    {
        if (!empty($request->online_update)){
            if (isset($request->exec_update)){
                $online_update = collect($request->online_update)->map(function($terms, $class){
                    return collect($terms);
                });

                $exec_result = $this->periodsService->exec_online_update($online_update);
                $result = 1;
                $message = $request->exec_update."成功";

            }else if (isset($request->exec_remove)){
                $this->periodsService->exec_remove($request->update);
                $message = $request->exec_remove;
                $result = 1;
                $message .= "成功";
            }

        }else{
            $result = 0;
            $message = "未選取期別";
        }

        return back()->with('result', $result)->with('html_message', $message);
    }
    /*
        檢查是否已往下分配人數
    */
    public function checkAssignOtherOrgan(Request $request)
    {
        $this->validate($request,[
            'online_update' => 'required'
        ]);

        $result = $this->periodsService->checkAssignOtherOrgan($request->online_update);

        $result = $result->map(function($t04tb){
            return $t04tb->class . ' 第' . $t04tb->term.'期';
        });
        return response()->json(["message" => join(", ", $result->toArray())]);
    }
}

