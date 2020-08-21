<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SignupOrganService;
use App\Services\Term_processService;
use App\Services\User_groupService;
use DB;


class SignupOrganController extends Controller
{
    public function __construct(SignupOrganService $signupOrganService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        setProgid('signup');
        $this->signupOrganService = $signupOrganService; 
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('signup', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });        
    }

    public function create($class, $term)
    {
        $class_info = [
            "class" => $class,
            "term" => $term
        ];
        
        $t04tb = $this->signupOrganService->getT04tb($class_info);
        $m17tbs = collect(["" => "請選擇"]);
        // $m17tbs = $m17tbs->union($this->signupOrganService->getCompetentAuthoritys()); // 取得主管機關
        return view('admin/signup_organ/form', compact('t04tb', 'm17tbs'));
    }

    public function edit($id)
    {  

        $online_apply_organ = $this->signupOrganService->getOnlineApplyOrgan($id);  
        $t04tb = $online_apply_organ->t04tb;
        $m17tbs = collect(["" => "請選擇"]);
        $m17tbs = $m17tbs->union($this->signupOrganService->getCompetentAuthoritys()); // 取得主管機關
    
        return view('admin/signup_organ/form', compact('online_apply_organ', 't04tb', 'm17tbs'));        
    }

    public function store(Request $request, $class, $term)
    {
        $this->validate($request, [
            'enrollorg' => 'required',
        ],[
            "enrollorg.required" => "機關 不可為空",
        ]);

        $online_apply_organ = $request->only([
            "enrollorg",
            "officially_enroll",
            "secondary_enroll",
            "open_belong_apply"
        ]);
        $online_apply_organ['class'] = $class;
        $online_apply_organ['term'] = $term;

        $insert = $this->signupOrganService->storeOnlineApplyOrgan($online_apply_organ, "insert");
        if ($insert){
            return redirect("admin/signup/edit/{$class}/{$term}")->with('result', 1)
                                                                 ->with('message', '新增成功');
        }else{
            return back()->with('result', 0)
                         ->with('message', '新增失敗');                
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'enrollorg' => 'required',
        ],[
            "enrollorg.required" => "機關 不可為空",
        ]);

        $online_apply_organ = $request->only([
            'enrollorg',
            "officially_enroll",
            "secondary_enroll",
            "open_belong_apply"
        ]);

        $update = $this->signupOrganService->storeOnlineApplyOrgan($online_apply_organ, "update", $id);
        if ($update){
            return redirect("admin/signup_organ/edit/{$id}")->with('result', 1)
                                                            ->with('message', '更新成功');
        }else{
            return back()->with('result', 0)
                         ->with('message', '更新失敗');                
        }        
    }

    public function delete($id)
    {
        
    }    
}