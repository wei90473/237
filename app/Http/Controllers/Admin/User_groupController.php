<?php
namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Models\User_group;
use App\Models\User_group_auth;
use Hash;
use Session;
use DB ;

class User_groupController extends Controller
{
    /**
     * User_groupController constructor.
     * @param User_groupService $user_groupService
     */
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
        	$user_data = \Auth::user();
        	$user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
        	if(in_array('user_group', $user_group_auth)){
        		return $next($request);
        	}else{
        		return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
        	}
        });
        setProgid('user_group');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // $value = session('1');
        // dd($value);
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;

        $data = $this->user_groupService->getUser_groupList($queryData);

        return view('admin/user_group/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $user_group_auth = array();
        return view('admin/user_group/form', compact('user_group_auth'));
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
        // dd($data);

        $fields = array(
            'name' => $data['name'],
        );
        $result = User_group::create($fields);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('user_group')){
            $nowdata = User_group::where('id', $result->id)->get()->toarray();
            createModifyLog('I','User_group','',$nowdata,end($sql));
        }

        // dd($result);
        if(!empty($data['auth'])){
            foreach($data['auth'] as $row){
                $fields = array(
                    'user_group_id' => $result->id,
                    'menu' => $row,
                );
                User_group_auth::create($fields);
            }

            $sql = DB::getQueryLog();
	        if(checkNeedModifyLog('user_group')){
	            $nowdata = User_group_auth::where('user_group_id', $result->id)->get()->toarray();
	            createModifyLog('I','User_group_auth','',$nowdata,end($sql));
	        }

        }

        return redirect('/admin/user_group/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $id
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * 編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $user_group_auth = $this->user_groupService->getUser_group_auth($id);

        $data = User_group::find($id);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();

        return view('admin/user_group/form', compact('data', 'user_group_auth'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        $fields = array(
            'name' => $data['name'],
        );

        if(checkNeedModifyLog('user_group')){
            $olddata = User_group::where('id', $id)->get()->toarray();
        }

        $result = User_group::where('id', $id)->update($fields);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('user_group')){
            $nowdata = User_group::where('id', $id)->get()->toarray();
            createModifyLog('U','User_group',$olddata,$nowdata,end($sql));
        }

        $user_group_auth = $this->user_groupService->getUser_group_auth($id);
        if(!empty($user_group_auth)){

        	if(checkNeedModifyLog('user_group')){
                $olddata = User_group_auth::where('id', $id)->get()->toarray();
            }

            User_group_auth::find($id)->delete();

            $sql = DB::getQueryLog();
            if(checkNeedModifyLog('user_group')){
                createModifyLog('D','User_group_auth',$olddata,'',end($sql));
            }

        }
        // dd($result);
        if(!empty($data['auth'])){
            foreach($data['auth'] as $row){
                $fields = array(
                    'user_group_id' => $id,
                    'menu' => $row,
                );
                User_group_auth::create($fields);
            }

            $sql = DB::getQueryLog();
	        if(checkNeedModifyLog('user_group')){
	            $nowdata = User_group_auth::where('user_group_id', $result->id)->get()->toarray();
	            createModifyLog('I','User_group_auth','',$nowdata,end($sql));
	        }

        }

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {

        if ($id) {

            if($id == '3' || $id == '4' || $id == '6'){
                return back()->with('result', '0')->with('message', '系統管理員、辦班人員、場地管理 無法刪除!');
            }

            $data = User_group::find($id);
            if($data){

            	if(checkNeedModifyLog('user_group')){
	                $olddata = User_group_auth::where('id', $id)->get()->toarray();
	            }

                User_group_auth::find($id)->delete();

                $sql = DB::getQueryLog();
	            if(checkNeedModifyLog('user_group')){
	                createModifyLog('D','User_group_auth',$olddata,'',end($sql));
	            }
                if(checkNeedModifyLog('user_group')){
                    $olddata = User_group::where('id', $id)->get()->toarray();
                }

                User_group::find($id)->delete();

                $sql = DB::getQueryLog();
	            if(checkNeedModifyLog('user_group')){
	                createModifyLog('D','User_group',$olddata,'',end($sql));
	            }

                return back()->with('result', '1')->with('message', '刪除成功!');
            }else{
                return back()->with('result', '0')->with('message', $getDelete['msg']);
            }

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    public function destroy_from($id)
    {

        if ($id) {

            if($id == '3' || $id == '4' || $id == '6'){
                return back()->with('result', '0')->with('message', '系統管理員、辦班人員、場地管理 無法刪除!');
            }

            $data = User_group::find($id);
            // dd($getDelete);
            if($data){

            	if(checkNeedModifyLog('user_group')){
	                $olddata = User_group_auth::where('id', $id)->get()->toarray();
	            }

                User_group_auth::find($id)->delete();

                $sql = DB::getQueryLog();
	            if(checkNeedModifyLog('user_group')){
	                createModifyLog('D','User_group_auth',$olddata,'',end($sql));
	            }
                if(checkNeedModifyLog('user_group')){
                    $olddata = User_group::where('id', $id)->get()->toarray();
                }

                User_group::find($id)->delete();

                $sql = DB::getQueryLog();
	            if(checkNeedModifyLog('user_group')){
	                createModifyLog('D','User_group',$olddata,'',end($sql));
	            }

                return redirect('/admin/user_group')->with('result', '1')->with('message', '刪除成功!');
            }else{
                return back()->with('result', '0')->with('message', $getDelete['msg']);
            }

        } else {

            return redirect('/admin/user_group')->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
