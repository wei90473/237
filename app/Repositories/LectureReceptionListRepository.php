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
use App\Models\Edu_classroom;

class LectureReceptionListRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLectureReceptionList2($queryData = [])
    {
        $idno = array();
        $query = Teacher_room::select('*');
        $Teacher_room = $query->where('date', $queryData['sdatetw'])->get()->toArray();
        $Teacher_room_data = array();
        foreach($Teacher_room as $Teacher_room_row){
            $Teacher_room_data[$Teacher_room_row['idno']] = $Teacher_room_row;
        }

        foreach($Teacher_room_data as $room_row){
            $idno[] = $room_row['idno'];
        }

        $query = Teacher_car::select('*');
        $Teacher_car = $query->where('date', $queryData['sdatetw'])->get()->toArray();
        $Teacher_car_data = array();
        $Teacher_car_count_1 = array();//type1併車
        $Teacher_car_count_2 = array();//type2併車
        foreach($Teacher_car as $Teacher_car_row){
            $Teacher_car_data[$Teacher_car_row['idno'].'_'.$Teacher_car_row['type']] = $Teacher_car_row;
        }

        foreach($Teacher_car_data as $car_row){
            if(!in_array($car_row['idno'], $idno)){
                $idno[] = $car_row['idno'];
            }
            if($car_row['type'] == '1'){
                if(!in_array($car_row['car'], $Teacher_car_count_1)){
                    $Teacher_car_count_1[$car_row['car']] = '1';
                }else{
                    $Teacher_car_count_1[$car_row['car']] = $Teacher_car_count_1[$car_row['car']]+'1';
                }
            }else{
                if(!in_array($car_row['car'], $Teacher_car_count_2)){
                    $Teacher_car_count_2[$car_row['car']] = '1';
                }else{
                    $Teacher_car_count_2[$car_row['car']] = $Teacher_car_count_2[$car_row['car']]+'1';
                }
            }
        }

        $query = Teacher_food::select('*');
        $Teacher_food = $query->where('date', $queryData['sdatetw'])->get()->toArray();
        $Teacher_food_data = array();
        foreach($Teacher_food as $Teacher_food_row){
            $Teacher_food_data[$Teacher_food_row['idno']] = $Teacher_food_row;
        }

        foreach($Teacher_food_data as $food_row){
            if(!in_array($food_row['idno'], $idno)){
                $idno[] = $food_row['idno'];
            }
        }

        $data = array();

        foreach($idno as $row){

            if(isset($Teacher_room_data[$row])){
                $class_weeks_id = $Teacher_room_data[$row]['class_weeks_id'];
            }else if(isset($Teacher_car_data[$row])){
                $class_weeks_id = $Teacher_car_data[$row]['class_weeks_id'];
            }else if(isset($Teacher_food_data[$row])){
                $class_weeks_id = $Teacher_food_data[$row]['class_weeks_id'];
            }

            $Teacher_by_week = Teacher_by_week::where('idno', $row)->where('class_week_id', $class_weeks_id)->first()->toArray();
            $Class_weeks = Class_weeks::where('id', $Teacher_by_week['class_week_id'])->first()->toArray();
            $t09tb = T09tb::where('class', $Class_weeks['class'])->where('term', $Class_weeks['term'])->where('idno', $row)->get()->toArray();
            $class_time = ''; // 授課時間
            foreach($t09tb as $t09tb_row){
                $t06tb = T06tb::where('class', $t09tb_row['class'])->where('term', $t09tb_row['term'])->where('course', $t09tb_row['course'])->where('date', $queryData['sdatetw'])->get()->toArray();
                if(!empty($t06tb)){
                    $class_time .= $t06tb[0]['stime'] ." ~ ". $t06tb[0]['etime']."\n";
                }
            }

            $M01tb = M01tb::where('idno', $row)->first()->toArray();
            $T01tb = T01tb::where('class', $Class_weeks['class'])->first()->toArray();
            $T04tb = T04tb::where('class', $Class_weeks['class'])->where('term', $Class_weeks['term'])->first()->toArray();
            $M09tb = M09tb::where('userid', $T04tb['sponsor'])->first()->toArray();

            if(!empty($T04tb['site'])){
                if($T04tb['site_branch'] == 1){
                    $M14tb = M14tb::where('site', $T04tb['site'])->first()->toArray();
                    $T04tb['site'] = $M14tb['name'];
                } else if($T04tb['site_branch'] == 2){
                    $M14tb = Edu_classroom::where('roomno', $T04tb['site'])->first()->toArray();
                    $T04tb['site'] = $M14tb['roomname'];
                }    
            }

            $breakfast = "";
            $lunch = "";
            $dinner = "";

            if(!empty($Teacher_food_data[$row])){
                if($Teacher_food_data[$row]['breakfast'] == 'Y'){
                    if($Teacher_food_data[$row]['breakfast_type2'] == '1'){
                        $breakfast = "V";
                    }else{
                        $breakfast = "◎";
                    }
                    if($Teacher_food_data[$row]['breakfast_type'] == '2'){
                        $breakfast .= "\n素";
                    }
                }
                if($Teacher_food_data[$row]['lunch'] == 'Y'){
                    if($Teacher_food_data[$row]['lunch_type2'] == '1'){
                        $lunch = "V";
                    }else{
                        $lunch = "◎";
                    }
                    if($Teacher_food_data[$row]['lunch_type'] == '2'){
                        $lunch .= "\n素";
                    }
                }
                if($Teacher_food_data[$row]['dinner'] == 'Y'){
                    if($Teacher_food_data[$row]['dinner_type2'] == '1'){
                        $dinner = "V";
                    }else{
                        $dinner = "◎";
                    }
                    if($Teacher_food_data[$row]['dinner_type'] == '2'){
                        $dinner .= "\n素";
                    }
                }
            }

            $stay = "";

            if(!empty($Teacher_room_data[$row])){
                if($Teacher_room_data[$row]['morning'] == 'Y'){
                    $stay .= "早";
                }
                if($Teacher_room_data[$row]['noon'] == 'Y'){
                    $stay .= "午";
                }
                if($Teacher_room_data[$row]['evening'] == 'Y'){
                    $stay .= "夜";
                }
            }

            $car_type_1 = '';
            $car_type_2 = '';
            $end = '';
            $start = '';
            $car1 = '';
            $car2 = '';
            $license_plate1 = '';
            $license_plate2 = '';
            $remark1 = '';
            $remark2 = '';

            if($Teacher_by_week['come_by_self'] == 'Y'){
                if($Teacher_by_week['the_day_before'] == 'N'){
                    if($Class_weeks['sdate'] == $queryData['sdatetw']){
                        $car_type_1 = '自來';
                    }
                }else{
                    if(date('Ymd',strtotime('-1 day', strtotime(($Class_weeks['sdate']+19110000)))) == ($queryData['sdatetw']+19110000)){
                        $car_type_1 = '自來';
                    }
                }
            }
            if($Teacher_by_week['go_by_self'] == 'Y'){
                if($Teacher_by_week['the_day_after'] == 'N'){
                    if($Class_weeks['edate'] == $queryData['sdatetw']){
                        $car_type_2 = '自回';
                    }
                }else{
                    if(date('Ymd',strtotime('+1 day', strtotime(($Class_weeks['sdate']+19110000)))) == ($queryData['sdatetw']+19110000)){
                        $car_type_2 = '自回';
                    }
                }
            }

            if(isset($Teacher_car_data[$row.'_1'])){
                $car_type_1 = '接 '.substr($Teacher_car_data[$row.'_1']['time'], 0, 5).' '.$Teacher_car_data[$row.'_1']['start'];
                $end = $Teacher_car_data[$row.'_1']['end'];
                if(!empty($Teacher_car_data[$row.'_1']['car'])){
                    $car1 = config('app.car_1')[$Teacher_car_data[$row.'_1']['car']];
                }else{
                    $car1 = '尚未安排';
                }
                if($Teacher_car_count_1[$Teacher_car_data[$row.'_1']['car']] > '1'){
                    $car1 .= "(併".$Teacher_car_count_1[$Teacher_car_data[$row.'_1']['car']].")";
                }
                if(!empty($Teacher_car_data[$row.'_1']['license_plate'])){
                    $License = License_plate_setting::where('license_plate', $Teacher_car_data[$row.'_1']['license_plate'])->first();
                    if(!empty($License)){
                        $license_plate1 = $License->call;
                    }
                }
                if(!empty($Teacher_car_data[$row.'_1']['remark'])){
                    $remark1 = $Teacher_car_data[$row.'_1']['remark'];
                }

            }
            if(isset($Teacher_car_data[$row.'_2'])){
                $car_type_2 = '送 '.substr($Teacher_car_data[$row.'_2']['time'], 0, 5).' '.$Teacher_car_data[$row.'_2']['end'];
                $start = $Teacher_car_data[$row.'_2']['start'];
                if(!empty($Teacher_car_data[$row.'_2']['car'])){
                    $car2 = config('app.car_A')[$Teacher_car_data[$row.'_2']['car']];
                }else{
                    $car2 = '尚未安排';
                }

                if($Teacher_car_count_2[$Teacher_car_data[$row.'_2']['car']] > '1'){
                    $car1 .= "(併".$Teacher_car_count_2[$Teacher_car_data[$row.'_2']['car']].")";
                }
                if(!empty($Teacher_car_data[$row.'_2']['license_plate'])){
                    $License = License_plate_setting::where('license_plate', $Teacher_car_data[$row.'_2']['license_plate'])->first();
                    if(!empty($License)){
                        $license_plate1 = $License->call;
                    }
                }
                if(!empty($Teacher_car_data[$row.'_2']['remark'])){
                    $remark2 = $Teacher_car_data[$row.'_2']['remark'];
                }

            }

            $data[$row] = array(
                'name' => $M01tb['cname'],//講座姓名
                'position' => $M01tb['dept'].$M01tb['position'],//單位職稱
                'class_name' => $T01tb['name']."\n".$T04tb['term']."期\n".$T04tb['site']."(".$M09tb['username'].")",//班期別 教室（承辦人）
                'breakfast' => $breakfast,//早餐
                'lunch' => $lunch,//中餐
                'dinner' => $dinner,//晚餐
                'stay' => $stay,//住宿(早中晚修)
                'room' => $Teacher_by_week['room_no'],//房號
                'clas_time' => $class_time,//授課時間
                'car_type_1' => $car_type_1,//接時間地點
                'mobiltel' => $M01tb['mobiltel'],//講師手機
                'car_type_2' => $car_type_2,//送時間地點
                'end' => $end,//下車處
                'start' => $start,//上車處
                'car1' => $car1,//接車輛安排 (併幾)
                'car2' => $car2,//送車輛安排 (併幾)
                'license_plate1' => $license_plate1,//接呼號
                'license_plate2' => $license_plate2,//送呼號
                'remark1' => $remark1,//接備註
                'remark2' => $remark2,//送備註
            );

            if($data[$row]['car_type_1']=='' && $data[$row]['car_type_2']==''){
                $car_type_1 = '續住';
            }

        }

        // dd($data);

        return $data;
    }

    public function getLectureReceptionList1($queryData = [])
    {
        $idno = array();
        $query = Teacher_room::select('*');
        $Teacher_room = $query->where('date', $queryData['sdatetw'])->get()->toArray();
        $Teacher_room_data = array();
        foreach($Teacher_room as $Teacher_room_row){
            $Teacher_room_data[$Teacher_room_row['idno']] = $Teacher_room_row;
        }

        foreach($Teacher_room_data as $room_row){
            $idno[] = $room_row['idno'];
        }

        $query = Teacher_car::select('*');
        $Teacher_car = $query->where('date', $queryData['sdatetw'])->get()->toArray();
        $Teacher_car_data = array();
        $Teacher_car_count_1 = array();//type1併車
        $Teacher_car_count_2 = array();//type2併車
        foreach($Teacher_car as $Teacher_car_row){
            $Teacher_car_data[$Teacher_car_row['idno'].'_'.$Teacher_car_row['type']] = $Teacher_car_row;
        }

        foreach($Teacher_car_data as $car_row){
            if(!in_array($car_row['idno'], $idno)){
                $idno[] = $car_row['idno'];
            }
            if($car_row['type'] == '1'){
                if(!in_array($car_row['car'], $Teacher_car_count_1)){
                    $Teacher_car_count_1[$car_row['car']] = '1';
                }else{
                    $Teacher_car_count_1[$car_row['car']] = $Teacher_car_count_1[$car_row['car']]+'1';
                }
            }else{
                if(!in_array($car_row['car'], $Teacher_car_count_2)){
                    $Teacher_car_count_2[$car_row['car']] = '1';
                }else{
                    $Teacher_car_count_2[$car_row['car']] = $Teacher_car_count_2[$car_row['car']]+'1';
                }
            }
        }

        $query = Teacher_food::select('*');
        $Teacher_food = $query->where('date', $queryData['sdatetw'])->get()->toArray();
        $Teacher_food_data = array();
        foreach($Teacher_food as $Teacher_food_row){
            $Teacher_food_data[$Teacher_food_row['idno']] = $Teacher_food_row;
        }

        foreach($Teacher_food_data as $food_row){
            if(!in_array($food_row['idno'], $idno)){
                $idno[] = $food_row['idno'];
            }
        }

        $data = array();

        foreach($idno as $row){

            if(isset($Teacher_room_data[$row])){
                $class_weeks_id = $Teacher_room_data[$row]['class_weeks_id'];
            }else if(isset($Teacher_car_data[$row])){
                $class_weeks_id = $Teacher_car_data[$row]['class_weeks_id'];
            }else if(isset($Teacher_food_data[$row])){
                $class_weeks_id = $Teacher_food_data[$row]['class_weeks_id'];
            }

            $Teacher_by_week = Teacher_by_week::where('idno', $row)->where('class_week_id', $class_weeks_id)->first()->toArray();
            $Class_weeks = Class_weeks::where('id', $Teacher_by_week['class_week_id'])->first()->toArray();
            $t09tb = T09tb::where('class', $Class_weeks['class'])->where('term', $Class_weeks['term'])->where('idno', $row)->get()->toArray();
            $class_time = ''; // 授課時間
            foreach($t09tb as $t09tb_row){
                $t06tb = T06tb::where('class', $t09tb_row['class'])->where('term', $t09tb_row['term'])->where('course', $t09tb_row['course'])->where('date', $queryData['sdatetw'])->get()->toArray();
                if(!empty($t06tb)){
                    $class_time .= $t06tb[0]['stime'] ." ~ ". $t06tb[0]['etime']."\n";
                }
            }

            $M01tb = M01tb::where('idno', $row)->first()->toArray();
            $T01tb = T01tb::where('class', $Class_weeks['class'])->first()->toArray();
            $T04tb = T04tb::where('class', $Class_weeks['class'])->where('term', $Class_weeks['term'])->first()->toArray();
            $M09tb = M09tb::where('userid', $T04tb['sponsor'])->first()->toArray();

            if(!empty($T04tb['site'])){
                if($T04tb['site_branch'] == 1){
                    $M14tb = M14tb::where('site', $T04tb['site'])->first()->toArray();
                    $T04tb['site'] = $M14tb['name'];
                } else if($T04tb['site_branch'] == 2){
                    $M14tb = Edu_classroom::where('roomno', $T04tb['site'])->first()->toArray();
                    $T04tb['site'] = $M14tb['roomname'];
                }
            }

            $breakfast = "";
            $lunch = "";
            $dinner = "";

            if(!empty($Teacher_food_data[$row])){
                if($Teacher_food_data[$row]['breakfast'] == 'Y'){
                    if($Teacher_food_data[$row]['breakfast_type2'] == '1'){
                        $breakfast = "V";
                    }else{
                        $breakfast = "◎";
                    }
                    if($Teacher_food_data[$row]['breakfast_type'] == '2'){
                        $breakfast .= "\n素";
                    }
                }
                if($Teacher_food_data[$row]['lunch'] == 'Y'){
                    if($Teacher_food_data[$row]['lunch_type2'] == '1'){
                        $lunch = "V";
                    }else{
                        $lunch = "◎";
                    }
                    if($Teacher_food_data[$row]['lunch_type'] == '2'){
                        $lunch .= "\n素";
                    }
                }
                if($Teacher_food_data[$row]['dinner'] == 'Y'){
                    if($Teacher_food_data[$row]['dinner_type2'] == '1'){
                        $dinner = "V";
                    }else{
                        $dinner = "◎";
                    }
                    if($Teacher_food_data[$row]['dinner_type'] == '2'){
                        $dinner .= "\n素";
                    }
                }
            }

            $stay = "";

            if(!empty($Teacher_room_data[$row])){
                if($Teacher_room_data[$row]['morning'] == 'Y'){
                    $stay .= "早";
                }
                if($Teacher_room_data[$row]['noon'] == 'Y'){
                    $stay .= "午";
                }
                if($Teacher_room_data[$row]['evening'] == 'Y'){
                    $stay .= "夜";
                }
            }

            $car_type_1 = '';
            $car_type_2 = '';
            $end = '';
            $start = '';
            $car1 = '';
            $car2 = '';
            $license_plate1 = '';
            $license_plate2 = '';
            $remark1 = '';
            $remark2 = '';

            if($Teacher_by_week['come_by_self'] == 'Y'){
                if($Teacher_by_week['the_day_before'] == 'N'){
                    if($Class_weeks['sdate'] == $queryData['sdatetw']){
                        $car_type_1 = '自來';
                    }
                }else{
                    if(date('Ymd',strtotime('-1 day', strtotime(($Class_weeks['sdate']+19110000)))) == ($queryData['sdatetw']+19110000)){
                        $car_type_1 = '自來';
                    }
                }
            }
            if($Teacher_by_week['go_by_self'] == 'Y'){
                if($Teacher_by_week['the_day_after'] == 'N'){
                    if($Class_weeks['edate'] == $queryData['sdatetw']){
                        $car_type_2 = '自回';
                    }
                }else{
                    if(date('Ymd',strtotime('+1 day', strtotime(($Class_weeks['sdate']+19110000)))) == ($queryData['sdatetw']+19110000)){
                        $car_type_2 = '自回';
                    }
                }
            }

            if(isset($Teacher_car_data[$row.'_1'])){
                $car_type_1 = '接 '.substr($Teacher_car_data[$row.'_1']['time'], 0, 5).' '.$Teacher_car_data[$row.'_1']['start'];
                $end = $Teacher_car_data[$row.'_1']['end'];
                if(!empty($Teacher_car_data[$row.'_1']['car'])){
                    $car1 = config('app.car_1')[$Teacher_car_data[$row.'_1']['car']];
                }else{
                    $car1 = '尚未安排';
                }
                if($Teacher_car_count_1[$Teacher_car_data[$row.'_1']['car']] > '1'){
                    $car1 .= "(併".$Teacher_car_count_1[$Teacher_car_data[$row.'_1']['car']].")";
                }
                if(!empty($Teacher_car_data[$row.'_1']['license_plate'])){
                    $License = License_plate_setting::where('license_plate', $Teacher_car_data[$row.'_1']['license_plate'])->first();
                    if(!empty($License)){
                        $license_plate1 = $License->call;
                    }
                }
                if(!empty($Teacher_car_data[$row.'_1']['remark'])){
                    $remark1 = $Teacher_car_data[$row.'_1']['remark'];
                }

            }
            if(isset($Teacher_car_data[$row.'_2'])){
                $car_type_2 = '送 '.substr($Teacher_car_data[$row.'_2']['time'], 0, 5).' '.$Teacher_car_data[$row.'_2']['end'];
                $start = $Teacher_car_data[$row.'_2']['start'];
                if(!empty($Teacher_car_data[$row.'_2']['car'])){
                    $car2 = config('app.car_A')[$Teacher_car_data[$row.'_2']['car']];
                }else{
                    $car2 = '尚未安排';
                }
                if($Teacher_car_count_2[$Teacher_car_data[$row.'_2']['car']] > '1'){
                    $car1 .= "(併".$Teacher_car_count_2[$Teacher_car_data[$row.'_2']['car']].")";
                }
                if(!empty($Teacher_car_data[$row.'_2']['license_plate'])){
                    $License = License_plate_setting::where('license_plate', $Teacher_car_data[$row.'_2']['license_plate'])->first();
                    if(!empty($License)){
                        $license_plate1 = $License->call;
                    }
                }
                if(!empty($Teacher_car_data[$row.'_2']['remark'])){
                    $remark2 = $Teacher_car_data[$row.'_2']['remark'];
                }

            }

            $data[$row] = array(
                'name' => mb_substr($M01tb['cname'], 0, 1, "UTF-8").'○'.mb_substr($M01tb['cname'], 2, 1, "UTF-8"),//講座姓名
                'position' => $M01tb['dept'].$M01tb['position'],//單位職稱
                'class_name' => $T01tb['name']."\n".$T04tb['term']."期\n".$T04tb['site']."(".$M09tb['username'].")",//班期別 教室（承辦人）
                'breakfast' => $breakfast,//早餐
                'lunch' => $lunch,//中餐
                'dinner' => $dinner,//晚餐
                'stay' => $stay,//住宿(早中晚修)
                'room' => $Teacher_by_week['room_no'],//房號
                'clas_time' => $class_time,//授課時間
                'car_type_1' => $car_type_1,//接時間地點
                'mobiltel' => '',//講師手機
                'car_type_2' => $car_type_2,//送時間地點
                'end' => $end,//下車處
                'start' => $start,//上車處
                'car1' => $car1,//接車輛安排 (併幾)
                'car2' => $car2,//送車輛安排 (併幾)
                'license_plate1' => $license_plate1,//接呼號
                'license_plate2' => $license_plate2,//送呼號
                'remark1' => $remark1,//接備註
                'remark2' => $remark2,//送備註
            );

            if($data[$row]['car_type_1']=='' && $data[$row]['car_type_2']==''){
                $car_type_1 = '續住';
            }

        }

        // dd($data);

        return $data;
    }

}
