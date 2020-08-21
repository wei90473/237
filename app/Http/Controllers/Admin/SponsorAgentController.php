<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SponsorAgentService;
use App\Services\User_groupService;

class SponsorAgentController extends Controller
{
    /**
     * UsersController constructor.
     * @param UsersService $usersService
     */
    public function __construct(SponsorAgentService $sponsorAgentService, User_groupService $user_groupService)
    {
        $this->sponsorAgentService = $sponsorAgentService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('sponsor_agent', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function index(Request $request)
    {
        $queryData = $request->only([
            'username'
        ]);

        $data = [];
        if (!empty($request->all())){
            $data = $this->sponsorAgentService->getM09tbs($queryData, true);
        }

        return view('admin/sponsor_agent/index', compact(['data', 'queryData']));
    }

    public function edit($userid)
    {
        $m09tbs = $this->sponsorAgentService->getM09tbs(null, false);
        $sponsor = $this->sponsorAgentService->getM09tb($userid);

        return view('admin/sponsor_agent/form', compact(['m09tbs', 'sponsor']));
    }

    public function store(Request $request, $userid)
    {
        $this->validate($request, [
            'agent_userid' => 'required'
        ]);
        $agent = $this->sponsorAgentService->getSponsorAgent($userid, $request->agent_userid);

        if ($agent !== null){
            return back()->with('result', 0)->with('message', '代理人已存在');
        }

        $insert = $this->sponsorAgentService->addAgent($userid, $request->agent_userid);

        if ($insert){
            return back()->with('result', 1)->with('message', '新增成功');
        }else{
            return back()->with('result', 1)->with('message', '新增失敗');
        }
    }

    public function delete($sponsor_agent_id)
    {
        $delete = $this->sponsorAgentService->deleteSponsorAgent($sponsor_agent_id);
        if ($delete){
            return back()->with('result', 1)->with('message', '刪除成功');
        }else{
            return back()->with('result', 1)->with('message', '刪除失敗');
        }
    }
}