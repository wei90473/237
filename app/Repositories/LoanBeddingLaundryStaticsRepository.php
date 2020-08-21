<?php
namespace App\Repositories;

use DB;
use App\Models\Edu_loansroom;
use App\Models\Edu_bed;
use App\Models\Edu_classroomcls;
use App\Models\Edu_unitset;
use App\Models\T04tb;
use App\Models\T13tb;

class LoanBeddingLaundryStaticsRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getLoanBeddingLaundryStatics($queryData = [])
    {

        $query = Edu_loansroom::select('edu_loansroom.applyno','edu_loansroom.startdate','edu_loansroom.enddate','edu_loanplace.applyuser','edu_loanplace.mstay','edu_loanplace.fstay');

        $query->join('edu_loanplace', function($join)
        {
            $join->on('edu_loanplace.applyno', '=', 'edu_loansroom.applyno');
        });

        $query->where('edu_loansroom.startdate', '>=', $queryData['sdate']);
        $query->where('edu_loansroom.enddate', '<=', $queryData['edate']);
        $query->where('edu_loanplace.applyno', '!=', '0');

        $query->orderBy('edu_loansroom.startdate', 'asc');
        $query->orderBy('edu_loansroom.enddate', 'asc');

        $query->groupBy('edu_loansroom.applyno', 'edu_loansroom.startdate', 'edu_loansroom.enddate');

        $data = $query->get()->toArray();

        foreach($data as & $row){

        	$query = Edu_unitset::select('washingfare');
        	$washingfare = $query->first()->toArray();
        	$row['washingfare'] = (int) floor($washingfare['washingfare']);

        	$floor = array();
            $query = Edu_loansroom::select('edu_loansroom.croomclsno', 'edu_classroomcls.croomclsname');
            $query->join('edu_classroomcls', function($join)
	        {
	            $join->on('edu_classroomcls.croomclsno', '=', 'edu_loansroom.croomclsno');
	        });
            $query->where('edu_loansroom.applyno', $row['applyno']);
            $query->where('edu_loansroom.startdate', '>=', $row['startdate']);
            $query->where('edu_loansroom.enddate', '<=', $row['enddate']);
            $query->groupBy('edu_loansroom.croomclsno');

            $floor_data = $query->get()->toArray();
            $row['floor_data'] = $floor_data;

            foreach($row['floor_data'] as & $floor_row){

                $query = Edu_loansroom::select('bedroom');
	            $query->where('edu_loansroom.applyno', $row['applyno']);
	            $query->where('edu_loansroom.croomclsno', $floor_row['croomclsno']);
	            $query->where('edu_loansroom.startdate', '>=', $row['startdate']);
	            $query->where('edu_loansroom.enddate', '<=', $row['enddate']);
	            $query->orderBy('edu_loansroom.bedroom', 'asc');
	            $query->groupBy('edu_loansroom.bedroom');
	            $bed_data = $query->get()->toArray();

	            if(count($bed_data)>'1'){
	            	$floor_row['room_count'] = count($bed_data);
	            	$room_count = $floor_row['room_count']-1;
	            	$query = Edu_bed::select('roomname');
		            $query->where('edu_bed.bedroom', $bed_data[0]['bedroom']);
		            $room1 = $query->first()->toArray();
		            $query = Edu_bed::select('roomname');
		            $query->where('edu_bed.bedroom', $bed_data[$room_count]['bedroom']);
		            $room2 = $query->first()->toArray();
		            $floor_row['room'] = '('.$room1['roomname'].'~'.$room2['roomname'].')';
	            }else if(count($bed_data)=='1'){
	            	$floor_row['room_count'] = '1';
	            	$query = Edu_bed::select('roomname');
		            $query->where('edu_bed.bedroom', $bed_data[0]['bedroom']);
		            $room1 = $query->first()->toArray();
		            $floor_row['room'] = '('.$room1['roomname'].')';
	            }

            }

        }
        // dd($data);

        return $data;
    }
    /**
     * 取得寢具洗滌數量統計表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getBeddingLaundryStatics($queryData=[])
    {
        $query = Edu_unitset::select('washingfare')->first()->toArray(); // 洗滌費用
        $washingfare = (int) floor($query['washingfare']);

        $query = T04tb::select('t04tb.class','t04tb.term','t01tb.name','t01tb.process','t04tb.sdate','t04tb.edate','t04tb.section');

        $query->join('t01tb', function($join)
        {
            $join->on('t01tb.class', '=', 't04tb.class')->on('t01tb.branch','=',DB::raw("'2'"));
        });
        $query->where('t04tb.sdate', '>=', $queryData['sdate']);
        $query->where('t04tb.edate', '<=', $queryData['edate']);
        $data = $query->get()->toArray();
        // 計算人數
        foreach ($data as $key => $value) {
            $query = T13tb::where('dorm','Y');
            $query->where('class',$value['class']);
            $query->where('term',$value['term']);
            $query->where('status',1);
            $count = $query->count();
            $data[$key]['count'] = $count;
            $data[$key]['washingfare'] = $count*$washingfare;
        }
        return $data;
    }
}
