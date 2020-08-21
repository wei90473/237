<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UnitService;
use App\Services\Term_processService;
use App\Services\User_groupService;
use DB ;
use Auth;

class UnitController extends Controller
{
    /**
     * WaitingService constructor.
     * @param UnitController $siteScheduleRpository
     */
    public function __construct(
        UnitService $unitService, Term_processService $term_processService, User_groupService $user_groupService
    )
    {
        setProgid('arrangement');
        $this->unitService = $unitService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('arrangement', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });

    }

    public function index($class, $term)
    {
        $t04tb_info = [
            'class' => $class,
            'term' => $term
        ];
        $t04tb = $this->unitService->getT04tb($t04tb_info);
        return view('admin/unit/index',compact('t04tb'));
    }

    public function create($class, $term)
    {
        $t04tb_info = [
            'class' => $class,
            'term' => $term
        ];
        $t04tb = $this->unitService->getT04tb($t04tb_info);
        return view('admin/unit/form',compact('t04tb'));
    }

    public function edit($class, $term, $unit)
    {
        $t05tb_info = [
            'class' => $class,
            'term' => $term,
            'unit' => $unit
        ];
        $t05tb = $this->unitService->getT05tb($t05tb_info);
        $t04tb = $t05tb->t04tb;
        return view('admin/unit/form',compact('t04tb','t05tb'));
    }

    public function store(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('unit', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        $this->validate($request, [
            'name' => 'required'
        ],[
            "name.required" => "單元名稱 欄位不可為空",
        ]);

        $t05tb = $request->only([
            'name',
            'remark'
        ]);

        $t05tb["class"] = $class;
        $t05tb["term"] = $term;

        $insert = $this->unitService->storeT05tb($t05tb, "insert");

        if ($insert){
            return back()->with('result', 1)->with('message', '新增成功');
        }else{
            return back()->with('result', 0)->with('message', '新增失敗');
        }
    }

    public function update(Request $request, $class, $term, $unit)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('unit', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $this->validate($request, [
            'name' => 'required'
        ],[
            "name.required" => "單元名稱 欄位不可為空",
        ]);

        $t05tb = $request->only([
            'name',
            'remark'
        ]);

        $unit_info["class"] = $class;
        $unit_info["term"] = $term;
        $unit_info["unit"] = $unit;

        $update = $this->unitService->storeT05tb($t05tb, "update", $unit_info);

        if ($update){
            return back()->with('result', 1)->with('message', '更新成功');
        }else{
            return back()->with('result', 0)->with('message', '更新失敗');
        }
    }

    public function delete($class, $term, $unit)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('unit', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }

        $t05tb_info = compact(['class', 'term', 'unit']);
        $delete = $this->unitService->deleteT05tb($t05tb_info);
        if ($delete){
            return redirect("/admin/unit/{$class}/{$term}")->with('result', 1)->with('message', '刪除成功');
        }else{
            return back()->with('result', 0)->with('message', '刪除失敗');
        }
    }
}