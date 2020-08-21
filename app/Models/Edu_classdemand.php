<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_classdemand extends Model
{

    protected $table = 'edu_classdemand';

    public $timestamps = false;

    // protected $primaryKey = 'id';

    protected $fillable = array('type','class', 'term', 'date', 'signupcnt', 'signupvegan', 'checkincnt', 'checkinvegan', 'counselorcnt', 'counselorvegan','counselorboy','counselorgirl', 'nodincnt', 'earlystaycnt', 'cropoutcnt', 'cropoutvegan', 'endbento','blfcnt');
}