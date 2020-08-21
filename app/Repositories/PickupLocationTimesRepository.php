<?php
namespace App\Repositories;

use DB;
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
use App\Models\Car_fare;

class PickupLocationTimesRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getPickupLocationTimes($queryData = [])
    {

        $query = Teacher_car::select('teacher_car.location1', 'teacher_car.location2', 'car_fare.area');
        $query->join('car_fare', function($join)
        {
            $join->on('car_fare.county', '=', 'teacher_car.location1')
            ->on('car_fare.id', '=', 'teacher_car.location2');
        });
        $query->whereBetween('teacher_car.date', array($queryData['sdate'], $queryData['edate']));
        $query->orderBy('teacher_car.location1', 'asc');
        $query->orderBy('teacher_car.location2', 'asc');
        $query->distinct();
        $location_data = $query->get()->toArray();
        // dd($location_data);
        foreach($location_data as & $row){
            $row['total'] = '0';
            $row['count'] = '0';
            $query = Teacher_car::select(DB::raw('SUM(teacher_car.price) as total, count(1) as count'));
            $query->whereBetween('teacher_car.date', array($queryData['sdate'], $queryData['edate']));
            $query->where('teacher_car.location1', $row['location1']);
            $query->where('teacher_car.location2', $row['location2']);
            $count_data = $query->get()->toArray();
            if(!empty($count_data)){
                $row['total'] = $count_data[0]['total'];
                $row['count'] = $count_data[0]['count'];
            }

            $row['location'] = config('app.county')[$row['location1']].$row['area'];

        }

        // dd($location_data);

        return $location_data;
    }

}
