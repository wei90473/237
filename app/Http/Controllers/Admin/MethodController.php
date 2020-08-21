<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MethodService;
use App\Models\T98tb;
use App\Models\T08tb;
use DB;
use App\Services\Term_processService;
use App\Services\User_groupService;
use App\Helpers\ModifyLog;

class MethodController extends Controller
{
    /**
     * MethodController constructor.
     * @param MethodService $methodService
     */
    public function __construct(MethodService $methodService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        $this->methodService = $methodService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('method', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('method');
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $queryData['teaching'] = 'Y';
        // 年度
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 班號
        $queryData['class'] = $request->get('class');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 辦班院區
        $queryData['sitebranch'] = $request->get('sitebranch');
        // 班別名稱
        $queryData['name'] = $request->get('name');
        // 取得期別
        $queryData['term'] = $request->get('term');
        // 分班名稱**
        $queryData['branchname'] = $request->get('branchname');
        // 班別類型
        $queryData['process'] = $request->get('process');
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
        $queryData['edate'] = $request->get('edate');
        $queryData['sdate2'] = $request->get('sdate2');
        $queryData['edate2'] = $request->get('edate2');
        $queryData['sdate3'] = $request->get('sdate3');
        $queryData['edate3'] = $request->get('edate3');
        //不要列洽借班期
        $queryData['type13'] = 'Y';
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 教學方法處理專用
        $queryData['method'] = 'Y';
        // 取得列表資料
     //   $data = $this->methodService->getMethodList($queryData);
        // 取得班別列表
        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData2['class'] = $sess['class'];
                $queryData2['term'] = $sess['term'];
                $queryData2['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->methodService->getClassList($queryData2);
                return view('admin/method/list', compact('queryData', 'data'));
            }
            return view('admin/method/list', compact('queryData'));
        }
        $data = $this->methodService->getClassList($queryData);
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/method/list', compact('queryData', 'data'));
    }

    /**
     * 編輯
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $class_term)
    {

        $term = $queryData['term'] = substr($class_term, -2);
        $class = $queryData['class'] = substr($class_term, 0,-2);
        $queryData = $this->methodService->getClassList($queryData);
        $queryData = $queryData[0];
        $filter  = is_null($request->get('filter') )? '1': $request->get('filter');
        $data = $this->methodService->getCurriculumList($class,$term,$filter);
  
        //$data = $this->methodService->getMethodRowData($id);
        if ( count($data)>0 ) {
            return view('admin/method/form', compact('queryData','data'));
        }else{
            return back()->with('result', '0')->with('message', '無符合條件之資料');
        }
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $data = $request->all();

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('method', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法更新');
        }

        $list = T08tb::select('id','course','idno')->where('class',$data['class'])->where('term',$data['term'])->get()->toArray();
        $indata = array();
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            //寫入
            foreach ($list as $key => $value) {
                $indata['mark'] = $request->input('mark'.$value['course'].$value['idno']);
                if ($indata['mark'] == 'Y') {
                    $indata['method1'] = '';
                    $indata['method2'] = '';
                    $indata['method3'] = '';
                }else{
                    $indata['method1'] = $request->input('method1'.$value['course'].$value['idno']);
                    $indata['method2'] = $request->input('method2'.$value['course'].$value['idno']);
                    $indata['method3'] = $request->input('method3'.$value['course'].$value['idno']);
                }
                $indata['class'] = $data['class'];
                $indata['term'] = $data['term'];
                $indata['course'] = $value['course'];
                $indata['idno'] = $value['idno'];
                $olddata = T98tb::where('class',$data['class'])->where('term',$data['term'])->where('course', $value['course'])->where('idno', $value['idno'])->get()->toarray();
                T98tb::where('class',$data['class'])->where('term',$data['term'])->where('course', $value['course'])->where('idno', $value['idno'])->delete();
                $sql = DB::getQueryLog();
                createModifyLog('D','t98tb',$olddata,'',end($sql));
                T98tb::create($indata);
                $sql = DB::getQueryLog();
                $nowdata = T98tb::where('class',$indata['class'])->where('term',$indata['term'])->where('course', $indata['course'])->where('idno', $indata['idno'])->get()->toarray();
                createModifyLog('I','t98tb','',$nowdata,end($sql));
                unset($indata);
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '儲存成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '更新失敗，請稍後再試!');
        }

    }

    /**
     * 取得期別
     *
     * @param $class
     * @return string
     */
    // public function getTerm(Request $request)
    // {
    //     $class = $request->input('classes');

    //     $selected = $request->input('selected');

    //     if (is_numeric( mb_substr($class, 0, 1))) {

    //         $data = DB::select('SELECT DISTINCT term FROM t04tb WHERE class = \''.$class.'\' ORDER BY `term`');
    //     } else {

    //         $data = DB::select('SELECT DISTINCT term FROM t38tb WHERE meet = \''.$class.'\' ORDER BY `term`');
    //     }

    //     $result = '';

    //     foreach ($data as $va) {
    //         $result .= '<option value="'.$va->term.'"';
    //         $result .= ($selected == $va->term)? ' selected>' : '>';
    //         $result .= $va->term.'</option>';
    //     }

    //     return $result;
    // }
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
