<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher_by_week extends Model
{

    protected $table = 'teacher_by_week';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'class_week_id', 'idno', 't09tb_id', 'the_day_before', 'the_day_after', 'drive_by_self', 'come_by_self', 'go_by_self', 'demand', 'confirm', 'confirm2');
}