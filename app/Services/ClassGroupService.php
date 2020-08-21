<?php
namespace App\Services;

use App\Repositories\ClassGroupRepository;


class ClassGroupService
{
    /**
     * ClassGroupService constructor.
     * @param ClassGroupRepository $classgroupRpository
     */
    public function __construct(ClassGroupRepository $classgroupRpository)
    {
        $this->classGroupRepository = $classgroupRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getGroupList($queryData = [])
    {
        return $this->classGroupRepository->getGroupList($queryData);
    }
   
}
