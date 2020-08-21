<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EffectivenessProcess extends Model
{
    use SoftDeletes;

    protected $table = 'effectiveness_process';

    protected $primaryKey = 'effectiveness_process_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('class', 'term', 'times', 'course', 'idno', 'serno', 'ans1', 'ans2', 'ans3', 'upddate', 'fillmk', 'crtdate');
}