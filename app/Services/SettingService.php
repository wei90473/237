<?php
namespace App\Services;

use App\Repositories\SettingRepository;
use App\Http\Controllers\Controller;


class SettingService extends Controller
{
    /**
     * SettingService constructor.
     * @param SettingRepository $settingRepository
     */
    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * 取得設定值
     *
     * @param $unitAry
     * @return array
     */
    public function get($unitAry)
    {
        if (is_string($unitAry)) {

            $unitAry = array($unitAry);
        }

        $result = array();

        foreach ($unitAry as $unit) {

            $result[$unit] = $this->settingRepository->get($unit);
        }

        return (object)$result;
    }

    /**
     * 儲存設定值
     *
     * @param $data
     */
    public function set($data)
    {
        foreach ($data as $unit => $value) {

            $this->settingRepository->set($unit, $value);
        }
    }
}
