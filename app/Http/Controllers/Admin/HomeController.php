<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Config;
use App\Services\Term_processService;
use App\Models\M09tb;
use App\Models\M22tb;
use App\Models\M21tb;
use DB;

class HomeController extends Controller
{
	public function __construct(Term_processService $term_processService)
    {
        $this->term_processService = $term_processService;
    }

    public function index(Request $request)
    {
    	$Process_non_complete = $this->term_processService->getProcess_non_complete();
        return view('admin/home/content', compact('Process_non_complete'));
    }

    public function goToTrain()
    {
    	if(empty(auth()->user()->idno)){
    		return back()->with('result', '0')->with('message', '尚未設定身分證號,請聯絡系統管理員設定!');
    	}

        $query = M21tb::select('userid');
        $query->where('userid', '=', auth()->user()->idno);
        $M21tb_data = $query->get()->toArray();
        if(!empty($M21tb_data)){
            $user_data = $M21tb_data[0];
        }

        $user_data = array();
    	$query = M22tb::select('userid');
        $query->where('userid', '=', auth()->user()->idno);
        $M22tb_data = $query->get()->toArray();
        if(!empty($M22tb_data)){
            $user_data = $M22tb_data[0];
        }

        if(empty($user_data)){
        	return back()->with('result', '0')->with('message', '訓練需求及學習服務系統沒有您的資料');
        }else{
        	$fields = array(
	            'login_token' => md5(date('Y-m-d H')),
	        );
	        $result = M09tb::where('userid', auth()->user()->userid)->update($fields);
        }
        return redirect(env('WEB_URL').'wFrmSysLogin/csdi/'.auth()->user()->userid);
        // }
    	// dd(md5(date('Y-m-d H:i')));

    }
    public function goToEApp()
    {
     
        // 驗證碼
        $vaild_code = (intval(date('Ymd')) + 5401) * 365;
        $indo_data  = auth()->user()->idno;
        $indo_data  = $this->Encrypt_Str($indo_data);
        if($indo_data==''){
            die('加密過程發生問題');
        }
        $post_data = array(
            "code"     => $vaild_code,
            "data"     => $indo_data 
        );
        
        $url = "https://appweb-fet.hrd.gov.tw/api/sunnet_ticket.php";        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Google Bot");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            
        if(!curl_errno($ch)){
            $data = curl_exec($ch);           
            $temp = json_decode( $data , true);
            $url = 'https://appweb-fet.hrd.gov.tw/api/sunnet_login.php?ticket='.$temp['data'].'&data='.$indo_data;
 
            return redirect($url);
        }else{	 
            echo 'curl_error : ' . curl_error($ch); 
        }

    }

    public function Encrypt_Str($val) {
        $val = trim($val);
        $key = md5('fet@hrd.sun.net');
        $sql = "SELECT HEX(AES_ENCRYPT('{$val}','{$key}')) as infodata";
        $result = DB::select($sql);
       
        return $result[0]->infodata;
    }

    
}
