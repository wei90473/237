<?php
namespace App\Services;

use App\Repositories\Web_simulationRepository;


class Web_simulationService
{
    /**
     * Web_simulationService constructor.
     * @param Web_simulationRepository $web_simulationRpository
     */
    public function __construct(Web_simulationRepository $web_simulationRpository)
    {
        $this->web_simulationRpository = $web_simulationRpository;
    }

    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getWeb_simulation($queryData = [])
    {
        return $this->web_simulationRpository->getWeb_simulation($queryData);
    }

}
