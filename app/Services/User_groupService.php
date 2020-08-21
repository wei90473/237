<?php
namespace App\Services;

use App\Repositories\User_groupRepository;


class User_groupService
{
    /**
     * User_groupService constructor.
     * @param User_groupRepository $user_groupRpository
     */
    public function __construct(User_groupRepository $user_groupRpository)
    {
        $this->user_groupRpository = $user_groupRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getUser_groupList($queryData = [])
    {
        return $this->user_groupRpository->getUser_groupList($queryData);
    }

    public function getUser_group_auth($user_group_id)
    {
        return $this->user_groupRpository->getUser_group_auth($user_group_id);
    }

    public function getUser_auth($user_group_id)
    {
        return $this->user_groupRpository->getUser_auth($user_group_id);
    }

}
