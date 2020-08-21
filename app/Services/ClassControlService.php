<?php
namespace App\Services;

use App\Repositories\ClassControlRepository;


class ClassControlService
{
    /**
     * ClassControlService constructor.
     * @param ClassControlRepository $classControlRepository
     */
    public function __construct(ClassControlRepository $classControlRepository)
    {
        $this->classControlRepository = $classControlRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassControlList($queryData = [])
    {
        if ($queryData['type'] == '') {
            // 未搜尋
            return array();
        } elseif ($queryData['type'] == '1' || $queryData['type'] == '3') {
            // 需求
            return $this->classControlRepository->getClassControlList($queryData);

        } else {
            // 確認
        }

    }
}
