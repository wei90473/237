<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_holiday extends Model
{
    protected $table = 'edu_holiday';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('holiday', 'holidayname');
}