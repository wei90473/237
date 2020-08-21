<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itineracy_course extends Model
{
    protected $table = 'itineracy_course';

    public $timestamps = false;
    
    protected $fillable = array('class','yerly','term','city','annual_id','remake');
}
  
