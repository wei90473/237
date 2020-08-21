<?php
namespace App\Repositories;

use App\Models\Class_process;
use App\Models\Class_process_job;

class Class_processRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getList($queryData = [])
    {
        $query = Class_process::select('id', 'name', 'branch', 'process', 'preset');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('id', 'asc');
        }

        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('class_process.name', 'LIKE', '%'.$queryData['name'].'%');
        }

        if ( isset($queryData['branch']) && $queryData['branch'] ) {
            $query->where('class_process.branch', '=', $queryData['branch']);
        }

        if ( isset($queryData['process']) && $queryData['process'] ) {
            $query->where('class_process.process', 'LIKE', '%'.$queryData['process'].'%');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    public function getSub($id)
    {
        $query = Class_process_job::select('id', 'class_process_id', 'name', 'type', 'job', 'deadline', 'deadline_type', 'deadline_day', 'email', 'freeze', 'file');
        $query->where('class_process_id', '=', $id);
        $query->orderBy('id', 'asc');
        $query->orderBy('type', 'asc');
        $data = $query->get()->toArray();

        foreach($data as & $row){
            if($row['deadline_type'] == '1' || $row['deadline_type'] == '4'){
                $row['deadline_day'] = $row['deadline_day'].'天';
            }
            if($row['deadline_type'] == '2'){
                $row['deadline_day'] = '上週星期'.config('app.day_of_week.'.$row['deadline_day']);
            }
            if($row['deadline_type'] == '3'){
                $row['deadline_day'] = '上月'.$row['deadline_day'].'號';
            }
            if($row['deadline_type'] == '5'){
                $row['deadline_day'] = '下週星期'.config('app.day_of_week.'.$row['deadline_day']);
            }
            if($row['deadline_type'] == '6'){
                $row['deadline_day'] = '下月'.$row['deadline_day'].'號';
            }
            // dd($row['deadline_day']);
        }

        return $data;
    }

}
