<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_clsroomfee extends Model
{
    protected $table = 'edu_clsroomfee';

    public $timestamps = false;

    //protected $primaryKey = 'id';

    protected $fillable = array('clsroomno', 'feetype', 'timetype', 'fee','holidayfee');


    public function feetypeCode()
    {
		return $this->belongsTo('App\Models\Edu_classcode', 'feetype', 'code')->whereIn('class', [75,77]);    	
    }
}