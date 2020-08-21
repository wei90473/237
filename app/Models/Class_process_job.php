<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Class_process_job extends Model
{

    protected $table = 'class_process_job';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'class_process_id', 'name', 'type', 'job', 'deadline', 'deadline_type', 'deadline_day', 'email', 'freeze', 'file');
}