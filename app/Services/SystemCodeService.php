<?php
namespace App\Services;

use App\Repositories\SystemCodeRepository;


class SystemCodeService
{
    /**
     * SystemCodeService constructor.
     * @param SystemCodeRepository $systemCodeRpository
     */
    public function __construct(SystemCodeRepository $systemCodeRpository)
    {
        $this->systemCodeRpository = $systemCodeRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSystemCodeList($queryData = [])
    {
        return $this->systemCodeRpository->getSystemCodeList($queryData);
    }
}
