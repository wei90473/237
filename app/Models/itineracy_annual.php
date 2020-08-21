<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itineracy_annual extends Model
{
    protected $table = 'itineracy_annual';

    public $timestamps = false;
    
    protected $fillable = array('id','yerly','term','items','type1','type2','type3','modifytime');
}
  
