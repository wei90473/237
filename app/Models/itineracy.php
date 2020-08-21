<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itineracy extends Model
{
    protected $table = 'itineracy';

    public $timestamps = false;
    
    protected $fillable = array('yerly','term','name','surveysdate','surveyedate','sdate','edate','topics');
}
  
