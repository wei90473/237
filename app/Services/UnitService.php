<?php
namespace App\Services;

use App\Repositories\T05tbRepository;
use App\Repositories\T04tbRepository;


class UnitService
{
    /**
     * TrainService constructor.
     * @param T05tbRepository $t05tbRpository
     */
    public function __construct(
        T05tbRepository $t05tbRepository,
        T04tbRepository $t04tbRepository)
    {
        $this->t05tbRepository = $t05tbRepository;
        $this->t04tbRepository = $t04tbRepository;
    }

    public function storeT05tb($t05tb, $action, $unit_info = null)
    {
        if ($action == "insert"){
            $unit_info = [
                'class' => $t05tb['class'],
                'term' => $t05tb['term']
            ];
            $last_unit = $this->t05tbRepository->getLastUnit($unit_info);
            $new_unit = str_pad((int)$last_unit+1, 2, '0', STR_PAD_LEFT);
            $t05tb['unit'] = $new_unit;
            return $this->t05tbRepository->insert($t05tb);
        }elseif($action == "update"){
            return $this->t05tbRepository->update($unit_info, $t05tb);
        }
    }

    public function deleteT05tb($t05tb_info)
    {
        return $this->t05tbRepository->delete($t05tb_info);
    }

    public function getT04tb($t04tb_info)
    {
        return $this->t04tbRepository->find($t04tb_info);
    }

    public function getT05tb($t05tb_info)
    {
        return $this->t05tbRepository->find($t05tb_info);
    }
}
