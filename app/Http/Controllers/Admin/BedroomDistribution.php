<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\BedroomDistributionService;

class BedroomDistribution extends Controller
{
    public function __construct(User_groupService $user_groupService,BedroomDistributionService $bedroomDistributionService)
    {
        $this->user_groupService = $user_groupService;
        $this->bedroomDistributionService = $bedroomDistributionService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('bedroom_distribution', $user_group_auth)){
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
        $result="";
        return view('admin/bedroom_distribution/list',compact('result'));
    }

    public function export(Request $request)
    {
        $sdate = $request->only(['sdatetw']);
        $edate = $request->only(['edatetw']);

        $chechWeek = $this->bedroomDistributionService->checkSameWeek($sdate['sdatetw'],$edate['edatetw']);

        if(!$chechWeek){
            return redirect('/admin/bedroom_distribution')->with('result', '2')->with('message', '起訖日期不同週');
        }

        $this->bedroomDistributionService->export($sdate['sdatetw'],$edate['edatetw']);
        exit;

    }
}
