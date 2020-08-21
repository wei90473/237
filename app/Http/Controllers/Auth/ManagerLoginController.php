<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Loginlog;


class ManagerLoginController extends Controller
{
    use AuthenticatesUsers;


    /**
    * @param Request $request
    */
    protected function validateLogin(Request $request)
    {

        $this->validate($request,[
            'userid' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required'
            ], [
            'password.required' => '密碼必須',
            'g-recaptcha-response.required' => '請勾選我不是機器人',
        ]);
    }

    /**
     * 取代原有的登入驗證,加入log
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {

    
        // if(""==$request['g-recaptcha-response'])
        // {
        //     return $this->sendFailedLoginResponse($request);
        // }
        

        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * 登入後轉址
     *
     * @var string
     */
    protected $redirectTo = '/admin/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:managers', ['except' => 'logout']);
    }

    /**
     * 修改載入的 login 頁面.
     */
    function showLoginForm()
    {
        return view('admin/login/login');
    }

    /**
     * 修改帳號欄位
     */
    function username()
    {
        return 'userid';
    }

    /**
     * 修改驗證時使用的 guard
     */
    protected function guard()
    {
        return \Auth::guard('managers');
    }

    /**
     * 修改登出後的轉址路徑
     */
    public function logout(Request $request)
    {
        $this->guard('managers')->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/admin/login');
    }
}
