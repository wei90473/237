<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class M12tb extends Model 
{

    
    protected $table = 'm12tb';

    public $timestamps = false;

    protected $fillable = array('date' ,'holiday');
}