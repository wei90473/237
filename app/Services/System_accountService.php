<?php
namespace App\Services;

use App\Repositories\System_accountRepository;


class System_accountService
{
    /**
     * System_accountService constructor.
     * @param System_accountRepository $system_accountRpository
     */
    public function __construct(System_accountRepository $system_accountRpository)
    {
        $this->system_accountRpository = $system_accountRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSystem_accountList($queryData = [])
    {
        return $this->system_accountRpository->getSystem_accountList($queryData);
    }

    public function getSections()
    {
        return $this->system_accountRpository->getSections();
    }

    public function getUser_group()
    {
        return $this->system_accountRpository->getUser_group();
    }

    public function getBy_user_group_id($user_group_id)
    {
        return $this->system_accountRpository->getBy_user_group_id($user_group_id);
    }

    public function getDelete($userid)
    {
        return $this->system_accountRpository->getDelete($userid);
    }

}
