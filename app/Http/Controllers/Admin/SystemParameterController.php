<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Models\S02tb;


class SystemParameterController extends Controller
{

    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('system_parameter', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }
    /**
     * 顯示頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show()
    {
        return $this->edit();
    }

    /**
     * 編輯頁
     *
     * @param $setting_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit()
    {
        // 取得資料
        $data = S02tb::first();

        return view('admin/system_parameter/form', compact('data'));
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
        unset($data['_method'], $data['_token']);

        S02tb::where('edusyear', 'LIKE', '%')->update($data);

        return back()->withInput()->with('result', '1')->with('message', '儲存成功!');
    }
}
