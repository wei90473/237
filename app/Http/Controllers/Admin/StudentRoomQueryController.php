<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StudentRoomQueryService;
use DB;

class StudentRoomQueryController extends Controller
{
    public function __construct(StudentRoomQueryService $studentRoomQueryService)
    {
        $this->studentRoomQueryService = $studentRoomQueryService;
    }

    public function index(Request $request)
    {   
        $queryData = $request->only([
            'orgcode',
            'orgname',
            'year',                 
            'period',                               
            'classname',                 
            'process',              
            'startdate1',               
            'startdate2',
            'startdate3',
            'startdate4',              
            'enddate1',               
            'enddate2',
            'floorno',
            'idno',
            'studentid', 
            'name',              
            '_paginate_qty',       
        ]);


        $floorList = $this->studentRoomQueryService->getFloorList()->toArray();
        $data = $this->studentRoomQueryService->ForStudentRoomBedSet($queryData);

        return view("/admin/StudentRoomQuery/index",compact('queryData','floorList','data'));
    }
}

?>