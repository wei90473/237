<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use DB;
use Auth;
use Session;

use App\Models\M09tb;

class RoleSimulateController extends Controller
{
    /**
     * SignupController constructor.
     * @param SignupService $signupService
     */
    public function __construct(User_groupService $user_groupService)
    {
        $this->user_groupService = $user_groupService;
        //檢查權限
        // $this->middleware(function($request, $next){
        //     $user_data = \Auth::user();
        //     $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
        //     if(in_array('role_simulate', $user_group_auth)){
        //         return $next($request);
        //     }else{
        //         return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
        //     }
        // });
    }

    public function index(Request $request)
    {
        $user_data = \Auth::user();
        $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
        if(in_array('role_simulate', $user_group_auth)){
            // return $next($request);
        }else{
            return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
        }

        $queryData = $request->only([
            'username'
        ]);
        $data = M09tb::where($queryData)->paginate(10);
        return view("admin/role_simulate/index", compact(['data', 'queryData']));
    }

    public function simulate(Request $request)
    {
        $user_data = \Auth::user();
        $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
        if(in_array('role_simulate', $user_group_auth)){
            // return $next($request);
        }else{
            return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
        }

        $this->validate($request, [
            'simulate_user_id' => 'required'
        ]);
        if ($request->session()->get('simulate_origin_user') == null){
            $request->session()->put('simulate_origin_user', Auth::user());
            Auth::loginUsingId($request->simulate_user_id);
            return back()->with('result', 1)->with('message', '模擬成功');
        }else{
            return back()->with('result', 0)->with('message', '目前已模擬其他使用者，模擬失敗');
        }
    }

    public function returnOriginUser(Request $request)
    {
        Auth::loginUsingId($request->session()->get('simulate_origin_user')->id);
        $request->session()->forget('simulate_origin_user');
        return back()->with('result', 1)->with('message', '切回成功');
    }
}