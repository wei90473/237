<?php
namespace App\Services;

use App\Repositories\UsersRepository;


class UsersService
{
    /**
     * UsersService constructor.
     * @param UsersRepository $usersRpository
     */
    public function __construct(UsersRepository $usersRpository)
    {
        $this->usersRpository = $usersRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getUsersList($queryData = [])
    {
        return $this->usersRpository->getUsersList($queryData);
    }
}
