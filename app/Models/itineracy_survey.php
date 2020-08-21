<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itineracy_survey extends Model
{
    protected $table = 'itineracy_survey';

    public $timestamps = false;
    
    protected $fillable = array('id','yerly','term','city','sponsor','phone1','phone2','mail','fax','presetdate','day','actualdate','actualdays');
}
  
