<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SignatureService;
use App\Services\User_groupService;
use DB;
use Auth;

class SignatureController extends Controller
{
    /**
     * SignupController constructor.
     * @param SignatureService $signatureService
     */
    public function __construct(SignatureService $signatureService, User_groupService $user_groupService)
    {
        $this->signatureService = $signatureService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('signature', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
    }

    public function index(Request $request)
    {
        $queryData = $request->only(['name']);
        $data = $this->signatureService->getSignatures($queryData);
        return view('/admin/signature/index', compact(['data', 'queryData']));
    }

    public function create()
    {
        $action = "create";
        return view('/admin/signature/form', compact(['action']));
    }

    public function edit($id)
    {
        $action = "edit";
        $signature = $this->signatureService->getSignature($id);
        return view('/admin/signature/form', compact(['action', 'signature']));
    }

    public function store(Request $request)
    {
        $signature_file = $request->file('signature');
        $upload_path = 'Uploads/signatures/';

        if ($request->hasFile('signature') && $signature_file->isValid()){
            $rand_str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
            $extension = $signature_file->getClientOriginalExtension(); //副檔名
            $origin_name = $signature_file->getClientOriginalName();
            $file_name = substr(str_shuffle($rand_str), 26, 5).'_'.time().".".$extension;    //重新命名
            $signature_file->move(public_path().'/'.$upload_path, $file_name);
        }else{
            return back()->with('result', 1)->with('message', '電子章錯誤');
        }

        $this->validate($request, [
            'name' => 'required'
        ]);

        $signature = $request->only([
            'name', 'sort'
        ]);

        $signature['img_path'] = $file_name;

        $insert = $this->signatureService->storeSignature($signature, 'create');

        if ($insert){
            return back()->with('result', 1)->with('message', '新增成功');
        }else{
            return back()->with('result', 0)->with('message', '新增失敗');
        }

    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required'
        ]);

        $signature = $request->only([
            'name', 'sort'
        ]);

        $signature_file = $request->file('signature');

        if ($request->hasFile('signature') && $signature_file->isValid()){
            $upload_path = 'Uploads/signatures/';
            $rand_str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
            $extension = $signature_file->getClientOriginalExtension(); //副檔名
            $origin_name = $signature_file->getClientOriginalName();
            $file_name = substr(str_shuffle($rand_str), 26, 5).'_'.time().".".$extension;    //重新命名
            $signature_file->move(public_path().'/'.$upload_path, $file_name);
            $signature['img_path'] = $file_name;
        }

        $update = $this->signatureService->storeSignature($signature, 'edit', $id);

        if ($update){
            return back()->with('result', 1)->with('message', '更新成功');
        }else{
            return back()->with('result', 0)->with('message', '更新失敗');
        }
    }

    public function delete($id)
    {
        $delete = $this->signatureService->deleteSignature($id);
        if ($delete){
            return redirect('/admin/signature')->with('result', 1)->with('message', '刪除成功');
        }else{
            return back()->with('result', 1)->with('message', '刪除失敗');
        }
    }
}