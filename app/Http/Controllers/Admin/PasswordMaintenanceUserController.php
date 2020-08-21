<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PasswordMaintenanceUserService;
use App\Models\M22tb;


class PasswordMaintenanceUserController extends Controller
{
    /**
     * PasswordMaintenanceController constructor.
     * @param PasswordMaintenanceUserService $passwordMaintenanceUserService
     */
    public function __construct(PasswordMaintenanceUserService $passwordMaintenanceUserService)
    {
        $this->passwordMaintenanceUserService = $passwordMaintenanceUserService;
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 身分證字號
        $queryData['userid'] = $request->get('userid');
        // 使用狀況
        $queryData['status'] = $request->get('status');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->passwordMaintenanceUserService->getPasswordMaintenanceUserList($queryData);

        return view('admin/password_maintenance_user/list', compact('data', 'queryData'));
    }

    /**
     * 使用者設定
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function act1(Request $request)
    {
        $userid = $request->input('userid');
        $userorg = $request->input('userorg');

        $data['status'] = $request->input('status');

        if ($data['status'] == 'Y') {
            $data['pswerrcnt'] = 0;
        }

        M22tb::where('userid', $userid)->where('userorg', $userorg)->update($data);

        return back()->with('result', '1')->with('message', '使用者設定儲存成功!');
    }

    /**
     * 密碼重設
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function act2(Request $request)
    {
        $userid = $request->input('userid');
        $userorg = $request->input('userorg');

        M22tb::where('userid', $userid)->where('userorg', $userorg)->update(['userpsw' => 'csdi1234']);

        return back()->with('result', '1')->with('message', '密碼重設成功!');
    }

    /**
     * 帳號重設
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function act3(Request $request)
    {
        $userid = $request->input('userid');
        $userorg = $request->input('userorg');

        M22tb::where('userid', $userid)->where('userorg', $userorg)->update(['selfid' => '']);

        return back()->with('result', '1')->with('message', '密碼重設成功!');
    }
}
