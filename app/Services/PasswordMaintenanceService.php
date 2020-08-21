<?php
namespace App\Services;

use App\Repositories\PasswordMaintenanceRepository;


class PasswordMaintenanceService
{
    /**
     * PasswordMaintenanceService constructor.
     * @param PasswordMaintenanceRepository $passwordMaintenanceRpository
     */
    public function __construct(PasswordMaintenanceRepository $passwordMaintenanceRpository)
    {
        $this->passwordMaintenanceRpository = $passwordMaintenanceRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPasswordMaintenanceList($queryData = [])
    {
        return $this->passwordMaintenanceRpository->getPasswordMaintenanceList($queryData);
    }
}
