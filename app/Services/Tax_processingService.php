<?php
namespace App\Services;

use App\Repositories\Tax_processingRepository;


class Tax_processingService
{
    /**
     * Tax_processingService constructor.
     * @param Tax_processingRepository $tax_processingRpository
     */
    public function __construct(Tax_processingRepository $tax_processingRpository)
    {
        $this->tax_processingRpository = $tax_processingRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTax_processingList($queryData = [])
    {
        return $this->tax_processingRpository->getTax_processingList($queryData);
    }

    public function TaxExists($queryData = [])
    {
        return $this->tax_processingRpository->TaxExists($queryData);
    }

    public function taxReturn($queryData = [])
    {
        return $this->tax_processingRpository->taxReturn($queryData);
    }

    public function getFile($queryData = [])
    {
        return $this->tax_processingRpository->getFile($queryData);
    }

    public function getFileName($queryData = [])
    {
        return $this->tax_processingRpository->getFileName($queryData);
    }

}
