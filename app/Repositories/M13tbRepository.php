<?php

namespace App\Repositories;

use App\Models\M13tb;
use DB;
use App\Repositories\Repository;
use DateTime;

class M13tbRepository extends Repository
{
    public function __construct(M13tb $m13tb)
    {
        $this->model = $m13tb;
    }  

    public function getMainOrgans()
    {
    	$now = new DateTime();
    	return $this->model->where('kind', '=', 'Y')
		    			   ->where(function($query) use($now){
		    			   	   $now = $now->format('Ymd');
		    				   $ceDate = (substr($now, 0, 4) - 1911).substr($now, 4);
		    				   $query->where('expdate', '>=', $ceDate)
		    					     ->orWhere('expdate', '=', '');
		    			   })
		    			   ->get();
    }
}