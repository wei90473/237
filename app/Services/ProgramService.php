<?php
namespace App\Services;

use App\Repositories\ProgramRepository;
use App\Http\Controllers\Controller;

class ProgramService extends Controller
{
    /**
     * ProgramService constructor.
     * @param ProgramRepository $programRepository
     */
    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getProgramList($queryData = [])
    {
        return $this->programRepository->getProgramList($queryData);
    }

    /**
     * 取得查詢列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSearchList($queryData = [])
    {
        return $this->programRepository->getSearchList($queryData);
    }

}
