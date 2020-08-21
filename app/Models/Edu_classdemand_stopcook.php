<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_classdemand_stopcook extends Model
{

    protected $table = 'edu_classdemand_stopcook';

    public $timestamps = false;

    // protected $primaryKey = 'id';

    protected $fillable = array('class', 'term','cooktype', 'stopdate');
}