<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandSurveyCommissioned extends Authenticatable
{
    protected $table = 'demand_survey_commissioned';

    public $timestamps = false;

    protected $fillable = array('yerly' ,'item_id' ,'sdate' ,'edate' ,'remark');
}