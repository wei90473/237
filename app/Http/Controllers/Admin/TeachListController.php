<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TeachListService;
use App\Services\User_groupService;
use App\Models\T01tb;
use App\Models\T98tb;
use App\Services\MethodService;
use DB ;
use App\Helpers\ModifyLog;

class TeachListController extends Controller
{
    /**
     * MethodController constructor.
     * @param TeachListService $teachListService
     */
    public function __construct(TeachListService $teachListService, User_groupService $user_groupService)
    {
        $this->teachListService = $teachListService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('teachlist', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('teachlist');
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){
        // 年度
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 班號
        $queryData['class'] = $request->get('class');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 班別名稱
        $queryData['name'] = $request->get('name');
        // 取得期別
        // $queryData['term'] = $request->get('term');
        // 分班名稱
        $queryData['branchname'] = $request->get('branchname');
        // 上課地點
        $queryData['sitebranch'] = $request->get('sitebranch');
        // 班別類型
        $queryData['process'] = $request->get('process');
        // 班務人員
        $queryData['sponsor'] = $request->get('sponsor');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班別性質
        $queryData['type'] = $request->get('type');
        //不要列洽借班期
        $queryData['type13'] = 'Y';
        // 類別1
        $queryData['categoryone'] = $request->get('categoryone');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得班別列表
        $queryData['group'] = 't01tb.class';
        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            return view('admin/teachlist/list', compact('queryData'));
        }
        $data = $this->teachListService->getClassList($queryData);
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/teachlist/list', compact('queryData', 'data'));

    }
    public function edit(Request $request){
        $data = $request->all();
        // var_dump($data);exit();
        unset($data['_method'],$data['_token']);
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            foreach ($data as $class => $value) {
                if(strlen($class) <8){ //針對班期
                    $old01 = t01tb::where('class',$class)->get()->toarray();
                    $old98 = t98tb::where('class',$class)->get()->toarray();
                    if($value =='N' && !isset($data['teaching'.$class])){
                        t01tb::where('class',$class)->update(array('teaching'=>'Y'));
                        $sql = DB::getQueryLog();
                        $nowdata = t01tb::where('class',$class)->get()->toarray();
                        createModifyLog('U','t01tb',$old01,$nowdata,end($sql));
                        t98tb::where('class',$class)->update(array('mark'=>''));
                        $sql = DB::getQueryLog();
                        $nowdata = t98tb::where('class',$class)->get()->toarray();
                        createModifyLog('U','t98tb',$old98,$nowdata,end($sql));
                    }elseif($value =='Y' && isset($data['teaching'.$class])){
                        t01tb::where('class',$class)->update(array('teaching'=>'N'));
                        $sql = DB::getQueryLog();
                        $nowdata = t01tb::where('class',$class)->get()->toarray();
                        createModifyLog('U','t01tb',$old01,$nowdata,end($sql));
                        t98tb::where('class',$class)->update(array('method1'=>'','method2'=>'','method3'=>'','mark'=>'Y'));
                        $sql = DB::getQueryLog();
                        $nowdata = t98tb::where('class',$class)->get()->toarray();
                        createModifyLog('U','t98tb',$old98,$nowdata,end($sql));
                    }
                    
                }
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '修改成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '修改失敗，請稍後再試!');
        }
    }


    public function maintain(Request $request)
    {
        return view('admin/teachlist/maintain');
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