<?php
namespace App\Helpers;

/*
Sam 調用網路傳輸的function
*/
class NetworkTool
{
    protected $api_key = 'NULL';
  	/**
	* utf8字元轉換成Unicode字元
	* @param [type] $utf8_str Utf-8字元
	* @return [type]      Unicode字元
	*/
	function utf8_str_to_unicode($utf8_str) {
        $unicode = 0;
        $unicode = (ord($utf8_str[0]) & 0x1F) << 12;
        $unicode |= (ord($utf8_str[1]) & 0x3F) << 6;
        $unicode |= (ord($utf8_str[2]) & 0x3F);
        return dechex($unicode);
    }
    
    /**
    * Unicode字元轉換成utf8字元
    * @param [type] $unicode_str Unicode字元
    * @return [type]       Utf-8字元
    */
    function unicode_to_utf8($unicode_str) {
    $utf8_str = '';
    $code = intval(hexdec($unicode_str));
    //這裡注意轉換出來的code一定得是整形，這樣才會正確的按位操作
    $ord_1 = decbin(0xe0 | ($code >> 12));
    $ord_2 = decbin(0x80 | (($code >> 6) & 0x3f));
    $ord_3 = decbin(0x80 | ($code & 0x3f));
    $utf8_str = chr(bindec($ord_1)) . chr(bindec($ord_2)) . chr(bindec($ord_3));
    return $utf8_str;
    }

    function httpGet($url)
    {
        $ch     = curl_init();  	 
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);	 
        curl_close($ch);
        return $output;
    }

    function httpPost($url,$post_data)
    {
    
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
            return $data = curl_exec($ch);
        }else{	 
            return 'curl_error : ' . curl_error($ch); 
        }
    }
}


