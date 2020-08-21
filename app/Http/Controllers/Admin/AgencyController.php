<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AgencyService;
use App\Services\User_groupService;
use App\Models\M07tb;
use App\Models\M11tb;
use App\Models\M13tb;
use App\Models\M17tb;
use DB;


class AgencyController extends Controller
{
    /**
     * AgencyController constructor.
     * @param AgencyService $agencyService
     */
    public function __construct(AgencyService $agencyService, User_groupService $user_groupService)
    {
        $this->agencyService = $agencyService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('agency', $user_group_auth)){
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
        // 訓練機構代碼
        $queryData['agency'] = $request->get('agency');
        // 訓練機構名稱
        $queryData['name'] = $request->get('name');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        if(empty($request->all())) {
            return view('admin/agency/list', compact('queryData'));
        }
        // 取得列表資料
        $data = $this->agencyService->getAgencyList($queryData);

        return view('admin/agency/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $organ =  M13tb::select('organ')->where('kind','Y')->get()->toarray();
        $enrollorglist = M17tb::select('enrollorg','enrollname')->whereIn('organ',$organ)->orderby('enrollorg')->get();

        return view('admin/agency/form', compact('enrollorglist'));
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $check = M07tb::where('agency',$data['agency'])->first();
        if($check) return back()->with('result', '0')->with('message', '訓練機構代碼已存在，不能重複！');

        $result = M07tb::create($data);

        return redirect('/admin/agency/'.$data['agency'])->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $progid
     */
    public function show($agency)
    {
        return $this->edit($agency);
    }

    /**
     * 編輯頁
     *
     * @param $progid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($agency)
    {
        $data = M07tb::where('agency', $agency)->first( );

        if ( ! $data) {

            return view('admin/errors/error');
        }
        $organ =  M13tb::select('organ')->where('kind','Y')->get()->toarray();
        $enrollorglist = M17tb::select('enrollorg','enrollname')->whereIn('organ',$organ)->orderby('enrollorg')->get();
        return view('admin/agency/form', compact('data','enrollorglist'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $agency)
    {

        $data = $request->all();
        unset($data['_token'],$data['_method']);
        // 取得POST資料

        //更新
        M07tb::where('agency', $agency)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    /**
     * 刪除處理
     *
     * @param $agency
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($agency)
    {
        if ($agency) {

            M07tb::where('agency', $agency)->delete();

            return redirect('/admin/agency')->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
    //ajax
    public function getenrollname(Request $request=null){

        $enrollname = $request->enrollname;

        if(!isset($enrollname))  echo json_encode(array('status' => '1' , 'msg' => '查無資料'));

        $enrollorglist = M17tb::select('enrollorg','enrollname')->where('enrollname', 'LIKE', '%'.$enrollname.'%')->get()->toarray();


        if($enrollorglist){
            echo json_encode(array('status' => '0' , 'msg' => $enrollorglist));
        }else{
            echo json_encode(array('status' => '1' , 'msg' => '查無資料'));
        }

    }
}
