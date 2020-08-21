<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\M09tb;
use Hash;
use Auth;


class ProfileController extends Controller
{
    /**
     * 編輯頁
     *
     * @param $article_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit()
    {
        return view('admin/profile/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // 驗證欄位
        $rules = array(
            'old_password' => 'required', // 舊密碼
            'password' => 'required|confirmed', // 密碼
            'password_confirmation' => 'required', // 確認密碼
        );

        $messages = array(
            'old_password.required' => '請填寫舊密碼',
            'password.required' => '請填寫新密碼',
            'password_confirmation.required' => '請填寫確認密碼',
            'password.confirmed' => '新密碼與確認密碼不一致!'
        );

        $this->validate($request, $rules, $messages);

        $data['old_password'] = $request->input('old_password');

        // 檢查舊密碼是否正確
        if ( ! Hash::check($data['old_password'], Auth::guard()->user()->password)) {

            return back()->with('message', '舊密碼錯誤');
        }

        $password = Hash::make($request->input('password'));

        //更新
        M09tb::where('id', Auth::guard()->user()->id)->update(array('password' => $password));

        return back()->with('result', '1')->with('message', '更新成功!');
    }
}
