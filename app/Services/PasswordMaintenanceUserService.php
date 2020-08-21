<?php
namespace App\Services;

use App\Repositories\PasswordMaintenanceUserRepository;


class PasswordMaintenanceUserService
{
    /**
     * PasswordMaintenanceUserService constructor.
     * @param PasswordMaintenanceUserRepository $passwordMaintenanceUserRpository
     */
    public function __construct(PasswordMaintenanceUserRepository $passwordMaintenanceUserRpository)
    {
        $this->passwordMaintenanceUserRpository = $passwordMaintenanceUserRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPasswordMaintenanceUserList($queryData = [])
    {
        return $this->passwordMaintenanceUserRpository->getPasswordMaintenanceUserList($queryData);
    }
}
