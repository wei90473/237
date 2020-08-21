<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher_room extends Model
{

    protected $table = 'teacher_room';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'date', 'morning', 'noon', 'evening', 'confirm', 'class_weeks_id', 'idno', 'mark', 'only_morning');
}