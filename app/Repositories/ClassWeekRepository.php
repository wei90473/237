<?php
namespace App\Repositories;

use App\Models\ClassWeek;
use App\Models\Teacher_by_week;
use App\Models\Teacher_room;
use App\Models\Teacher_car;
use App\Models\Teacher_food;
use App\Repositories\Repository;
use DateTime;
use DB;

class ClassWeekRepository extends Repository
{
    public function __construct(ClassWeek $class_week)
    {
        $this->model = $class_week;
    }

    public function deleteWeek($queryData = [])
    {
        // dd($queryData);
        $query = ClassWeek::select('id');
        $classWeek_id = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();

        if(!empty($classWeek_id)){
        	foreach($classWeek_id as $week_row){
	        	Teacher_by_week::where('class_week_id', $week_row['id'])->delete();
	        	Teacher_room::where('class_weeks_id', $week_row['id'])->delete();
	        	Teacher_car::where('class_weeks_id', $week_row['id'])->delete();
	        	Teacher_food::where('class_weeks_id', $week_row['id'])->delete();
	        }

	        ClassWeek::where('class', $queryData['class'])->where('term', $queryData['term'])->delete();
        }
    }

    public function get_exist($queryData = [])
    {
        // dd($queryData);
        $query = ClassWeek::select('id');
        $classWeek_id = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();

        if(!empty($classWeek_id)){
        	return false;
        }else{
        	return true;
        }

    }

}