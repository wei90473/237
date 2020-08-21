<?php
namespace App\Helpers;

use DateTime;

class Common{
    /*
        日期格式轉換
        中華民國年 轉成 西元年
    */
    public static function dateRocToCeFormat($date)
    {
        $length = strlen((string)$date);
        if ($length != 7) return false;
        $year = (int)substr($date, 0, 3) + 1911; 
        $date = $year.substr($date, 3);

        if (DateTime::createFromFormat("Ymd", $date)->format('Ymd') == $date){
            return $date;
        }else{
            return false;
        }
    }

    /*
        日期格式轉換
        西元年 轉成 中華民國年
    */

    public static function dateCeToRocFormat($date)
    {
        $length = strlen((string)$date);
        if ($length != 8) return false;
        $year = (int)substr($date, 0, 4) - 1911; 
        return $year.substr($date, 4);
    }
    /*
        是否為假日
    */
    public static function isHoliday($date)
    {
        $date = new DateTime($date);
        return ((int)$date->format("w") === 0 || (int)$date->format("w") === 6);
    }


    public static function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public static function computeAge($birthday, $year = null)
    {
        $birthday_year = (int)substr($birthday, 0, 3);
        $now = new DateTime();
        $year = (empty($year)) ? (int)($now->format("Y"))-1911 : $year;
        $age = $year - $birthday_year;            
        return ($age > 0) ? $age : 0;
    }
    
    public static function checkRepeat($array)
    {
        if (count($array) != count(array_unique($array))) {
            // 獲取去掉重複資料的陣列   
            $unique_arr = array_unique ($array);   
            // 獲取重複資料的陣列   
            $repeat_arr = array_diff_assoc ($array, $unique_arr);
            return $repeat_arr;
        }
        return [];
    }

    public static function keyBy($skey, $array)
    {
        $new_array = [];
        foreach ($array as $data){
            if (is_array($data)){
                $new_array[$data[$skey]] = $data;
            }else if (is_object($data)){
                $new_array[$data->$skey] = $data;
            }
        }
        return $new_array;
    }

    public static function addDateSlash($Rocdate)
    {
        if (strlen($Rocdate) !== 7) return false;

        $year = substr($Rocdate, 0, 3);
        $month = substr($Rocdate, 3, 2);
        $day = substr($Rocdate, 5, 2);

        return "{$year}/{$month}/{$day}";
    }
}