<?php
namespace App\Repositories;

use App\Models\Edu_stayweeks;
use App\Repositories\Repository;
use DateTime;
use DB;

class EdustayweeksRepository extends Repository
{
    public function __construct(Edu_stayweeks $eduStayweeks)
    {
        $this->model = $eduStayweeks;
    }  

    public function getStayweeks($class,$term)
    {
    	return $this->model->select(DB::raw("*"))
                           ->where('class','=',$class)
                           ->where('term','=',$term)
                     	   ->orderBy('week')
                           ->get();
    }

    public function getLongAutoType($class,$term,$week)
    {
        return $this->model->select('auto_type')
                           ->where('class','=',$class)
                           ->where('term','=',$term)
                           ->where('week','=',$week)
                           ->get();
    }

    public function getInfoFromStayweeks($class,$term,$week)
    {
    	$model = $this->model->selectRaw('t04tb.class,
                                          t01tb.yerly,
                                          t04tb.client,
                                          t01tb.name,
                                          t01tb.branchname,
                                          t04tb.term,
                                          t04tb.lock,
                                          edu_stayweeks.week,
                                          edu_stayweeks.staystartdate,
                                          edu_stayweeks.stayenddate, 
                                          edu_stayweeks.staystarttime,
                                          edu_stayweeks.stayendtime')
                            ->Join('t04tb', function($join){
				                  $join->on('t04tb.class', '=', 'edu_stayweeks.class')
				                       ->on('t04tb.term', '=', 'edu_stayweeks.term');
				            })
				            ->Join('t01tb', 't01tb.class', '=', 'edu_stayweeks.class')
				            ->where('edu_stayweeks.class','=',$class)
                           	->where('edu_stayweeks.term','=',$term)
                           	->where('edu_stayweeks.week','=',$week);
                           	
        $data = $model->get();
    	
    	return $data;
    }

    public function getLongDormDateWeek($class,$term)
    {
    	$model = $this->model->selectRaw('staystartdate,stayenddate,week');

    	$model->where('class','=',$class);
    	$model->where('term','=',$term);

    	$data = $model->get();
    	
    	return $data;
    }

    public function createWeeks($class,$term,$courseDate)
    {
    	$dt=array();
        $weekstart=0;
        $i=0;
        foreach ($courseDate as $key => $value) {
            $y=intval(substr($value['date'],0,3))+1911;
            $m=intval(substr($value['date'],3,2));
            $d=intval(substr($value['date'],5,2));
            $toUnixtime=mktime(0,0,0,$m,$d,$y);
            $w=date('w',$toUnixtime);

            if($w==0){
                $time = $toUnixtime + (-6*86400);
                $y=date('Y', $time)-1911;
                $m=date('m', $time);
                $d=date('d', $time);

                $weekmonday = str_pad($y, 3,'0',STR_PAD_LEFT).str_pad($m, 2,'0',STR_PAD_LEFT).str_pad($d, 2,'0',STR_PAD_LEFT);
            } else {
                $j=$w-1;
                $time = $toUnixtime + (($j*-1)*86400);
                $y=date('Y', $time)-1911;
                $m=date('m', $time);
                $d=date('d', $time);

                $weekmonday = str_pad($y, 3,'0',STR_PAD_LEFT).str_pad($m, 2,'0',STR_PAD_LEFT).str_pad($d, 2,'0',STR_PAD_LEFT);
            }

            if ($weekstart != $weekmonday){
                $i++;
                $rec=array();
                $rec['week']=$i;
                $rec['staystartdate']=$value['date'];
                $rec['stayenddate']=$value['date'];
                $rec['staystarttime']='1';
                $rec['stayendtime']='1';
                $rec['washing']='0';
                $dt[$i]=$rec;

                $weekstart=$weekmonday;
            } else {
                $dt[$i]['stayenddate'] = $value['date'];
            }
        }

        $updateKey = array();
        $updateKey['class'] = $class;
        $updateKey['term'] = $term;
        foreach ($dt as $key => $value) {
        	$updateKey['week'] = $value['week'];
        	$this->updateOrCreate($updateKey,$dt[$key]);
        }

        return true;
    }

    public function getDormStudentInfo($class,$term,$sdate,$edate,$hasBed='')
    {
    	$model = $this->model->selectRaw('m02tb.cname as student_name,edu_stayweeksdt.floorno,edu_stayweeksdt.bedroom,edu_stayweeksdt.bedno,edu_stayweeks.week');

    	$model->Join('edu_stayweeksdt', function($join){
	                  $join->on('edu_stayweeks.class', '=', 'edu_stayweeksdt.class')
	                       ->on('edu_stayweeks.term', '=', 'edu_stayweeksdt.term');
	            });
    	$model->Join('m02tb','edu_stayweeksdt.idno', '=', 'm02tb.idno');

    	$model->where('edu_stayweeks.class', '=', $class);
    	$model->where('edu_stayweeks.term', '=', $term);

    	$where = sprintf("edu_stayweeks.staystartdate BETWEEN '%s' and '%s'",$sdate,$edate);
    	$model->whereRaw($where);

    	if($hasBed == 'Y'){
    		$model->whereRaw("edu_stayweeksdt.bedno is not null");
    	} else if($hasBed == 'N') {
    		$model->whereRaw("edu_stayweeksdt.bedno is null");
    	}

   	 	$data = $model->get();

   	 	return $data;
    }

}