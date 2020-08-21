<?php
namespace App\Services;

use App\Repositories\Term_processRepository;
use App\Repositories\M14tbRepository;
use App\Repositories\M25tbRepository;

class Term_processService
{

    public function __construct(
        Term_processRepository $term_processRepository,
        M14tbRepository $m14tbRepository,
        M25tbRepository $m25tbRepository
    )
    {
        $this->m14tbRepository = $m14tbRepository;
        $this->m25tbRepository = $m25tbRepository;
        $this->term_processRepository = $term_processRepository;
    }

    public function getList($queryData = [])
    {
        return $this->term_processRepository->getList($queryData);
    }

    public function getFreeze($job, $class, $term)
    {
        return $this->term_processRepository->getFreeze($job, $class, $term);
    }

    public function getProcess_non_complete()
    {
        return $this->term_processRepository->getProcess_non_complete();
    }

    public function getMail()
    {
        return $this->term_processRepository->getMail();
    }

    public function getExport($queryData = [])
    {
        return $this->term_processRepository->getExport($queryData);
    }

    public function getClassRooms()
    {
        $class_room = [
            "m14tb" => $this->m14tbRepository->getClassRoom(1)->pluck("name", "site")->toArray(),
            "m25tb" => $this->m25tbRepository->get()->pluck("name", "site")->toArray(),
        ];
        return $class_room;
    }

}
