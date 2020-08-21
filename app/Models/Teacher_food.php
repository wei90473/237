<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher_food extends Model
{

    protected $table = 'teacher_food';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'idno', 'class_weeks_id', 'breakfast', 'breakfast_type', 'breakfast_type2', 'lunch', 'lunch_type', 'lunch_type2', 'dinner', 'dinner_type', 'dinner_type2', 'date');
}