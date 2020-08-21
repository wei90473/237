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

class LectureBedroomUsageRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLectureBedroomUsage($queryData = [])
    {

        $query = Teacher_room::select('teacher_by_week.room_no','m01tb.cname','teacher_room.date','teacher_room.idno','teacher_room.class_weeks_id');

        $query->join('teacher_by_week', function($join)
        {
            $join->on('teacher_by_week.class_week_id', '=', 'teacher_room.class_weeks_id');
            $join->on('teacher_by_week.idno', '=', 'teacher_room.idno');
        });

        $query->join('m01tb', function($join)
        {
            $join->on('m01tb.idno', '=', 'teacher_room.idno');
        });

        $query->whereBetween('teacher_room.date', array($queryData['sdate'], $queryData['edate']));
        $query->where('teacher_by_week.room_no', '!=', '');

        $room_data = $query->get()->toArray();
        $date = array();
        foreach($room_data as $row){
            $room_day = $row['room_no'].'_'.substr($row['date'], 5,2);
            $date[$room_day] = $row['cname'];
        }

        // dd($date);

        return $date;
    }

}
