<?php
namespace App\Services;

use App\Repositories\SessionRepository;


class SessionService
{
    /**
     * SessionService constructor.
     * @param SessionRepository $sessionRpository
     */
    public function __construct(SessionRepository $sessionRpository)
    {
        $this->sessionRpository = $sessionRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSessionList($queryData = [])
    {
        return $this->sessionRpository->getSessionList($queryData);
    }
}
