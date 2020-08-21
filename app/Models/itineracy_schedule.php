<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itineracy_schedule extends Model
{
    protected $table = 'itineracy_schedule';

    public $timestamps = false;
    
    protected $fillable = array('class','yerly','term','city','presetdate','actualdate','quota','staff','address','fee');
}
  
