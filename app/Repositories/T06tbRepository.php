<?php
namespace App\Repositories;

use App\Models\T06tb;
use App\Repositories\Repository;
use DateTime;
use DB;

class T06tbRepository extends Repository
{
    public function __construct(T06tb $t06tb)
    {
        $this->model = $t06tb;
    }  
    
    public function getLastCourse($course_info)
    {
        return $this->model->where($course_info)->max('course');
    }

    public function getHoursInfo($class_info)
    {
        return $this->model->select(DB::raw("is_must_read, sum(hour) total_hour"))
                           ->where($class_info)
                           ->groupBy(['is_must_read'])
                           ->get();
        
    }
    /*
        取得某班課程資訊不包含(預設課程)
        回傳 數量
    */ 
    public function getByClassNoDefault($copy_info)
    {
        $guard = ['報到', '班務介紹'];
        return $this->model->whereNotIn('name', $guard)
                           ->where($copy_info)
                           ->count();
    }

    public function getLongClass($class,$term)
    {
        return $this->model->select(DB::raw("date"))
                           ->where('class','=',$class)
                           ->where('term','=',$term)
                           ->whereRaw('date is not null')
                           ->orderBy('date')
                           ->get();
    }


}