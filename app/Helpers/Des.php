<?php
namespace App\Helpers;

class Des
{
    public static function encode($data, $key)
    {
        $token = base64_encode(openssl_encrypt($data, 'des-ede3', $key));      
        $token = str_replace(array(' ', '/', '='), array('-', '_', ''), $token);
        return $token;  
    }

    public static function decode($data, $key)
    {
        $data = str_replace(array('-', '_'), array(' ', '/'), $data);
        return openssl_decrypt(base64_decode($data), 'des-ede3', $key);
    }

    public static function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public static function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

    public static function encrypt($key, $data) {
        $size = mcrypt_get_block_size('des', 'ecb');
        $data = DES::pkcs5_pad($data, $size);
        $td = @mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $data);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $data;
    }

    public static function decrypt($key, $data) {
        $td =@ mcrypt_module_open('des','','ecb','');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = @mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        $decrypted = @mdecrypt_generic($td, $data);
        @mcrypt_generic_deinit($td);
        @mcrypt_module_close($td);
        $result = DES::pkcs5_unpad($decrypted);
        return $result;
    }

    public static function base64url_encode($data) {
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data) {
      return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public static function sso_des_encode($text,$key='sam') {
        $base64 = base64_encode(openssl_encrypt($text, 'des-ecb', $key));
        return rtrim(strtr($base64,'+/','-_'),'=');
       }
         
    public static  function sso_des_decode($text,$key='sam') {
    $base64 = base64_decode(str_pad(strtr($text,'-_','+/'),strlen($text) % 4,'='));
    return openssl_decrypt($base64, 'des-ecb', $key);
    }
}


