<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassGroupService;
use App\Services\User_groupService;
use App\Models\Class_group;
use App\Models\T01tb;
use App\Models\T67tb;
use App\Models\S04tb;
use App\Models\S06tb;
use App\Models\T06tb;
use DB;

class ClassGroupController extends Controller
{
    /**
     * ClassGroupController constructor.
     * @param ClassGroupService $classgroupService
     */
    public function __construct(ClassGroupService $classgroupService, User_groupService $user_groupService)
    {
        $this->classgroupService = $classgroupService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('class_group', $user_group_auth)){
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
        // 群組名稱
        $queryData['class_group'] = $request->get('class_group');
        // 班號
        $queryData['class'] = $request->get('class');
        // 班別名稱
        $queryData['name'] = $request->get('name');
        // 分班名稱
        // $queryData['branchname'] = $request->get('branchname');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        if(empty($request->all())) {
            return view('admin/class_group/list', compact('queryData'));
        }

        $data = $this->classgroupService->getGroupList($queryData);
        return view('admin/class_group/list', compact('data', 'queryData'));
    }

    // 新增群組
    public function create(Request $request)    {
        // 取得POST資料
        $data = $request->all();
        $groupid = Class_group::max('groupid');
        $groupid = $groupid+1;
        Class_group::create(array('groupid'=>$groupid,'class_group'=>$data['c_name']));
        return back()->with('result', '1')->with('message', '新增成功!');
    }
    /**
     * 編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($groupid){
        $data = Class_group::where('groupid',$groupid)->orderby('id')->get()->toarray();
        return view('admin/class_group/edit', compact('data'));

    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request,$groupid)    {
        // 取得POST資料
        $data = $request->all();
        // $data['class'] = substr($data['class'], 0,6);
        $check = T01tb::select('name','branchcode')->where('class',$data['class'])->first();
        // var_dump($data);exit();
        if($check){
            $name = $check['name'];
            $branchcode = $check['branchcode'];
            $check = Class_group::where('class',$data['class'])->first();
            if($check){
               return back()->with('result', '0')->with('message', '新增失敗，此班別已有群組!');
            }else{
                $class_group = Class_group::select('class_group')->where('groupid',$groupid)->first();
                Class_group::create(array(  'class_group'   =>$class_group['class_group'],
                                            'groupid'       =>$groupid,
                                            'class'         =>$data['class'],
                                            'name'          =>$name,
                                            'branchcode'    =>$branchcode));
                T01tb::where('class',$data['class'])->update(array('samecourse'=>$groupid));
                return back()->with('result', '1')->with('message', '新增成功!');
            }
        }else{
           return back()->with('result', '0')->with('message', '新增失敗，無此班別!');
        }

    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $class)    {
        $data = $request->all();
        if($data['groupid']=='') return back()->with('result', '0')->with('message', '群組名稱不可空白!');

        $updata = Class_group::where('groupid',$data['groupid'])->update(array('class_group'=>$data['class_group']));
        return back()->with('result', '1')->with('message', '更新成功!');
    }

    /**
     * 刪除處理
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id){
        $check = Class_group::where('id',$id)->first();
        if($check){
            Class_group::where('id',$id)->delete();
            T01tb::where('class',$check['class'])->update(array('samecourse'=>''));
            return back()->with('result', '1')->with('message', '刪除成功');
        }else{
            return back()->with('result', '0')->with('message', '刪除失敗，查無資料!');
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
