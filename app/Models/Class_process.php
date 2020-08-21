<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Class_process extends Model
{

    protected $table = 'class_process';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'name', 'branch', 'process', 'preset');
}