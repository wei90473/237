<?php
namespace App\Repositories;

use App\Models\T08tb;
use App\Models\T09tb;
use App\Models\T06tb;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\M09tb;
use App\Models\M01tb;
use App\Models\S02tb;
use App\Models\M14tb;
use App\Models\Class_weeks;
use App\Models\Teacher_by_week;
use App\Models\Teacher_room;
use App\Models\Teacher_car;
use App\Models\Teacher_food;
use App\Models\Room;
use App\Models\License_plate_setting;

class LecturePickupRecordRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLecturePickupRecord($queryData = [])
    {
        $query = Teacher_car::select('license_plate_setting.call', 'license_plate_setting.name as car_name', 't01tb.name', 't01tb.process', 'class_weeks.class', 'class_weeks.term', 'm01tb.cname', 'teacher_car.date', 'teacher_car.idno', 'teacher_car.class_weeks_id', 'teacher_car.type', 'teacher_car.start', 'teacher_car.end', 'teacher_car.address', 'teacher_car.price', 'teacher_car.license_plate', 'teacher_car.car', 'teacher_car.remark');
        $query->join('m01tb', function($join)
        {
            $join->on('m01tb.idno', '=', 'teacher_car.idno');
        });
        $query->join('class_weeks', function($join)
        {
            $join->on('class_weeks.id', '=', 'teacher_car.class_weeks_id');
        });
        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 'class_weeks.class');
        });
        $query->leftjoin('license_plate_setting', function($join)
        {
            $join->on('license_plate_setting.license_plate', '=', 'teacher_car.license_plate');
        });
        $query->orderBy('teacher_car.date', 'asc');
        $Teacher_car = $query->whereBetween('date', array($queryData['sdate'], $queryData['edate']))->get()->toArray();

        foreach($Teacher_car as & $row){

        	$row['process'] = config('app.process')[$row['process']];
        	if(!empty($row['car'])){
        		if($row['type'] == '1'){
        			$row['car'] = config('app.car_1')[$row['car']];
        		}else{
        			$row['car'] = config('app.car_A')[$row['car']];
        		}
            }else{
                $row['car'] = '尚未安排';
            }
        }

        // dd($Teacher_car);

        return $Teacher_car;
    }

    public function getLecturePickupRecord2($queryData = [])
    {

    	$class_weeks_id = Class_weeks::select('id')->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
    	$week_id = array();
    	foreach($class_weeks_id as $class_weeks_id_row){
    		$week_id[] = $class_weeks_id_row['id'];
    	}

        $query = Teacher_car::select('license_plate_setting.call', 'license_plate_setting.name as car_name', 't01tb.name', 't01tb.process', 'class_weeks.class', 'class_weeks.term', 'm01tb.cname', 'teacher_car.date', 'teacher_car.idno', 'teacher_car.class_weeks_id', 'teacher_car.type', 'teacher_car.start', 'teacher_car.end', 'teacher_car.address', 'teacher_car.price', 'teacher_car.license_plate', 'teacher_car.car', 'teacher_car.remark');
        $query->join('m01tb', function($join)
        {
            $join->on('m01tb.idno', '=', 'teacher_car.idno');
        });
        $query->join('class_weeks', function($join)
        {
            $join->on('class_weeks.id', '=', 'teacher_car.class_weeks_id');
        });
        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 'class_weeks.class');
        });
        $query->leftjoin('license_plate_setting', function($join)
        {
            $join->on('license_plate_setting.license_plate', '=', 'teacher_car.license_plate');
        });
        $query->orderBy('teacher_car.date', 'asc');
        $query->whereIn('class_weeks_id', $week_id);
        $Teacher_car = $query->get()->toArray();

        foreach($Teacher_car as & $row){

        	$row['process'] = config('app.process')[$row['process']];
        	if(!empty($row['car'])){
        		if($row['type'] == '1'){
        			$row['car'] = config('app.car_1')[$row['car']];
        		}else{
        			$row['car'] = config('app.car_A')[$row['car']];
        		}
            }else{
                $row['car'] = '尚未安排';
            }
        }

        // dd($Teacher_car);

        return $Teacher_car;
    }

}
