<?php
namespace App\Services;
 
use App\Repositories\NoticeEmailRepository;

class NoticeEmailService
{
    /**
     * NoticeEmailService constructor.
     * @param NoticeEmailRepository $noticeEmailRpository
     */
    public function __construct(NoticeEmailRepository $noticeEmailRpository)
    {
        $this->noticeEmailRpository = $noticeEmailRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getNoticeEmailList($queryData = [])
    {
        return $this->noticeEmailRpository->getNoticeEmailList($queryData);
    }

    public function getSponsor()
    {
        return $this->noticeEmailRpository->getSponsor();
    }

    public function getClass($queryData = [])
    {
        return $this->noticeEmailRpository->getClass($queryData);
    }

    public function getMailData($queryData = [])
    {
        return $this->noticeEmailRpository->getMailData($queryData);
    }

    public function getStudentMail($queryData = [])
    {
        return $this->noticeEmailRpository->getStudentMail($queryData);
    }

    public function getTTLMailData($queryData = [])
    {
        return $this->noticeEmailRpository->getTTLMailData($queryData);
    }

    public function getTTLStudentMail($queryData = [])
    {
        return $this->noticeEmailRpository->getTTLStudentMail($queryData);
    }
}
