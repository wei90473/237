<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_loansroom extends Model
{
    protected $table = 'edu_loansroom';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('applyno', 'startdate', 'enddate', 'croomclsno','bedroom', 'floorno', 'bedno', 'sex');
}