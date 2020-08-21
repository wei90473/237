<?php
namespace App\Services;

use App\Helpers\Des;
use DateTime;
use DB;

class StudentTakeLeaveService
{
    /**
     * StudentTakeLeaveService constructor.
     * 
     */
    public function __construct()
    {

    }

    public function getOpenClassList($queryData)
    {
        return $this->t04tbRepository->get($queryData);
    }


}
