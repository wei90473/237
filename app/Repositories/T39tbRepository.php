<?php
namespace App\Repositories;

use App\Models\T39tb;
use App\Repositories\Repository;
use DateTime;
use App\Helpers\TrainSchedule;
use DB;

class T39tbRepository extends Repository{
    public function __construct(T39tb $t39tb)
    {
        $this->model = $t39tb;
    }     

    public function get($queryData, $paginate = true)
    {
        $model = $this->model->select("*");

        $field = [
            'like' => ['enrollid'],
            'equal' => ['idno', 'cname', 'class', 'term']
        ];

        $model = $this->queryField($model, $field, $queryData);

        if (isset($queryData['prove'])){
            if ($queryData['prove'] == 'NA'){
                $model->where('prove', '<>', 'A');
            }else{
                $model->where('prove', '=', $queryData['prove']);
            }
        }

        $model->orderBy('idno');

        if ($paginate){
            return $model->paginate(10);
        }else{
            return $model->get();
        }
        
    }

    public function getT39tbMaxIdno($t04tb_info)
    {
        return $this->model->where($t04tb_info)
                    ->where('idno', 'LIKE', $t04tb_info['class'].$t04tb_info['term'].'%')
                    ->orderBy('idno', 'desc')
                    ->first();
    }

    public function setCondition($t04tb_info, $idnos, $chk)
    {
        return $this->model->where($t04tb_info)
                           ->whereIn('idno', $idnos)
                           ->update([$chk => 'Y']);
    }

    public function batchUpdateProve($classInfo, $prove, $idnos)
    {
        return $this->model->where($classInfo)
                           ->whereIn('idno', $idnos)
                           ->update(['prove' => $prove]);
    }

    public function getByIdnos($t04tb_info, $idnos, $select = "*")
    {
        return $this->model->select($select)
                           ->where($t04tb_info)
                           ->whereIn('idno', $idnos)
                           ->get();
    } 

    public function getByt04tb($t04tbKey)
    {
        return $this->model->where('class', '=', $t04tbKey['class'])
                           ->where('term', '=', $t04tbKey['term'])
                           ->get();
    }        
}