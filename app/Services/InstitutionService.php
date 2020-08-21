<?php
namespace App\Services;

use App\Repositories\InstitutionRepository;
use App\Http\Controllers\Controller;

class InstitutionService extends Controller
{
    /**
     * InstitutionService constructor.
     * @param InstitutionRepository $institutionRepository
     */
    public function __construct(InstitutionRepository $institutionRepository)
    {
        $this->institutionRepository = $institutionRepository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getInstitutionList($queryData = [])
    {
        return $this->institutionRepository->getInstitutionList($queryData);
    }
    
    
}
