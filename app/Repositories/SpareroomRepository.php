<?php
namespace App\Repositories;

use App\Models\Spareroom;
use App\Repositories\Repository;
use DateTime;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class SpareroomRepository extends Repository
{
    public function __construct(Spareroom $spareroom)
    {
        $this->model = $spareroom;
    }  

    public function addSpareroom($insertData)
    {
    	DB::table('spareroom')->insert($insertData);

    	return true;
    }

    public function getSpareroom($class,$term,$sex,$week)
    {
    	$model = $this->model->selectRaw('floorno,bedroom,bedno');       
        $model->where('class', '=', $class);
        $model->where('term', '=', $term);
        $model->where('sex', '=', $term);

        if(!empty($week)){
        	$model->where('week','=',$week);
        }

        $data = $model->get();

        return $data;
    }

    public function getSpareroomAll($class,$term,$sex,$week)
    {
    	$model = $this->model->selectRaw('edu_bed.floorno,edu_bed.bedroom,edu_bed.bedno');    
    	$model->Join('edu_bed', function($join){
	                $join->on('spareroom.floorno', '=', 'edu_bed.floorno')
	                     ->on('spareroom.bedroom', '=', 'edu_bed.bedroom');
	            });
    	$model->where('class', '=', $class);
        $model->where('term', '=', $term);
        $model->where('sex', '=', $term);
    	
        if(!empty($week)){
        	$model->where('week','=',$week);
        }

        $data = $model->get();

        return $data;
    }
}

?>