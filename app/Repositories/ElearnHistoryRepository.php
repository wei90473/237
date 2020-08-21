<?php
namespace App\Repositories;

use App\Models\Elearn_history;
use App\Repositories\Repository;
use DateTime;

class ElearnHistoryRepository extends Repository
{
    public function __construct(Elearn_history $elearn_class)
    {
        $this->model = $elearn_class;
    }  

    public function getByElearnIds($ids)
    {
        return $this->model->whereIn('elearn_class_id', $ids)
                           ->get();
    }        

    public function batchNoPass($elearn_id, $idnos)
    {
        $this->model->where('elearn_class_id', '=', $elearn_id)
                    ->whereIn('idno', $idnos)
                    ->update(['status' => 'N']);
    }

    public function batchPass($elearn_id, $idnos)
    {
        $this->model->where('elearn_class_id', '=', $elearn_id)
                    ->whereIn('idno', $idnos)
                    ->update(['status' => 'Y']);        
    }    

}