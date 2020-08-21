<?php
namespace App\Services;

use App\Repositories\T04tbRepository;
use App\Repositories\T01tbRepository;
use App\Repositories\M17tbRepository;
use App\Repositories\M14tbRepository;
use App\Repositories\EdufloorRepository;
use App\Repositories\EdubedRepository;
use DateTime;

use DB;

class FieldService
{
    public function __construct(
        T04tbRepository $t04tbRepository,
        T01tbRepository $t01tbRepository,
        M17tbRepository $m17tbRepository,
        M14tbRepository $m14tbRepository,
        EdufloorRepository $eduFloorRepository,
        EdubedRepository $eduBedRepository
    )
    {
        $this->t04tbRepository = $t04tbRepository;
        $this->t01tbRepository = $t01tbRepository;
        $this->m17tbRepository = $m17tbRepository;
        $this->m14tbRepository = $m14tbRepository;
        $this->eduFloorRepository = $eduFloorRepository;
        $this->eduBedRepository = $eduBedRepository;
    }
    
    public function getT01tb($class, $queryData)
    {
        $t01tb = $this->t01tbRepository->find($class);
        $t04tbs = $t01tb->t04tbs()->select(['term', 'sdate', 'edate']);
        
        if (!empty($queryData['s_month'])){
            $queryData['s_month'] = str_pad($queryData['s_month'], 2, '0', STR_PAD_LEFT);
            $queryData['s_month'] = new DateTime(($queryData['yerly'] + 1911).$queryData['s_month'].'01');
            $t04tbs->where('sdate', '>=', $queryData['yerly'].$queryData['s_month']->format('md'));
        }

        if (!empty($queryData['e_month'])){
            $queryData['e_month'] += 1;
            $queryData['e_month'] = str_pad($queryData['e_month'], 2, '0', STR_PAD_LEFT);
            $queryData['e_month'] = new DateTime(($queryData['yerly'] + 1911).$queryData['e_month'].'01');
            $queryData['e_month']->modify('-1 day');
            $t04tbs->where('sdate', '<=', $queryData['yerly'].$queryData['e_month']->format('md'));
        }
        $t01tb->t04tbs = $t04tbs->get();
        return $t01tb;
    }

    public function getT01tbs($queryData)
    {
        $queryData['_paginate_qty'] = 30;
        return $this->t01tbRepository->get($queryData, true, "class id, CONCAT(class,' ',name) text");
    }

    public function getT04tbs($queryData)
    {
        return $this->t04tbRepository->get($queryData, true, ['t04tb' => ['class', 'term', 'sdate', 'edate'], 't01tb' => ['class', 'name']]);
    }

    public function getT04tb($queryData)
    {
        $t04tb = $this->t04tbRepository->find($queryData);

        $data['t04tb'] = $t04tb->toArray();
        $data['t04tb']['sdateformat'] = $t04tb->sdateformat;
        $data['t04tb']['edateformat'] = $t04tb->edateformat;                
        $data['t04tb']['t01tb'] = ['name' => $t04tb->t01tb->name];
        $data['t04tb']['t01tb']['s01tb'] = $t04tb->t01tb->s01tb->toArray();
        $data['t04tb']['m09tb'] = (empty($t04tb->m09tb)) ? [] : $t04tb->m09tb->toArray() ;
        return $data;
    }

    public function getM17tbs($queryData)
    {
        return $this->m17tbRepository->get($queryData, true, ["m13tb" => ['enrollorg', 'enrollname']]);
    }

    public function getM14tbs($queryData)
    {
        return $this->m14tbRepository->get($queryData);
    }

    public function getAllFloors()
    {
        return $this->eduFloorRepository->getAllFloors();
    }

    public function selectEmptyBed($queryData)
    {
        return $this->eduBedRepository->selectEmptyBed($queryData);
    }

    public function getBeds($queryData)
    {
        return $this->eduFloorRepository->getBeds($queryData);
    }

    public function getFloors($queryData)
    {
        return $this->eduFloorRepository->getFloors($queryData);
    }

    public function getEmptyBed($queryData)
    {
        return $this->eduBedRepository->getEmptyBed($queryData);
    }
}