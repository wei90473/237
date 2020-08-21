<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassWeek extends Model
{

    protected $table = 'class_weeks';

    protected $guarded = [];

    public $timestamps = false;
}