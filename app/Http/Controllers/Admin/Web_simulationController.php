<?php
namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\Web_simulationService;
use App\Models\web_simulation;
use Hash;
use Session;

class Web_simulationController extends Controller
{
    /**
     * Web_simulationController constructor.
     * @param User_groupService $user_groupService
     */
    public function __construct(Web_simulationService $web_simulationService, User_groupService $user_groupService)
    {
        $this->web_simulationService = $web_simulationService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
        	$user_data = \Auth::user();
        	$user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
        	if(in_array('web_simulation', $user_group_auth)){
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

        $queryData['idno'] = $request->get('idno');
        $data = array();
        if(!empty($queryData['idno'])){
            $data = $this->web_simulationService->getWeb_simulation($queryData);
        }

        return view('admin/web_simulation/list', compact('data', 'queryData'));
    }

    public function simulate(Request $request)
    {
        $queryData['idno'] = $request->get('simulate_user_id');
        $queryData['type'] = $request->get('simulate_user_type');

        if(empty($queryData['idno']) || empty($queryData['type'])){
            return back()->with('result', 0)->with('message', '操作錯誤');
        }

        $fields = array(
            'md5_idno' => md5($queryData['idno']),
            'idno' => $queryData['idno'],
            'type' => $queryData['type'],
        );
        web_simulation::create($fields);

        return redirect(env('WEB_URL').'wFrmSysLogin/simulation/'.md5($queryData['idno']).'_'.$queryData['type']);
    }


}
