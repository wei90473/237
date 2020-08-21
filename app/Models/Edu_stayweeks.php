<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_stayweeks extends Model
{
    protected $table = 'edu_stayweeks';

    public $timestamps = false;

    protected $primaryKey = 'id';
    protected $fillable = array('annualplan_id', 'class', 'term', 'week', 'staystartdate', 'staystarttime', 'stayenddate', 'stayendtime', 'washing', 'staymcount', 'stayfcount');
}