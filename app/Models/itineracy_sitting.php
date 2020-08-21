<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itineracy_sitting extends Model
{
    protected $table = 'itineracy_sitting';

    public $timestamps = false;
    
    protected $fillable = array('id','type','code','name');
}
  
