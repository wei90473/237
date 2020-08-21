<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Models\Managers;
use Config;
use DB;
use App\Helpers\NetworkTool;

class ConnectionController extends Controller
{
    /**
     * 伺服器測試
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

  
        $data = Managers::first();

        $result['web'] = 1;

        $result['db'] = ($data)? 1 : 0;

        $result['is_demo'] = (config('app.is_demo'))? 1 : 0;

        $result['url'] = Config::get('app.url');

        return response()->json($result);
    }




    /**
     * 測試福華介接
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function howard()
    {

        //檢查目前主機是否為172.16.10.18
        if('172.16.10.18'==$_SERVER["SERVER_ADDR"]){
            //開始同步
         
         // $sql ='select top 10 * from pupil;';

         $submit = DB::statement('call usp_out_affirm(?, ?)',['1090101','1090630']);
         dd($submit);
         $ret = DB::connection('sqlsrv')->select($sql);

            dd($ret);
        }else{
            die('目前並非處於可以同步更新的主機，目前主機ＩＰ'.$_SERVER["SERVER_ADDR"]);
        }
    }

/*

    Route::get('/sunnet_wholeans', 'ConnectionController@sunnet_wholeans'); //研習問卷資料
Route::get('/sunnet_profans', 'ConnectionController@sunnet_profans');//講座問卷資料
Route::get('/sunnet_sign', 'ConnectionController@sunnet_sign'); //簽到資料
*/
    /**
     * 研習問卷資料
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sunnet_wholeans()
    {

      $class = "109207"; // 班號
      $term  = "01";    // 期別
        $networkTool = new NetworkTool();
        //檢查目前主機是否為172.16.10.18
        if('172.16.10.18'==$_SERVER["SERVER_ADDR"]){
          $today = date('Ymd');
          $today_code = (intval($today) + 5401) * 365; //當天日期轉數字 8 碼 20200518 + 指定代碼 5401）* 365
          $url = "https://appweb-fet.hrd.gov.tw/api/sunnet_wholeans.php";
          $data=array(
            "code"       =>$today_code, 
            "class"      => $class,
            "term"       => $term
          );

          $result = $networkTool->httpPost($url,$data);
          $result = $networkTool->unicode_to_utf8($result);
          dd($result);
        }else{
            die('目前並非處於可以同步更新的主機，目前主機ＩＰ'.$_SERVER["SERVER_ADDR"]);
        }
    }
  
    /**
     * 講座問卷資料
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sunnet_profans()
    {
      $class  = "109267"; // 班號
      $term   = "01";      // 期別
      $times  = "1";        // 梯次
      $course = "08";    // 課程編號
        $networkTool = new NetworkTool();
        //檢查目前主機是否為172.16.10.18
        if('172.16.10.18'==$_SERVER["SERVER_ADDR"]){
          $today = date('Ymd');
          $today_code = (intval($today) + 5401) * 365; //當天日期轉數字 8 碼 20200518 + 指定代碼 5401）* 365
          $url = "https://appweb-fet.hrd.gov.tw/api/sunnet_profans.php";
          $data=array(
            "code"       =>$today_code, 
            "class"      => $class,
            "term"       => $term,
            "times"      =>   $times,
            "course"     =>  $course
          );
          $result = $networkTool->httpPost($url,$data);
          dd($result);
        }else{
            die('目前並非處於可以同步更新的主機，目前主機ＩＰ'.$_SERVER["SERVER_ADDR"]);
        }
    }

    /**
     * 簽到資料
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sunnet_sign()
    {
      $class = "109207"; // 班號
      $term  = "01";    // 期別
        $networkTool = new NetworkTool();
        //檢查目前主機是否為172.16.10.18
        if('172.16.10.18'==$_SERVER["SERVER_ADDR"]){
          $today = date('Ymd');
          $today_code = (intval($today) + 5401) * 365; //當天日期轉數字 8 碼 20200518 + 指定代碼 5401）* 365
          $url = "https://appweb-fet.hrd.gov.tw/api/sunnet_sign.php";
    
          $data=array(
            "code"       => $today_code, 
            "class"      => $class,
            "term"       => $term
          );

          $result = $networkTool->httpPost($url,$data);
          dd($result);
        }else{
            die('目前並非處於可以同步更新的主機，目前主機ＩＰ'.$_SERVER["SERVER_ADDR"]);
        }
    }


    /**
     * 同步到南投測試機
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync2csdi()
    {

        //檢查目前主機是否為172.16.10.18
        if('172.16.10.18'==$_SERVER["SERVER_ADDR"]){
            //開始同步
     
            $results = shell_exec('rsync -avzh --delete --exclude=.env /var/www/html/csdi root@210.69.78.168:/var/www/html 2>&1');
            // $results = shell_exec('pwd');
            echo $results;
            die('已完成同步');
        }else{
            die('目前並非處於可以同步更新的主機，目前主機ＩＰ'.$_SERVER["SERVER_ADDR"]);
        }
    }


    /*
        簡訊測試
        ＜簡訊設定連線資訊＞
        ＜簡訊設定連線資訊＞
        簡訊特碼伺服器之IP：203.66.172.133
        Port：8001
        發送的門號：0911517635
        帳號：17635
        密碼：17635
    */

    public function sms()
    {

        //檢查目前主機是否為172.16.10.18
       // if('172.16.10.18'==$_SERVER["SERVER_ADDR"]){

            // $INCLUDE_ROOT = '/var/www/html/csdi/app/Http/Controllers/';
            // include_once($INCLUDE_ROOT . "sms2_php7.php");
            error_reporting (E_ALL);
            
            echo "<h2> 測試簡訊 </h2>\n";
            
            //static encoding number
            $ENCODING_BIG5=1;
            $ENCODING_UCS2=3;
            $ENCODING_UTF8=4;
            
            /* Socket to Air Server IP ,Port */
            $server_ip = '203.66.172.133';
            $server_port = 8001;
            $TimeOut=10;
     
            $user_acc  = "17635";
            $user_pwd  = "17635";
            $mobile_number= "0934059998";

            $origin_message= "Sam message";
            $message = mb_convert_encoding($origin_message, "big5", "utf-8"); //big5 utf-8 ucs-2
            //mb_convert_encoding(message,encodeTo,encodeFrom), encodeTo:使用哪種編碼傳送, encodeFrom:系統環境編碼
            
            
            /*建立連線*/
            $encoding = $ENCODING_UTF8;
            $mysms = new sms2($encoding);
            $ret_code = $mysms->create_conn($server_ip, $server_port, $TimeOut, $user_acc, $user_pwd);
            $ret_msg = $mysms->get_ret_msg();
            
            if($ret_code==0){ 
                  echo "連線成功"."<br>\n";
                  /*如欲傳送多筆簡訊，連線成功後使用迴圈執行$mysms->send_text()即可*/
                  $ret_code = $mysms->send_text($mobile_number, $message);
                  $ret_msg = $mysms->get_ret_msg();
                  if($ret_code==0){
                     echo "簡訊傳送成功"."<br>";
                     echo "ret_code=".$ret_code."<br>\n";
                     echo "ret_msg=".$ret_msg."<br>\n";
                  }else{
                       echo "簡訊傳送失敗"."<br>\n";
                     echo "ret_code=".$ret_code."<br>\n";
                     echo "ret_msg=".$ret_msg."<br>\n";
                  }
            } else {  
                  echo "連線失敗"."<br>\n";
                  echo "ret_code=".$ret_code."<br>\n";
                  echo "ret_msg=".$ret_msg."<br>\n";
            }
            
            /*關閉連線*/
            $mysms->close_conn();
            
     //   }else{
     //       die('目前並非處於可以測試簡訊的主機，目前主機ＩＰ'.$_SERVER["SERVER_ADDR"]);
    //    }
    }

}


class sms2{

   var $usenet_handle;    /* socket handle*/
   var $ret_code;
   var $ret_msg;
   var $send_msisdn="";
   var $send_msg_len=266; /* Socket 傳送 SendMsg 的長度為266 */
   var $ret_msg_len=244;  /* Socket 接收 RetMsg 的長度為244 */
   var $send_set_len=100;
   var $send_content_len=160;
   var $ENCODING_BIG5=1;
   var $ENCODING_UCS2=3;
   var $ENCODING_UTF8=4;
   var $encoding_num= 1; #1:BIG5, 3:UCS-2, 4:UTF-8
   
   //function sms2(){}
   function sms2($encoding_num = null){ 
       if($encoding_num==$this->ENCODING_UCS2){
           $this->encoding_num = $this->ENCODING_UCS2;
       }elseif($encoding_num==$this->ENCODING_UTF8) {
           $this->encoding_num = $this->ENCODING_UTF8;
       }else{
           $this->encoding_num = $this->ENCODING_BIG5;
       }
    }

   /* 函式說明：建立連線與認證
    * $server_ip:伺服器IP, $server_port:伺服器Port, $TimeOut:連線timeout時間
    * $user_acc:帳號, $user_pwd:密碼
    * return -1：網路連線失敗, 0：連線與認證成功, 1:連線成功，認證失敗
    */
   function create_conn($server_ip, $server_port, $TimeOut, $user_acc, $user_pwd){
      $msg_type=0;	   /* 0:檢查帳號密碼 1:傳送簡訊 2:查詢傳送結果 */

      $this->usenet_handle = fsockopen($server_ip, $server_port, $errno, $errstr, $TimeOut);
   
      if(!$this->usenet_handle) {
      	 $this->ret_code=-1;
      	 $this->ret_msg="Connection failed!";
      	 return $this->ret_code;
      }
      /* 帳號密碼檢查 */
      $msg_set=$user_acc . "\0" . $user_pwd . "\0";
      $in_temp = pack("C",$msg_type) . pack("C",1) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set)) . pack("C",0) . $msg_set;
      
   
      /*---將未滿$send_msg_len的資料填\0補滿 */
      $len_p = $this->send_msg_len - strlen($in_temp);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      
      $in = $in_temp . $zero_buf;

      $out = '';
      $write = fwrite($this->usenet_handle, $in); 
      $out = fread($this->usenet_handle, $this->ret_msg_len);
   
      /* 取出ret_code */
      $ret_C = substr($out, 0, 1);           /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
    //   $ret_code_value = each($ret_code_array);    /* array[1]為ret_code的值 */
      $ret_code_value = $ret_code_array[1];    /* array[1]為ret_code的值 */
      /* 取出ret_content*/
      $ret_CL = substr($out, 3, 1);          /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL);  /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
    //   $ret_content_len = each($ret_cl_array); /* array[1]為ret_content_len的值 */
      //   $ret_content = substr($out, 84, $ret_content_len[1]); /* 取得回傳的內容*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/

    //   $this->ret_code=$ret_code_value[1];  /* array[1]為ret_code的值 */
      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   }   

   /* 函式說明：傳送文字簡訊
    * $tel:接收門號, 簡訊內容
    * return ret_code
    */
   function send_text( $mobile_number, $message){   	  
   	  if(substr($mobile_number, 0, 1)== "+" ){
      	 $msg_type=15; /* 傳送國際簡訊 */
      }else{
      	 $msg_type=1; /* 傳送國內簡訊 */
      }
      	 
      $send_type="01"; /* 01 : 即時傳送*/
      $msg_set_str=$mobile_number . "\0" . $send_type . "\0";

      /*---將未滿$msg_set長度的資料填\0補滿 */
      $len_p = $this->send_set_len - strlen($msg_set_str);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      $msg_set = $msg_set_str . $zero_buf;
   
      /*---將未滿$msg_content長度的資料填\0補滿 */
      $len_p = $this->send_content_len - strlen($message);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      $msg_content = $message . $zero_buf;

      $in = pack("C",$msg_type) . pack("C",$this->encoding_num) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set_str)) . pack("C",strlen($message)) . $msg_set . $msg_content;
      
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      $ret_C = substr($out, 0, 1); /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
    //   $ret_code_value = each($ret_code_array); /* array[1]為ret_code的值 */
      $ret_code_value = $ret_code_array[1]; /* array[1]為ret_code的值 */
   
      $ret_CL = substr($out, 3, 1); /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
    //   $ret_content_len = each($ret_cl_array); /* array[1]為ret_content_len的值 */
      //   $ret_content = substr($out, 84, $ret_content_len[1]); /* 取得回傳的內容*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/
      
    //   $this->ret_code=$ret_code_value[1];  /* array[1]為ret_code的值 */
      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   }


   /* 函式說明：傳送WapPush簡訊
    * $tel:接收門號, 簡訊內容
    * return ret_code
    */
   function send_wappush( $mobile_number, $wap_title, $wap_url){
      $msg_type=13; /* 傳送簡訊 */
      $send_type="01"; /* 01:SI*/
      $msg_set_str=$mobile_number . "\0" . $send_type . "\0";

      /*---將未滿$msg_set長度的資料填\0補滿 */
      $len_p = $this->send_set_len - strlen($msg_set_str);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      $msg_set = $msg_set_str . $zero_buf;
   
      /*---將未滿$msg_content長度的資料填\0補滿 */
      $msg_content_tmp = $wap_url . "\0" . $wap_title . "\0";
      $len_p = $this->send_content_len - strlen($msg_content_tmp);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      $msg_content = $msg_content_tmp . $zero_buf;

      $in = pack("C",$msg_type) . pack("C",$this->encoding_num) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set_str)) . pack("C",strlen($msg_content_tmp)) . $msg_set . $msg_content;
      
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      $ret_C = substr($out, 0, 1); /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
    //   $ret_code_value = each($ret_code_array); /* array[1]為ret_code的值 */
      $ret_code_value = $ret_code_array[1]; /* array[1]為ret_code的值 */
   
      $ret_CL = substr($out, 3, 1); /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
    //   $ret_content_len = each($ret_cl_array); /* array[1]為ret_content_len的值 */
    //   $ret_content = substr($out, 84, $ret_content_len[1]); /* 取得回傳的內容*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/
      
    //   $this->ret_code=$ret_code_value[1];  /* array[1]為ret_code的值 */
      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   }

   /* 函式說明：查詢text發訊結果
    * $messageid:訊息ID
    * return ret_code
    */
   function query_text( $messageid){
      $msg_type=2; /* 查詢text傳送結果 */
      $msg_set=$messageid;
      $in_temp = pack("C",$msg_type) . pack("C",1) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set)) . pack("C",0) . $msg_set;
      
      /*---將未滿$send_msg_len的資料填\0補滿 */
      $len_p = $this->send_msg_len - strlen($in_temp);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      
      $in = $in_temp . $zero_buf;
      $out = '';
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      $ret_C = substr($out, 0, 1); /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
    //   $ret_code_value = each($ret_code_array); /* array[1]為ret_code的值 */
      $ret_code_value = $ret_code_array[1]; /* array[1]為ret_code的值 */
   
      $ret_CL = substr($out, 3, 1); /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
    //   $ret_content_len = each($ret_cl_array); /* array[1]為ret_content_len的值 */
    //   $ret_content = substr($out, 84, $ret_content_len[1]); /* 取得回傳的內容*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/
      
    //   $this->ret_code=$ret_code_value[1];  /* array[1]為ret_code的值 */
      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   }


   /* 函式說明：查詢wappush發訊結果
    * $messageid:訊息ID
    * return ret_code
    */
   function query_wappush( $messageid){
      $msg_type=14; /* 查詢wappush傳送結果 */
      $msg_set=$messageid;
      $in_temp = pack("C",$msg_type) . pack("C",1) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set)) . pack("C",0) . $msg_set;
      
      /*---將未滿$send_msg_len的資料填\0補滿 */
      $len_p = $this->send_msg_len - strlen($in_temp);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      
      $in = $in_temp . $zero_buf;
      $out = '';
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      $ret_C = substr($out, 0, 1); /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
    //   $ret_code_value = each($ret_code_array); /* array[1]為ret_code的值 */
      $ret_code_value = $ret_code_array[1]; /* array[1]為ret_code的值 */
   
      $ret_CL = substr($out, 3, 1); /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
    //   $ret_content_len = each($ret_cl_array); /* array[1]為ret_content_len的值 */
    //   $ret_content = substr($out, 84, $ret_content_len[1]); /* 取得回傳的內容*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/
      
    //   $this->ret_code=$ret_code_value[1];  /* array[1]為ret_code的值 */
      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      return $this->ret_code;
   }

   /* 函式說明：接收回傳的訊息
    * return ret_code
    */
   function recv_msg(){
      $msg_type=3; /* 接收回傳的訊息 */
      $msg_set="";
      $in_temp = pack("C",$msg_type) . pack("C",1) . pack("C",1) . pack("C",0) . pack("C",strlen($msg_set)) . pack("C",0) . $msg_set;
      
      /*---將未滿$send_msg_len的資料填\0補滿 */
      $len_p = $this->send_msg_len - strlen($in_temp);
      $zero_buf='';
      for($i=0;$i<$len_p;$i++){
         $zero_buf = $zero_buf . "\0";
      }
      
      $in = $in_temp . $zero_buf;
      $out = '';
      $write = fwrite($this->usenet_handle, $in);
      $out = fread($this->usenet_handle, $this->ret_msg_len);
      $ret_C = substr($out, 0, 1); /* 取出 ret_code */
      $ret_code_array = unpack("C", $ret_C); /* 將$ret_C 轉成unsigned char , unpack 會return array*/
    //   $ret_code_value = each($ret_code_array); /* array[1]為ret_code的值 */
      $ret_code_value = $ret_code_array[1]; /* array[1]為ret_code的值 */

      $ret_CL = substr($out, 2, 1); /* 取出 ret_set_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
    //   $ret_set_len = each($ret_cl_array); /* array[1]為ret_set_len的值 */
    //   $ret_set = substr($out, 4, $ret_set_len[1]); /* 取得回傳set的內容*/
      $ret_set_len = $ret_cl_array[0]; /* array[1]為ret_set_len的值 */
      $ret_set = substr($out, 4, $ret_set_len); /* 取得回傳set的內容*/
      $send_msisdn_array = split('\x0',$ret_set); /* 取得傳回者的手機門號*/

      $ret_CL = substr($out, 3, 1); /* 取出 ret_content_len */
      $ret_cl_array = unpack("C", $ret_CL); /* 將$ret_CL 轉成unsigned char , unpack 會return array*/
    //   $ret_content_len = each($ret_cl_array); /* array[1]為ret_content_len的值 */
    //   $ret_content = substr($out, 84, $ret_content_len[1]); /* 取得回傳的內容*/
      $ret_content_len = $ret_cl_array[1]; /* array[1]為ret_content_len的值 */
      $ret_content = substr($out, 84, $ret_content_len); /* 取得回傳的內容*/
      
    //   $this->ret_code=$ret_code_value[1];  /* array[1]為ret_code的值 */
      $this->ret_code=$ret_code_value;  /* array[1]為ret_code的值 */
      $this->ret_msg=$ret_content;
      $this->send_msisdn=$send_msisdn_array[0]; /* array[0]為回傳者的門號 */
      return $this->ret_code;
   }   

   /* 回傳ret_content的值 */
   function get_ret_msg(){
      return $this->ret_msg;
   }

   /* 回傳send_tel的值 */
   function get_send_tel(){
      return $this->send_msisdn;
   }
  
   /* 關閉連線 */
   function close_conn(){
   	  if($this->usenet_handle)
         fclose ($this->usenet_handle);
   }
}