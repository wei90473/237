<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandSurveyCommissionedPre extends Authenticatable
{
    protected $table = 'demand_survey_commissioned_pre';

    public $timestamps = false;

    protected $fillable = array( 'id','year','item_id','class_name','object','target', 'periods','periods_people','training_days','sdate','edate','entrusting_orga',
    'entrusting_unit','entrusting_contact','phone','e-mail','classroom_type','remarks','audit_status','enable');
}