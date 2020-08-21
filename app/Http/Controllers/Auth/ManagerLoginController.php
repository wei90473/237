<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Loginlog;
use App\Models\M09tb;
use App\Models\S02tb;
use App\User;
use App\Helpers\Des;

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
     * 提供給e+ sso使用
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ssoLogin(Request $request)
    {
      
        if(isset($request->token)){
            // $encode_token   = DES::base64url_decode($request->token);
            // $login_token    = DES::decrypt('LMBrqQ', $encode_token);
            $login_token    = DES::sso_des_decode($request->token,'LMBrqQ');
            
            $token_sec      = explode(",",$login_token);
            $login_pid      = $token_sec[0];
            $login_time     = $token_sec[1];
            $login_type    =  $token_sec[2];
            $timeout        = time()-$login_time;
            if($timeout < 6000) //10分鐘
            {        
                $user_idno = $login_pid;

                $Logins_data = S02tb::select('Logins')->get()->toArray();
                $Logins = $Logins_data[0]['Logins']; 
                if ($this->hasTooManyLoginAttempts($request, $Logins)) {
                    $this->fireLockoutEvent($request);        
                    return $this->sendLockoutResponse($request);
                }
                $user_data = M09tb::select("userid", 'Last_login_time', 'Last_logins', 'Logins', 'login_time')
                        ->where('idno', $user_idno)
                        ->get()->toArray();
               
                $request->merge(['userid' => $user_data[0]['userid']]);
    
                if ($this->attemptLoginAD($request)) {
         
                    $fields = array(
                        'Last_login_time' => $user_data[0]['login_time'],
                        'login_time' => date('Y-m-d H:i:s'),
                        'Last_logins' => $user_data[0]['Logins'],
                        'Logins' => '0',
                    );
     
                    M09tb::where('userid', $request->userid)->update($fields);
                    
                    return $this->sendLoginResponse($request);
                }else{
                    return back()->with('result', '0')->with('message', '超時!');
                }
     
            }
           


        }else{
            return back()->with('result', '0')->with('message', '該身分證於系統尚未設定身分證號,請聯絡系統管理員設定!');
        }


    }




     /**
     * 使用身分證字號直接登入
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function gogoLogin(Request $request)
    {
        if(($_SERVER["REMOTE_ADDR"]=='61.216.24.96') ||('172.16.10.18'==$_SERVER["SERVER_ADDR"]) ){
            if(isset($request->idno)){

                $user_idno = $request->idno;
    
                $Logins_data = S02tb::select('Logins')->get()->toArray();
                $Logins = $Logins_data[0]['Logins']; 
                if ($this->hasTooManyLoginAttempts($request, $Logins)) {
                    $this->fireLockoutEvent($request);        
                    return $this->sendLockoutResponse($request);
                }
                $user_data = M09tb::select("userid", 'Last_login_time', 'Last_logins', 'Logins', 'login_time')
                        ->where('idno', $user_idno)
                        ->get()->toArray();
               
                $request->merge(['userid' => $user_data[0]['userid']]);
    
                if ($this->attemptLoginAD($request)) {
         
                    $fields = array(
                        'Last_login_time' => $user_data[0]['login_time'],
                        'login_time' => date('Y-m-d H:i:s'),
                        'Last_logins' => $user_data[0]['Logins'],
                        'Logins' => '0',
                    );
     
                    M09tb::where('userid', $request->userid)->update($fields);
                    
                    return $this->sendLoginResponse($request);
                }
     
    
    
            }else{
                return back()->with('result', '0')->with('message', '該身分證於系統尚未設定身分證號,請聯絡系統管理員設定!');
            }    
        }

    }


    /**
     * 取代原有的登入驗證,加入log
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {


        // get_current_user();
        // $username = get_current_user();
        // echo $username;
     //   echo $_SERVER['REMOTE_USER'];
       // die(); 
        //phpinfo();
//        dd($_SERVER['AUTH_USER']);
        //die();        
        if(($request->radio == 1)) //AD 登入
        {
  
             //驗證AD
            //  $ldap_password = '!QAZ2wsx3edc';
             $ldap_password = $request->password;
             $ldap_username = $request->userid.'@csdi.gov.tw';
             $ldap_connection = ldap_connect("10.111.1.3");
             
             if (FALSE === $ldap_connection){
                 echo '目前無法連線AD Server，請洽數位組處理。';
                 die();
             }
              
             ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
             ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.
             if (TRUE === ldap_bind($ldap_connection, $ldap_username, $ldap_password)){
                ldap_unbind($ldap_connection); // AD驗證通過以後就關閉AD連線，改用通過驗證的帳號登入


                //  $ldap_base_dn   = 'DC=csdi,DC=gov,DC=tw'; 
                $Logins_data = S02tb::select('Logins')
                ->get()->toArray();

                $Logins = $Logins_data[0]['Logins'];
                // dd($Logins);
               if ($this->hasTooManyLoginAttempts($request, $Logins)) {
                    $this->fireLockoutEvent($request);        
                    return $this->sendLockoutResponse($request);
                }
        
                $user_data = M09tb::select("userid", 'Last_login_time', 'Last_logins', 'Logins', 'login_time')
                        ->where('userid', $request->input('userid'))
                        ->get()->toArray();
                    
                if ($this->attemptLoginAD($request)) {
        
                    $fields = array(
                        'Last_login_time' => $user_data[0]['login_time'],
                        'login_time' => date('Y-m-d H:i:s'),
                        'Last_logins' => $user_data[0]['Logins'],
                        'Logins' => '0',
                    );
                    // dd($user_data[0]['Logins']);
                    M09tb::where('userid', $request->input('userid'))->update($fields);
                 
                    return $this->sendLoginResponse($request);
                }
        
                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
               
                // $this->incrementLoginAttempts($request);

                // dd($user_data);
                if(!empty($user_data)){
                    if(empty($user_data[0]['Logins'])){
                        $user_data[0]['Logins'] = '0';
                    }
                    $fields = array(
                        'login_time' => date('Y-m-d H:i:s'),
                        'Logins' => $user_data[0]['Logins']+1,
                    );
                    M09tb::where('userid', $request->input('userid'))->update($fields);
                }
              
               
        
             }else{
                die('登入失敗，本功能僅限本學院同仁使用');
                //END ldap_bind
             } 
               
        }else{
            $this->validateLogin($request);

            $Logins_data = S02tb::select('Logins')
                    ->get()->toArray();
    
            $Logins = $Logins_data[0]['Logins'];
            // dd($Logins);
            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request, $Logins)) {
                $this->fireLockoutEvent($request);
    
                return $this->sendLockoutResponse($request);
            }
    
            $user_data = M09tb::select("userid", 'Last_login_time', 'Last_logins', 'Logins', 'login_time')
                    ->where('userid', $request->input('userid'))
                    ->get()->toArray();
    
            if ($this->attemptLogin($request)) {
    
                $fields = array(
                    'Last_login_time' => $user_data[0]['login_time'],
                    'login_time' => date('Y-m-d H:i:s'),
                    'Last_logins' => $user_data[0]['Logins'],
                    'Logins' => '0',
                );
                // dd($user_data[0]['Logins']);
                M09tb::where('userid', $request->input('userid'))->update($fields);
    
                return $this->sendLoginResponse($request);
            }
    
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
    
            $this->incrementLoginAttempts($request);
            // dd($user_data);
            if(!empty($user_data)){
                if(empty($user_data[0]['Logins'])){
                    $user_data[0]['Logins'] = '0';
                }
                $fields = array(
                    'login_time' => date('Y-m-d H:i:s'),
                    'Logins' => $user_data[0]['Logins']+1,
                );
                M09tb::where('userid', $request->input('userid'))->update($fields);
            }
        }
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
