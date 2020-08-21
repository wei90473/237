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

class LecturePickupRecordSummaryRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLecturePickupRecordSummary($queryData = [])
    {

        $query = Teacher_car::select('class_weeks.class', 'class_weeks.term', 't01tb.name', 't01tb.process', 't04tb.section', 'section.section_id')->whereBetween('teacher_car.date', array($queryData['sdate'], $queryData['edate']));
        $query->join('class_weeks', function($join)
        {
            $join->on('class_weeks.id', '=', 'teacher_car.class_weeks_id');
        });

        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 'class_weeks.class');
        });
        $query->join('t04tb', function($join)
        {
            $join->on('t04tb.class', '=', 'class_weeks.class')
            ->on('t04tb.term', '=', 'class_weeks.term');
        });
        $query->join('section', function($join)
        {
            $join->on('section.section_name', '=', 't04tb.section');
        });
        $query->orderBy('t01tb.process', 'asc');
        $query->orderBy('class_weeks.class', 'asc');
        $query->orderBy('class_weeks.term', 'asc');
        $query->orderBy('section.section_id', 'asc');
        $query->distinct();
        $class_data = $query->get()->toArray();

        foreach($class_data as & $row){
            $row['total'] = '0';
            $query = Teacher_car::select(DB::raw('SUM(teacher_car.price) as total'));
            $query->join('class_weeks', function($join)
            {
                $join->on('class_weeks.id', '=', 'teacher_car.class_weeks_id');
            });
            $query->where('class_weeks.class', $row['class']);
            $query->where('class_weeks.term', $row['term']);
            $query->whereBetween('date', array($queryData['sdate'], $queryData['edate']));
            $total = $query->first()->toArray();
            if(isset($total) && !empty($total)){
                $row['total'] = $total['total'];
            }
            $row['process_name'] = config('app.process')[$row['process']];
        }

        // dd($class_data);

        return $class_data;
    }

}
