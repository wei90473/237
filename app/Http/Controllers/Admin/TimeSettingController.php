<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Models\S02tb;
use DB;

class TimeSettingController extends Controller
{
    /**
     * TimeSettingController constructor.
     * @param time_settingService $time_settingService
     */
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('time_setting', $user_group_auth)){
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
    public function index(Request $request){

        $data = S02tb::select('tmst','tmet','tast','taet','tnst','tnet','nmst','nmet','nast','naet','nnst','nnet')->first();
        return view('admin/time_setting/list', compact('data'));
    }

    /**
     * 編輯頁更新處理
     */
    public function update(Request $request) {
        $updata = S02tb::where(DB::raw('1'),'=','1')->update($request->except(['_method','_token']));
        if($updata=='1'){
            return back()->with('result', '1')->with('message', '更新成功!');
        }else{
            return back()->with('result', '0')->with('message', '更新失敗!');
        }
    }


}
