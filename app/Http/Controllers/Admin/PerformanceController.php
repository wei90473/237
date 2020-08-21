<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PerformanceService;
use App\Services\MethodService;
use App\Services\User_groupService;
use App\Models\T06tb;
use App\Models\T36tb;
use App\Models\T08tb;
use App\Models\T04tb;
use App\Models\T66tb;
use App\Models\M14tb;
use App\Models\M25tb;
use DB;


class PerformanceController extends Controller
{
    private $weeklist = array('1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','7'=>'日');
    /**
     * PerformanceController constructor.
     * @param PerformanceService $performanceServiceService
     */
    public function __construct(PerformanceService $performanceService,MethodService $methodService, User_groupService $user_groupService)
    {
        $this->performanceService = $performanceService;
        $this->methodService = $methodService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('performance', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
            // dd($user_data);
            // dd(\session());
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
        // 年度
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 班號
        $queryData['class'] = $request->get('class');
        // 班別名稱
        $queryData['name'] = $request->get('name');
        // 分班名稱
        $queryData['branchname'] = $request->get('branchname');
        // 上課地點
        $queryData['sitebranch'] = $request->get('sitebranch');
        // 取得期別
        $queryData['term'] = $request->get('term');
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
        // 類別1
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
        $queryData['select'] = $request->get('select');
        // 取得列表資料
        // if($queryData['select'] == '1'){ //自行建檔
        //     $data = $this->performanceService->getPerformanceList($queryData);
        // }elseif($queryData['select'] == '2'){  //電子檔匯入
        //     $data = T66tb::select('class')->where('class','like', $queryData['yerly'].'%')->get()->toArray();
        // }else{
        //     $queryData['choices'] = $this->_get_year_list();
        //     return view('admin/performance/list', compact('queryData'));
        // }
        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData2['class'] = $sess['class'];
                $queryData2['term'] = $sess['term'];
                $queryData2['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->performanceService->getPerformanceList($queryData2);
                return view('admin/performance/list', compact('data', 'queryData'));
            }
            return view('admin/performance/list', compact('queryData'));
        }
        $data = $this->performanceService->getPerformanceList($queryData);
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/performance/list', compact('data', 'queryData'));
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
        $queryData = $this->performanceService->getPerformanceList($queryData);
        $queryData = $queryData[0];
        // var_dump($queryData);exit();
        $data = T06tb::select('class','term','course','name','hour','date','stime','etime','site','location')->where('class',$class)->where('term',$term)->orderBy('date')->orderBy('stime')->get();
        if (!$data||empty($data)) {
            return back()->with('result', '0')->with('message', '查無課程資料！');
        }

        return view('admin/performance/form', compact('queryData'));
    }

     /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request,$class_term){
        $term = $queryData['term'] = substr($class_term, -2);
        $class = $queryData['class'] = substr($class_term, 0,-2);
        $data = $request->all();
        unset($data['_method'],$data['_token']);
        $updata = T04tb::where('class',$class)->where('term',$term)->update($data);
        if($updata){
            return back()->with('result', '1')->with('message', '更新成功');
        }else{
            return back()->with('result', '0')->with('message', '更新失敗');
        }

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
