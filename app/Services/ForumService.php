<?php
namespace App\Services;

use App\Repositories\ForumRepository;


class ForumService
{
    /**
     * ForumService constructor.
     * @param ForumRepository $forumRpository
     */
    public function __construct(ForumRepository $forumRpository)
    {
        $this->forumRpository = $forumRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getForumList($queryData = [])
    {
        if ($queryData['type'] == '2') {

            $result = $this->forumRpository->getT34tbList($queryData);

        } else {

            $result = $this->forumRpository->getT33tbList($queryData);
        }

        return $result;
    }
}
