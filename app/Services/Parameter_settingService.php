<?php
namespace App\Services;

use App\Repositories\Parameter_settingRepository;


class Parameter_settingService
{
    /**
     * Parameter_settingService constructor.
     * @param Parameter_settingRepository $parameter_settingRpository
     */
    public function __construct(Parameter_settingRepository $parameter_settingRpository)
    {
        $this->parameter_settingRpository = $parameter_settingRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getParameter_setting1List($queryData = [])
    {
        return $this->parameter_settingRpository->getParameter_setting1List($queryData);
    }

    public function getParameter_setting2List($queryData = [])
    {
        return $this->parameter_settingRpository->getParameter_setting2List($queryData);
    }

    public function getParameter_setting3List($queryData = [])
    {
        return $this->parameter_settingRpository->getParameter_setting3List($queryData);
    }

}
