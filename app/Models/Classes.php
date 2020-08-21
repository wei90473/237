<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use SoftDeletes;

    protected $table = 'classes';

    protected $primaryKey = 'classes_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('number', 'location', 'name', 'name_en', 'type', 'style', 'position_grade', 'handle', 'check', 'traintype', 'is_room', 'commission', 'track', 'hours', 'period', 'period_type', 'qualified', 'waitinglist', 'total_days', 'learn_type', 'digital_hours', 'total_hours', 'entity_hours', 'participant', 'target', 'method', 'remarks', 'upload1', 'public', 'performance_calculation', 'signup', 'participate_unit', 'unit_area', 'officium_1', 'is_supervisor_1', 'is_hr_1', 'officium_2', 'is_supervisor_2', 'is_hr_2', 'same');
}