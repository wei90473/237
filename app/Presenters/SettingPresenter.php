<?php

namespace App\Presenters;

use App\Models\Setting;


class SettingPresenter
{
    /**
     * 取得setting值
     *
     * @param $unit
     * @return string
     */
    public function get($unit)
    {
        $data = Setting::where('unit', $unit)->first();

        return (isset($data->value))? $data->value : '';
    }
}
