<?php
namespace App\Services;

use App\Repositories\EdufloorRepository;
use App\Repositories\T04tbRepository;
use DB;

class StudentRoomQueryService
{
    /**
     * PunchService constructor.
     * @param 
     */
    public function __construct(EdufloorRepository $eduFloorRepository,T04tbRepository $t04tbRepository)
    {
        $this->eduFloorRepository = $eduFloorRepository;
        $this->t04tbRepository = $t04tbRepository;
    }

    public function getFloorList()
    {
        return $this->eduFloorRepository->getFloorList();
    }

    public function ForStudentRoomBedSet($queryData)
    {
        return $this->t04tbRepository->ForStudentRoomBedSet($queryData);
    }
}

?>