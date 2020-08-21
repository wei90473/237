<?php
namespace App\Helpers;

use App\Helpers\SystemParam;
use App\Helpers\Common;
use DateTime;
use Cache;

class TrainSchedule{
    public static function getAffirmDate($date = null)
    {
        /*
            確認凍結日
            上課開始日期 $date
            如果是星期一~六 凍結日就是上個星期一
            如果是星期日    凍結日就是那個禮拜的星期一
        */
        $today = new DateTime();

        if ($date == null){
            $today = new DateTime($today->format("Y-m-d"));
        }else{
            $today = $date;
        }

        if (Cache::get('weekly') == null){
            $system_weekly = SystemParam::get()->weekly;
            Cache::put('weekly', $system_weekly, 1);
        }else{
            $system_weekly = Cache::get('weekly');
        }
        
        $system_weekly = ($system_weekly == false) ? 2 : $system_weekly;
        $system_weekly--;
        $affirm_date = $today->modify("+".(string)(($system_weekly - 7 - (int)$today->format("w")))."day");

        return $affirm_date;
    }
    
    public static function getRequestDate($date = null)
    {
        // 需求確認日 = 這個月的 系統參數檔 monthly 日
        $today = new DateTime();

        if ($date == null){
            $today = new DateTime($today->format("Y-m-d"));
        }else{
            $today = $date;
        }

        if (Cache::get('monthly') == null){
            $system_monthly = SystemParam::get()->weekly;
            Cache::put('monthly', $system_monthly, 1);
        }else{
            $system_monthly = Cache::get('monthly');
        }

        $system_monthly = ($system_monthly == false) ? 1 : $system_monthly;
        $system_monthly = str_pad((string)$system_monthly, 2, '0', STR_PAD_LEFT);
        $request_date = new DateTime($today->format("Y-m-".$system_monthly));
        return $request_date;
    }


    /**
     * 取得某教室某時段的費用
     *
     * @param M14tb $m14tb
     * @param string $time 
     * @return int
     */
    public static function getSiteFee($m14tb, $time, $date)
    {
        $fee = [
            "A" => "feea",
            "B" => "feeb",
            "C" => "feec"
        ];
        $m14tb = $m14tb->toArray();
        $fees = [];

        if (!empty($m14tb)){
            $need_fee = ["2", "3", "4", "5"];
            if (!in_array($m14tb['feetype'], $need_fee)){
                return 0;
            }

            if ($m14tb['feetype'] == 3){
                // 週末假日收費
                if (Common::isHoliday($date)){
                    return $m14tb[$fee[$time]];
                }
            }else{
                return $m14tb[$fee[$time]];
            }
        }

        return 0;
    }    

    public static function getTime($time = null)
    {
        switch ($time) {
            case 'D':
                $times = [
                    "A" => config("t22tbtimes.times")["A"],
                    "B" => config("t22tbtimes.times")["B"]
                ];
                break;
            case 'E':
                $times = [
                    "A" => config("t22tbtimes.times")["A"],
                    "B" => config("t22tbtimes.times")["B"],
                    "C" => config("t22tbtimes.times")["C"]
                ];
                break;                                
            default:
                $times = [
                    $time => config("t22tbtimes.times")[$time]
                ];
                break;
        }
         
        return $times;       
    }
}