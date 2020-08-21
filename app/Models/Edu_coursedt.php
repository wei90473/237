<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_coursedt extends Model
{
    protected $table = 'edu_coursedt';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = array('id');
}