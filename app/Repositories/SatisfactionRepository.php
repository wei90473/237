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
use App\Models\T54tb;
use App\Models\M16tb;
use DB ;

class SatisfactionRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSatisfactionList($queryData = [])
    {
        $query = T09tb::select('t09tb.class', 't09tb.term', 't09tb.course', 't09tb.idno' ,'t09tb.okrate', 't06tb.hour', 't06tb.name as class_name', 't01tb.name', 't01tb.branchname', 't01tb.yerly', 'm01tb.cname', 'm01tb.dept', 'm01tb.position');

        $query->join('t06tb', function($join)
        {
            $join->on('t06tb.class', '=', 't09tb.class')
            ->on('t06tb.term', '=', 't09tb.term')
            ->on('t06tb.course', '=', 't09tb.course');
        });

        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't09tb.class');
        });

        $query->join('m01tb', function($join)
        {
            $join->on('m01tb.idno', '=', 't09tb.idno');
        });

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('t01tb.yerly', 'desc');
            $query->orderBy('class', 'asc');
            $query->orderBy('term', 'asc');
            $query->orderBy('t06tb.course', 'asc');
            $query->orderBy('t06tb.date', 'asc');
        }

        if(isset($queryData['year_or_day'])){
            if($queryData['year_or_day'] == '1'){
                if ( isset($queryData['yerly1']) && $queryData['yerly1'] ) {
                    $queryData['yerly1'] = str_pad($queryData['yerly1'],3,'0',STR_PAD_LEFT);
                    $queryData['yerly2'] = str_pad($queryData['yerly2'],3,'0',STR_PAD_LEFT);
                    $query->whereBetween('t01tb.yerly', array($queryData['yerly1'], $queryData['yerly2']));
                }

            }else{
                if(!empty($queryData['sdate']) && !empty($queryData['edate'])){
                    $query->whereBetween('t06tb.date', array($queryData['sdate'], $queryData['edate']));
                }
            }
        }

        if ( isset($queryData['experience']) && $queryData['experience'] ) {
            $M16tb_query = M16tb::select('m16tb.idno');
            $M16tb_data = $M16tb_query->where('specialty', $queryData['experience'])->distinct()->get()->toArray();
            if(!empty($M16tb_data)){
                $query->whereIn('t09tb.idno', $M16tb_data);
            }
        }

        if ( isset($queryData['term']) && $queryData['term'] ) {

            // $queryData['term'] = str_pad($queryData['term'] ,2,'0',STR_PAD_LEFT);

            $query->where('t09tb.term', $queryData['term']);
        }

        if(!empty($queryData['class_name_1']) && empty($queryData['class_name_2']) && empty($queryData['class_name_3'])){
            $query->where('t06tb.name', 'like', "%{$queryData['class_name_1']}%");
        }

        if(!empty($queryData['class_name_1']) && !empty($queryData['class_name_2']) && empty($queryData['class_name_3'])){
            $query->where('t06tb.name', 'like', "%{$queryData['class_name_1']}%")->orWhere('t06tb.name', 'like', "%{$queryData['class_name_2']}%");
        }

        if(!empty($queryData['class_name_1']) && !empty($queryData['class_name_2']) && !empty($queryData['class_name_3'])){
            $query->where('t06tb.name', 'like', "%{$queryData['class_name_1']}%")->orWhere('t06tb.name', 'like', "%{$queryData['class_name_2']}%")->orWhere('t06tb.name', 'like', "%{$queryData['class_name_3']}%");
        }

        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t09tb.class', 'LIKE', '%'.$queryData['class'].'%');
        }

        // 班別
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'LIKE', '%'.$queryData['name'].'%');
        }

        if ( isset($queryData['class_branch_name']) && $queryData['class_branch_name'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['class_branch_name'].'%');
        }

        if ( isset($queryData['class_branch_name']) && $queryData['class_branch_name'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['class_branch_name'].'%');
        }

        if ( isset($queryData['idno']) && $queryData['idno'] ) {
            $query->where('t09tb.idno', '=', $queryData['idno']);
        }

        if ( isset($queryData['teacher']) && $queryData['teacher'] ) {
            $query->where('m01tb.cname', '=', $queryData['teacher']);
        }

        if ( isset($queryData['dept']) && $queryData['dept'] ) {
            $query->where('m01tb.dept', '=', $queryData['dept']);
        }


        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        foreach($data as & $row){
            $query = T54tb::select('t54tb.idno');
            $T54tb_data = $query->where('class', $row->class)->where('term', $row->term)->where('course', $row->course)->where('idno', $row->idno)->get()->toArray();
            if(empty($T54tb_data)){
                $row->okrate = '不列入統計';
            }else{
                if($row->okrate < 1){
                    $row->okrate = '尚未調查';
                }
            }
        }

        return $data;
    }

    public function getSatisfaction($queryData = [])
    {
        $query = T09tb::select('t09tb.class', 't09tb.term', 't09tb.course', 't09tb.idno' ,'t09tb.okrate', 't06tb.hour', 't06tb.name as class_name', 't01tb.name', 't01tb.branchname', 't01tb.yerly', 'm01tb.cname', 'm01tb.dept', 'm01tb.position');

        $query->join('t06tb', function($join)
        {
            $join->on('t06tb.class', '=', 't09tb.class')
            ->on('t06tb.term', '=', 't09tb.term')
            ->on('t06tb.course', '=', 't09tb.course');
        });

        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't09tb.class');
        });

        $query->join('m01tb', function($join)
        {
            $join->on('m01tb.idno', '=', 't09tb.idno');
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
            $query->orderBy('t06tb.date', 'asc');
        }

        if(isset($queryData['year_or_day'])){
            if($queryData['year_or_day'] == '1'){
                if ( isset($queryData['yerly1']) && $queryData['yerly1'] ) {
                    $queryData['yerly1'] = str_pad($queryData['yerly1'],3,'0',STR_PAD_LEFT);
                    $queryData['yerly2'] = str_pad($queryData['yerly2'],3,'0',STR_PAD_LEFT);
                    $query->whereBetween('t01tb.yerly', array($queryData['yerly1'], $queryData['yerly2']));
                }

            }else{
                if(!empty($queryData['sdate']) && !empty($queryData['edate'])){
                    $query->whereBetween('t06tb.date', array($queryData['sdate'], $queryData['edate']));
                }
            }
        }

        if ( isset($queryData['experience']) && $queryData['experience'] ) {
            $M16tb_query = M16tb::select('m16tb.idno');
            $M16tb_data = $M16tb_query->where('specialty', $queryData['experience'])->distinct()->get()->toArray();
            if(!empty($M16tb_data)){
                $query->whereIn('t09tb.idno', $M16tb_data);
            }
        }

        if ( isset($queryData['term']) && $queryData['term'] ) {

            // $queryData['term'] = str_pad($queryData['term'] ,2,'0',STR_PAD_LEFT);

            $query->where('t09tb.term', $queryData['term']);
        }

        if(!empty($queryData['class_name_1']) && empty($queryData['class_name_2']) && empty($queryData['class_name_3'])){
            $query->where('t06tb.name', 'like', "%{$queryData['class_name_1']}%");
        }

        if(!empty($queryData['class_name_1']) && !empty($queryData['class_name_2']) && empty($queryData['class_name_3'])){
            $query->where('t06tb.name', 'like', "%{$queryData['class_name_1']}%")->orWhere('t06tb.name', 'like', "%{$queryData['class_name_2']}%");
        }

        if(!empty($queryData['class_name_1']) && !empty($queryData['class_name_2']) && !empty($queryData['class_name_3'])){
            $query->where('t06tb.name', 'like', "%{$queryData['class_name_1']}%")->orWhere('t06tb.name', 'like', "%{$queryData['class_name_2']}%")->orWhere('t06tb.name', 'like', "%{$queryData['class_name_3']}%");
        }

        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t09tb.class', 'LIKE', '%'.$queryData['class'].'%');
        }

        // 班別
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'LIKE', '%'.$queryData['name'].'%');
        }

        if ( isset($queryData['class_branch_name']) && $queryData['class_branch_name'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['class_branch_name'].'%');
        }

        if ( isset($queryData['class_branch_name']) && $queryData['class_branch_name'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['class_branch_name'].'%');
        }

        if ( isset($queryData['idno']) && $queryData['idno'] ) {
            $query->where('t09tb.idno', '=', $queryData['idno']);
        }

        if ( isset($queryData['teacher']) && $queryData['teacher'] ) {
            $query->where('m01tb.cname', '=', $queryData['teacher']);
        }

        if ( isset($queryData['dept']) && $queryData['dept'] ) {
            $query->where('m01tb.dept', '=', $queryData['dept']);
        }


        $data = $query->get()->toArray();

        foreach($data as & $row){
            $query = T54tb::select('t54tb.idno');
            $T54tb_data = $query->where('class', $row['class'])->where('term', $row['term'])->where('course', $row['course'])->where('idno', $row['idno'])->get()->toArray();
            if(empty($T54tb_data)){
                $row['okrate'] = '不列入統計';
            }else{
                if($row['okrate'] < 1){
                    $row['okrate'] = '尚未調查';
                }
            }
        }

        $teacher_data = array();

        foreach($data as $data_key => $data_row){
            $teacher_data[$data_key] = array(
                'yerly' => $data_row['yerly'],
                'cname' => $data_row['cname'],
                'dept' => $data_row['dept'],
                'position' => $data_row['position'],
                'name' => $data_row['name'],
                'branchname' => $data_row['branchname'],
                'class_name' => $data_row['class_name'],
                'hour' => $data_row['hour'],
                'okrate' => $data_row['okrate'],
            );
        }

        return $teacher_data;
    }

    public function getSatisfaction2($queryData = [])
    {
        $date_now = date('Ymd') - 19110000;
        // dd($date_now);
        $query = T09tb::select('t09tb.idno' , DB::raw('round(AVG( case when t09tb.okrate > 0 then t09tb.okrate end),2) as okrate'), DB::raw('SUM(t06tb.hour) as total_hour'), DB::raw('SUM(case when t06tb.date < '.$date_now.' then t06tb.hour else 0 end) as hour1'), DB::raw('SUM(case when t06tb.date > '.$date_now.' then t06tb.hour else 0 end) as hour2'), 't01tb.yerly', 'm01tb.cname');

        $query->join('t06tb', function($join)
        {
            $join->on('t06tb.class', '=', 't09tb.class')
            ->on('t06tb.term', '=', 't09tb.term')
            ->on('t06tb.course', '=', 't09tb.course');
        });

        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't09tb.class');
        });

        $query->join('m01tb', function($join)
        {
            $join->on('m01tb.idno', '=', 't09tb.idno');
        });

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('cname', 'asc');
            $query->orderBy('yerly', 'desc');
            $query->groupBy('yerly');
            $query->groupBy('idno');
        }

        if(isset($queryData['year_or_day'])){
            if($queryData['year_or_day'] == '1'){
                if ( isset($queryData['yerly1']) && $queryData['yerly1'] ) {
                    $queryData['yerly1'] = str_pad($queryData['yerly1'],3,'0',STR_PAD_LEFT);
                    $queryData['yerly2'] = str_pad($queryData['yerly2'],3,'0',STR_PAD_LEFT);
                    $query->whereBetween('t01tb.yerly', array($queryData['yerly1'], $queryData['yerly2']));
                }

            }else{
                if(!empty($queryData['sdate']) && !empty($queryData['edate'])){
                    $query->whereBetween('t06tb.date', array($queryData['sdate'], $queryData['edate']));
                }
            }
        }

        if ( isset($queryData['experience']) && $queryData['experience'] ) {
            $M16tb_query = M16tb::select('m16tb.idno');
            $M16tb_data = $M16tb_query->where('specialty', $queryData['experience'])->distinct()->get()->toArray();
            if(!empty($M16tb_data)){
                $query->whereIn('t09tb.idno', $M16tb_data);
            }
        }

        if ( isset($queryData['term']) && $queryData['term'] ) {

            // $queryData['term'] = str_pad($queryData['term'] ,2,'0',STR_PAD_LEFT);

            $query->where('t09tb.term', $queryData['term']);
        }

        if(!empty($queryData['class_name_1']) && empty($queryData['class_name_2']) && empty($queryData['class_name_3'])){
            $query->where('t06tb.name', 'like', "%{$queryData['class_name_1']}%");
        }

        if(!empty($queryData['class_name_1']) && !empty($queryData['class_name_2']) && empty($queryData['class_name_3'])){
            $query->where('t06tb.name', 'like', "%{$queryData['class_name_1']}%")->orWhere('t06tb.name', 'like', "%{$queryData['class_name_2']}%");
        }

        if(!empty($queryData['class_name_1']) && !empty($queryData['class_name_2']) && !empty($queryData['class_name_3'])){
            $query->where('t06tb.name', 'like', "%{$queryData['class_name_1']}%")->orWhere('t06tb.name', 'like', "%{$queryData['class_name_2']}%")->orWhere('t06tb.name', 'like', "%{$queryData['class_name_3']}%");
        }

        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t09tb.class', 'LIKE', '%'.$queryData['class'].'%');
        }

        // 班別
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'LIKE', '%'.$queryData['name'].'%');
        }

        if ( isset($queryData['class_branch_name']) && $queryData['class_branch_name'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['class_branch_name'].'%');
        }

        if ( isset($queryData['class_branch_name']) && $queryData['class_branch_name'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['class_branch_name'].'%');
        }

        if ( isset($queryData['idno']) && $queryData['idno'] ) {
            $query->where('t09tb.idno', '=', $queryData['idno']);
        }

        if ( isset($queryData['teacher']) && $queryData['teacher'] ) {
            $query->where('m01tb.cname', '=', $queryData['teacher']);
        }

        if ( isset($queryData['dept']) && $queryData['dept'] ) {
            $query->where('m01tb.dept', '=', $queryData['dept']);
        }


        $data = $query->get()->toArray();
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r(round(((100+96.69)/2) , 2));
        // echo "\n</pre>\n";
        // die();

        $teacher_data = array();

        foreach($data as $data_key => $data_row){
            $teacher_data[$data_key] = array(
                'yerly' => $data_row['yerly'],
                'cname' => $data_row['cname'],
                'total_hour' => $data_row['total_hour'],
                'hour1' => $data_row['hour1'],
                'hour2' => $data_row['hour2'],
                'okrate' => $data_row['okrate'],
            );
        }



        return $teacher_data;
    }

    public function getExport($queryData = [])
    {
        $query = T09tb::select('t09tb.class', 't09tb.term', 't09tb.course', 't09tb.idno' ,'t09tb.okrate', 't06tb.hour', 't06tb.name as class_name', 't01tb.name', 't01tb.branchname', 't01tb.yerly', 'm01tb.cname', 'm01tb.dept', 'm01tb.position', 'm09tb.username', 't06tb.date');

        $query->join('t06tb', function($join)
        {
            $join->on('t06tb.class', '=', 't09tb.class')
            ->on('t06tb.term', '=', 't09tb.term')
            ->on('t06tb.course', '=', 't09tb.course');
        });

        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't09tb.class');
        });

        $query->join('t04tb', function($join)
        {
            $join->on('t04tb.class', '=', 't09tb.class')
            ->on('t04tb.term', '=', 't09tb.term');
        });

        $query->join('m09tb', function($join)
        {
            $join->on('m09tb.userid', '=', 't04tb.sponsor');
        });

        $query->join('m01tb', function($join)
        {
            $join->on('m01tb.idno', '=', 't09tb.idno');
        });

        $query->whereBetween('t06tb.date', array($queryData['sdatetw'], $queryData['edatetw']));

        $query->orderBy('t06tb.date', 'asc');
        $query->orderBy('t06tb.course', 'asc');
        $query->orderBy('class', 'asc');
        $query->orderBy('term', 'asc');

        $data = $query->get()->toArray();

        // dd($data);

        foreach($data as & $row){
            $query = T54tb::select('t54tb.idno');
            $T54tb_data = $query->where('class', $row['class'])->where('term', $row['term'])->where('course', $row['course'])->where('idno', $row['idno'])->get()->toArray();
            if(empty($T54tb_data)){
                $row['okrate'] = '不列入統計';
            }else{
                if($row['okrate'] < 1){
                    $row['okrate'] = '尚未調查';
                }
            }
        }

        return $data;

    }


}
