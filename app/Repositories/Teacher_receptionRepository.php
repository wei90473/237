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
use App\Models\Class_weeks;
use App\Models\Teacher_by_week;
use App\Models\Teacher_room;
use App\Models\Teacher_car;
use App\Models\Teacher_food;
use App\Models\Room;
use App\Models\License_plate_setting;

class Teacher_receptionRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getTeacher_receptionList($queryData = [])
    {
        $query = T01tb::select('t04tb.sponsor', 't04tb.term', 't01tb.class', 't01tb.name', 't01tb.branch' , 't01tb.process' , 't01tb.branchname' , 'class_weeks.sdate', 'class_weeks.edate');

        $query->join('t04tb', function($join)
        {
            $join->on('t04tb.class', '=', 't01tb.class');
        });

        $query->join('class_weeks', function($join)
        {
            $join->on('t04tb.class', '=', 'class_weeks.class')
            ->on('t04tb.term', '=', 'class_weeks.term');
        });

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('class', 'asc');
            $query->orderBy('term', 'asc');
            $query->orderBy('class_weeks.sdate', 'asc');
        }
        //year
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
            $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
            $query->where('t01tb.yerly', $queryData['yerly']);
        }

        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t01tb.class', 'LIKE', '%'.$queryData['class'].'%');
        }

        // 班別
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'LIKE', '%'.$queryData['name'].'%');
        }

        if ( isset($queryData['class_branch_name']) && $queryData['class_branch_name'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['class_branch_name'].'%');
        }

        if ( isset($queryData['branch']) && $queryData['branch'] ) {
            $query->where('t01tb.branch', $queryData['branch']);
        }

        if ( isset($queryData['process']) && $queryData['process'] ) {
            $query->where('t01tb.process', 'LIKE', '%'.$queryData['process'].'%');
        }

        if ( isset($queryData['traintype']) && $queryData['traintype'] ) {
            $query->where('t01tb.traintype', 'LIKE', '%'.$queryData['traintype'].'%');
        }

        if ( isset($queryData['categoryone']) && $queryData['categoryone'] ) {
            $query->where('t01tb.categoryone', $queryData['categoryone']);
        }

        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $query->where('t04tb.sponsor', '=', $queryData['sponsor']);
        }

        if ( isset($queryData['type']) && $queryData['type'] ) {
            $query->where('t01tb.type', 'LIKE', '%'.$queryData['type'].'%');
        }

        if ( isset($queryData['sdate']) && $queryData['sdate'] ) {
            $queryData['sdate'] = str_pad($queryData['sdate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '>=', $queryData['sdate']);
        }
        if ( isset($queryData['edate']) && $queryData['edate'] ) {
            $queryData['edate'] = str_pad($queryData['edate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '<=', $queryData['edate']);
        }

        if ( isset($queryData['sdate2']) && $queryData['sdate2'] ) {
            $queryData['sdate2'] = str_pad($queryData['sdate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '>=', $queryData['sdate2']);
        }
        if ( isset($queryData['edate2']) && $queryData['edate2'] ) {
            $queryData['edate2'] = str_pad($queryData['edate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '<=', $queryData['edate2']);
        }

        if(isset($queryData['sdate3']) && $queryData['sdate3'] && isset($queryData['edate3']) && $queryData['edate3'] ){
            $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);
            $queryData['edate3'] = str_pad($queryData['edate3'],7,'0',STR_PAD_LEFT);

            $query->leftJoin('t06tb', function($join)
            {
                $join->on('t04tb.class', '=', 't06tb.class')
                ->on('t04tb.term', '=', 't06tb.term');
            });

            // //
            // $class_no = T06tb::select('class')
            // ->where('date', '>=', $queryData['sdate3'])
            // ->where('date', '<=', $queryData['edate3'])
            // ->get();
            // $class_no_in = array();
            // foreach ($class_no as $row) {
            //     $class_no_in[] = $row->class;
            // }
            // $query->whereIn('t01tb.class', $class_no_in);
            // //
            $query->where('t06tb.date', '>=', $queryData['sdate3']);
            $query->where('t06tb.date', '<=', $queryData['edate3']);
            $query->distinct();
            $query->groupBy('class_weeks.class', 'class_weeks.term', 'class_weeks.sdate');
        }else{
            if ( isset($queryData['sdate3']) && $queryData['sdate3'] ) {
                $query->leftJoin('t06tb', function($join)
                {
                    $join->on('t04tb.class', '=', 't06tb.class')
                    ->on('t04tb.term', '=', 't06tb.term');
                });
                $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);
                // //
                // $class_no = T06tb::select('class')
                // ->where('date', '>=', $queryData['sdate3'])
                // ->get();
                // $class_no_in = array();
                // foreach ($class_no as $row) {
                //     $class_no_in[] = $row->class;
                // }
                // $query->whereIn('t01tb.class', $class_no_in);
                // //
                $query->where('t06tb.date', '>=', $queryData['sdate3']);
                $query->distinct();
            }
            if ( isset($queryData['edate3']) && $queryData['edate3'] ) {
                $query->leftJoin('t06tb', function($join)
                {
                    $join->on('t04tb.class', '=', 't06tb.class')
                    ->on('t04tb.term', '=', 't06tb.term');
                });
                $queryData['edate3'] = str_pad($queryData['edate3'],7,'0',STR_PAD_LEFT);
                // //
                // $class_no = T06tb::select('class')
                // ->where('date', '<=', $queryData['edate3'])
                // ->get();
                // $class_no_in = array();
                // foreach ($class_no as $row) {
                //     $class_no_in[] = $row->class;
                // }
                // $query->whereIn('t01tb.class', $class_no_in);
                // //
                $query->where('t06tb.date', '<=', $queryData['edate3']);
                $query->distinct();
            }
            $query->groupBy('class_weeks.class', 'class_weeks.term', 'class_weeks.sdate');
        }

        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {

            // $queryData['term'] = str_pad($queryData['term'] ,2,'0',STR_PAD_LEFT);

            $query->where('t04tb.term', $queryData['term']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    public function getClass($queryData = [])
    {
        $query = T01tb::select('t01tb.class', 't01tb.name', 't01tb.branch', 't01tb.branchname', 't01tb.process');
        $results = $query->where('class', $queryData['class'])->get()->toArray();
        $class_data = $results[0];
        $query = T04tb::select('t04tb.term', 't04tb.sponsor');
        $results = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
        $class_data['term'] = $results[0]['term'];
        $class_data['sponsor'] = $results[0]['sponsor'];
        $query = Class_weeks::select('class_weeks.id', 'class_weeks.sdate', 'class_weeks.edate');
        $results = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('sdate', $queryData['sdate'])->get()->toArray();
        $class_data['class_weeks_id'] = $results[0]['id'];
        $class_data['sdate'] = $results[0]['sdate'];
        $class_data['edate'] = $results[0]['edate'];

        if(!empty($class_data['sponsor'])){
            $query = M09tb::select('m09tb.username');
            $results = $query->where('userid', $class_data['sponsor'])->get()->toArray();
            $class_data['sponsor'] = $results[0]['username'];
            //123
        }
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($class_data);
        // echo "\n</pre>\n";
        // die();

        return $class_data;
    }

    public function getDetailList($queryData = [])
    {
        $query = T06tb::select('t06tb.course');
        $course_data = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('date', '>=',$queryData['sdate'])->where('date', '<=',$queryData['edate'])->get()->toArray();

        $query = T09tb::select('t09tb.id', 't09tb.course', 't09tb.idno');
        $list = $query->whereIn('t09tb.course', $course_data)->where('class', $queryData['class'])->where('term', $queryData['term'])->groupBy('idno')->get()->toArray();

        foreach($list as $key => $row){

            $query = T08tb::select('t08tb.cname');
            $t08data = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('course', $row['course'])->where('idno', $row['idno'])->get()->toArray();

            if(isset($t08data[0]['cname'])){
                $list[$key]['cname'] = $t08data[0]['cname'];
            }else{
                $query = M01tb::select('m01tb.cname');
                $m01data = $query->where('idno', $row['idno'])->get()->toArray();
                $list[$key]['cname'] = $m01data[0]['cname'];
            }

            $query = Teacher_by_week::select('teacher_by_week.idno', 'teacher_by_week.confirm', 'teacher_by_week.confirm2');
            $data_isset = $query->where('class_week_id', $queryData['class_weeks_id'])->where('t09tb_id', $row['id'])->get()->toArray();

            if(!empty($data_isset)){
                $list[$key]['id_isset'] = "Y";
                $list[$key]['confirm'] = $data_isset[0]['confirm'];
                $list[$key]['confirm2'] = $data_isset[0]['confirm2'];
            }else{
                $list[$key]['id_isset'] = "N";
                $list[$key]['confirm'] = 'N';
                $list[$key]['confirm2'] = 'N';
            }
            // echo '<pre style="text-align:left;">' . "\n";
            // print_r($t09data);
            // echo "\n</pre>\n";


        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($list);
        // echo "\n</pre>\n";
        // die();
        return $list;
    }

    public function getFoodList($queryData = [])
    {
        $query = Teacher_food::select('teacher_by_week.confirm2', 'class_weeks.class', 'class_weeks.term', 'teacher_by_week.t09tb_id', 'teacher_food.id', 'teacher_food.idno', 'teacher_food.class_weeks_id', 'teacher_food.date', 'teacher_food.breakfast', 'teacher_food.lunch', 'teacher_food.dinner');

        $query->join('teacher_by_week', function($join)
        {
            $join->on('teacher_food.class_weeks_id', '=', 'teacher_by_week.class_week_id')
            ->on('teacher_food.idno', '=', 'teacher_by_week.idno');
        });

        $query->join('class_weeks', function($join)
        {
            $join->on('teacher_food.class_weeks_id', '=', 'class_weeks.id');
        });

        // 排序
        $query->orderBy('teacher_food.id', 'asc');
        $query->orderBy('teacher_food.idno', 'asc');
        $query->where('teacher_by_week.confirm2', 'Y');
        if ( isset($queryData['date']) && $queryData['date'] ) {
            $query->where('teacher_food.date', $queryData['date']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        foreach($data as & $row){
            $query = T01tb::select('name');
            $class_name = $query->where('class', $row->class)->get()->toArray();
            $row->class_name = '';
            if(!empty($class_name)){
                $row->class_name = $class_name[0]['name'].' 第'.$row->term.'期';
            }
            $query = M01tb::select('cname', 'dept', 'position');
            $member_data = $query->where('idno', $row->idno)->get()->toArray();
            $row->name = '';
            $row->dept = '';
            $row->position = '';
            if(!empty($member_data)){
                $row->name = $member_data[0]['cname'];
                $row->dept = $member_data[0]['dept'];
                $row->position = $member_data[0]['position'];
            }
            $query = T09tb::select('t09tb.course');
            $course_data = $query->where('class', $row->class)->where('term', $row->term)->where('idno', $row->idno)->get()->toArray();
            $query = T06tb::select('stime', 'etime');
            $time_data = $query->whereIn('course', $course_data)->where('class', $row->class)->where('term', $row->term)->where('date', $queryData['date'])->orderBy('stime', 'asc')->get()->toArray();
            $time = array();
            $row->time = array();
            if(!empty($time_data)){
                foreach($time_data as $time_row){
                    $time[] = $time_row['stime'].'~'.$time_row['etime'];
                }
                $row->time = $time;
            }
            $query = Teacher_room::select('teacher_room.morning', 'teacher_room.noon', 'teacher_room.evening', 'teacher_room.confirm');
            $RoomDate = $query->where('date', $queryData['date'])->where('idno', $row->idno)->get()->toArray();
            $row->room = '';
            if(!empty($RoomDate)){
                if($RoomDate[0]['morning'] == 'Y'){
                    $row->room .= '早';
                }
                if($RoomDate[0]['noon'] == 'Y'){
                    $row->room .= '午';
                }
                if($RoomDate[0]['evening'] == 'Y'){
                    $row->room .= '晚';
                }
            }
            if($row->breakfast == 'Y'){
                $row->breakfast = 'V';
            }else{
                $row->breakfast = '';
            }
            if($row->lunch == 'Y'){
                $row->lunch = 'V';
            }else{
                $row->lunch = '';
            }
            if($row->dinner == 'Y'){
                $row->dinner = 'V';
            }else{
                $row->dinner = '';
            }
            // echo '<pre style="text-align:left;">' . "\n";
            // print_r($row);
            // echo "\n</pre>\n";
        }
        // dd($data);

        return $data;
    }

    public function getRoomList($queryData = [])
    {
        $query = Teacher_room::select('teacher_by_week.room_no', 'teacher_by_week.confirm2', 'class_weeks.class', 'class_weeks.term', 'teacher_by_week.t09tb_id', 'teacher_room.id', 'teacher_room.idno', 'teacher_room.class_weeks_id', 'teacher_room.date', 'teacher_room.morning', 'teacher_room.noon', 'teacher_room.evening');

        $query->join('teacher_by_week', function($join)
        {
            $join->on('teacher_room.class_weeks_id', '=', 'teacher_by_week.class_week_id')
            ->on('teacher_room.idno', '=', 'teacher_by_week.idno');
        });

        $query->join('class_weeks', function($join)
        {
            $join->on('teacher_room.class_weeks_id', '=', 'class_weeks.id');
        });

        // 排序
        $query->orderBy('teacher_room.id', 'asc');
        $query->orderBy('teacher_room.idno', 'asc');
        $query->where('teacher_by_week.confirm2', 'Y');
        if ( isset($queryData['date']) && $queryData['date'] ) {
            $query->where('teacher_room.date', $queryData['date']);
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        foreach($data as & $row){
            $query = T01tb::select('name');
            $class_name = $query->where('class', $row->class)->get()->toArray();
            $row->class_name = '';
            if(!empty($class_name)){
                $row->class_name = $class_name[0]['name'].' 第'.$row->term.'期';
            }
            $query = M01tb::select('cname', 'dept', 'position');
            $member_data = $query->where('idno', $row->idno)->get()->toArray();
            $row->name = '';
            $row->dept = '';
            $row->position = '';
            if(!empty($member_data)){
                $row->name = $member_data[0]['cname'];
                $row->dept = $member_data[0]['dept'];
                $row->position = $member_data[0]['position'];
            }
            $query = T09tb::select('t09tb.course');
            $course_data = $query->where('class', $row->class)->where('term', $row->term)->where('idno', $row->idno)->get()->toArray();
            $query = T06tb::select('stime', 'etime');
            $time_data = $query->whereIn('course', $course_data)->where('class', $row->class)->where('term', $row->term)->where('date', $queryData['date'])->orderBy('stime', 'asc')->get()->toArray();
            $time = array();
            $row->time = '';
            if(!empty($time_data)){
                foreach($time_data as $time_row){
                    $time[] = $time_row['stime'].'~'.$time_row['etime'];
                }
                $row->time = $time;
            }

            if($row->morning == 'Y'){
                $row->morning = 'V';
            }else{
                $row->morning = '';
            }
            if($row->noon == 'Y'){
                $row->noon = 'V';
            }else{
                $row->noon = '';
            }
            if($row->evening == 'Y'){
                $row->evening = 'V';
            }else{
                $row->evening = '';
            }
            // echo '<pre style="text-align:left;">' . "\n";
            // print_r($row);
            // echo "\n</pre>\n";
        }
        // dd($data);

        return $data;
    }

    public function getCarList($queryData = [])
    {
        $query = Teacher_car::select('teacher_by_week.room_no', 'teacher_by_week.confirm2', 'class_weeks.class', 'class_weeks.term', 'teacher_by_week.t09tb_id', 'teacher_car.id', 'teacher_car.idno', 'teacher_car.class_weeks_id', 'teacher_car.date', 'teacher_car.start', 'teacher_car.end', 'teacher_car.license_plate', 'teacher_car.price', 'teacher_car.type', 'teacher_car.time', 'teacher_car.remark', 'teacher_car.car');

        $query->join('teacher_by_week', function($join)
        {
            $join->on('teacher_car.class_weeks_id', '=', 'teacher_by_week.class_week_id')
            ->on('teacher_car.idno', '=', 'teacher_by_week.idno');
        });

        $query->join('class_weeks', function($join)
        {
            $join->on('teacher_car.class_weeks_id', '=', 'class_weeks.id');
        });

        // 排序
        $query->orderBy('teacher_car.id', 'asc');
        $query->orderBy('teacher_car.idno', 'asc');
        $query->where('teacher_by_week.confirm2', 'Y');
        if ( isset($queryData['date']) && $queryData['date'] ) {
            $query->where('teacher_car.date', $queryData['date']);
        }

        $data = $query->get()->toArray();
        // dd($data);
        foreach($data as & $row){
            $row['type1'] = '';
            $row['type2'] = '';
            if($row['type'] == '1'){
                $row['type1'] = 'V';
                $row['location'] = $row['start'];
            }
            if($row['type'] == '2'){
                $row['type2'] = 'V';
                $row['location'] = $row['end'];
            }

            // if(!empty($row['license_plate'])){
            //     $query = License_plate_setting::select('license_plate', 'call', 'id', 'type');
            //     $license_plate_data = $query->where('license_plate', $row['license_plate'])->get()->toArray();
            //     if(!empty($license_plate_data)){
            //         if($license_plate_data[0]['type'] == '1'){
            //             $row['car_type'] = '計程車';
            //         }else{
            //             $row['car_type'] = '公務車';
            //         }
            //     }
            // }

            $query = M01tb::select('cname', 'dept', 'position');
            $member_data = $query->where('idno', $row['idno'])->get()->toArray();
            $row['name'] = '';
            $row['dept'] = '';
            $row['position'] = '';
            if(!empty($member_data)){
                $row['name'] = $member_data[0]['cname'];
                $row['dept'] = $member_data[0]['dept'];
                $row['position'] = $member_data[0]['position'];
            }
            $query = T09tb::select('t09tb.course');
            $course_data = $query->where('class', $row['class'])->where('term', $row['term'])->where('idno', $row['idno'])->get()->toArray();
            $query = T06tb::select('stime', 'etime');
            $time_data = $query->whereIn('course', $course_data)->where('class', $row['class'])->where('term', $row['term'])->where('date', $queryData['date'])->orderBy('stime', 'asc')->get()->toArray();
            $time = array();
            $row['class_time'] = '';
            if(!empty($time_data)){
                foreach($time_data as $time_row){
                    $time[] = $time_row['stime'].'~'.$time_row['etime'];
                }
                $row['class_time'] = $time;
            }
            // echo '<pre style="text-align:left;">' . "\n";
            // print_r($row);
            // echo "\n</pre>\n";
        }
        // dd($data);

        return $data;
    }

    public function getLicenseData()
    {
        $query = License_plate_setting::select('license_plate', 'call', 'id');
        $license_plate_data = $query->get()->toArray();
        $data = array();
        foreach($license_plate_data as $row){
            $data[$row['license_plate']] = $row;
            $data[$row['license_plate']]['count'] = '0';
        }
        // dd($license_plate_data);
        return $data;
    }

    public function getRoomDate($queryData = [])
    {

        $query = T09tb::select('t09tb.course');
        $course_data = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('idno', $queryData['idno'])->get()->toArray();

        $query = T06tb::select('date');
        $RoomDate_class = $query->whereIn('course', $course_data)->where('class', $queryData['class'])->where('term', $queryData['term'])->where('date', '>=',$queryData['sdate'])->where('date', '<=',$queryData['edate'])->orderBy('date', 'asc')->groupBy('date')->get()->toArray();
        $return_data = array();
        foreach ($RoomDate_class as $row) {
            $row['morning'] = 'N';
            $row['noon'] = 'N';
            $row['evening'] = 'N';
            $row['confirm'] = '';
            $row['mark'] = '';
            $return_data[$row['date']] = $row;
        }

        $query = Teacher_room::select('teacher_room.date', 'teacher_room.morning', 'teacher_room.noon', 'teacher_room.evening', 'teacher_room.confirm', 'teacher_room.mark', 'teacher_room.only_morning');
        $RoomDate = $query->where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $queryData['idno'])->get()->toArray();

        foreach ($RoomDate as $RoomDate_row) {
            $return_data[$RoomDate_row['date']] = $RoomDate_row;
        }
        ksort($return_data);
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($list);
        // echo "\n</pre>\n";
        // die();
        return $return_data;
    }

    public function getRoom()
    {
        $query = Room::select('id', 'room_number');
        $RoomDate = $query->get()->toArray();
        return $RoomDate;
    }

    public function change_room($queryData = [])
    {

        $query = Teacher_food::select('id', 'date', 'breakfast', 'breakfast_type', 'breakfast_type2', 'lunch', 'lunch_type', 'lunch_type2', 'dinner', 'dinner_type', 'dinner_type2');
        //$Teacher_food_data = $query->where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $queryData['idno'])->where('date', '>=',$queryData['sdate'])->where('date', '<=',$queryData['edate'])->orderBy('date', 'asc')->orderBy('date', 'asc')->get()->toArray();
        $Teacher_food_data = $query->where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $queryData['idno'])->orderBy('date', 'asc')->orderBy('date', 'asc')->get()->toArray();
        $room_data = $this->getRoomDate($queryData);
        // dd($room_data);
        if(!empty($Teacher_food_data)){
            $food_data = array();
            foreach($Teacher_food_data as $food_row1){
                if(!in_array($food_row1['date'], $queryData['confirm_date'])){
                    Teacher_food::where('id', $food_row1['id'])->delete();
                }else{
                    $food_data[$food_row1['date']] = $food_row1;
                    if($food_row1['breakfast'] == 'Y'){
                        $fields = array(
                            'breakfast' => 'N',
                            'breakfast_type' => '1',
                            'breakfast_type2' => '',
                        );
                        Teacher_food::where('id', $food_row1['id'])->update($fields);
                    }
                }
            }
            if(!empty($queryData['confirm_date'])){
                foreach($queryData['confirm_date'] as $date_row){

                    if(!isset($room_data[$date_row])){
                        // dd($date_row);
                        $insert_day = $date_row;
                        $insert_day2 = date('Ymd',strtotime(($insert_day+19110000)));
                        // dd($date_row);
                        $insert_day3 = ['日', '一', '二', '三', '四', '五', '六'][date('w', strtotime($insert_day2))];
                        if($insert_day3 != '六'){
                            if($insert_day3 != '日'){
                                $fields_day = array(
                                    "breakfast" => "N",
                                    "breakfast_type" => "1",
                                    "breakfast_type2" => "",
                                    "lunch" => "N",
                                    "lunch_type" => "1",
                                    "lunch_type2" => "",
                                    "dinner" => "N",
                                    "dinner_type" => "1",
                                    "dinner_type2" => "",
                                    "date" => ($insert_day2-19110000),
                                );
                                $fields_day['idno'] = $queryData['idno'];
                                $fields_day['class_weeks_id'] = $queryData['class_weeks_id'];
                                Teacher_food::create($fields_day);
                            }
                        }
                    }
                    // dd($food_data[$date_row]);
                    $tomorrow = $date_row;
                    $tomorrow2 = date('Ymd',strtotime('+1 day', strtotime(($tomorrow+19110000))));
                    if(isset($food_data[($tomorrow2-19110000)])){
                        $fields['breakfast'] = 'Y';
                        Teacher_food::where('id', $food_data[($tomorrow2-19110000)]['id'])->update($fields);
                    }else{

                        if(!in_array(($tomorrow2-19110000), $queryData['confirm_date'])){
                            $tomorrow3 = ['日', '一', '二', '三', '四', '五', '六'][date('w', strtotime($tomorrow2))];
                            if($tomorrow3 != '六'){
                                if($tomorrow3 != '日'){
                                    $fields = array(
                                        "breakfast" => "Y",
                                        "breakfast_type" => "1",
                                        "breakfast_type2" => "",
                                        "lunch" => "N",
                                        "lunch_type" => "1",
                                        "lunch_type2" => "",
                                        "dinner" => "N",
                                        "dinner_type" => "1",
                                        "dinner_type2" => "",
                                        "date" => ($tomorrow2-19110000),
                                    );
                                    $fields['idno'] = $queryData['idno'];
                                    $fields['class_weeks_id'] = $queryData['class_weeks_id'];
                                    Teacher_food::create($fields);
                                }
                            }
                        }

                    }

                }
            }

        }

    }

    public function getTeachDate($queryData = [])
    {
        $query = T09tb::select('t09tb.course');
        $course_data = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('idno', $queryData['idno'])->get()->toArray();

        $query = T06tb::select('date');
        $RoomDate_class = $query->whereIn('course', $course_data)->where('class', $queryData['class'])->where('term', $queryData['term'])->where('date', '>=',$queryData['sdate'])->where('date', '<=',$queryData['edate'])->orderBy('date', 'asc')->groupBy('date')->get()->toArray();
        $teachDate = array();
        foreach ($RoomDate_class as $row) {
            $teachDate[$row['date']] = $row;
        }
        $return_data = array();
        if(count($teachDate) > '1'){
          $teachDate_count = '1';
          foreach($teachDate as $teachDate_row){
            if($teachDate_count == '1'){
              $return_data['before_day'] = date('Ymd',strtotime('-1 day', strtotime(($teachDate_row['date']+19110000))))-19110000;
            }
            if($teachDate_count == count($teachDate)){
              $return_data['after_day'] = date('Ymd',strtotime('+1 day', strtotime(($teachDate_row['date']+19110000))))-19110000;
            }
            $teachDate_count++;
          }
        }
        if(count($teachDate) == '1'){
          foreach($teachDate as $teachDate_row){
            $return_data['before_day'] = date('Ymd',strtotime('-1 day', strtotime(($teachDate_row['date']+19110000))))-19110000;
            $return_data['after_day'] = date('Ymd',strtotime('+1 day', strtotime(($teachDate_row['date']+19110000))))-19110000;
          }
        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($return_data);
        // echo "\n</pre>\n";
        // die();
        return $return_data;
    }

    public function getCarDate($queryData = [])
    {

        $query = Teacher_car::select('teacher_car.type', 'teacher_car.time', 'teacher_car.date', 'teacher_car.location1', 'teacher_car.location2', 'teacher_car.location3', 'teacher_car.address');
        $CarDate = $query->where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $queryData['idno'])->get()->toArray();

        $return_data = array();
        foreach ($CarDate as $CarDate_row) {
            if($CarDate_row['type'] == '1'){
                $return_data['date1'] = $CarDate_row['date'];
                $return_data['time1'] = $CarDate_row['time'];
                $return_data['location1_1'] = $CarDate_row['location1'];
                $return_data['location2_1'] = $CarDate_row['location2'];
                $return_data['location3_1'] = $CarDate_row['location3'];
                $return_data['address_1'] = $CarDate_row['address'];
            }
            if($CarDate_row['type'] == '2'){
                $return_data['date2'] = $CarDate_row['date'];
                $return_data['time2'] = $CarDate_row['time'];
                $return_data['location1_2'] = $CarDate_row['location1'];
                $return_data['location2_2'] = $CarDate_row['location2'];
                $return_data['location3_2'] = $CarDate_row['location3'];
                $return_data['address_2'] = $CarDate_row['address'];
            }
        }
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($return_data);
        // echo "\n</pre>\n";
        // die();
        return $return_data;
    }

    public function getFoodDate($queryData = [])
    {

        $query = Teacher_food::select('date', 'breakfast', 'breakfast_type', 'breakfast_type2', 'lunch', 'lunch_type', 'lunch_type2', 'dinner', 'dinner_type', 'dinner_type2');
        //$Teacher_food_data = $query->where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $queryData['idno'])->where('date', '>=',$queryData['sdate'])->where('date', '<=',$queryData['edate'])->orderBy('date', 'asc')->orderBy('date', 'asc')->get()->toArray();
        $Teacher_food_data = $query->where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $queryData['idno'])->orderBy('date', 'asc')->orderBy('date', 'asc')->get()->toArray();


        if(!empty($Teacher_food_data)){
            $final_food_data = array();
            foreach($Teacher_food_data as $Teacher_food_row){
                $final_food_data[$Teacher_food_row['date']] = $Teacher_food_row;
            }
            $return_data = $final_food_data;
        }else{

            $query = T09tb::select('t09tb.course');
            $course_data = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('idno', $queryData['idno'])->get()->toArray();

            $query = T06tb::select('date', 'etime');
            $T06tb_class = $query->whereIn('course', $course_data)->where('class', $queryData['class'])->where('term', $queryData['term'])->where('date', '>=',$queryData['sdate'])->where('date', '<=',$queryData['edate'])->orderBy('date', 'asc')->orderBy('etime', 'asc')->get()->toArray();
            $return_data = array();

            foreach ($T06tb_class as $T06tb_row) {
                if(!array_key_exists($T06tb_row['date'],$return_data)){
                    $return_data[$T06tb_row['date']] = array(
                        'breakfast' => 'N',
                        'breakfast_type' => '1',
                        'breakfast_type2' => '',
                        'lunch' => 'N',
                        'lunch_type' => '1',
                        'lunch_type2' => '',
                        'dinner' => 'N',
                        'dinner_type' => '1',
                        'dinner_type2' => '',
                        'date' => $T06tb_row['date'],

                    );
                }

                if($T06tb_row['etime'] == '1200'){
                    $return_data[$T06tb_row['date']]['lunch'] = 'Y';
                }

                if($T06tb_row['etime'] == '1700'){
                    $return_data[$T06tb_row['date']]['dinner'] = 'Y';
                }

            }

            $query = Teacher_room::select('teacher_room.date', 'teacher_room.morning', 'teacher_room.noon', 'teacher_room.evening', 'teacher_room.confirm', 'teacher_room.mark');
            $RoomDate = $query->where('class_weeks_id', $queryData['class_weeks_id'])->where('idno', $queryData['idno'])->get()->toArray();

            if(!empty($RoomDate)){
                foreach ($RoomDate as $RoomDate_row) {
                    if(isset($return_data[$RoomDate_row['date']])){
                        $tomorrow = $RoomDate_row['date'];
                        $tomorrow2 = date('Ymd',strtotime('+1 day', strtotime(($tomorrow+19110000))));
                        if(isset($return_data[($tomorrow2-19110000)])){
                            $return_data[($tomorrow2-19110000)]['breakfast'] = 'Y';
                        }else{
                            $tomorrow3 = ['日', '一', '二', '三', '四', '五', '六'][date('w', strtotime($tomorrow2))];
                            if($tomorrow3 != '六'){
                                $return_data[($tomorrow2-19110000)] = array(
                                    "breakfast" => "Y",
                                    "breakfast_type" => "1",
                                    "breakfast_type2" => "",
                                    "lunch" => "N",
                                    "lunch_type" => "1",
                                    "lunch_type2" => "",
                                    "dinner" => "N",
                                    "dinner_type" => "1",
                                    "dinner_type2" => "",
                                    "date" => ($tomorrow2-19110000),
                                );
                            }
                        }
                    }else{
                        $add_day = $RoomDate_row['date'];
                        $add_day2 = date('Ymd',strtotime(($add_day+19110000)));
                        $add_day3 = ['日', '一', '二', '三', '四', '五', '六'][date('w', strtotime($add_day2))];
                        if($add_day3 != '六'){
                            if($add_day3 != '日'){
                                $return_data[($add_day2-19110000)] = array(
                                    "breakfast" => "N",
                                    "breakfast_type" => "1",
                                    "breakfast_type2" => "",
                                    "lunch" => "N",
                                    "lunch_type" => "1",
                                    "lunch_type2" => "",
                                    "dinner" => "N",
                                    "dinner_type" => "1",
                                    "dinner_type2" => "",
                                    "date" => ($add_day2-19110000),
                                );
                                $yesterday = date('Ymd',strtotime('-1 day', strtotime($add_day2)));
                                if(isset($return_data[($yesterday-19110000)])){
                                    $return_data[($add_day2-19110000)]['breakfast'] = 'Y';
                                }
                            }
                        }
                        $tomorrow = date('Ymd',strtotime('+1 day', strtotime($add_day2)));
                        if(isset($return_data[($tomorrow-19110000)])){
                            $return_data[($tomorrow-19110000)]['breakfast'] = 'Y';
                        }
                    }
                }
                // dd($RoomDate);
            }

        }
        // dd($return_data);
        foreach($return_data as & $week_day_row){
            $week_day_row['week_day'] = ['日', '一', '二', '三', '四', '五', '六'][date('w', strtotime(($week_day_row['date']+19110000)))];

        }
        ksort($return_data);
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($return_data);
        // echo "\n</pre>\n";
        // die();

        return $return_data;

    }

    public function getIdno($queryData = []){
        $query = T09tb::select('t09tb.idno');
        $idno_data = $query->where('id', $queryData['t09tb_id'])->get()->toArray();
        $query = M01tb::select('m01tb.cname');
        $m01data = $query->where('idno', $idno_data[0]['idno'])->get()->toArray();
        $idno_data[0]['cname'] = $m01data[0]['cname'];
        if(!empty($idno_data)){
            return $idno_data['0'];
        }else{
            return $idno_data;
        }
    }

    public function getClassWeek($queryData = []){
        $query = Class_weeks::select('class_weeks.class', 'class_weeks.term', 'class_weeks.sdate', 'class_weeks.edate');
        $class_week_data = $query->where('id', $queryData['class_weeks_id'])->get()->toArray();
        if(!empty($class_week_data)){
            return $class_week_data['0'];
        }else{
            return $class_week_data;
        }
    }

    public function getTeacherByWeek($queryData = []){
        $query = Teacher_by_week::select('teacher_by_week.id', 'teacher_by_week.idno', 'teacher_by_week.confirm', 'teacher_by_week.confirm2', 'teacher_by_week.the_day_before', 'teacher_by_week.the_day_after', 'teacher_by_week.drive_by_self', 'teacher_by_week.demand', 'teacher_by_week.go_by_self', 'teacher_by_week.come_by_self', 'teacher_by_week.room_no');
        $data_isset = $query->where('class_week_id', $queryData['class_weeks_id'])->where('t09tb_id', $queryData['t09tb_id'])->get()->toArray();
        if(!empty($data_isset)){
            return $data_isset['0'];
        }else{
            return $data_isset;
        }
    }

    public function getSponsor(){
        $query = T04tb::select('t04tb.sponsor', 'm09tb.username');
        $query->join('m09tb', function($join)
        {
            $join->on('m09tb.userid', '=', 't04tb.sponsor');
        });
        $results = $query->where('sponsor', '<>', '')->distinct()->get()->toArray();
        $sponsor = array();
        foreach($results as $row){
            $sponsor[$row['sponsor']] = $row['username'];
        }
        $sponsor[''] = '';

        return $sponsor;
    }

}
