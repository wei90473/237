<?php
namespace App\Repositories;

use App\Models\Setting;


class SettingRepository
{
    /**
     * 取得設定值
     *
     * @param $unit
     * @return string
     */
    public function get($unit)
    {
        $data = Setting::where('unit', $unit)->first();

        return (isset($data['value']))? $data['value'] : '';
    }

    /**
     * 儲存設定值
     *
     * @param $unit
     * @param $value
     */
    public function set($unit, $value)
    {
        Setting::updateOrCreate(['unit' => $unit], ['unit' => $unit, 'value' => $value]);
    }

}
