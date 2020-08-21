<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher_car extends Model
{

    protected $table = 'teacher_car';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'type', 'class_weeks_id', 'idno', 'time', 'date', 'location1', 'location2', 'location3', 'address', 'start', 'end', 'license_plate', 'price', 'remark', 'car');
}