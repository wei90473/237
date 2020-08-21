<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandSurvey extends Model
{
    use SoftDeletes;

    protected $table = 'demand_survey';

    protected $primaryKey = 'demand_survey_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('year', 'times', 'purpose', 'sdate', 'edate', 'classes_id');
}