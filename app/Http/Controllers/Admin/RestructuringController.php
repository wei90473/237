<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User_groupService;
use App\Services\RestructuringService;

class RestructuringController extends Controller
{
	public function __construct(RestructuringService $restructuringService, User_groupService $user_groupService)
    {
        setProgid('restructuring');
    	$this->restructuringService = $restructuringService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('restructuring', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
            // dd($user_data);
            // dd(\session());
        });
    }

    public function index(Request $request)
    {
        $queryData = [];

        $queryData['m17tb'] = $request->only(['enrollname']);
        $queryData['restructuring_detail'] = $request->only(['enrollorg']);

        $data = $this->restructuringService->getRestructuringList($queryData);
        // dd($restructurings);
        // $restructurings = $restructurings->groupBy('id')->map(function($id_group){
        //     return $id_group->groupBy('restructure_type');
        // });

    	return view('admin/restructuring/index', compact(['data', 'queryData']));
    }

    public function create()
    {
    	return view('admin/restructuring/form');
    }

    public function edit($id)
    {
        $restructuring = $this->restructuringService->getRestructuring($id);
        $action = "edit";
        return view('admin/restructuring/form', compact(['restructuring', 'action']));
    }

    public function store(Request $request)
    {
    	$this->validate($request,[
    		'new_before_enrollorg' => 'required',
    		'new_after_enrollorg' => 'required'
    	]);

        $detail = $request->only(['new_before_enrollorg', 'new_after_enrollorg']);
        $detail['new_before_enrollorg'] = array_filter($detail['new_before_enrollorg']);
        $detail['new_after_enrollorg'] = array_filter($detail['new_after_enrollorg']);

        // 檢查改制前後機關是否相同
        if (count(array_diff($detail['new_before_enrollorg'], $detail['new_after_enrollorg'])) == 0){
            return back()->with('result', 0)->with('message', '改制前後機關不可完全相同');
        }

        // 檢查是否已存在相同的改制關係
        $checkResult = $this->restructuringService->checkRestructuringExist($detail);

        if ($checkResult !== false){
            return back()->with('result', 0)->with('message', '改制關係已存在');
        }

    	$insert = $this->restructuringService->createRestructuring($detail);

        if ($insert['status'] === true){
            return back()->with('result', 1)->with('message', '新增成功');
        }else{
            return back()->with('result', 0)->with('message', $insert['message'] );
        }
    }

    public function update(Request $request, $id)
    {
        $detail = $request->only([
            'new_before_enrollorg',
            'new_after_enrollorg',
            'before_enrollorg',
            'after_enrollorg'
        ]);

        $detail['before_enrollorg'] = (is_array($detail['before_enrollorg'])) ? array_filter($detail['before_enrollorg']) : [];
        $detail['after_enrollorg'] = (is_array($detail['after_enrollorg'])) ? array_filter($detail['after_enrollorg']) : [];
        $detail['new_before_enrollorg'] = (is_array($detail['new_before_enrollorg'])) ? array_filter($detail['new_before_enrollorg']) : [];
        $detail['new_after_enrollorg'] = (is_array($detail['new_after_enrollorg'])) ? array_filter($detail['new_after_enrollorg']) : [];

        $updatedDetail['new_before_enrollorg'] = array_merge($detail['before_enrollorg'], $detail['new_before_enrollorg']);
        $updatedDetail['new_after_enrollorg'] = array_merge($detail['after_enrollorg'], $detail['new_after_enrollorg']);
        // 檢查是否已存在相同的改制關係
        $checkResult = $this->restructuringService->checkRestructuringExist($updatedDetail);

        if (count(array_diff($updatedDetail['new_before_enrollorg'], $updatedDetail['new_after_enrollorg'])) == 0){
            return back()->with('result', 0)->with('message', '改制前後機關不可完全相同');
        }

        if ($checkResult !== false && $id != $checkResult){
            return back()->with('result', 0)->with('message', '改制關係已存在');
        }

        $update = $this->restructuringService->updateRestructuring($id, $detail);
        if ($update['status'] === true){
            return back()->with('result', 1)->with('message', '更新成功');
        }else{
            return back()->with('result', 0)->with('message', $update['message'] );
        }

    }

    public function delete($id)
    {
        $delete = $this->restructuringService->deleteRestructuring($id);

        if ($delete['status'] === true){
            return redirect('/admin/restructuring')->with('result', 1)->with('message', '刪除成功');
        }
    }
}