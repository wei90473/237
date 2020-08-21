<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restructuring extends Model
{
    protected $table = 'restructuring';
    protected $guarded = [];

    public function details(){
        return $this->hasMany('App\Models\RestructuringDetail', 'restructuring_id', 'id')->with(['m17tb']) ;   	
    }

    public function after(){
    	return $this->hasMany('App\Models\RestructuringDetail', 'restructuring_id', 'id')->with(['m17tb'])->where('restructure_type', '=', 'after')->with(['m17tb']) ;
    }

    public function before(){
    	return $this->hasMany('App\Models\RestructuringDetail', 'restructuring_id', 'id')->with(['m17tb'])->where('restructure_type', '=', 'before')->with(['m17tb']) ;
    }   
}