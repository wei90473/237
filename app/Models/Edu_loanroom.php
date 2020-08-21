<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_loanroom extends Model
{
    protected $table = 'edu_loanroom';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('applyno', 'croomclsno', 'applydate', 'starttime','endtime', 'classroomno');
}