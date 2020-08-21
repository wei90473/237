<?php
namespace App\Services;

use App\Repositories\ClassMaterialRepository;


class ClassMaterialService
{
    /**
     * ClassMaterialService constructor.
     * @param ClassMaterialRepository $classMaterialRepository
     */
    public function __construct(ClassMaterialRepository $classMaterialRepository)
    {
        $this->classMaterialRepository = $classMaterialRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassMaterialList($queryData = [])
    {
        return $this->classMaterialRepository->getClassMaterialList($queryData);
    }

    /**
     * 取得已選取的教材
     *
     * @param $queryData
     * @return mixed
     */
    public function selectMaterial($queryData)
    {
        return $this->classMaterialRepository->selectMaterial($queryData);
    }

    /**
     * 取得本班講座
     *
     * @param $data
     * @return array
     */
    public function getTeacher($data)
    {
        $result = array();

        if (is_array($data)) {
            foreach ($data as $va) {
                $result[$va->idno] = $va->teacher;
            }
        }

        return $result;
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        return $this->classMaterialRepository->getClassList();
    }
}
