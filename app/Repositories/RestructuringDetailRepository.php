<?php
namespace App\Repositories;

use App\Models\RestructuringDetail;
use DB;
use App\Repositories\Repository;

class RestructuringDetailRepository extends Repository
{
    public function __construct(RestructuringDetail $restructuring_detail)
    {
        $this->model = $restructuring_detail;
    }  

    public function deleteByRestructuringID($restructuring_id, $enrollorgs = null, $restructure_type = null){
    	$model = $this->model->where('restructuring_id', '=', $restructuring_id);
    	if (null !== $enrollorgs){
    		$model->whereIn('enrollorg', $enrollorgs);
    	}

    	if (null !== $restructure_type){
    		$model->where('restructure_type', '=', $restructure_type);
    	}
    	return $model->delete();
	}
	
	// public function getByEnrollorgs($enrollorgs, $type)
	// {
	// 	$model = $this->model->whereIn('enrollorg', $enrollorgs)
	// 						 ->where('restructure_type', $type);
	// 	return $model->get();
	// }
	public function getByINEnrollorg($detail)
	{
		
		$joinSql = $this->model->selectRaw("DISTINCT restructuring_id")
							   ->where(function($query) use($detail){
								   $query->whereIn('enrollorg', $detail['new_after_enrollorg'])
									     ->where('restructure_type', '=', 'after');
							   })
							   ->orWhere(function($query) use($detail){
								   $query->whereIn('enrollorg', $detail['new_before_enrollorg'])
									     ->where('restructure_type', '=', 'before');
							   })
							   ->toSql();

		$sql = "SELECT * 
				FROM restructuring_detail
				JOIN ({$joinSql}) repeat_detail ON repeat_detail.restructuring_id = restructuring_detail.restructuring_id";
		dd($sql);
		return DB::select($sql);
	}
}