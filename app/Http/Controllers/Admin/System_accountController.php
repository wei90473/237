<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\System_accountService;
use App\Services\User_groupService;
use App\Models\M09tb;
use Hash;
use DB ;

class System_accountController extends Controller
{
    /**
     * System_accountController constructor.
     * @param System_accountService $system_accountService
     */
    public function __construct(System_accountService $system_accountService, User_groupService $user_groupService)
    {
        $this->system_accountService = $system_accountService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('system_account', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('system_account');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 班別
		if('' == $request->get('class')){
            $queryData['class'] = '';
        }else{
            $queryData['class'] = $request->get('class')=='全部'?'':$request->get('class');
        }


        // 取得關鍵字
        $queryData['keyword'] = $request->get('keyword');
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData);
        // echo "\n</pre>\n";
        // die();

        // email
        $queryData['email'] = $request->get('email');
        // 服務機關名稱
        $queryData['dept'] = $request->get('dept');

        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;

        $queryData['search'] = $request->get('search');

        if($queryData['search'] != 'search' ){
        	// 取得關鍵字
	        $queryData2['keyword'] = 'none';

        	$data = $this->system_accountService->getSystem_accountList($queryData2);
        }else{
        	$data = $this->system_accountService->getSystem_accountList($queryData);
        }

        return view('admin/system_account/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $section = $this->system_accountService->getSections();
        $user_group = $this->system_accountService->getUser_group();
        $user_group_auth = array();
        return view('admin/system_account/form', compact('section', 'user_group', 'user_group_auth'));
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        if(!empty($data['idno'])){
            $chk = $this->chk_pid($data['idno']);
            if($chk === false){
                return back()->with('result', '0')->with('message', '身分證字號錯誤');
            }

            $M09tb_data = M09tb::where('idno', $data['idno'])->get()->toArray();
            if(!empty($M09tb_data)){
                return back()->with('result', '0')->with('message', '身分證字號重複');
            }

        }

        unset($data['password'], $data['password_confirmation']);
        $new_password = $request->input('password');

        $rules = array(
            'password' => 'required|confirmed', // 密碼
            'password_confirmation' => 'required', // 確認密碼
        );

        $messages = array(
            'password.required' => '請填寫新密碼',
            'password_confirmation.required' => '請填寫確認密碼',
            'password.confirmed' => '新密碼與確認密碼不一致!'
        );

        $this->validate($request, $rules, $messages);


        $password = Hash::make($request->input('password'));
        $data['chgpswdate'] = date('Y-m-d H:i:s');
        $data['password'] = $password;

        $section = $this->system_accountService->getSections();
        if(!empty($data['deptid'])){
            foreach($section as $row){
                if($row['deptid'] == $data['deptid']){
                    $data['section'] = $row['section'];
                    continue;
                }
            }
        }
        $data['user_group_id'] = '';
        if(!empty($data['auth'])){
            $data['user_group_id'] = implode(",",$data['auth']);
        }
        unset($data['auth']);

        //新增
        $result = M09tb::create($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('system_account')){
            $nowdata = M09tb::where('idno', $data['idno'])->get()->toarray();
            createModifyLog('I','M09tb','',$nowdata,end($sql));
        }

        return redirect('/admin/system_account/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $id
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * 編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $section = $this->system_accountService->getSections();
        $user_group = $this->system_accountService->getUser_group();
        $data = M09tb::find($id);
        $user_group_auth = array();
        if(!empty($data->user_group_id)){
            $user_group_id = explode(",",$data->user_group_id);
            // dd($user_group_id);
            foreach($user_group_id as $row){
                $user_group_data = $this->system_accountService->getBy_user_group_id($row);
                $user_group_auth[] = $user_group_data['id'];
            }
        }
        if ( ! $data) {

            return view('admin/errors/error');
        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();

        return view('admin/system_account/form', compact('data', 'section', 'user_group', 'user_group_auth'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $old_data = M09tb::find($id);

        if(!empty($data['idno'])){
            $chk = $this->chk_pid($data['idno']);
            if($chk === false){
                return back()->with('result', '0')->with('message', '身分證字號錯誤');
            }

            $M09tb_data = M09tb::where('idno', $data['idno'])->first();
            // dd($old_data);
            if(!empty($M09tb_data) && $data['idno'] != $old_data['idno']){
                return back()->with('result', '0')->with('message', '身分證字號重複');
            }

        }

        unset($data['_method'], $data['_token']);
        unset($data['old_password'], $data['password'], $data['password_confirmation']);
        $old_password = $request->input('old_password');
        $new_password = $request->input('password');
        if(!empty($new_password)){

            $rules = array(
                'password' => 'required|confirmed', // 密碼
                'password_confirmation' => 'required', // 確認密碼
            );

            $messages = array(
                'password.required' => '請填寫新密碼',
                'password_confirmation.required' => '請填寫確認密碼',
                'password.confirmed' => '新密碼與確認密碼不一致!'
            );

            if(!empty($old_data->password)){
                $rules['old_password'] = 'required';
                $messages['old_password.required'] = '請填寫舊密碼';
            }

            $this->validate($request, $rules, $messages);

            // 檢查舊密碼是否正確
            if (!empty($old_data->password) && ! Hash::check($old_password, $old_data->password)) {

                return back()->with('message', '舊密碼錯誤');
            }

            $password = Hash::make($request->input('password'));
            $data['chgpswdate'] = date('Y-m-d H:i:s');
            $data['password'] = $password;
        }
        $section = $this->system_accountService->getSections();
        if(!empty($data['deptid'])){
            foreach($section as $row){
                if($row['deptid'] == $data['deptid']){
                    $data['section'] = $row['section'];
                    continue;
                }
            }
        }
        $data['user_group_id'] = '';
        if(!empty($data['auth'])){
            $data['user_group_id'] = implode(",",$data['auth']);
        }
        unset($data['auth']);
        // dd($data);

        if(checkNeedModifyLog('system_account')){
            $olddata = M09tb::where('id', $id)->get()->toarray();
        }

        //更新
        M09tb::where('id', $id)->update($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('system_account')){
            $nowdata = M09tb::where('id', $id)->get()->toarray();
            createModifyLog('U','M09tb',$olddata,$nowdata,end($sql));
        }

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {

        if ($id) {

            $data = M09tb::find($id);
            $getDelete = $this->system_accountService->getDelete($data->userid);
            if($getDelete['delete'] == 'Y'){

                if(checkNeedModifyLog('system_account')){
                    $olddata = M09tb::where('id', $id)->get()->toarray();
                }

                M09tb::find($id)->delete();

                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('system_account')){
                    createModifyLog('D','M09tb',$olddata,'',end($sql));
                }

                return back()->with('result', '1')->with('message', '刪除成功!');
            }else{
                return back()->with('result', '0')->with('message', $getDelete['msg']);
            }

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    public function destroy_from($id)
    {

        if ($id) {

            $data = M09tb::find($id);
            $getDelete = $this->system_accountService->getDelete($data->userid);
            // dd($getDelete);
            if($getDelete['delete'] == 'Y'){

                if(checkNeedModifyLog('system_account')){
                    $olddata = M09tb::where('id', $id)->get()->toarray();
                }

                M09tb::find($id)->delete();

                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('system_account')){
                    createModifyLog('D','M09tb',$olddata,'',end($sql));
                }

                return redirect('/admin/system_account')->with('result', '1')->with('message', '刪除成功!');
            }else{
                return back()->with('result', '0')->with('message', $getDelete['msg']);
            }

        } else {

            return redirect('/admin/system_account')->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    public function chk_pid($id) {
        if( !$id )return false;
        $id = strtoupper(trim($id)); //將英文字母全部轉成大寫，消除前後空白
        //檢查第一個字母是否為英文字，第二個字元1 2 A~D 其餘為數字共十碼
        $ereg_pattern= "^[A-Z]{1}[12ABCD]{1}[[:digit:]]{8}$";
        if(!preg_match("/".$ereg_pattern."/i", $id))return false;
        $wd_str="BAKJHGFEDCNMLVUTSRQPZWYX0000OI";   //關鍵在這行字串
        $d1=strpos($wd_str, $id[0])%10;
        $sum=0;
        if($id[1]>='A')$id[1]=chr($id[1])-65; //第2碼非數字轉換依[4]說明處理
        for($ii=1;$ii<9;$ii++)
            $sum+= (int)$id[$ii]*(9-$ii);
        $sum += $d1 + (int)$id[9];
        if($sum%10 != 0)return false;
        return true;
    }

}
